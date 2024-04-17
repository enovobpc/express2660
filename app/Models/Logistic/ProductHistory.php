<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ProductHistory extends \App\Models\BaseModel
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
    protected $table = 'products_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'source_id', 'destination_id', 'action', 'qty', 'document', 'document_id', 'obs', 'user_id'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id' => 'required',
        'action'     => 'required',
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
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function source()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'source_id');
    }

    public function destination()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'destination_id');
    }

    public function reception_order()
    {
        return $this->belongsTo('App\Models\Logistic\ReceptionOrder', 'document_id');
    }

    public function shipping_order()
    {
        return $this->belongsTo('App\Models\Logistic\ShippingOrder', 'document_id');
    }

    public function devolution()
    {
        return $this->belongsTo('App\Models\Logistic\Devolution', 'document_id');
    }

    public function inventory()
    {
        return $this->belongsTo('App\Models\Logistic\Inventory', 'document_id');
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
    public function setDestinationIdAttribute($value)
    {
        $this->attributes['destination_id'] = empty($value) ? null : $value;
    }
    public function setSourceIdAttribute($value)
    {
        $this->attributes['source_id'] = empty($value) ? null : $value;
    }

}
