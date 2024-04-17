<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class InventoryLine extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id', 'product_id', 'location_id', 'customer_id',
        'qty_existing', 'qty_real', 'qty_available', 'qty_damaged', 'qty_expired',
        'price', 'is_active', 'is_blocked'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'inventory_id' => 'required',
        'product_id'   => 'required',
        'location_id'  => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function inventory()
    {
        return $this->belongsTo('App\Models\Logistic\Inventory', 'inventory_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'location_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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
    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }
}
