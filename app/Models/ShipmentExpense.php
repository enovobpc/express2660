<?php

namespace App\Models;

class ShipmentExpense extends BaseModel
{

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_assigned_expenses';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_assigned_expenses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'expense_id', 'provider_id', 'billing_item_id', 'qty',
        'price', 'subtotal', 'vat', 'total', 'vat_rate', 'vat_rate_id',
        'cost_price', 'cost_subtotal', 'cost_vat', 'cost_total', 'cost_vat_rate', 'cost_vat_rate_id',
        'date', 'auto', 'provider_code', 'created_by'
    ];
    
   /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_id'       => 'required',
        'expense_id'        => 'required',
        'qty'               => 'required',
        'price'             => 'required',
        'subtotal'          => 'required'
    ];
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = [
        'qty'      => 'Quantidade',
        'price'    => 'Preço',
        'subtotal' => 'Subtotal'
    ];

    /**
     * Update shipment expenses totals attributes
     *
     * @param $shipmentId
     * @return array|bool
     */
    public static function updateShipmentTotal($shipmentId) {

        $shipmentExpense = ShipmentExpense::where('shipment_id', $shipmentId)->get();
        $price = $shipmentExpense->sum('subtotal');
        $cost  = $shipmentExpense->sum('cost_price');

        if(empty($price) || $price == 0.00) {
            $price = null;
        }

        if(empty($cost) || $cost == 0.00) {
            $cost = null;
        }

        $result = Shipment::where('id', $shipmentId)->update([
            'expenses_price'      => $price,
            'cost_expenses_price' => $cost
        ]);

        if($result) {
            return [
                'total' => $price,
                'cost'  => $cost
            ];
        }

        return false;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }
    
    public function expense()
    {
        return $this->belongsTo('App\Models\ShippingExpense');
    }
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function billingItem() {
        return $this->belongsTo('App\Models\Billing\Item', 'billing_item_id');
    }

    /**
     * Attributes
     */

    public function setBillingItemIdAttribute($value) {
        $this->attributes['billing_item_id'] = empty($value) ? null : $value;
    }
}
