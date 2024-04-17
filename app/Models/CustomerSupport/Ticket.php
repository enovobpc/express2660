<?php

namespace App\Models\CustomerSupport;

use App\Models\Agency;
use App\Models\BroadcastPusher;
use App\Models\Notification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Date, Setting, Mail;

class Ticket extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_support_tickets';

    /**
     * The status default constants
     *
     * @var string
     */
    const STATUS_PENDING  = '01_pending';
    const STATUS_ANALISYS = '03_analysis';
    const STATUS_REJECTED = '98_rejected';
    const STATUS_WAINTING = '02_wainting';
    const STATUS_WAINTING_CUSTOMER = '04_wainting-customer';
    const STATUS_CONCLUDED = '90_concluded';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'user_id', 'customer_id', 'code', 'subject', 'name', 'email', 'message', 'inline_attachments',
        'date', 'shipment_id', 'status', 'category', 'obs'
    ];

    /**
     * The attributes that are dates
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
        'subject' => 'required',
        'name'    => 'required',
        'message' => 'required',
    );

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setCode()
    {
        $this->save();

        $code = date('ym');
        $code.= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->code = $code;
        $this->save();
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel = null, $isTicketAnswer = false, $message = null)
    {
        $sourceClass = 'CustomerSupport';
        $sourceId    = $this->id;
        $ticket      = $this;

        $messageSubject = 'Novo pedido suporte #'.$this->code;
        if($isTicketAnswer) {
            $messageSubject = 'Novas respostas - Suporte #'.$this->code;
        }

        $agencies = Agency::filterSource()->pluck('id')->toArray();

        //get notification recipients
        $recipients = \App\Models\User::where(function($q) use($agencies) {
            $q->where(function($q) use ($agencies){
                    foreach($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                    }
                });
            })
            ->whereHas('roles.perms', function($query) {
                $query->where('name','customer_support');
                $query->orWhere('name','customers_support');
            });

        if($ticket->user_id) {
            $recipients = $recipients->where('id', $ticket->user_id);
        }

        $recipients = $recipients->get(['id', 'email']);

        $notification = null;
        $notificationEmails = [];
        foreach($recipients as $user) {
            $notification = Notification::firstOrNew([
                'source_class'  => $sourceClass,
                'source_id'     => $sourceId,
                'recipient'     => $user->id
            ]);

            $notification->source_class = $sourceClass;
            $notification->source_id    = $sourceId;
            $notification->recipient    = $user->id;
            $notification->message      = $messageSubject;
            $notification->alert_at     = date('Y-m-d H:i:s');
            $notification->read         = false;
            $notification->save();

            $notificationEmails[$user->email] = $user->email;
        }

        if($notification)  {
            $notification->setPusher($channel ? $channel : BroadcastPusher::getGlobalChannel());
        }


        //notifica por e-mail
        $emails = validateNotificationEmails($notificationEmails);
        $emails = $emails['valid'];

        $ticketMessage = $message;

        if($emails) {

            Mail::send('emails.customer_support.new_ticket', compact('ticket', 'ticketMessage','isTicketAnswer'), function ($message) use ($emails, $ticket, $messageSubject) {
                $message->to($emails)
                    ->from(env('MAIL_FROM'), @$ticket->customer->display_name)
                    ->subject($messageSubject);
            });
        }



        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        return Notification::where('source_class', 'BudgetMessage')
            ->where('source_id', $this->id)
            ->delete();
    }

    /**
     * Send email to agency
     *
     * @param null $date
     */
    public function sendEmailToAgency(){

        if(Setting::get('customer_support_notify_email')) {

            $emails = validateNotificationEmails(Setting::get('customer_support_notify_email'));
            $emails = $emails['valid'];

            if(!empty($emails)) {

                $isTicketAnswer = false;

                $ticket = $this;

                try {
                    Mail::send('emails.customer_support.new_ticket', compact('ticket', 'isTicketAnswer'), function ($message) use ($emails, $ticket) {
                        $message->to($emails)
                            ->from(env('MAIL_FROM'), @$ticket->customer->display_name)
                            ->subject('Novo pedido suporte - ' . $ticket->code);
                    });
                } catch (\Exception $e) {
                    return false;
                }

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    /*public static function cancelOutdated($date = null)
    {
        $date = new Date($date);
        $date = $date->subDays(30)->format('Y-m-d');

        $budgetsUnanswered = Ticket::filterSource()
            ->where('updated_at', '<', $date)
            ->whereIn('status', [Ticket::STATUS_WAINTING_CUSTOMER, Ticket::STATUS_PENDING])
            ->get();

        foreach ($budgetsUnanswered as $budget) {
            $budget->status = Ticket::STATUS_REJECTED;
            $budget->save();

            if(Setting::get('budgets_mail_autocancel_active') && Setting::get('budgets_mail_autocancel_html')) {
                $budget->sendCancelEmail();
            }
        }
    }*/

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\CustomerSupport\Message', 'ticket_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\CustomerSupport\TicketAttachment', 'ticket_id');
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
    public function scopeFilterAgencies($query)
    {
        return $query->whereSource(config('app.source'));
    }

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
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = empty($value) ? null : $value;
    }
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }
    public function setShipmentIdAttribute($value)
    {
        $this->attributes['shipment_id'] = empty($value) ? null : $value;
    }
    public function setInlineAttachmentsAttribute($value)
    {
        $this->attributes['inline_attachments'] = empty($value) ? null : json_encode($value);
    }
    public function getInlineAttachmentsAttribute($value)
    {
        return json_decode($value);
    }
    public function getDurationHoursAttribute($value)
    {
        $endDate = @$this->last_message->created_at;
        $endDate = new Date($endDate);

        return $this->created_at->diffInHours($endDate);
    }
}
