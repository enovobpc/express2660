<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Brand extends \App\Models\BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_logistic_brands';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'customer_id'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'  => 'required'
    );

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

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function models()
    {
        return $this->belongsTo('App\Models\Logistic\Model', 'brand_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Logistic\Product', 'brand_id');
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
        return $query->where('source', config('app.source'));
    }

    public function scopeFilterCustomer($query, $customerId) {
        return $query->where(function($q) use($customerId) {
           $q->whereNull('customer_id');
           $q->orWhere('customer_id', $customerId);
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
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

}
