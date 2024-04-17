<?php

namespace App\Models;

class ShipmentPallet extends BaseModel
{
    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_pallets';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_pallets';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'weight', 'qty', 'cost', 'price'
    ];
    
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_id' => 'required',
        'weight'      => 'required',
        'qty'         => 'required',
        'cost'        => 'required',
        'price'       => 'required',
    ];
    
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
}
