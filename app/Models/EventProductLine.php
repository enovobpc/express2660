<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class EventProductLine extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'event_products_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_manager_id', 'product_id', 'location_id', 'name', 'qty', 'qty_satisfied', 'price', 'barcode'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'event_manager_id' => 'required',
        'name' => 'max:255',
        'barcode' => 'max:255',
    );

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function eventManager()
    {
        return $this->belongsTo('App\Models\EventManager', 'event_manager_id');
    }

    /**
     * Get all of the EventProductLine for the EventManager
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }
}
