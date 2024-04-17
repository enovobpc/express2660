<?php

namespace App\Models;

class ProviderAssignedExpense extends BaseModel
{

    public $timestamps = false;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_providers_assigned_expenses';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'providers_assigned_expenses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id', 'expense_id', 'price', 'zone'
    ];

    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'provider_id' => 'required',
        'expense_id'  => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    
    public function expense()
    {
        return $this->belongsTo('App\Models\ShippingExpense');
    }
    
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
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
