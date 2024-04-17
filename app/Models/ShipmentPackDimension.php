<?php

namespace App\Models;

class ShipmentPackDimension extends BaseModel
{
    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_packs_dimensions';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_packs_dimensions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'weight', 'width', 'length', 'height', 'volume', 'description', 'type',
        'qty', 'pack_no', 'barcode', 'barcode2', 'barcode3', 'adr_letter', 'adr_number', 'adr_class', 'optional_fields', 'price',
        'sku', 'serial_no', 'lote', 'validity', 'product_id', 'total_cost', 'total_price',

    ];
    
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_id' => 'required',
        'length'      => 'required',
        'width'       => 'required',
        'height'      => 'required',
    ];
    
    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function type()
    {
        return $this->belongsTo('App\Models\PackType', 'type', 'code');
    }

    public function packtype()
    {
        return $this->belongsTo('App\Models\PackType', 'type', 'code');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
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
    public function setOptionalFieldsAttribute($value)
    {
        $this->attributes['optional_fields'] = empty($value) ? null : json_encode($value);
    }

    public function setQtyAttribute($value)
    {
        $this->attributes['qty'] = empty($value) ? null : $value;
    }

    public function setAdrLetterAttribute($value)
    {
        $this->attributes['adr_letter'] = empty($value) ? null : $value;
    }

    public function setAdrNumberAttribute($value)
    {
        $this->attributes['adr_number'] = empty($value) ? null : $value;
    }

    public function setAdrClassAttribute($value)
    {
        $this->attributes['adr_class'] = empty($value) ? null : $value;
    }

    public function setSerialNoAttribute($value)
    {
        $this->attributes['serial_no'] = empty($value) ? null : $value;
    }

    public function setLoteAttribute($value)
    {
        $this->attributes['lote'] = empty($value) ? null : $value;
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = empty($value) ? null : $value;
    }

    public function getOptionalFieldsAttribute($value)
    {
        return json_decode($value, true);
    }
}
