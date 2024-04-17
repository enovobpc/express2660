<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class PackType extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_pack_types';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pack_types';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'type', 'description',
        'weight', 'width', 'length', 'height', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
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
    public function scopeFilterCustomer($query, $customer = true){

        if($customer->enabled_packages) {
            return $query->whereIn('code', $customer->enabled_packages);
        }
    }

    public function scopeIsActive($query, $isActive = true){
        return $query->where('is_active', $isActive);
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

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = empty($value) ? null : strtolower($value);
    }

    public function setWidthAttribute($value)
    {
        $this->attributes['width'] = empty($value) ? null : $value;
    }

    public function setLengthAttribute($value)
    {
        $this->attributes['length'] = empty($value) ? null : $value;
    }

    public function setHeightAttribute($value)
    {
        $this->attributes['height'] = empty($value) ? null : $value;
    }

    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = empty($value) ? null : $value;
    }
}
