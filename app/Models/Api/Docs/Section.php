<?php

namespace App\Models\Api\Docs;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Section extends \App\Models\BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_version', 'category_id', 'name', 'slug', 'description', 'icon'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'  => 'required'
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

    public function category()
    {
        return $this->belongsTo('App\Models\Api\Docs\Category', 'category_id', 'slug');
    }

    public function methods()
    {
        return $this->hasMany('App\Models\Api\Docs\Method', 'section_id', 'slug');
    }

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
      | Accessors & Mutators
      |--------------------------------------------------------------------------
      |
      | Eloquent provides a convenient way to transform your model attributes when
      | getting or setting them. Simply define a 'getFooAttribute' method on your model
      | to declare an accessor. Keep in mind that the methods should follow camel-casing,
      | even though your database columns are snake-case.
      |
      */

}
