<?php

namespace App\Models\Core;

use App\Models\CacheSetting;
use File;

class Module extends \App\Models\BaseModel {

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
    protected $table = 'modules';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module', 'name', 'group'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'module' => 'required',
        'name'   => 'required',
    );

    /**
     * Set all active modules
     *
     * @return array
     */
    public static function setActiveModules($modules) {

        $filename = storage_path() . '/framework/modules';
        $result = File::put($filename, implode(',', $modules));

        return $result;
    }

    /**
     * Return all active modules
     *
     * @return array
     */
    public static function getActiveModules() {
        $filename = storage_path() . '/framework/modules';

        if(!File::exists($filename)) {
            File::put($filename, '');
        }

        $modules = File::get($filename);
        $modules = explode(',', $modules);
        $modules = array_filter($modules);

        return $modules;
    }

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
