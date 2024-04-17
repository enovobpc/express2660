<?php

namespace App\Models\Trip;

class TripShipment extends \App\Models\BaseModel
{

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_dtrips_shipments';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips_shipments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'shipment_id', 'sort'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function manifest()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
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
