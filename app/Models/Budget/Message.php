<?php

namespace App\Models\Budget;

use App\Models\BroadcastPusher;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redirect;
use Auth, Setting, Mail;

class Message extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_budgets';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'budgets_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'from_name', 'to_name', 'subject', 'message', 'attachments'
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

        $oClient = \Webklex\IMAP\Facades\Client::make([
            'host'          => Setting::get('budgets_mail_host'),
            'port'          => Setting::get('budgets_mail_port'),
            'encryption'    => Setting::get('budgets_mail_encryption'),
            'validate_cert' => true,
            'username'      => Setting::get('budgets_mail'),
            'password'      => Setting::get('budgets_mail_password'),
        ]);

        //Connect to the IMAP Server
        $oClient->connect();

        //get all unread messages from INBOX folder
        $oFolder = $oClient->getFolder('INBOX');
        $unreadMessages = $oFolder->messages()->unseen()->get();

        foreach($unreadMessages as $oMessage){
            $subject = @$oMessage->getSubject()[0];
            $message = $oMessage->getHTMLBody();
            $messageText = $oMessage->getTextBody();
            $date    = @$oMessage->getDate()[0];
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

            //marca como lido
            $oMessage->setFlag('seen');

            $posOrc = strpos($subject, '[ORC-');
            $posPrc = strpos($subject, '[PBR-');

            if ($posOrc !== false) { //check customer answers
                $budgetCode = substr($subject, ($posOrc + 5), 9);

                $budget = Budget::filterSource()->where('budget_no', $budgetCode)->first();

                if($budget->exists) {
                    $budgetMessage = new Message();
                    $budgetMessage->budget_id   = $budget->id;
                    $budgetMessage->subject     = $subject;
                    $budgetMessage->from        = $email;
                    $budgetMessage->from_name   = $email;
                    $budgetMessage->to          = Setting::get('budget_mail');
                    $budgetMessage->to_name     = 'Sistema';
                    $budgetMessage->message     = $message;
                    $budgetMessage->created_at  = $date;
                    $budgetMessage->attachments = $attachments;
                    $budgetMessage->save();

                    $budget->status = 'wainting';
                    $budget->save();

                    $budget->setNotification(BroadcastPusher::getGlobalChannel(), 'O cliente respondeu ao orçamento Nº' . $budget->budget_no);
                }

            } elseif($posPrc !== false) { //check provider answers

                $budgetCode = substr($subject, ($posPrc + 5), 9);

                $budget = Budget::filterSource()->where('budget_no', $budgetCode)->first();

                if($budget->exists) {
                    $propose = new Propose();
                    $propose->budget_id   = $budget->id;
                    $propose->email       = $email;
                    $propose->subject     = $subject;
                    $propose->from        = $email;
                    $propose->from_name   = $name ? $name : $email;
                    $propose->to          = Setting::get('budgets_mail');
                    $propose->to_name     = 'Sistema';
                    $propose->message     = $message;
                    $propose->created_at  = $date;
                    $propose->attachments = $attachments ? $attachments : [];
                    $propose->read        = 0;
                    $propose->save();

                    $budget->setNotification(BroadcastPusher::getGlobalChannel(), 'Nova resposta de fornecedor ao orçamento Nº' . $budget->budget_no);

                    $budget->provider_status = Budget::STATUS_PROVIDER_ANSWERED;
                    $budget->save();
                }

            } else { //insert new request

                $budget = new Budget();

                $budget->source      = config('app.source');
                $budget->subject     = $subject;
                $budget->name        = $name ? $name : $email;
                $budget->email       = $email;
                $budget->date        = $date;
                $budget->message     = $message;
                $budget->attachments = $attachments;
                $budget->status      = 'pending';
                $budget->setBudgetCode();

                $budget->setNotification(BroadcastPusher::getGlobalChannel(), 'Novo pedido de orçamento Nº' . $budget->budget_no);


                if(Setting::get('budgets_mail_autoresponse_active')) {
                    try {
                        $budget->subject = '[ORC-' . $budget->budget_no.'] ' . $budget->subject;


                        Mail::send('emails.budgets.confirm', compact('budget'), function ($message) use($budget) {
                            $message->to($budget->email)
                                ->from(Setting::get('budgets_mail'), config('mail.from.name'))
                                ->subject($budget->subject);
                        });


                        if(!empty(Setting::get('budgets_mail_notification'))) {

                            $emails = validateNotificationEmails(Setting::get('budgets_mail_notification'));

                            $text = 'Foi registado um novo pedido de orçamento com o número ' . $budget->budget_no;

                            Mail::raw($text, function ($message) use($budget, $emails) {
                                $message->to($emails['valid'])
                                    ->from(Setting::get('budgets_mail'), config('mail.from.name'))
                                    ->subject('Aviso de Novo Pedido de Orçamento - '.$budget->subject);
                            });
                        }

                    } catch (\Exception $e) {
                        Message::where('budget_id', $budget->id)->forceDelete();
                        return Redirect::back()->with('error', 'Erro ao enviar e-mail. O e-mail não foi enviado. Motivo: ' . $e->getMessage() .' on file ' . $e->getFile() . ' line '. $e->getLine());
                    }
                }

            }
        }
    }

    public function getSubject($subject, $budgetNo) {
        return '[ORC-' . $budgetNo . '] ' . $subject;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function budget()
    {
        return $this->belongsTo('App\Models\Budget\Budget', 'budget_id');
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
    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = empty($value) ? null : json_encode($value);
    }
    public function getAttachmentsAttribute($value)
    {
        return json_decode($value);
    }
}
