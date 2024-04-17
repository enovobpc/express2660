<?php

namespace App\Models\Traceability;

use App\Models\Traits\FileTrait;

class ShipmentTraceability extends \App\Models\BaseModel
{
    use FileTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_traceability';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'operator_id', 'volumes', 'read_point', 'agency_id', 'barcode',
        'event_id', 'location_id'
    ];
    
   /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'agency_id'   => 'required',
        'shipment_id' => 'required',
        'operator_id' => 'required',
        'volumes'     => 'required'
    ];

    
    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Traceability\Event', 'event_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Traceability\Location', 'location_id');
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
    public function setBarcodeAttribute($value)
    {
        $this->attributes['barcode'] = empty($value) ? null : $value;
    }

    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }
}
