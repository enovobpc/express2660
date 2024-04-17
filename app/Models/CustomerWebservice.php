<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerWebservice extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_webservices';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_webservices';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id', 'method', 'agency', 'user', 'password', 'session_id', 'active',
        'force_sender', 'session_validity','endpoint', 'department', 'settings'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['session_validity'];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'user'      => 'required',
    );


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */

    public function scopeIsActive($query, $isActive = true){
        return $query->where('active', $isActive);
    }

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
    
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function webservice_method()
    {
        return $this->belongsTo('App\Models\WebserviceMethod', 'method', 'method');
    }
    
    
    public function setSettingsAttribute($value) {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function getSettingsAttribute() {
        return json_decode(@$this->attributes['settings'], true);
    }
}
