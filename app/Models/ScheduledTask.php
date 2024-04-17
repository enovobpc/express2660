<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledTask extends BaseModel
{
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_scheduled_tasks';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scheduled_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'action', 'target', 'target_at', 'notify_at'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'action'    => 'required',
        'target'    => 'required',
        'target_at' => 'required',
    );

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
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
