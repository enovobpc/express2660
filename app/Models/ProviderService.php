<?php

namespace App\Models;

class ProviderService extends BaseModel
{

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_providers_assigned_services';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'providers_assigned_services';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id', 'customer_id', 'service_id', 'zone', 'min', 'max', 'price'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'provider_id'   => 'required',
        'service_id'    => 'required',
        'zone'          => 'required',
    );
    
    /**
     * 
     * Relashionships
     * 
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
    
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
}
