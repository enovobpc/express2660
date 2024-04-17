<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserAbsence extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_absences';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_absences';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type_id', 'status', 'start_date', 'end_date', 'duration', 'period', 'period_time', 'period_day', 'obs',
        'is_holiday', 'is_remunerated', 'is_meal_subsidy', 'is_adjust'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date', 'end_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'type_id'    => 'required',
        'start_date' => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\UserAbsenceType', 'type_id');
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
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = empty($value) ? null : $value;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }
}
