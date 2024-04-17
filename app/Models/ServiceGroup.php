<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ServiceGroup extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_services_groups';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services_groups';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'icon'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required',
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
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function services()
    {
        return $this->hasMany('App\Models\Service', 'group_id');
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

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower(str_replace(' ', '_', removeAccents($value)));
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }
}
