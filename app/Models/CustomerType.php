<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerType extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_types';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_types';
    
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
    
    /**
     * 
     * Relashionships
     * 
     */
    public function customers()
    {
        return $this->hasMany('App\Models\Customer');
    }
}
