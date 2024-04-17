<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBusinessHistory extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_business_history';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_business_history';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'operator_id', 'status', 'message'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'customer_id' => 'required',
    );
    
    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User');
    }
}
