<?php

namespace App\Models;

use App\Models\Billing\VatRate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth, DB, Setting;

class ShippingExpense extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Default expenses types
     */
    const TYPE_PICKUP        = 'pickup';
    const TYPE_CHARGE        = 'charge';
    const TYPE_RPACK         = 'rpack';
    const TYPE_RCHECK        = 'rcheck';
    const TYPE_RGUIDE        = 'rguide';
    const TYPE_FRAGILE       = 'fragile';
    const TYPE_SABADO        = 'sabado';
    const TYPE_RECFAIL       = 'recfail';
    const TYPE_OUT_STANDARD  = 'out_standard';
    const TYPE_WAINTING_TIME = 'wainting_time';
    const TYPE_OUT_HOUR      = 'out_hour';
    const TYPE_DISCHARGE     = 'discharge';
    const TYPE_RETURN        = 'return';
    const TYPE_PEAGES        = 'peages';
    const TYPE_OTHER         = 'other';
    const TYPE_FUEL          = 'fuel';
    const CACHE_TAG          = 'cache_shipping_expenses';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipping_expenses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'code', 'name', 'internal_name', 'short_name', 'price', 'zones', 'unity',
        'customer_customization', 'complementar_service', 'collection_complementar_service', 'account_complementar_service',
        'trigger_qty', 'uid_arr', 'ranges_arr', 'zones_arr', 'services_arr', 'values_arr', 'unity_arr', 'trigger_arr', 'trigger_value',
        'trigger_services', 'form_type_shipments',  'form_type_account', 'form_type_pickups', 'provider_code',
        'addon_text', 'addon_text_pickups', 'addon_text_account', 'tax_rate',  'start_at', 'end_at',
        'base_price_arr', 'min_price_arr', 'max_price_arr', 'vat_rate_arr', 'vat_rate_global',
        'trigger_fields', 'trigger_operators', 'trigger_values', 'trigger_joins', 'has_range_prices', 'range_unity',
        'every_arr', 'discount_arr', 'billing_item_id'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'      => 'required',
        'zones'     => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'code'  => 'Código',
        'name'  => 'Nome',
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
     * Calc price for current expense
     *
     * @param $shipment
     * @param $customer
     * @param int $qty
     * @return mixed
     */
    public function calcExpensePrice($shipment, $qty = 1, $customSalePrice = null, $customCostPrice = null) {

        $expense   = $this;
        $expenseId = (string) $expense->id;
        $customer  = $shipment->customer ? $shipment->customer : new Customer();
        $provider  = $shipment->provider ? $shipment->provider : new Provider(); //Provider::where('id', $input['provider'])->first();
        $agencyId  = $shipment->agency_id;
        $serviceId = $shipment->service_id ? $shipment->service_id : 'qq';
        $zone      = (string) ($shipment->zone ? $shipment->zone : 'qqz');
        $uidsArr   = array_flip($expense->uid_arr);
        $hasRange  = $expense->has_range_prices;
        $uidKey    = $serviceId.'#'.$zone;

        //obtem a key para opção selecionada
        if(isset($uidsArr[$uidKey])) { //correspondencia exata serviço + zona
            $key = $uidsArr[$uidKey];
        } elseif(isset($uidsArr['qq#'.$zone])) { //qualquer serviço + zona
            $uidKey = 'qq#'.$zone;
            $key    = $uidsArr[$uidKey];
        } elseif(isset($uidsArr[$serviceId.'#qqz'])) { //serviço + qualquer zona
            $uidKey = $serviceId.'#qqz';
            $key    = $uidsArr[$uidKey];
        } elseif(isset($uidsArr['qq#qqz'])) { //qualquer serviço + qualquer zona
            $uidKey = 'qq#qqz';
            $key    = $uidsArr[$uidKey];
        } else {
            return null; //não existem zonas válidas. sai da função e retorna null
        }

        //obtem taxa de iva
        if (@$expense->vat_rate_arr[$key]) {
            $vatRateId = $expense->vat_rate_arr[$key];  //taxa de iva personalizada por linha
        } else {
            if($expense->vat_rate_global) { //taxa de iva global definida
                $vatRateId = $expense->vat_rate_global;
            } else {
                if (empty($shipment->vat_rate_id) && !empty($shipment->vat_rate)) {
                    $vatRateId    = null;
                    $vatRateValue = $shipment->vat_rate;
                } else {
                    $vatRateId = $shipment->vat_rate_id ?? @VatRate::getDefaultRate()->id;
                }
            }
        }

        if (empty($vatRateValue)) {
            $vatRate      = VatRate::filterSource()->find($vatRateId);
            $vatRateValue = (float) ($vatRate->value ?? 0.00);
        }

        //obtem valores da taxa
        $salePrice  = (float) @$expense->values_arr[$key]; //valor
        $salePrice  = $customSalePrice ? (float) $customSalePrice : $salePrice;
        $minPrice   = (float) @$expense->min_price_arr[$key]; //preço minimo
        $maxPrice   = (float) @$expense->max_price_arr[$key]; //preço maximo
        $basePrice  = (float) @$expense->base_price_arr[$key]; //preço base do serviço
        $unity      = @$expense->unity_arr[$key];
        $trigger    = @$expense->trigger_arr[$key];

        //dd($salePrice);
        //obtem valores personalizados por cliente
        $hasCustomerPrice = false;
        $customerPrices   = @$customer->custom_expenses;
        if(is_numeric(@$customerPrices[$expenseId]['price'][$uidKey])) {

            if(@$customerPrices[$expenseId]['min_price'][$uidKey]) {
                $minPrice = (float) $customerPrices[$expenseId]['min_price'][$uidKey]; //subscreve preço minimo
            }

            if(@$customerPrices[$expenseId]['max_price'][$uidKey]) {
                $maxPrice = (float) $customerPrices[$expenseId]['max_price'][$uidKey]; //subscreve preço maximo
            }

            if(@$customerPrices[$expenseId]['base_price'][$uidKey]) {
                $basePrice = (float) $customerPrices[$expenseId]['base_price'][$uidKey]; //subscreve preço base do serviço
            }

            $salePrice = (float) $customerPrices[$expenseId]['price'][$uidKey];
            $hasCustomerPrice = true;
        }

        //obtem valores dos custos
        $percentCost  = 0;
        $costPrice    = $customCostPrice ? (float) $customCostPrice : 0;
        $minCostPrice = null;
        $maxCostPrice = null;
        $providerPrices = @$provider->custom_expenses;
        if(is_numeric(@$providerPrices[$expenseId]['price'][$uidKey])) {

            if (@$providerPrices[$expenseId]['min_price'][$uidKey]) {
                $minCostPrice = (float) $providerPrices[$expenseId]['min_price'][$uidKey];
            }

            if (@$providerPrices[$expenseId]['max_price'][$uidKey]) {
                $maxCostPrice = (float) $providerPrices[$expenseId]['max_price'][$uidKey];
            }

            $costPrice = (float) $providerPrices[$expenseId]['price'][$uidKey];

            if($provider->percent_total_price_gain) {
                $percentCost = (float) ($costPrice / 100);
            }
        }


        //calcula preço da taxa
        try {
            if($unity == 'percent') {
                $salePrice = $salePrice / 100; //converte % em numérico
                $costPrice = $costPrice / 100;
            }

            if($trigger == '') { //qtd inserida
                $subtotal     = $qty * $salePrice;
                $costSubtotal = $qty * $costPrice;
            } elseif($trigger == 'time') {
                $subtotal     = $shipment->wainting_time * $salePrice;
                $costSubtotal = $shipment->wainting_time * $costPrice;
                $qty = $shipment->wainting_time;
            } elseif($trigger == 'base_price') {
                $subtotal     = $shipment->base_price * $salePrice;
                $costSubtotal = $shipment->base_price * $costPrice;
                $qty = 1;
            } elseif($trigger == 'total_price') {
                $subtotal     = $shipment->total_price * $salePrice;
                $costSubtotal = $shipment->total_price * $costPrice;
                $qty = 1;
            } elseif($trigger == 'weight') {
                $subtotal     = $shipment->weight * $salePrice;
                $costSubtotal = $shipment->weight * $costPrice;
                $qty = $shipment->weight;
            } elseif($trigger == 'volumes') {
                $subtotal     = $shipment->volumes * $salePrice;
                $costSubtotal = $shipment->volumes * $costPrice;
                $qty = $shipment->volumes;
            } elseif($trigger == 'multiply') {
                $subtotal     = $shipment->volumeM3 * $salePrice;
                $costSubtotal = $shipment->volumeM3 * $costPrice;
                $qty = $shipment->volumeM3;
            } elseif($trigger == 'charge_price') {
                $subtotal     = $shipment->charge_price * $salePrice;
                $costSubtotal = $shipment->charge_price * $costPrice;
                $qty = 1;
            } elseif($trigger == 'empty') { //não aplicar cálculo
                $subtotal     = 0;
                $costSubtotal = 0;
                $qty          = 1;
            } elseif ($trigger == 'every') {
                $everyField = $this->every_arr['fields'][$key];
                $everyValue = $this->every_arr['values'][$key];

                if ($everyField == 'weight') {
                    $qty = $shipment->weight / $everyValue;
                } else if ($everyField == 'volumes') {
                    $qty = $shipment->volumes / $everyValue;
                } else if ($everyField == 'm3') {
                    $qty = $shipment->volumeM3 / $everyValue;
                }

                $qty = $qty <= 0 ? 1 : ceil($qty);
                $subtotal     = $qty * $salePrice;
                $costSubtotal = $qty * $costPrice;
            } else {
                $subtotal     = $qty * $salePrice;
                $costSubtotal = $qty * $costPrice;
            }

            // Calculate discount and apply it
            if (!empty($this->discount_arr['everies'][$key])) {
                $discountEvery = $this->discount_arr['everies'][$key];
                $discountPrice = $this->discount_arr['values'][$key];
    
                $discountQty = floor($qty / $discountEvery);
                if ($this->discount_arr['unities'][$key] == 'euro') {
                    $subtotal -= $discountQty * $discountPrice;
                } else {
                    $subtotal -= $discountQty * ($subtotal * ($discountPrice / 100));
                }
            }
        } catch (\Exception $e) {}

        if($percentCost > 0.00) {
            $costPrice    = $subtotal * $percentCost;
            $costSubtotal = $costPrice;
        }

        // Mínimo e máximo do custo
        if ($minCostPrice > 0.00 && $costSubtotal < $minCostPrice) {
            $costSubtotal = $minCostPrice;
        }

        if ($maxCostPrice > 0.00 && $costSubtotal > $maxCostPrice) {
            $costSubtotal = $maxCostPrice;
        }
        //--

        //se existe preço base, adiciona ao valor calculado o preço base
        if($basePrice > 0.00) {
            $subtotal  = $subtotal + $basePrice;
            $basePrice = $shipment->base_price + $basePrice;
        }

        //se o preço calculado é menor que o preço mínimo, assume o preço mínimo
        if($minPrice > 0.00 && $subtotal < $minPrice) {
            $subtotal = $minPrice;
        }

        //se o preço calculado é maior que o preço máximo, assume o preço máximo
        if($maxPrice > 0.00 && $subtotal > $maxPrice) {
            $subtotal = $maxPrice;
        }

        $costVat    = $costSubtotal * ($vatRateValue / 100);
        $billingVat = $subtotal * ($vatRateValue / 100);

        $response = [
            'expense' => [
                'id'            => $expense->id,
                'code'          => $expense->code,
                'name'          => $expense->name,
                'internal_name' => $expense->internal_name,
                'type'          => $expense->type,
                'base_price'    => $basePrice,
                'min_price'     => $minPrice,
                'max_price'     => $maxPrice,
                'zone'          => $zone,
                'service_id'    => $serviceId,
                'uid_key'       => $uidKey,
                'unity'         => $unity,
                'trigger'       => $trigger,
                'customer_price'=> $hasCustomerPrice,
            ],

            'billing' => [
                'customer_id'   => @$customer->id,
                'qty'           => $qty,
                'price'         => number($unity == 'euro' ? $salePrice : ($salePrice * 100), 2),
                'subtotal'      => number($subtotal, 2),
                'vat'           => number($billingVat, 2),
                'total'         => number($subtotal + $billingVat, 2),
                'vat_rate'      => number($vatRateValue, 2),
                'vat_rate_id'   => $vatRateId,
            ],

            'cost' => [
                'provider_id'   => @$provider->id,
                'qty'           => $qty,
                'price'         => number($unity == 'euro' ? $costPrice : ($costPrice * 100), 2),
                'subtotal'      => number($costSubtotal, 2),
                'vat'           => number($costVat, 2),
                'total'         => number($costSubtotal + $costVat, 2),
                'vat_rate'      => number($vatRateValue),
                'vat_rate_id'   => $vatRateId,
            ]
        ];

        $response['fillable'] = [
            'expense_id'        => $response['expense']['id'],
            'billing_item_id'   => @$expense->billingItem ? $expense->billing_item_id : null,
            'code'              => $response['expense']['code'],
            'type'              => $response['expense']['type'],
            'unity'             => $response['expense']['unity'],
            'name'              => $response['expense']['name'],

            'qty'               => $response['billing']['qty'],
            'price'             => $response['billing']['price'],
            'subtotal'          => $response['billing']['subtotal'],
            'vat'               => $response['billing']['vat'],
            'vat_rate'          => $response['billing']['vat_rate'],
            'vat_rate_id'       => $response['billing']['vat_rate_id'],
            'total'             => $response['billing']['total'],
            'customer_id'       => $response['billing']['customer_id'],

            'cost_price'        => $response['cost']['price'],
            'cost_subtotal'     => $response['cost']['subtotal'],
            'cost_vat'          => $response['cost']['vat'],
            'cost_total'        => $response['cost']['total'],
            'cost_vat_rate'     => $response['cost']['vat_rate'],
            'cost_vat_rate_id'  => $response['cost']['vat_rate_id'],
            'provider_id'       => $response['cost']['provider_id'],
        ];

        return $response;
    }

    /**
     * Return pickup expense
     *
     * @param null $returnId
     * @return mixed
     */
    public static function getPickupExpense($returnId = null) {
        $expense = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            //->filterSource()
            ->where('type', ShippingExpense::TYPE_PICKUP)
            ->first();

        if($returnId && $expense) {
            return $expense->id;
        }

        return $expense;
    }

    public static function getFuelExpense($shipment) {
        if (!Setting::get('fuel_tax')) {
            return null;
        }

        $date = date('Y-m-d');
        if ($shipment->date) {
            $date = $shipment->date;
        }

        $expenses = ShippingExpense::filterSource()
            ->where('type', ShippingExpense::TYPE_FUEL)
            ->where(function($q) use($date) {
                $q->where('start_at', '<=', $date);
                $q->where('end_at', '>=', $date);
            })
            ->orderBy('start_at', 'ASC')
            ->get();

        foreach ($expenses as $expense) {
            $price = $expense->calcExpensePrice($shipment);
            if ($price) {
                // Found valid fuel expense zone
                return $expense;
            }
        }

        return null;
    }

    /**
     * Return fuel tax or array with all available fuel taxes
     *
     * @param \App\Models\Shipment $shipment
     * @return array|mixed
     */
    public static function getFuelTax($shipment = null){

        if ($shipment->price_fixed) {
            return $shipment->fuel_tax;
        }

        if(Setting::get('fuel_tax')) {

            $date = date('Y-m-d');

            if($shipment->date) {
                $date = $shipment->date; //assume taxa à data do envio
            }

            $expenses = ShippingExpense::filterSource()
                ->where('type', 'fuel')
                ->where(function($q) use($date) {
                    $q->where('start_at', '<=', $date);
                    $q->where('end_at', '>=', $date);
                })
                ->orderBy('start_at', 'desc')
                ->orderBy('services_arr', 'asc')
                ->get(['id', 'services_arr', 'values_arr', 'zones_arr', 'start_at', 'end_at']);

            if($expenses->isEmpty() && $date >= $date) { //a comparação de data previne que sejam alterados envios anteriores a hoje caso se editem e não exista taxa de combustivel na data desse envio.
                $fuelTax = number(Setting::get('fuel_tax'));
            } else {

                //se não tem envio associado, retorna todos os valores possiveis
                if (empty($shipment)) {
                    $expense = $expenses->sortBy('services_arr')->first();

                    $fuelTax = [];
                    foreach ($expense->zones_arr as $key => $zone) {
                        $fuelTax[$zone] = @$expense->values_arr[$key];
                    }

                } else {

                    $serviceId = $shipment->service_id;
                    $zone      = $shipment->zone;

                    //obtem a taxa de combustivel para o serviço indicado
                    $expense = $expenses->filter(function($item) use($serviceId) {
                        return in_array($serviceId, $item->services_arr) || empty($item->services_arr);
                    })->sortByDesc('services_arr')->first();

                    if ($expense) {
                        $pos  = array_search($zone, $expense->zones_arr);

                        if($pos === false) { //não encontrou zona, testa se a taxa tem a zona "qqz"
                            $pos = array_search('qqz', $expense->zones_arr);
                        }
    
                        if($pos === false) { //se a taxa não tem a zona "qqz"
                            $fuelTax = number(Setting::get('fuel_tax'));
                        } else {
                            $fuelTax = number(@$expense->values_arr[$pos]);
                        }
                    } else {
                        $fuelTax = number(Setting::get('fuel_tax'));
                    }
                }
            }

            return $fuelTax;

        } else {
            return null;
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function shipments()
    {
        return $this->belongsToMany('App\Models\Shipment', 'shipments_assigned_expenses', 'expense_id', 'shipment_id')
                    ->withPivot('qty', 'price');
    }

    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider', 'providers_assigned_expenses', 'expense_id', 'provider_id')
                    ->withPivot('price', 'zone');
    }

    public function billingItem() {
        return $this->belongsTo('App\Models\Billing\Item', 'billing_item_id');
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

    public function scopeIsComplementarService($query, $value = true){
        $query->where('complementar_service', $value);
    }

    public function scopeIsCollectionComplementarService($query, $value = true){
        $query->where('collection_complementar_service', $value);
    }

    public function scopeIsCustomerCustomization($query, $value = true){
        $query->where('customer_customization', $value);
    }

    public function scopeIsReturnService($query, $value = true){
        $query->whereIn('type', ['rguide', 'rcheck']);
    }

    public function scopeIsFragileService($query, $value = true){
        $query->where('type', 'fragile');
    }

    public function scopeIsActive($query){
        $date = date('Y-m-d');
        $query->where(function($q) use($date) {
            $q->where('start_at', '>=', $date);
            $q->where('end_at', '<=', $date);
        });
    }

    public function scopeFilterZones($query, $zones){

        if(is_array($zones)) {
            $billingZone = Shipment::getBillingCountry($zones[0], $zones[1]);
        } else {
            $billingZone = $zones;
        }

        $query->where(function($q) use($billingZone) {
            $q->whereNull('zones');
            $q->orWhere('zones', $billingZone);
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
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
    public function setZonesAttribute($value)
    {
        $this->attributes['zones'] = empty($value) ? null : json_encode($value);
    }

    public function setShortNameAttribute($value)
    {
        $this->attributes['short_name'] = empty($value) ? $this->attributes['name'] : $value;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = empty($value) ? null : json_encode($value);
    }

    public function setZonesArrAttribute($value)
    {
        $this->attributes['zones_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setRangesArrAttribute($value)
    {
        $this->attributes['ranges_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setServicesArrAttribute($value)
    {
        $this->attributes['services_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setValuesArrAttribute($value)
    {
        $this->attributes['values_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerServicesAttribute($value)
    {
        $value = empty($value) ? null : json_encode($value);
        $this->attributes['trigger_services'] = $value;
    }
    public function setUidArrAttribute($value)
    {
        $this->attributes['uid_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setUnityArrAttribute($value)
    {
        $this->attributes['unity_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerArrAttribute($value)
    {
        $this->attributes['trigger_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerJoinsAttribute($value)
    {
        $this->attributes['trigger_joins'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerFieldsAttribute($value)
    {
        $this->attributes['trigger_fields'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerOperatorsAttribute($value)
    {
        $this->attributes['trigger_operators'] = empty($value) ? null : json_encode($value);
    }
    public function setTriggerValuesAttribute($value)
    {
        $this->attributes['trigger_values'] = empty($value) ? null : json_encode($value);
    }
    public function setBasePriceArrAttribute($value)
    {
        $this->attributes['base_price_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setMinPriceArrAttribute($value)
    {
        $this->attributes['min_price_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setMaxPriceArrAttribute($value)
    {
        $this->attributes['max_price_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setVatRateArrAttribute($value)
    {
        $this->attributes['vat_rate_arr'] = empty($value) ? null : json_encode($value);
    }
    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = empty($value) ? null : $value;
    }
    public function setEndAtAttribute($value)
    {
        $this->attributes['end_at'] = empty($value) ? null : $value;
    }

    public function setDiscountArrAttribute($value) {
        if (empty($value)) {
            $this->attributes['discount_arr'] = null;
            return;
        }

        for ($i = 0; $i < count($value['everies']); $i++) {
            if (!empty($value['everies'][$i]) && !empty($value['values'][$i])) {
                continue;
            }

            unset($value['everies'][$i]);
            unset($value['values'][$i]);
            unset($value['unities'][$i]);
        }

        $this->attributes['discount_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setEveryArrAttribute($value) {
        if (empty($value)) {
            $this->attributes['every_arr'] = null;
            return;
        }

        $totalRows = count($value['values']);
        for ($i = 0; $i < $totalRows; $i++) {
            if (!empty($value['values'][$i])) {
                continue;
            }

            unset($value['values'][$i]);
            unset($value['fields'][$i]);
        }

        $this->attributes['every_arr'] = empty($value) ? null : json_encode($value);
    }

    public function getZonesArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getRangesArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getServicesArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getUidArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getValuesArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getUnityArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerJoinsAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerFieldsAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerOperatorsAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerValuesAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getBasePriceArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getMinPriceArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getMaxPriceArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getVatRateArrAttribute($value)
    {
        return (array) json_decode($value);
    }
    public function getTriggerServicesAttribute($value)
    {
        $value = (array) json_decode($value);
        return array_map('intval', $value);
    }
    public function getZonesAttribute($value)
    {
        return json_decode($value);
    }
    public function getPriceAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getRulesAttribute($value)
    {
        return json_decode($value);
    }

    public function getDiscountArrAttribute($value) {
        return json_decode($value, true);
    }

    public function getEveryArrAttribute($value)
    {
        return json_decode($value, true);
    }
}
