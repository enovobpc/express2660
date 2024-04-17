<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class PurchaseInvoiceType extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_purchase_invoices_types';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_invoices_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'name', 'sort', 'is_static', 'color', 'target_type'
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    public function scopeFilterSource($query) {
        return $query->where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\PurchaseInvoice', 'type_id', 'id');
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
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = empty($value) ? null : $value;
    }
}
