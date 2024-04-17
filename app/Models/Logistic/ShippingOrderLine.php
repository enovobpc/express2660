<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ShippingOrderLine extends \App\Models\BaseModel
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
    protected $table = 'shipping_orders_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipping_order_id', 'product_id', 'location_id', 'product_location_id', 'qty', 'qty_satisfied',
        'price', 'unity', 'barcode', 'serial_no', 'lote'
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

    public function updateStockTotals() {

        $line = $this;

        // Atualiza stock total da product location
        $productLocationStockAllocated = ShippingOrderLine::whereHas('shipping_order', function($q){
                $q->whereIn('status_id', [ShippingOrderStatus::STATUS_PENDING, ShippingOrderStatus::STATUS_PROCESSING]);
            })
            ->where('product_id', $line->product_id)
            ->where('location_id', $line->location_id)
            ->sum('qty');

        if($line->location_id) {
            $productLocation = ProductLocation::where('product_id', $line->product_id)
                ->where('location_id', $line->location_id)
                ->first();

            $productLocation->stock_allocated = $productLocationStockAllocated;
            $productLocation->stock_available = $productLocation->stock - $productLocation->stock_allocated;
            $productLocation->save();
        }

        // Atualiza stock total do produto
        if ($line->product) {
            $productStockAllocated = ShippingOrderLine::whereHas('shipping_order', function($q){
                    $q->whereIn('status_id', [ShippingOrderStatus::STATUS_PENDING, ShippingOrderStatus::STATUS_PROCESSING]);
                })
                ->where('product_id', $line->product_id)
                ->sum('qty');

            $line->product->stock_allocated = $productStockAllocated;
            $line->product->save();
        }
    }
    
    public static function deleteByShippingOrderId($id) {
        $lines = ShippingOrderLine::where('shipping_order_id', $id)->get();
        $lines->each(function ($line) {
            $line->forceDelete();
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function edit_order()
    {
        return $this->belongsTo('App\Models\Logistic\ShippingOrder', 'shipping_order_id');
    }

    public function shipping_order()
    {
        return $this->belongsTo('App\Models\Logistic\ShippingOrder', 'shipping_order_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function product_location()
    {
        return $this->belongsTo('App\Models\Logistic\ProductLocation', 'product_id', 'product_id')
            ->where('location_id', $this->location_id);
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
    public function scopeFilterAgencies($query) {
        return $query->whereHas('customer', function($q){
            $q->filterAgencies();
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
}
