<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\SoftDeletes;

class TripHistory extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'trip_vehicle_id', 'action', 'date', 'location', 
        'obs', 'target', 'target_id', 'operator_id'
    ];

    /**
     * Date attributes 
     * 
     * @var type 
     */
    protected $dates = [
        'date'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function manifest() {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }

    public function operator() {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function trip_vehicle() {
        return $this->belongsTo('App\Models\Trip\TripVehicle', 'trip_vehicle_id');
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
    public function setTripVehicleIdAttribute($value)
    {
        $this->attributes['trip_vehicle_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setTargetAttribute($value)
    {
        $this->attributes['target'] = empty($value) ? null : $value;
    }

    public function setTargetIdAttribute($value)
    {
        $this->attributes['target_id'] = empty($value) ? null : $value;
    }    
}
