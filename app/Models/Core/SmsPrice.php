<?php

namespace App\Models\Core;

class SmsPrice extends \App\Models\BaseModel {

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sources';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_enovo';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'price_un', 'qty'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'title'     => 'required',
        'price_un'  => 'required',
        'qty'       => 'required',
    );


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */

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
