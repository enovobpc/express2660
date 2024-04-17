<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class ProductSale extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_products_sales';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products_sales';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'customer_id', 'qty', 'price', 'subtotal', 'vat_rate', 'cost_price', 'date', 'obs'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id'  => 'required',
        'customer_id' => 'required',
        'qty'         => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'product_id'  => 'Produto',
        'customer_id' => 'Cliente',
        'qty'         => 'Quantidade',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'sold_by');
    }

    public function scopeFilterSource($query) {
        return $query->whereHas('customer', function($q){
            $q->filterSource();
        });
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
    public function getVatPercentAttribute($value)
    {
        return $this->vat_percent = Setting::get('vat_rate_' . $value);
    }
}
