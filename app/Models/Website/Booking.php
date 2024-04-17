<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends \App\Models\BaseModel
{
    use SoftDeletes;


    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_website';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bookings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id', 'step', 'bags', 'volumes', 'weight',

        'pickup_type', 'pickup_point', 'pickup_date', 'pickup_hour',
        'sender_name', 'sender_address', 'sender_zip_code', 'sender_city',

        'delivery_type', 'delivery_point', 'delivery_date', 'delivery_hour',
        'recipient_name', 'recipient_address', 'recipient_zip_code', 'recipient_city',

        'customer_name', 'customer_phone_pref', 'customer_phone', 'customer_email',
        'promo_code', 'subtotal', 'vat', 'total',

        'payment_method', 'payment_entity', 'payment_reference',
        'payment_cc_first_name', 'payment_cc_last_name', 'payment_cc_number','payment_cc_month', 'payment_cc_year', 'payment_cc_cvc'

    ];


    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'booking_id' => 'required',
    ];

    protected $dates = [
        'pickup_date', 'delivery_date'
    ];


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function pickupDelegation()
    {
        return $this->belongsTo('App\Models\PickupPoint', 'pickup_point');
    }

    public function deliveryDelegation()
    {
        return $this->belongsTo('App\Models\PickupPoint', 'delivery_point');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\PaymentGateway\Base', 'payment_id');
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
    public function setBagsAttribute($value)
    {
        $this->attributes['bags'] = empty($value) ? null : json_encode($value);
    }

    public function getBagsAttribute($value)
    {
        return json_decode($value, true);
    }
}
