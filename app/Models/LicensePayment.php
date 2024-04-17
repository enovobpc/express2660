<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LicensePayment extends BaseModel
{

    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'licenses_payments';

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['payment_deadline', 'payment_date', 'issuance_date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'license_id', 'description', 'total', 'paid_value',
        'issuance_date', 'payment_deadline', 'payment_date', 'mb_reference', 'payment_method', 'status'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'description' => 'required',
        'total'       => 'required',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function license()
    {
        return $this->belongsTo('App\Models\License');
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
    public function setPaidValueAttribute($value) {
        $this->attributes['paid_value'] = empty($value) ? null : $value;
    }

    public function setPaymentDateAttribute($value) {
        $this->attributes['payment_date'] = empty($value) ? null : $value;
    }
}
