<?php

namespace App\Models;

use App\Models\Traits\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class Product extends BaseModel
{

    use SoftDeletes,
    FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_products';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/products';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'ref', 'name', 'cost_price', 'price', 'promo_price', 
        'vat_rate', 'stock', 'stock_min', 'stock_warning', 'unity', 'obs', 'filename', 'filename', 'is_cover'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'source' => 'required',
        'name'   => 'required',
        'price'  => 'required',
        'vat_rate' => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'name'  => 'Nome',
        'price' => 'Preço'
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function info()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Cart\ProductsCart', 'payment_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
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
    public function setPromoPriceAttribute($value)
    {
        $this->attributes['promo_price'] = empty($value) || $value == 0.00 ? null : $value;
    }
    
    public function getVatPercentAttribute($value)
    {
        return $this->vat_percent = Setting::get('vat_rate_' . $value);
    }
}
