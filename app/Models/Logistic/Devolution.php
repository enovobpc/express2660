<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Setting;

class Devolution extends \App\Models\BaseModel
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
    protected $table = 'devolutions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'customer_id', 'shipment_id', 'shipping_order_id', 'date', 'obs', 'document', 'date',
        'user_id', 'total_items_original', 'total_qty_original', 'total_items', 'total_qty',
        'total_qty_damaged'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'customer_id'  => 'required',
    );

    /**
     * The status default constants
     *
     * @var string
     */
    const STATUS_PROCESSING = 'processing';
    const STATUS_CONCLUDED = 'concluded';

    /**
     * Set product location barcode
     * @param $stockValue
     */
    public function setCode()
    {

        $this->save();

        $code = date('ym');
        $code .= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->code = $code;
        $this->save();
    }

    /**
     * Atualiza a informação da shipping order
     * @param null $shippingOrderCollection
     * @return bool
     */
    public function updateShippingOrderProducts($shippingOrderCollection = null)
    {

        $devolution = $this;

        if (!$shippingOrderCollection) {
            $shippingOrderCollection = ShippingOrder::find($devolution->shipping_order_id);
        }

        //altera todas as linhas da shipping order para 0
        ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)->update(['qty_devolved' => 0]);

        $devolutionItems = $devolution->items->groupBy('product_id');

        //cria um array contendo produto => total devolvido
        $updateLines = [];
        foreach ($devolutionItems as $productId => $devolutionItem) {
            $qty = $devolutionItem->sum('qty');
            $updateLines[$productId] = $qty;
        }

        //atualiza as linhas da ordem de saida
        $shippingOrderLines = ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)->get();
        foreach ($shippingOrderLines as $shippingOrderLine) {

            $qtyDevolved = @$updateLines[$shippingOrderLine->product_id];

            $shippingOrderLine->qty_devolved = $qtyDevolved;
            $shippingOrderLine->save();
        }

        $totalDevolved = $shippingOrderLines->sum('qty_devolved');

        $shippingOrderCollection->update(['qty_devolved' => $totalDevolved]);

        $devolution->update([
            'total_items'          => $devolution->items->count(),
            'total_qty'            => $devolution->items->sum('qty'),
            'total_qty_damaged'    => $devolution->items->filter(function ($item) {
                return $item->status == 'damaged';
            })->sum('qty'),
            'total_items_original' => $shippingOrderLines->count(),
            'total_qty_original'   => $shippingOrderLines->sum('qty_satisfied'),
        ]);
        return $devolution;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function items()
    {
        return $this->hasMany('App\Models\Logistic\DevolutionItem', 'devolution_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }

    public function shipping_order()
    {
        return $this->belongsTo('App\Models\Logistic\ShippingOrder', 'shipping_order_id');
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
    public function scopeFilterAgencies($query)
    {
        return $query->whereHas('customer', function ($q) {
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
