<?php

namespace App\Models\Budget;

use App\Models\Agency;
use App\Models\BroadcastPusher;
use App\Models\Notification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Date, Setting, Mail;

class Budget extends \App\Models\BaseModel
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
    protected $table = 'budgets';

    /**
     * The status default constants
     *
     * @var string
     */
    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WAINTING = 'wainting';
    const STATUS_WAINTING_CUSTOMER = 'wainting-customer';
    const STATUS_PROVIDER_REQUESTED = 'requested';
    const STATUS_PROVIDER_ANSWERED = 'answered';
    const STATUS_OUTDATED = 'outdated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'name', 'email', 'message', 'obs', 'date',
        'provider', 'shipment_id', 'status', 'total', 'attachments', 'user_id', 'budget_id'
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
        'email'   => 'required',
        'message' => 'required',
    );

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setBudgetCode()
    {
        $this->save();

 /*       $total = Budget::filterSource()->withTrashed()->orderBy('budget_no', 'desc')->first();
        $total = $total->budget_no;
        $total = (int) substr($total, 4);
        $total++;*/

        $code = date('ym');
        $code.= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->budget_no = $code;
        $this->save();
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel, $message)
    {
        $sourceClass = 'BudgetMessage';
        $sourceId    = $this->id;

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
                $query->whereName('budgets');
            })
            ->get(['id']);

        foreach($recipients as $user) {
            $notification = Notification::firstOrNew([
                'source_class'  => $sourceClass,
                'source_id'     => $sourceId,
                'recipient'     => $user->id
            ]);

            $notification->source_class = $sourceClass;
            $notification->source_id    = $sourceId;
            $notification->recipient    = $user->id;
            $notification->message      = $message;
            $notification->alert_at     = date('Y-m-d H:i:s');
            $notification->read         = false;
            $notification->save();
        }

        if($notification)  {
            $notification->setPusher($channel ? $channel : BroadcastPusher::getGlobalChannel());
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
     * Send cancelation information email
     *
     * @param null $date
     */
    public function sendCancelEmail(){

        $budget = $this;

        $emails = validateNotificationEmails($budget->email);
        $emails = $emails['valid'];

        if (!empty($emails)) {
            //Mail::send(transEmail('emails.budgets.cancel', $budget), compact('budget'), function ($message) use ($emails, $budget) {
            Mail::send('emails.budgets.cancel', compact('budget'), function ($message) use ($emails, $budget) {
                $message->to($emails);
                $message->subject('[ORC-'.$budget->budget_no.'] Cancelamento da proposta');
            });

            if (count(Mail::failures()) > 0) {
                throw new Exception('Falhou o envio do e-mail para o remetente. Tente de novo.');
            }
        }
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public static function cancelOutdated($date = null)
    {
        $date = new Date($date);
        $date = $date->subDays(30)->format('Y-m-d');

        $budgetsUnanswered = Budget::filterSource()
            ->where('updated_at', '<', $date)
            ->whereIn('status', [Budget::STATUS_WAINTING_CUSTOMER, Budget::STATUS_PENDING])
            ->get();

        foreach ($budgetsUnanswered as $budget) {
            $budget->status = Budget::STATUS_REJECTED;
            $budget->save();

            if(Setting::get('budgets_mail_autocancel_active') && Setting::get('budgets_mail_autocancel_html')) {
                $budget->sendCancelEmail();
            }
        }
    }

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
        return $this->hasMany('App\Models\Budget\Message', 'budget_id');
    }

    public function proposes()
    {
        return $this->hasMany('App\Models\Budget\Propose', 'budget_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }

    public function budget()
    {
        return $this->belongsTo('App\Models\Budget\BudgetCourier', 'courier_budget_id');
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
    public function setBudgetIdAttribute($value)
    {
        $this->attributes['budget_id'] = empty($value) ? null : $value;
    }
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
    public function setShipmentIdAttribute($value)
    {
        $this->attributes['shipment_id'] = empty($value) ? null : $value;
    }
    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = empty($value) ? null : $value;
    }
    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = empty($value) ? null : json_encode($value);
    }
    public function getAttachmentsAttribute($value)
    {
        return json_decode($value);
    }

}
