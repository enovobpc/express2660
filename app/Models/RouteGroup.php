<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RouteGroup extends BaseModel
{
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_routes_groups';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'routes_groups';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'code' => 'Código',
        'name' => 'Nome',
    );
}
