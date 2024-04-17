<?php

namespace App\Models;

class ServiceVolumetricFactor extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services_volumetric_factor';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_id', 'provider_id', 'volume_min', 'factor', 'factor_provider', 'zone'
    ];
    
   /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'service_id' => 'required',
        'volume_min' => 'required',
        'factor'     => 'required',
    ];
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = [
        'service_id' => 'Serviço',
        'volume_min' => 'M3 minimos',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
    
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setFactorProviderAttribute($value)
    {
        $this->attributes['factor_provider'] = empty($value) ? null : $value;
    }
}
