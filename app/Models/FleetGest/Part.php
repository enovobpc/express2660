<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Part extends \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_parts';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_parts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference', 'name', 'brand_name', 'model_name', 'category', 'stock_total',
        'provider_id', 'cost_price', 'purchase_invoice', 'obs'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'name'      => 'required',
        'category'  => 'required'
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
        return $this->belongsToMany('App\Models\FleetGest\Maintenance', 'fleet_maintenance_assigned_parts', 'maintenance_id', 'part_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setCostPriceAttribute($value)
    {
        $this->attributes['cost_price'] = empty($value) || $value == 0.00 ? null : $value;
    }
}
