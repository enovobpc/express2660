<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ReceptionOrderStatus extends \App\Models\BaseModel implements Sortable
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
    const CACHE_TAG = 'cache_reception_order_status';

    const STATUS_REQUESTED = '1';
    const STATUS_WAINTING  = '2';
    const STATUS_CONCLUDED = '3';
    const STATUS_CANCELED  = '4';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reception_orders_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['source', 'name', 'color'];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'  => 'required',
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

    public function reception_order()
    {
        return $this->hasMany('App\Models\Logistic\ShippingOrder', 'status_id');
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
        return $query->where(function ($q) {
            $q->where('source', config('app.source'));
            $q->orWhereNull('source');
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


}
