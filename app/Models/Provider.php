<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth, Setting, DB;

class Provider extends BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_providers';

    /**
     * Provider types
     */
    const CATEGORY_GAS_STATION    = 'gas_station';
    const CATEGORY_MECHANIC       = 'mechanic';
    const CATEGORY_CAR_INSPECTION = 'car_inspection';
    const CATEGORY_INSURER        = 'insurer';
    const CATEGORY_TOLL           = 'tolls';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'providers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'sort', 'agencies', 'type', 'code', 'name', 'color', 'mapping', 'daily_report', 'daily_report_email', 'locale',
        'email', 'autodetect_agencies', 'expenses_expedition', 'expenses_delivery', 'allow_out_of_standard',
        'volumetric_max_weight', 'volumetric_max_length', 'bank_iban', 'customer_id',
        'vat', 'company', 'address', 'zip_code', 'city', 'state', 'country', 'phone', 'attn', 'percent_total_price_gain',
        'webservice_method', 'obs', 'payment_method', 'ignore_stats', 'category_id', 'category_slug', 'is_active',
        'fuel_tax', 'agency_id', 'operation_zip_codes','mobile', 'custom_expenses'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'type_id' => 'required',
        'name'    => 'required',
    );

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = array(
        'agencies'  => 'Agências',
        'name'      => 'Nome',
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
     * Set code
     * @param bool $save
     */
    public function setCode($save = true)
    {

        if (!$this->code) {
            $prefix = empty(Setting::get('customers_code_prefix')) ? '' : Setting::get('customers_code_prefix');

            if (Setting::get('customers_use_empty_codes')) {

                $allCodes = Provider::filterSource()
                    ->where('code', '<>', 'CFINAL')
                    ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) asc')
                    ->select([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')])
                    ->pluck('code')
                    ->toArray();


                $allCodes = array_values(array_filter(array_map('intval', $allCodes)));
                $allCodes = array_unique($allCodes);
                $maxCode = end($allCodes);
                $maxCode = $maxCode > 999999 ? 999999 : $maxCode;
                $possibleCodes = range(1, $maxCode);

                $diff = array_diff($possibleCodes, $allCodes);

                if (empty($diff)) {
                    $code = end($allCodes) + 1;
                } else {
                    $code = @array_values($diff)[0];
                }
            } else {
                $lastCode = Provider::filterSource()
                    ->where('code', '<>', 'CFINAL')
                    ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) desc')
                    ->first([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')]);

                if (empty($lastCode)) {
                    $code = 1;
                } elseif (empty($prefix)) {
                    $lastCode->code = preg_replace('/[^0-9]/', '', @$lastCode->code);
                    $code = empty($lastCode->code) ? 0 : $lastCode->code + 1;
                }
            }

            $padLength = intval(Setting::get('customers_code_pad_left'));
            $code = str_pad($code, $padLength, '0', STR_PAD_LEFT);
            $code = Setting::get('customers_code_prefix') . $code;

            if ($save) {
                $this->code = $code;
                $this->save();
            }

            return $code;
        } else {
            $this->save();
            return $this->code;
        }
    }

    /**
     * Return next model ID
     * @param $query
     * @return mixed
     */
    public function nextId()
    {
        return Provider::filterAgencies()
            ->where('id', '>', $this->id)
            ->min('id');
    }

    /**
     * Return previous model ID
     * @param $query
     * @return mixed
     */
    public function previousId()
    {
        return Provider::filterAgencies()
            ->where('id', '<', $this->id)
            ->max('id');
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function volumetricFactors()
    {
        return $this->belongsToMany('App\Models\ServiceVolumetricFactor', 'services_volumetric_factor', 'provider_id', 'service_id')
            ->withPivot('volume_min', 'factor', 'fator_provider');
    }

    public function pickupPoints()
    {
        return $this->hasMany('App\Models\PickupPoint', 'provider_id');
    }

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'providers_assigned_services', 'provider_id', 'service_id')
            ->withPivot('min', 'max', 'price', 'zone', 'agency_id', 'type', 'customer_id', 'is_adicional', 'adicional_unity');
    }

    public function expenses()
    {
        return $this->belongsToMany('App\Models\ShippingExpense', 'providers_assigned_expenses', 'provider_id', 'expense_id')
            ->withPivot('price');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProviderCategory', 'category_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function paymentCondition()
    {
        return $this->belongsTo('App\Models\PaymentCondition', 'payment_method', 'code');
    }

    public function webservice()
    {
        return $this->belongsTo('App\Models\WebserviceMethod', 'webservice_method', 'method');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function webservice_config() {
        return $this->hasOne('App\Models\WebserviceConfig');
    }

    public function sind_invoice()
    {
        return $this->hasOne('App\Models\PurchaseInvoice', 'provider_id', 'id')
            ->where('doc_type', PurchaseInvoice::DOC_TYPE_SIND)
            ->where('is_deleted', 0);
    }

    public function sinc_invoice()
    {
        return $this->hasOne('App\Models\PurchaseInvoice', 'provider_id', 'id')
            ->where('doc_type', PurchaseInvoice::DOC_TYPE_SINC)
            ->where('is_deleted', 0);
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

    public function scopeFilterSource($query)
    {
        return $query->where(function ($q) {
            $q->where('source', config('app.source'));
            $q->orWhereNull('source');
        });
    }

    public function scopeFilterFleetProviders($query)
    {

        $categories = [
            Provider::CATEGORY_GAS_STATION,
            Provider::CATEGORY_MECHANIC,
            Provider::CATEGORY_TOLL,
            Provider::CATEGORY_INSURER,
            Provider::CATEGORY_CAR_INSPECTION,
        ];

        return $query->whereIn('category_slug', $categories);
    }

    public function scopeIsCarrier($query)
    {
        return $query->where('type', 'carrier')
            ->where('is_active', 1);
    }

    public function scopeIsOther($query)
    {
        return $query->where('type', 'other');
    }

    public function scopeCategoryMechanic($query)
    {
        return $query->where('category_slug', 'mechanic');
    }

    public function scopeCategoryGasStation($query)
    {
        return $query->where('category_slug', 'gas_station');
    }

    public function scopeCategoryInsurer($query)
    {
        return $query->where('category_slug', 'insurer');
    }

    public function scopeCategoryCarInspection($query)
    {
        return $query->where('category_slug', 'car_inspection');
    }

    public function scopeCategoryTolls($query)
    {
        return $query->where('category_slug', 'tolls');
    }

    public function scopeFilterActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFilterAgencies($query)
    {

        $user = Auth::user();

        if ($user) {
            $agencies = $user->agencies;

            $query->filterSource();

            if (!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {

                $query->where(function ($q) use ($agencies) {
                    foreach ($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                    }
                });
            }

            return $query;
        }
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
    public function setAgenciesAttribute($value)
    {
        $this->attributes['agencies'] = empty($value) ? null : json_encode($value);
    }

    public function setMappingAttribute($value)
    {
        $this->attributes['mapping'] = empty($value) ? null : json_encode($value);
    }

    public function setExpensesExpeditionAttribute($value)
    {
        $this->attributes['expenses_expedition'] = empty($value) ? null : json_encode($value);
    }

    public function setExpensesDeliveryAttribute($value)
    {
        $this->attributes['expenses_delivery'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomExpensesAttribute($value)
    {
        $this->attributes['custom_expenses'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setSourceAttribute($value)
    {
        $this->attributes['source'] = empty($value) ? null : $value;
    }

    public function setVolumetricMaxWeightAttribute($value)
    {
        $this->attributes['volumetric_max_weight'] = empty($value) ? null : $value;
    }

    public function setVolumetricMaxLengthAttribute($value)
    {
        $this->attributes['volumetric_max_length'] = empty($value) ? null : $value;
    }

    public function setPercentTotalPriceGainAttribute($value)
    {
        $this->attributes['percent_total_price_gain'] = empty($value) ? null : $value;
    }

    public function setFuelTaxAttribute($value)
    {
        $this->attributes['fuel_tax'] = empty($value) ? null : $value;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = empty($value) ? null : $value;
    }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = empty($value) ? null : $value;
    }

    public function getBillingEmailAttribute()
    {
        return @$this->attributes['billing_email'] ? @$this->attributes['billing_email'] : @$this->attributes['email'];
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }

    public function getMappingAttribute()
    {
        return json_decode(@$this->attributes['mapping']);
    }

    public function getExpensesExpeditionAttribute()
    {
        return json_decode(@$this->attributes['expenses_expedition'], true);
    }

    public function getExpensesDeliveryAttribute()
    {
        return json_decode(@$this->attributes['expenses_delivery'], true);
    }

    public function getCustomExpensesAttribute($value)
    {
        return json_decode($value, true);
    }
}
