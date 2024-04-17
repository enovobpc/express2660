<?php

namespace App\Models\Traceability;

use App\Models\Traits\FileTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Event extends \App\Models\BaseModel implements Sortable
{
    use FileTrait, SortableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_traceability_events';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'action', 'agency_id', 'status_id', 'location_id', 'sort'
    ];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'name'      => 'required',
        'action'    => 'required',
        'agency_id' => 'required',
        'status_id' => 'required',
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
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

    public function status()
    {
        return $this->belongsTo('App\Models\ShippingStatus', 'status_id');
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
    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }

}
