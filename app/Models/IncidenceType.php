<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class IncidenceType extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_incidences_types';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'incidences_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'name_es', 'name_en', 'name_fr', 
        'is_active', 'photo_required', 'date_required', 'pudo_required', 'operator_visible',
        'is_shipment', 'is_pickup'
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
    public function scopeIsActive($query, $isActive = true)
    {
        return $this->where('is_active', $isActive);
    }

    public function scopeIsOperatorVisible($query, $isActive = true)
    {
        return $this->where('operator_visible', $isActive);
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
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setNameEnAttribute($value)
    {
        $this->attributes['name_en'] = empty($value) ? null : trim($value);
    }

    public function setNameFrAttribute($value)
    {
        $this->attributes['name_fr'] = empty($value) ? null : trim($value);
    }

    public function setNameEsAttribute($value)
    {
        $this->attributes['name_es'] = empty($value) ? null : trim($value);
    }
}
