<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\SoftDeletes;

class TripExpense extends \App\Models\BaseModel
{

    use SoftDeletes;

    const ALLOWANCE = 'allowance';
    const WEEKEND   = 'weekend';
    const OTHER     = 'other';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips_expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'date', 'type', 'description', 'total',
        'purchase_invoice_id', 'provider_id', 'operator_id'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'operator_id'   => 'numeric',
        'type'          => 'required',
        'total'         => 'required|numeric'
    ];

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = [
        'operator_id' => 'Operador',
        'type'        => 'Tipo',
        'total'       => 'Total',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function manifest() {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }

    public function operator() {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function purchase_invoice() {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'purchase_invoice_id');
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
    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setPurchaseInvoiceIdAttribute($value)
    {
        $this->attributes['purchase_invoice_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setDescriptionAttribute($value) {
        $this->attributes['description'] = empty($value) ? null : $value;
    }

    public function setTotalAttribute($value) {
        $this->attributes['total'] = empty($value) ? null : $value;
    }

    public function getTypeTextAttribute() {
        return trans('admin/trips.types.expenses.' . $this->type) ?? $this->type;
    }
}
