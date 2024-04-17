<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentNotification extends BaseModel
{
    use SoftDeletes;
    

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments_notifications';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'proforma_id', 'gateway', 'method', 'type', 'value', 'currency', 'expiration_time', 'payment_key', 'description',
        'account_id', 'customer_code', 'customer_name', 'customer_address', 'customer_country', 'customer_email',
        'customer_phone', 'customer_vat', 'key', 'transaction_id', 'status', 'reference', 'entity', 'visa_url',
        'card_last_digits', 'card_type', 'mbw_alias', 'last_error'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expiration_time', 'paid_at'];

    /**
     * Default constant values
     */
    const STATUS_WAINTING = 'waiting';
    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_ACTIVE   = 'active';
    const STATUS_FAILED   = 'failed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_DELETED  = 'deleted';

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'description'  => 'required',
        'payment_date' => 'required',
        'total'        => 'required'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function history()
    {
        return $this->hasMany('App\Models\PaymentNotificationHistory', 'payment_notification_id');
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
    public function scopeIsPaid($query) {
        return $query->where('status', 'paid');
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
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = empty($value) ? 'failed' : $value;
    }
}
