<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Date;

class CustomerMessage extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_messages';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_messages';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'message', 'send_all', 'send_email', 'to_emails', 'is_static'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'subject' => 'required',
        'message' => 'required',
    );

    /**
     * Get unread messages
     *
     * @param $customerId
     * @return mixed
     */
    public static function getUnread($customerId, $limitDays = 60) {

        if(!empty($limitDays)) {
            $date = new Date();
            $date = $date->subDays($limitDays)->format('Y-m-d');
        }

        $messages = CustomerMessage::whereHas('customers', function($q) use($customerId) {
                $q->where('customers.id', $customerId);
                $q->where('customers_assigned_messages.is_read', '=', 0);
            });

        if(!empty($limitDays)) {
            $messages = $messages->where('created_at', '>=', $date);
        }

        $messages = $messages->orderBy('created_at', 'desc')
            ->get();

        return $messages;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function customers()
    {
        return $this->belongsToMany('App\Models\Customer', 'customers_assigned_messages', 'message_id', 'customer_id')
            ->withPivot('is_read', 'deleted_at')
            ->where('customers_assigned_messages.deleted_at', null);

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
    public function setToEmailsAttribute($value) {
        $this->attributes['to_emails'] = empty($value) ? null : json_encode($value);
    }

    public function getToEmailsAttribute($value) {
        return json_decode($value);
    }
}
