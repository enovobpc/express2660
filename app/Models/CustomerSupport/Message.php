<?php

namespace App\Models\CustomerSupport;

use App\Models\BroadcastPusher;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redirect;
use Webklex\IMAP\Client;
use Auth, Setting, Mail;

class Message extends \App\Models\BaseModel
{

    use SoftDeletes;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_support_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'from_name', 'to_name', 'subject', 'message', 'inline_attachments'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'message' => 'required',
        'from'    => 'required',
        'to'      => 'required'
    );

    /**
     * Check if has answers
     */
    public function checkAnswers() {

        $oClient = new Client([
            'host'          => Setting::get('customer_support_mail_host'),
            'port'          => Setting::get('customer_support_mail_port'),
            'encryption'    => Setting::get('customer_support_mail_encryption'),
            'validate_cert' => true,
            'username'      => Setting::get('customer_support_mail'),
            'password'      => Setting::get('customer_support_mail_password'),
        ]);

        //Connect to the IMAP Server
        $oClient->connect();

        //get all unread messages from INBOX folder
        $oFolder = $oClient->getFolder('INBOX');
        $unreadMessages = $oClient->getUnseenMessages($oFolder);

        foreach($unreadMessages as $oMessage){
            $subject = $oMessage->subject;
            $message = $oMessage->getHTMLBody();
            $messageText = $oMessage->getTextBody();
            $date    = $oMessage->getDate();
            $email   = $oMessage->getFrom();
            $email   = $email[0];
            $name    = $email->personal;
            $email   = $email->mail;

            try {
                $subject = imap_mime_header_decode($subject);
                $subject = @$subject[0]->text;
            } catch(\Exception $e) {}

            $message = empty($message) ? nl2br($messageText) : $message;

            $attachments = [];
            $oMessage->getAttachments()->each(function ($oAttachment) use (&$attachments) {
                $attachments[] = [
                    'name'    => $oAttachment->name,
                    'content_type' => $oAttachment->content_type,
                    'content' => base64_encode($oAttachment->content)
                ];
            });

            $posOrc = strpos($subject, '[#');
            //$posPrc = strpos($subject, '[PBR-');

            if ($posOrc !== false) { //check customer answers
                $ticketCode = substr($subject, ($posOrc + 2), 9);

                $ticket = Ticket::filterSource()->where('code', $ticketCode)->first();

                if($ticket->exists) {
                    $ticketMessage = new Message();
                    $ticketMessage->ticket_id   = $ticket->id;
                    $ticketMessage->subject     = $subject;
                    $ticketMessage->from        = $email;
                    $ticketMessage->from_name   = $email;
                    $ticketMessage->to          = Setting::get('budget_mail');
                    $ticketMessage->to_name     = 'Sistema';
                    $ticketMessage->message     = $message;
                    $ticketMessage->created_at  = $date;
                    $ticketMessage->inline_attachments = $attachments;
                    $ticketMessage->save();

                    $ticket->status = 'wainting';
                    $ticket->save();

                    $ticket->setNotification(BroadcastPusher::getGlobalChannel(), 'Nova resposta pedido suporte ' . $ticket->code);
                }

            } 
            
           /* elseif($posPrc !== false) { //check provider answers

                $ticketCode = substr($subject, ($posPrc + 5), 9);

                $ticket = Ticket::filterSource()->where('code', $ticketCode)->first();

                if($ticket->exists) {
                    $propose = new Propose();
                    $propose->ticket_id   = $ticket->id;
                    $propose->email       = $email;
                    $propose->subject     = $subject;
                    $propose->from        = $email;
                    $propose->from_name   = $name ? $name : $email;
                    $propose->to          = Setting::get('tickets_mail');
                    $propose->to_name     = 'Sistema';
                    $propose->message     = $message;
                    $propose->created_at  = $date;
                    $propose->attachments = $attachments ? $attachments : [];
                    $propose->read        = 0;
                    $propose->save();

                    $ticket->setNotification(BroadcastPusher::getGlobalChannel(), 'Nova resposta de fornecedor ao orçamento Nº' . $ticket->code);

                    $ticket->provider_status = Ticket::STATUS_PROVIDER_ANSWERED;
                    $ticket->save();
                }

            } */
            
            else { //insert new request

                $ticket = new Ticket();

                $ticket->source      = config('app.source');
                $ticket->subject     = $subject;
                $ticket->name        = $name ? $name : $email;
                $ticket->email       = $email;
                $ticket->date        = $date;
                $ticket->message     = $message;
                $ticket->attachments = $attachments;
                $ticket->status      = 'pending';
                $ticket->setCode();

                $ticket->setNotification(BroadcastPusher::getGlobalChannel(), 'Novo pedido de suporte Nº' . $ticket->code);

                if(Setting::get('tickets_mail_autoresponse_active')) {
                    try {
                        $ticket->subject = '[#' . $ticket->code.'] ' . $ticket->subject;

                        Mail::send('emails.tickets.confirm', compact('budget'), function ($message) use($ticket) {
                            $message->to($ticket->email)
                                ->from(Setting::get('tickets_mail'), config('mail.from.name'))
                                ->subject($ticket->subject);
                        });

                        if(!empty(Setting::get('customer_support_mail_notification'))) {

                            $emails = validateNotificationEmails(Setting::get('tickets_mail_notification'));

                            $text = 'Novo pedido de suporte com o número ' . $ticket->code;

                            Mail::raw($text, compact('budget'), function ($message) use($ticket, $emails) {
                                $message->to($emails['valid'])
                                    ->from(Setting::get('tickets_mail'), config('mail.from.name'))
                                    ->subject('Aviso - Novo Pedido de Suporte - '.$ticket->subject);
                            });
                        }

                    } catch (\Exception $e) {
                        Message::where('ticket_id', $ticket->id)->forceDelete();
                        return Redirect::back()->with('error', 'Erro ao enviar e-mail. O e-mail não foi enviado. Motivo: ' . $e->getMessage() .' on file ' . $e->getFile() . ' line '. $e->getLine());
                    }
                }
            }
        }
    }

    public function getSubject($subject, $ticketCode) {
        return '[#' . $ticketCode . '] ' . $subject;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function ticket()
    {
        return $this->belongsTo('App\Models\CustomerSupport\Ticket', 'ticket_id');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\CustomerSupport\MessageAttachment', 'message_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function setInlineAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = empty($value) ? null : json_encode($value);
    }
    public function getInlineAttachmentsAttribute($value)
    {
        return json_decode($value);
    }
}
