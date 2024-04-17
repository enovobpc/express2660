<?php

namespace App\Models\Core;

use File;

class TerminalCommand extends \App\Models\BaseModel {

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_terminal_commands';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'terminal_commands';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'command'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'command' => 'required'
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
