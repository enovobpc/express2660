<?php

namespace App\Models;

class CustomerService extends BaseModel
{

    /**
     * Disable timestamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_assigned_services';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_assigned_services';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'service_id', 'zone', 'min', 'max', 'price', 'origin_zone'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'customer_id'   => 'required',
        'service_id'    => 'required',
        'zone'          => 'required',
    );
    
    /**
     * 
     * Relashionships
     * 
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
}
