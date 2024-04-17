<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class WebserviceConfig extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_webservices_configs';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'webservices_configs';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'method', 'provider_id', 'agency', 'user', 'password', 'session_id', 'endpoint', 'department',
        'force_sender', 'auto_enable', 'active', 'agency_id', 'mapping_services', 'settings'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'method'      => 'required',
        'provider_id' => 'required',
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
    public function setAgencyIdAttribute($value)
    {
        $this->attributes['agency_id'] = empty($value) ? null : $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function source_agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function webservice_method()
    {
        return $this->belongsTo('App\Models\WebserviceMethod', 'method', 'method');
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

    public function setMappingServicesAttribute($value)
    {
        $this->attributes['mapping_services'] = empty($value) ? null : json_encode($value);
    }

    public function getMappingServicesAttribute()
    {
        return json_decode(@$this->attributes['mapping_services'], true);
    }
    
    public function setSettingsAttribute($value) {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function getSettingsAttribute() {
        return json_decode(@$this->attributes['settings'], true);
    }

}
