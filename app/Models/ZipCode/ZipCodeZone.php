<?php

namespace App\Models\ZipCode;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class ZipCodeZone extends \App\Models\BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_routes';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zip_codes_zones';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'type', 'country', 'zip_codes', 'services', 'provider_id'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'code' => 'Código',
        'name' => 'Nome',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */


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

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = empty($value) ? 'blocked' : trim($value);
    }
    
    public function setZipCodesAttribute($value)
    {
        $this->attributes['zip_codes'] = empty($value) ? null : json_encode($value);
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = empty($value) ? null : json_encode($value);
    }

    public function getZipCodesAttribute($value)
    {
        return empty($value) ? null : json_decode($value);
    }

    public function getServicesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }

}
