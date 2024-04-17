<?php

namespace App\Models\Api;

use Auth;

class OauthClient extends \App\Models\BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_clients';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked',
        'daily_limit', 'last_call', 'daily_counter'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'secret'     => 'required',
        'redirect'   => 'required'
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'user_id')->withTrashed();
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
