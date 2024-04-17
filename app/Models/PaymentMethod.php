<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class PaymentMethod extends BaseModel implements Sortable
{
    use SoftDeletes,
        SortableTrait;
    

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_methods';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'is_active'
    ];
    
    /**
     * Default sort column
     * 
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'name' => 'required',
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
    public function scopeIsActive($query, $active = true) {
        return $query->where('is_active', $active);
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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }
}
