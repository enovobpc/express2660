<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ReceptionOrderConfirmation extends \App\Models\BaseModel
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
    protected $table = 'reception_orders_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reception_order_id', 'product_id', 'location_id', 'qty', 'qty_received'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id'  => 'required',
        'location_id' => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function reception_order()
    {
        return $this->belongsTo('App\Models\Logistic\ReceptionOrder', 'reception_order_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'location_id');
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

    public function setProductLocationIdAttribute($value)
    {
        $this->attributes['product_location_id'] = empty($value) ? null : $value;
    }

    public function setQtyReceivedAttribute($value)
    {
        $this->attributes['qty_received'] = empty($value) ? 0 : $value;
    }
}
