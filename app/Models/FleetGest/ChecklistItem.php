<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth, Date;

class ChecklistItem extends  \App\Models\BaseModel
{
    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_fleet_checklists_items';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_checklists_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'checklist_id', 'name', 'description', 'type'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'checklist_id' => 'required',
        'name' => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function checklist()
    {
        return $this->belongsTo('App\Models\FleetGest\Checklist', 'checklist_id');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\FleetGest\ChecklistAnswer', 'checklist_item_id');
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
