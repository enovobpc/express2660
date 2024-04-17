<?php

namespace App\Models;


class ShipmentHistoryNotification extends BaseModel
{

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_history_notification';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_history_notifications';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_history_id', 'email', 'mobile', 'target', 'type'
    ];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_history_id' => 'required',
        'email'               => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    
    public function history()
    {
        return $this->belongsTo('App\Models\ShipmentHistory', 'shipment_history_id');
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
