<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class OauthAccessToken extends \App\Models\BaseModel
{

    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'name', 'scopes', 'revoked'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'client_id' => 'required'
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
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }

    public function oauth_client()
    {
        return $this->belongsTo('App\Models\Api\OauthClient', 'client_id');
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
