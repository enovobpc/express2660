<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerEcommerceGateway extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_ecommerce_gateways';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_ecommerce_gateways';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'name', 'method', 'endpoint',
        'user', 'password', 'key', 'secret', 'settings'
    ];

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

    /**
     * Attributes
     */

    public function setSettingsAttribute($value) {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function getSettingsAttribute() {
        return empty($this->attributes['settings']) ? null : json_decode($this->attributes['settings'], true);
    }
}
