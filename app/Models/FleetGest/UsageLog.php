<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;


class UsageLog extends  \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_usage_log';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_usage_log';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vehicle_id', 'operator_id', 'type', 'start_date', 'end_date', 'start_km', 'end_km'];


    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date'];

    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/vehicles/usage';
    
    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'vehicle_id'  => 'required',
        'operator_id' => 'required',
        'start_date'  => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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
    public function scopeFilterSource($query) {
        return $query->whereHas('vehicle', function($q){
            $q->filterSource();
        });
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
    public function getTotalKmAttribute()
    {
        $this->attributes['total_km'] = $this->attributes['end_km'] - $this->attributes['start_km'];
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }
}
