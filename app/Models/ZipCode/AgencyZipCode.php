<?php

namespace App\Models\ZipCode;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyZipCode extends \App\Models\BaseModel
{

    use SoftDeletes;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_agencies_zip_codes';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agencies_zip_codes';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'zip_code', 'city', 'country', 'zone', 'agency_id', 'provider_id', 'kms', 'services', 'is_regional'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'zip_code'  => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'zip_code'  => 'Código Postal',
        'city'      => 'Localidade',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
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
    public function setAgencyIdAttribute($value)
    {
        $this->attributes['agency_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setKmsAttribute($value)
    {
        $this->attributes['kms'] = empty($value) ? null : $value;
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = empty($value) ? null : json_encode($value);
    }

    public function getServicesAttribute()
    {
        return json_decode(@$this->attributes['services'], true);
    }
}
