<?php

namespace App\Models\Traceability;

use App\Models\Traits\FileTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Location extends \App\Models\BaseModel implements Sortable
{
    use FileTrait, SortableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_traceability_locations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'agency_id', 'sort'
    ];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'name'      => 'required',
        'code'      => 'required',
        'agency_id' => 'required',
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
