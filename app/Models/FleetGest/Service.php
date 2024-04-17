<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Service extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_fleet_services';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_services';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'name' => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function maintenances()
    {
        return $this->belongsToMany('App\Models\FleetGest\Maintenance', 'maintenance_id', 'service_id');
    }

    public function expenses()
    {
        return $this->belongsToMany('App\Models\FleetGest\Expense', 'expense_id', 'service_id');
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
    public function scopeIsMaintenance($query){
        return $query->where('type', 'maintenance');
    }

    public function scopeIsExpense($query){
        return $query->where('type', 'expense');
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
}
