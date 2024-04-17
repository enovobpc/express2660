<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class LocationMap extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_logistic_location_maps';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'locations_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'warehouse_id', 'map', 'obj_id', 'title', 'left', 'top', 'width', 'height',
        'color', 'border', 'rack', 'bay'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'warehouse_id'  => 'required',
        'code'          => 'required',
        'hall'          => 'required',
        'rack'          => 'required',
        'shelf'         => 'required',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Logistic\Warehouse', 'warehouse_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Logistic\LocationType', 'type_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Logistic\Product', 'products_locations', 'location_id');
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
    public function scopeFilterSource($query) {
        return $query->whereHas('warehouse', function($q){
            $q->filterSource();
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
    public function setBarcodeAttribute($value)
    {
        $this->attributes['barcode'] = strtoupper($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function setWidthAttribute($value)
    {
        $this->attributes['width'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setLengthAttribute($value)
    {
        $this->attributes['length'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setHeightAttribute($value)
    {
        $this->attributes['height'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightAttribute($value)
    {
        $this->attributes['max_weight'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxPalletsAttribute($value)
    {
        $this->attributes['max_pallets'] = empty($value) || $value == 0.00 ? null : $value;
    }

}
