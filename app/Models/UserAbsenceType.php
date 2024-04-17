<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserAbsenceType extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_absences_types';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_absences_types';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'periods', 'is_holiday', 'is_remunerated', 'is_meal_subsidy', 'is_adjust'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'name' => 'Nome',
    );
    
    /**
     * 
     * Relashionships
     * 
     */
    public function absenses()
    {
        return $this->hasMany('App\Models\UserAbsence', 'user_id');
    }
}
