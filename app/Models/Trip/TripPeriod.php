<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class TripPeriod extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_trips_periods';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips_periods';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'title', 'start_hour', 'end_hour'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'title'      => 'required',
        'start_hour' => 'required',
        'end_hour'   => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function manifests()
    {
        return $this->hasMany('App\Models\Trip\Trip', 'period_id');
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

    public function setTitleHourAttribute($value)
    {
        $this->attributes['title'] = trim($value);
    }

    public function setStartHourAttribute($value)
    {
        $this->attributes['start_hour'] = trim($value);
    }

    public function setEndHourAttribute($value)
    {
        $this->attributes['end_hour'] = trim($value);
    }
}
