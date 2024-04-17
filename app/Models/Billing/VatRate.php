<?php

namespace App\Models\Billing;

use App\Models\InvoiceLine;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Setting;

class VatRate extends \App\Models\BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_billing_vat_rates';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_vat_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'code', 'name', 'name_abrv', 'class', 'subclass', 'zone', 'value', 'exemption_reason',
        'billing_code', 'is_sales', 'is_purchases', 'is_default', 'is_active'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'code'  => 'required',
        'name'  => 'required',
        'class' => 'required',
        'value' => 'required'
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Check vat rate usages
     * @return mixed
     */
    public function checkUsage()
    {

        $vatRate = $this;

        if ($this->subclass == 'ise') {
            $countUsages = InvoiceLine::where(function ($q) use ($vatRate) {
                $q->where('tax_rate', $vatRate->value);
                $q->where('exemption_reason', $vatRate->exemption_reason);
            })->count();
        } else {
            $countUsages = InvoiceLine::where('tax_rate', $vatRate->value)->count();
        }

        return $countUsages;
    }

    /**
     * Return billing code from vat rate code
     * @param $code
     * @return mixed
     */
    public static function getBillingCode($code)
    {

        $vatRate = VatRate::filterSource()
            ->isActive()
            ->where('code', $code)
            ->first();

        return @$vatRate->billing_code;
    }

    /**
     * Return vat rate from given code
     * 
     * @param $code
     */
    public static function getByCode($code)
    {

        $vatRate = VatRate::filterSource()
            ->isActive()
            ->where('code', $code)
            ->first();

        return $vatRate;
    }

    /**
     * Return default vat rate for a given subclass
     *  
     * @param $subclass
     */
    public static function getDefaultRate($subclass = 'nor')
    {

        $vatRate = VatRate::filterSource()
            ->isActive()
            ->isDefault()
            ->where('subclass', $subclass)
            ->first();

        return $vatRate;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
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
    public function scopeIsActive($query, $value = true)
    {
        $query->where(function ($q) use ($value) {
            $q->where('is_active', $value);
            $q->whereNotNull('billing_code');
        });
    }

    public function scopeIsSales($query, $value = true)
    {
        $query->where('is_sales', $value);
    }

    public function scopeIsPurchases($query, $value = true)
    {
        $query->where('is_purchases', $value);
    }

    public function scopeIsDefault($query, $value = true)
    {
        $query->where('is_default', $value);
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
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(str_replace(' ', '', strtoupper($value)));
    }

    public function setBillingCodeAttribute($value)
    {
        if (empty($value)) {
            $value = null;
            $this->attributes['is_active'] = true; //força a desativar porque não tem ligação ao sistema faturação.
        }
        $this->attributes['billing_code'] = $value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }
}
