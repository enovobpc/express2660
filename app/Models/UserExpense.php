<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserExpense extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_expenses';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_expenses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'user_id', 'provider_id', 'type_id', 'description', 'date', 'total', 'start_date', 'end_date',
        'obs', 'is_fixed', 'purchase_invoice_id', 'created_by'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
     //   'start_date', 'end_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'user_id'     => 'required',
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
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\PurchaseInvoiceType', 'type_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function purchase_invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'purchase_invoice_id');
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
    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = empty($value) ? null : $value;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = empty($value) ? null : $value;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }

    public function setPurchaseInvoiceAttribute($value)
    {
        $this->attributes['purchase_invoice_id'] = empty($value) ? null : $value;
    }
}
