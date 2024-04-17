<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class WebserviceMethod extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_webservice_methods';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'webservice_methods';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'method', 'sources', 'enabled'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'   => 'required',
        'method' => 'required',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];
      
    /**
     * Limit query to user agencies
     * 
     * @return type
     */
    public function scopeFilterSources($query){
        $source = config('app.source');
        return $query->where('sources', 'like', '%"'.$source.'"%');
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
    
    public function setSourcesAttribute($value)
    {
        $this->attributes['sources'] = empty($value) ? null : json_encode($value);
    }
    
    public function getSourcesAttribute()
    {
        return json_decode(@$this->attributes['sources'], true);
    }
}
