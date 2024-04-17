<?php

namespace App\Models\Api\Docs;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Method extends \App\Models\BaseModel implements Sortable
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
    protected $table = 'api_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_version', 'name', 'description', 'url', 'method', 'level', 'params', 'body',
        'response_ok', 'response_error',
        'fields1_title', 'fields2_title','fields3_title','fields4_title',
        'fields1', 'fields2', 'fields3','fields4'
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

    public function section()
    {
        return $this->belongsTo('App\Models\Api\Docs\Section', 'section_id', 'slug');
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
    public function setLevelsAttribute($value)
    {
        $this->attributes['levels'] = empty($value) ? null : json_encode($value);
    }

    public function setParamsAttribute($value)
    {
        $this->attributes['params'] = empty($value) ? null : json_encode($value);
    }

    public function setFields1Attribute($value)
    {
        $this->attributes['fields1'] =  empty($value) ? null : json_encode($value);
    }

    public function setFields2Attribute($value)
    {
        $this->attributes['fields2'] =  empty($value) ? null : json_encode($value);
    }
    public function setFields3Attribute($value)
    {
        $this->attributes['fields3'] =  empty($value) ? null : json_encode($value);
    }
    public function setFields4Attribute($value)
    {
        $this->attributes['fields4'] =  empty($value) ? null : json_encode($value);
    }
    public function getParamsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getFields1Attribute($value)
    {
        return json_decode($value, true);
    }
    public function getFields2Attribute($value)
    {
        return json_decode($value, true);
    }
    public function getFields3Attribute($value)
    {
        return json_decode($value, true);
    }
    public function getFields4Attribute($value)
    {
        return json_decode($value, true);
    }

    public function getLevelsAttribute($value)
    {
        return json_decode($value, true);
    }
}
