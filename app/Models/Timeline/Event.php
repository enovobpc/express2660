<?php

namespace App\Models\Timeline;

use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cargo_planning_events';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cargo_planning_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'title', 'start_date', 'end_date', 'type_id', 'resource', 'color', 'icon', 'obs'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'title'      => 'required',
        'start_date' => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function type()
    {
        return $this->belongsTo('App\Models\Timeline\EventType', 'type_id');
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
