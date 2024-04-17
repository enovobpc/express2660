<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\SoftDeletes;

class TripVehicle extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips_vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'vehicle', 'trailer', 'operator_id', 'action', 
        'start_at', 'end_at', 'start_kms', 'end_kms', 'consumption', 'obs'
    ];

    /**
     * Date attributes 
     * 
     * @var type 
     */
    protected $dates = [
        'start_at',
        'end_at'
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

    public function histories() {
        return $this->hasMany('App\Models\Trip\TripHistory', 'trip_vehicle_id');
    }

    public function operator() {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function vehicle_info() {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle', 'license_plate');
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
    public function setVehicleAttribute($value)
    {
        $this->attributes['vehicle'] = empty($value) ? null : $value;
    }

    public function setTrailerAttribute($value)
    {
        $this->attributes['trailer'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setStartKmsAttribute($value)
    {
        $this->attributes['start_kms'] = empty($value) ? null : $value;
    }

    public function setEndKmsAttribute($value)
    {
        $this->attributes['end_kms'] = empty($value) ? null : $value;
    }

    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = empty($value) ? null : $value;
    }

    public function setEndAtAttribute($value)
    {
        $this->attributes['end_at'] = empty($value) ? null : $value;
    }
}
