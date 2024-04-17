<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class BillingZone extends BaseModel
{

    use SoftDeletes;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_billing_zones';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_zones';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'code', 'name', 'unity', 'mapping', 'country', 'distance_min', 'distance_max',
        'pack_types', 'matrix'
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    public function scopeFilterSource($query) {
        return $query->where(function ($q){
            $q->where('source', config('app.source'));
            $q->orWhereNull('source');
        });
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

    public function setCountruAttribute($value)
    {
        $this->attributes['country'] = empty($value) ? null : $value;
    }

    public function setPackTypesAttribute($value)
    {
        $this->attributes['pack_types'] = empty($value) ? null : json_encode($value);
    }

    public function setMatrixAttribute($value)
    {
        $this->attributes['matrix'] = empty($value) ? null : json_encode($value);
    }

    public function setMappingAttribute($value)
    {
        $this->attributes['mapping'] = empty($value) ? null : json_encode($value);
    }

    public function setDistanceMinAttribute($value)
    {
        $this->attributes['distance_min'] = empty($value) ? null : $value;
    }

    public function setDistanceMaxAttribute($value)
    {
        $this->attributes['distance_max'] = empty($value) ? null : $value;
    }

    public function getPackTypesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    public function getMatrixAttribute($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    public function getMappingAttribute($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }
}
