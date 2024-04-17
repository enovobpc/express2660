<?php

namespace App\Models;

use Auth;

class UserLocation extends BaseModel
{

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_locations';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_locations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'operator_id', 'latitude', 'longitude'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'operator_id' => 'required',
        'latitude'    => 'required',
        'longitude'   => 'required'
    );

    /**
     * Set pusher notification
     * @param $channel
     */
    public function requestMobileLocation($operatorId){

        $data['request_time'] = date('Y-m-d H:i:s');
        $data['action']       = 'request_location';

        $channel = BroadcastPusher::getOperatorGpsChannel($operatorId);

        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
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
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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
