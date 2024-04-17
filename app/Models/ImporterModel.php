<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ImporterModel extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_importer_models';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'importer_models';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'mapping', 'mapping_method', 'date_format', 'provider_id', 'service_id', 'customer_code',
        'agency_id', 'type_id', 'agencies', 'available_customers', 'start_row', 'provider_slug'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
        'type' => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'name' => 'Nome',
        'type' => 'Tipo'
    );

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
    public function setMappingAttribute($value) {
        $value = array_map('strtolower', $value);
        $this->attributes['mapping'] = empty($value) ? null : json_encode($value);
    }

    public function setAgenciesAttribute($value) {
        $this->attributes['agencies'] = empty($value) ? null : json_encode($value);
    }

    public function getMappingAttribute($value) {
        return json_decode($value);
    }

    public function getAgenciesAttribute() {
        return json_decode(@$this->attributes['agencies']);
    }

}
