<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkgroup extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_workgroups';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_workgroups';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_assigned_workgroups', 'workgroup_id', 'user_id');
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

    public function setValuesAttribute($value) {
        $this->attributes['values'] = empty($value) ? null : json_encode($value);
    }

    public function getValuesAttribute() {
        $values = $this->attributes['values'] ?? "[]";
        return json_decode($values, true);
    }
}
