<?php

namespace App\Models\SepaTransfer;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGroup extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sepa_payments_groups';
    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACCEPTED_PARTIAL = 'partial';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sepa_payments_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'code', 'service_type', 'sequence_type', 'processing_date',
        'category', 'company', 'bank_id', 'bank_name', 'bank_iban', 'bank_swift', 'credor_code',
        'transactions_count', 'transactions_total', 'status', 'error_code', 'error_msg'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected  $dates = ['processing_date'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'payment_id'      => 'required',
        'service_type'    => 'required',
        'sequence_type'   => 'required',
        'processing_date' => 'required'
    );



    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function payment()
    {
        return $this->belongsTo('App\Models\SepaTransfer\Payment', 'payment_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\SepaTransfer\PaymentTransaction', 'group_id');
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
    public function setErrorCodeAttribute($value) {
        $this->attributes['error_code'] = empty($value) || $value == 'L000' ? null : $value;
    }

    public function setErrorMsgAttribute($value) {
        $this->attributes['error_msg'] = empty($value) ? null : $value;
    }
}
