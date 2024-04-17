<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_contacts';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_contacts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department', 'name', 'phone', 'mobile', 'email'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'      => 'required',
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
        return $this->belongsTo('App\Models\Customer');
    }
}
