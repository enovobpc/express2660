<?php

namespace App\Models\AirWaybill;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Agent extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_awb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'air_waybills_agents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iata_code', 'name', 'display_name', 'address', 'zip_code', 'city', 'country', 'phone', 'email'
    ];


    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function awb()
    {
        return $this->hasMany('App\Models\AirWaybill\Waybill', 'agent_id');
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
