<?php

namespace App\Models;

use App\Models\Billing\VatRate;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderLine;
use App\Models\Sms\Sms;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\ZipCode\ZipCodeZone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use LynX39\LaraPdfMerger\PdfManage;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Jenssegers\Date\Date;
use Mockery\Exception;
use Mpdf\Mpdf;
use Setting, Auth, View, DB, Redirect, File, Mail, App, Excel, Log;

class Shipment extends BaseModel implements Sortable
{
    use SoftDeletes,
        SortableTrait;
    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments';
    const TYPE_SHIPMENT     = 'S';
    const TYPE_PICKUP       = 'P';
    const TYPE_DEVOLUTION   = 'D';
    const TYPE_RETURN       = 'R';
    const TYPE_RECANALIZED  = 'C';
    const TYPE_LINKED       = 'L';
    const TYPE_MASTER       = 'M';
    const TYPE_TRANSHIPMENT = 'T';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tracking_code', 'type', 'keywords', 'parent_tracking_code', 'children_type', 'children_tracking_code', 'transport_type_id',
        'provider_cargo_agency', 'provider_sender_agency', 'provider_recipient_agency', 'provider_tracking_code',

        'agency_id', 'sender_agency_id', 'recipient_agency_id', 'service_id', 'provider_id',
        'customer_id', 'requested_by', 'department_id', 'requester_name', 'sender_attn', 'sender_vat', 'sender_name', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_state', 'sender_country', 'sender_phone', 'sender_latitude', 'sender_longitude',
        'recipient_id', 'sender_id', 'sender_pudo_id', 'recipient_pudo_id', 'recipient_name', 'recipient_attn', 'recipient_vat', 'recipient_address', 'recipient_zip_code', 'recipient_city', 'recipient_state', 'recipient_country', 'recipient_phone', 'recipient_email',
        'recipient_latitude', 'recipient_longitude', 'weight', 'volumetric_weight', 'conferred_weight', 'customer_weight', 'provider_weight', 'extra_weight', 'volume_m3', 'fator_m3', 'volumes', 'conferred_volumes', 'charge_price',
        'status_id', 'pickup_operator_id', 'operator_id', 'has_return', 'created_by_customer', 'obs', 'obs_delivery', 'obs_internal', 'reference', 'reference2', 'reference3', 'is_collection', 'is_import',
        'dimensions', 'webservice_method', 'webservice_error', 'submited_at', 'cod', 'ignore_billing', 'price_fixed', 'is_closed',
        'is_blocked', 'kms', 'ldm', 'liters', 'count_discharges', 'count_loads', 'hours', 'start_hour', 'end_hour', 'start_hour_pickup', 'end_hour_pickup', 'guide_required', 'map_lat', 'map_lng',
        'conferred', 'packaging_type', 'vehicle', 'trailer', 'expenses',  'zone', 'complementar_services', 'custom_fields', 'optional_fields',
        'is_scheduled', 'is_printed', 'payment_method', 'route_id', 'purchase_invoice_id', 'dispatcher_id', 'incoterm',
        'estimated_delivery_time_min', 'estimated_delivery_time_max', 'refund_method', 'cod_method', 'without_pickup',
        'goods_price', 'insurance_price', 'total_price_when_collecting', 'out_zone', 'price_kg_unity', 'has_assembly', 'has_sku',

        'date', 'shipping_date', 'delivery_date', 'billing_date', 'status_date', 'pickuped_date', 'distribution_date', 'delivered_date', 'inbound_date',

        'price_kg', 'shipping_base_price', 'shipping_price', 'expenses_price', 'fuel_price', 'fuel_tax',
        'cost_billing_zone', 'cost_shipping_base_price', 'cost_shipping_price', 'cost_expenses_price',
        'cost_billing_subtotal', 'cost_billing_vat', 'cost_billing_total', 'cost_fuel_price', 'cost_fuel_tax',

        'billing_subtotal', 'billing_vat', 'billing_total', 'vat_rate', 'vat_rate_id', 'billing_item', 'trip_id', 'trip_code',  'is_insured',

        'cod_methods', 'delivery_attempts', 'sms_code', 'dims_bigger_weight', 'dims_bigger_size', 'dims_bigger_side', 'tags', 'sort',

        'shipper_name', 'shipper_address', 'shipper_zip_code', 'shipper_city', 'shipper_country', 'shipper_vat', 'shipper_phone',
        'receiver_name', 'receiver_address', 'receiver_zip_code', 'receiver_city', 'receiver_country', 'receiver_vat', 'receiver_phone',

        'ecommerce_gateway_id', 'ecommerce_gateway_order_code', 'ship_code', 'container_type', 'container_code', 'container_seal',
        'adr_class', 'adr_onu',

        //obsoletos desde outubro 2022
        'payment_at_recipient', 'total_price',
        'cost_price', 'base_price', 'total_expenses', 'total_expenses_cost', 'total_price_after_pickup',
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'delivery_date',
        'shipping_date',
        'pickuped_date',
        'distribution_date',
        'delivered_date',
        'inbound_date'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'customer_id'           => 'required',
        'agency_id'             => 'required',
        'sender_agency_id'      => 'required',
        'recipient_agency_id'   => 'required',
        'provider_id'           => 'required',
        'sender_name'           => 'required',
        'sender_address'        => 'required',
        'sender_zip_code'       => 'required',
        'sender_city'           => 'required',
        'sender_country'        => 'required',
        'volumes'               => 'numeric',
        'weight'                => 'numeric',
        'total'                 => 'numeric',
    ];

    public $only_final_status = false;
    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = [
        'customer_id'       => 'Cliente',
        'agency_id'         => 'Agência Pagadora',
        'sender_agency_id'  => 'Agência de Origem',
        'recipient_agency_id' => 'Agência de Destino',
        'service_id'        => 'Serviço',
        'provider_id'       => 'Fornecedor',
        'sender_name'       => 'Nome do Remetente',
        'sender_address'    => 'Morada do Remetente',
        'sender_zip_code'   => 'Código Postal do Remetente',
        'sender_city'       => 'Localidade do Remetente',
        'sender_country'    => 'País do Remetente',
        'sender_phone'      => 'Telefone do Remetente',
        'recipient_name'    => 'Nome do Destinatário',
        'recipient_address' => 'Morada do Destinatário',
        'recipient_zip_code' => 'Código Postal do Destinatário',
        'recipient_city'    => 'Localidade do Destinatário',
        'recipient_country' => 'País do Destinatário',
        'recipient_phone'   => 'Telefone do Destinatário',
        'volumes'           => 'Volumes',
        'weight'            => 'Peso',
        'total'             => 'Total',
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    public function __construct(array $attributes = [])
    {
        /**
         * @author Daniel Almeida
         * --
         * Como existem relacionamentos em base de dados diferentes,
         * temos que dizer qual o nome da base de dados desta tabela porque senão
         * o mysql não consegue encontrá-la
         */
        $this->table = env('DB_DATABASE') . ".{$this->table}";
        parent::__construct($attributes);
    }
    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
    }

    public function recipientAgency()
    {
        return $this->belongsTo('App\Models\Agency', 'recipient_agency_id', 'id');
    }

    public function senderAgency()
    {
        return $this->belongsTo('App\Models\Agency', 'sender_agency_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Customer', 'department_id');
    }

    public function requested_customer()
    {
        return $this->belongsTo('App\Models\Customer', 'requested_by');
    }

    public function recipient()
    {
        return $this->belongsTo('App\Models\CustomerRecipient');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    public function route()
    {
        return $this->belongsTo('App\Models\Route');
    }

    public function delivery_route()
    {
        return $this->belongsTo('App\Models\Route', 'route_id');
    }

    public function trip()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }

    public function transport_type()
    {
        return $this->belongsTo('App\Models\TransportType');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\ShippingStatus', 'status_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function dispatcher()
    {
        return $this->belongsTo('App\Models\User', 'dispatcher_id');
    }

    public function schedule()
    {
        return $this->hasOne('App\Models\ShipmentSchedule', 'shipment_id');
    }

    public function firstHistory()
    {
        return $this->hasOne('App\Models\ShipmentHistory', 'shipment_id');
    }

    public function lastHistory()
    {
        return $this->hasOne('App\Models\ShipmentHistory', 'shipment_id')->latest();
    }

    public function lastIncidence()
    {
        return $this->hasOne('App\Models\ShipmentHistory', 'shipment_id')->where('status_id', 9)->latest();
    }

    public function last_incidence()
    {
        return $this->hasOne('App\Models\ShipmentHistory', 'shipment_id')->where('status_id', 9)->latest();
    }

    public function last_history()
    {
        return $this->hasOne('App\Models\ShipmentHistory', 'shipment_id')->latest('id');
    }

    public function history()
    {
        return $this->hasMany('App\Models\ShipmentHistory', 'shipment_id');
    }

    public function datatable_incidences_resolutions()
    {
        return $this->hasMany('App\Models\ShipmentIncidenceResolution', 'shipment_id', 'shipment_id');
    }

    public function incidences_resolutions()
    {
        return $this->hasMany('App\Models\ShipmentIncidenceResolution', 'shipment_id');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\FileRepository', 'source_id')->where('source_class', '=', 'Shipment');
    }

    public function traceability()
    {
        return $this->hasMany('App\Models\Traceability\ShipmentTraceability');
    }

    public function incidence()
    {
        return $this->belongsTo('App\Models\IncidenceType', 'incidence_id');
    }

    public function delivery_pudo()
    {
        return $this->belongsTo('App\Models\PickupPoint', 'recipient_pudo_id');
    }

    public function pickup_pudo()
    {
        return $this->belongsTo('App\Models\PickupPoint', 'sender_pudo_id');
    }

    public function last_incidence_resolution()
    {
        return $this->hasOne('App\Models\ShipmentIncidenceResolution', 'shipment_id')->latest();
    }

    public function refund_control()
    {
        return $this->hasOne('App\Models\RefundControl', 'shipment_id');
    }

    public function refund_agencies()
    {
        return $this->hasOne('App\Models\RefundControlAgency', 'shipment_id');
    }

    public function cod_control()
    {
        return $this->hasOne('App\Models\PaymentAtRecipientControl', 'shipment_id');
    }

    public function pack_dimensions()
    {
        return $this->hasMany('App\Models\ShipmentPackDimension');
    }

    public function interventions()
    {
        return $this->hasMany('App\Models\ShipmentIntervention');
    }

    public function pallets()
    {
        return $this->hasMany('App\Models\ShipmentPallet');
    }

    public function expenses()
    {
        return $this->belongsToMany('App\Models\ShippingExpense', 'shipments_assigned_expenses', 'shipment_id', 'expense_id')
            ->withPivot(
                'id',
                'qty',
                'price',
                'subtotal',
                'vat',
                'total',
                'vat_rate',
                'vat_rate_id',
                'cost_price',
                'cost_subtotal',
                'cost_vat',
                'cost_total',
                'cost_vat_rate',
                'cost_vat_rate_id',
                'provider_id',
                'provider_code',
                'auto',
                'date',
                'unity',
                'created_by'
            );
    }

    public function ignored_warnings()
    {
        return $this->hasMany('App\Models\ShipmentWarningIgnored', 'shipment_id');
    }

    public function paymentCondition()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method', 'code');
    }

    public function gatewayPayment() {
        return $this->hasOne('App\Models\GatewayPayment\Base', 'target_id', 'id')->where('target', 'Shipment');
    }

    public function setRefundMethodAttribute($value)
    {
        $this->attributes['refund_method'] = empty($value) ? null : $value;
    }

    public function setCodMethodAttribute($value)
    {
        $this->attributes['cod_method'] = empty($value) ? null : $value;
    }

    /**
     * Return selectbox current hour
     */
    public static function chooseSelectboxCurrentHour()
    {

        $hour = date('H');
        $minutes = date('i');

        if ($minutes < 5) {
            $minutes = '00';
        } elseif ($minutes >= 5 && $minutes < 15) {
            $minutes = '10';
        } elseif ($minutes >= 15 && $minutes < 25) {
            $minutes = '20';
        } elseif ($minutes >= 25 && $minutes < 35) {
            $minutes = '30';
        } elseif ($minutes >= 35 && $minutes < 45) {
            $minutes = '40';
        } elseif ($minutes >= 45 && $minutes < 55) {
            $minutes = '50';
        } elseif ($minutes >= 55) {
            $minutes = '00';
            $hour = $hour == 23 ? '00' : ($hour + 1);
            $hour = $hour < 10 ? '0' . $hour : $hour;
        }

        return $hour . ':' . $minutes;
    }

    /**
     * Autoselect most economic provider
     */
    public function chooseEconomicProvider()
    {
        $providers = Provider::filterSource()
            ->whereHas('services')
            ->isCarrier()
            ->pluck('id')
            ->toArray();

        $mostEconomic = [];
        $mostEconomic['cost'] = '99999999999999';
        if ($providers) {
            foreach ($providers as $providerId) {
                $this->provider_id = $providerId;
                $prices = $this->calcPrices($this);
                $prices['provider_id'] = $providerId;

                if ($prices['cost'] > 0.00) {
                    $mostEconomic = $prices['cost'] > $mostEconomic['cost'] ? $mostEconomic : $prices;
                }
            }
            return $mostEconomic;
        }
        return false;
    }

    /**
     * Get Locale from country
     */
    public function getRecipientLocale()
    {

        $recipientCountry = $this->recipient_country;

        if (in_array($recipientCountry, ['pt', 'br', 'ao', 'mz'])) {
            return 'pt';
        } else if ($recipientCountry == 'es' || $recipientCountry == 'mx') {
            return 'es';
        } else if (in_array($recipientCountry, ['fr', 'lu', 'ch', 'mz'])) {
            return 'fr';
        }

        return 'en';
    }

    /**
     * Calculate shipment price
     *
     * @param Shipment $shipment
     * @param bool $fullResponse retorna resposta completa. Se false, retorna apenas preços
     * @return array|null
     */
    public static function calcPrices($shipment, $fullResponse = true, $manualExpenses = null)
    {
        $service = $shipment->service;

        //substitui cliente dos portes
        $originalCustomer = $shipment->customer;
        if ($shipment->cod == 'D' || $shipment->cod == 'S') {

            if ($shipment->cod == 'D') {
                $targetVat = $shipment->recipient_vat;
                $targetId  = $shipment->recipient_id;
            } else {
                $targetVat = $shipment->sender_vat;
                $targetId  = $shipment->sender_id;
            }

            if ($targetVat || $targetId) {

                $customer = null;
                if ($targetVat) { //se o destinatário tem nif, procura o cliente pelo nif
                    $customer = Customer::where('vat', $targetVat)->where('has_prices', 1)->first();
                }

                if (!$customer && $targetId) { //se nao encontrou o cliente pelo nif, procura o destinatario
                    $recipient = CustomerRecipient::find($targetId);
                    $customer  = @$recipient->assigned_customer;
                }

                if ($customer) {
                    $shipment->customer    = $customer;
                    $shipment->customer_id = $customer->id;
                }
            }
        }

        //calcula preços por tipo de mercadoria
        if (@$service->price_per_pack && $shipment->pack_dimensions) {

            $dimensions = $shipment->pack_dimensions;
            $shipment->pack_dimensions = null; //evita que no ciclo recursivo entre de novo nesta condição

            $pricesPerDimension = $packPrices = $prices = [];
            foreach ($dimensions as $key => $packDimension) {
                $shipment->pack_type     = $packDimension['type'];
                $shipment->pack_qty      = $packDimension['qty'];
                $shipment->pack_weight   = $packDimension['weight'];
                $shipment->pack_fator_m3 = $packDimension['fator_m3'];

                $dimPrice = self::calcPrices($shipment, $fullResponse, $manualExpenses);

                if(!empty($dimPrice)) {
                    $pricesPerDimension[] = $dimPrice;

                    $packPrices[] = [
                        'type'     => $packDimension['type'],
                        'qty'      => @$packDimension['qty'],
                        'width'    => @$packDimension['width'],
                        'height'   => @$packDimension['height'],
                        'length'   => @$packDimension['length'],
                        'weight'   => @$packDimension['weight'],
                        'fator_m3' => @$packDimension['fator_m3'],
                        'volumetric_weight' => ($packDimension['fator_m3'] ? (float)$packDimension['fator_m3'] : 0.0) * $dimPrice['parcels']['coefficient_m3'],
                        'subtotal' => $dimPrice['prices']['shipping']
                    ];
                }
            }

            if(!empty($pricesPerDimension)) {
                $prices = self::mergePricesPerDimension($pricesPerDimension);
                $prices['pack_prices'] = $packPrices;
            }

            unset($shipment->pack_type, $shipment->pack_qty, $shipment->pack_dimensions, $shipment->pack_weight, $shipment->pack_fator_m3);
            
            return $prices;
        }

        $originalProviderId = $shipment->provider_id;
        $shipment->provider_id = $shipment->provider_id ? $shipment->provider_id : Setting::get('shipment_default_provider');
        if (@$service->provider_id) { //força fornecedor
            $shipment->provider_id = $service->provider_id;
        }

        //if ($shipment->price_fixed || $shipment->is_blocked || $shipment->invoice_id || empty($service) || empty($service->zones)) { //comentado em 10/02/2023
        if ($shipment->is_blocked || $shipment->invoice_id || empty($service) || empty($service->zones)) {
            return null;
        }

        $originalService = $service;
        if ($shipment->is_collection) {
            if ($service->is_collection) {
                $shipment->service_id = @$service->id;
            } else {
                $service = $service->assignedService;
                $shipment->service    = $service;
                $shipment->service_id = @$service->id;
            }
        }

        //normaliza as variaveis de cálculo
        $shipment->weight    = forceDecimal($shipment->weight);
        $shipment->volumes   = empty($shipment->volumes) ? 0 : (int) $shipment->volumes;
        $shipment->volume_m3 = empty($shipment->volume_m3) ? null : forceDecimal($shipment->volume_m3);
        $shipment->fator_m3  = empty($shipment->fator_m3) ? null : forceDecimal($shipment->fator_m3);
        $shipment->kms       = empty($shipment->kms) ? 0 : forceDecimal($shipment->kms);
        $shipment->hours     = empty($shipment->hours) ? 0 : forceDecimal($shipment->hours);
        $transitTime         = @$service->transit_time;

        //obtem as zonas de faturação e preços
        $pricesZones = Self::getPricesZone($shipment);
        $shipment->zone         = $pricesZones['billing_zone'];
        $shipment->pickup_zone  = $pricesZones['origin_zone'];
        $shipment->cost_zone    = $pricesZones['cost_zone'];

        $zipCodeZones = Self::getZipCodeZones($shipment);
        $shipment->origin_remote_zone      = $zipCodeZones['origin_remote_zone'];
        $shipment->destination_remote_zone = $zipCodeZones['destination_remote_zone'];
        $shipment->blocked_zone            = $zipCodeZones['blocked_zone'];

        //obtem informação das dimensões
        $dims = Self::getShipmentDimensions($shipment);
        $shipment->dims_bigger_weight = $dims['max_weight'];
        $shipment->dims_bigger_side   = $dims['max_side'];
        $shipment->dims_bigger_size   = $dims['max_size'];
        $shipment->has_sku            = $dims['has_sku'];

        //obtem pesos taxaveis e cubicagem
        $weights = Self::getShipmentWeights($shipment);
        $originalWeight             = $weights['billing']['original'];
        $taxableWeight              = $weights['billing']['taxable'];
        $coefficientM3              = $weights['billing']['coefficient_m3'];
        $volumetricMinM3            = $weights['billing']['volumetric_min_m3'];
        $volumetricWeight           = $weights['billing']['volumetric_weight'];
        $averageWeight              = $weights['billing']['is_avg_weight'];
        $costTaxableWeight          = $weights['cost']['taxable'];
        $costCoeficientM3           = $weights['cost']['coefficient_m3'];
        $volumetricWeightProvider   = $weights['cost']['volumetric_weight'];
        $shipment->taxable_weight          = $taxableWeight;
        $shipment->provider_taxable_weight = $costTaxableWeight;
        $shipment->is_avg_weight           = $averageWeight;
        $shipment->coefficient_m3          = $coefficientM3;

        //a zona encontrada não pertence às zonas do serviço. Não é possivel calcular preços
        if (
            !in_array($shipment->zone, $service->zones)
            && ($service->zones_provider && !in_array($shipment->zone, $service->zones_provider))
        ) {
            unset(
                $shipment->coefficient_m3,
                $shipment->original_provider_id,
                $shipment->coefficient_m3,
                $shipment->is_avg_weight,
                $shipment->pickup_zone,
                $shipment->cost_zone,
                $shipment->origin_remote_zone,
                $shipment->destination_remote_zone,
                $shipment->blocked_zone
            );
            return null;
        }


        //calcula preço de venda
        if ($shipment->price_fixed) {
            //se o preço está bloqueado, mantém o preço inserido manualmente
            $outOfRange      = false;
            $priceRow        = '0-0';
            $shippingPrice   = $shipment->shipping_price;
            $extraPrice      = 0;
            $extraKg         = 0;
            $defaultPrice    = 0;
        } else {
            //calcula o preço a partir das tabelas de preço
            $billingPrices = self::calcBillingPrices($shipment);
            $outOfRange      = $billingPrices['out_range'];
            $priceRow        = $billingPrices['price_row'];
            $shippingPrice   = $billingPrices['price'];
            $extraPrice      = $billingPrices['extra_price'];
            $extraKg         = $billingPrices['extra_kg'];
            $defaultPrice    = $billingPrices['is_pvp'];
            $shipment->shipping_price = $shippingPrice;
        }

        //calcula preço de custo
        if ($shipment->price_fixed) {
            $costOutOfRange    = false;
            $costPriceRow      = '0-0';
            $costShippingPrice = $shipment->cost_shipping_price;
            $costExtraPrice    = 0;
            $costExtraKg       = 0;
            $providerFuelTax   = 0;
        } else {
            $costPrices = self::calcCostPrices($shipment);
            $providerFuelTax   = $costPrices['fuel_tax'];
            $costOutOfRange    = $costPrices['out_range'];
            $costPriceRow      = $costPrices['price_row'];
            $costShippingPrice = $costPrices['price'];
            $costExtraPrice    = $costPrices['extra_price'];
            $costExtraKg       = $costPrices['extra_kg'];
            $shipment->cost_price = $costShippingPrice;
        }

        //aplica taxa de combustivel do fornecedor
        if ($providerFuelTax) {
            $costShippingPrice = $costShippingPrice + ($costShippingPrice * ($providerFuelTax / 100));
        }

        //preço de venda é dado por percentagem do preço de custo
        if (@$service->unity == 'costpercent') {
            $percentValue = $shippingPrice; //neste caso o valor da tabela de preços é a percentagem a aplicar.
            $shippingPrice = $costShippingPrice + ($costShippingPrice * ($percentValue / 100));
        }

        //determina taxa de iva a aplicar
        $originalVatRateId     = $shipment->vat_rate_id;
        $vatRate               = $shipment->getVatRate();
        $vatRateId             = @$vatRate['id'];
        $billingItem           = @$vatRate['item'];
        $vatRate               = (float) @$vatRate['value'];

        // Substituir a taxa do envio para as taxas adicionais serem calculada corretamente
        $shipment->vat_rate_id = $vatRateId;
        $shipment->vat_rate    = $vatRate;
        // Voltar à taxa original (necessário caso a taxa seja automática/vazia)
        $vatRateId = $originalVatRateId;

        //calcula preço das taxas adicionais
        $expenses        = $shipment->getAvailableExpenses($manualExpenses, $shipment);
        $expensesPrice   = @$expenses['subtotal'];
        $expensesCost    = @$expenses['cost_subtotal'];
        $expensesCostVat = @$expenses['cost_vat'];
        $expensesItems   = @$expenses['items'];

        //prepara as tags do envio
        $tags = $expenses['tags'] ? $expenses['tags'] : [];  //lista de icones a apresentar no envio
        unset($expenses['items'], $expenses['tags']);

        if (!empty($shipment->has_return)) {
            if (in_array('rpack', @$shipment->has_return) && !in_array('rpack', $tags)) {
                $tags[] = 'rpack';
            }

            if (in_array('rguide', @$shipment->has_return) && !in_array('rguide', $tags)) {
                $tags[] = 'rguide';
            }
        }

        if (!empty($shipment->charge_price) && !in_array('charge', $tags)) {
            $tags[] = 'charge';
        }

        if (!empty($shipment->has_sku) && !in_array('sku', $tags)) {
            $tags[] = 'sku';
        }

        //calcula substotais do envio
        $subtotal     = (float) $shippingPrice + (float) $expensesPrice;
        $costSubtotal = (float) $costShippingPrice + (float) $expensesCost;

        // Calculate fuel tax
        $fuelTax       = $shipment->fuel_tax;
        $costFuelTax   = $shipment->cost_fuel_tax;

        $fuelPrice = $costFuelPrice = 0;
        if ($shipment->price_fixed) {
            $fuelPrice     = $subtotal * ($fuelTax / 100);
            $costFuelPrice = $costSubtotal * ($costFuelTax / 100);
        } else {
            $tempShipment = clone $shipment;
            $tempShipment->base_price  = $shippingPrice;
            $tempShipment->total_price = $subtotal;

            $fuelTax = $fuelPrice = $costFuelPrice = 0;
            $fuelExpense = ShippingExpense::getFuelExpense($tempShipment);
            if ($fuelExpense) {
                $fuelExpensePrice = $fuelExpense->calcExpensePrice($tempShipment);
                
                $fuelTax          = $fuelExpensePrice['fillable']['price'];
                $fuelPrice        = $fuelExpensePrice['fillable']['subtotal'];

                $costFuelTax      = $fuelExpensePrice['fillable']['cost_price'];
                $costFuelPrice    = $fuelExpensePrice['fillable']['cost_subtotal'];
            }
        }

        $subtotal     += $fuelPrice;
        $costSubtotal += $costFuelPrice;

        //calcula o valor dos IVA
        $billingVat     = (($shippingPrice + $fuelPrice) * ($vatRate / 100)) + @$expenses['vat'];
        $costVat        = ($costShippingPrice * ($vatRate / 100)) + @$expenses['cost_vat'];

        //prepara response do pedido
        if (Setting::get('shipments_round_up_weight')) {
            $originalWeight   = roundUp($originalWeight);
            $volumetricWeight = roundUp($volumetricWeight);
        }


        //repoe o serviço original, por causa do caso das recolhas em que mudamos o serviço
        $service = $originalService;
        $shipment->customer    = $originalCustomer;
        $shipment->customer_id = @$originalCustomer->id;
        $shipment->service     = $originalService;
        $shipment->service_id  = @$originalService->id;

        //prepara resposta
        $response = [
            "service"  => $service ? $service->toArray() : null,
            "expenses" => $expensesItems,
            "zones" => [
                "billing"            => $shipment->zone,
                "cost_billing"       => $shipment->cost_zone,
                "delivery"           => $shipment->zone,
                "pickup"             => $shipment->pickup_zone,
                "origin_remote"      => $shipment->origin_remote_zone,
                "destination_remote" => $shipment->destination_remote_zone,
                "blocked"            => $shipment->blocked_zone,
            ],
            "billing" => [
                "customer_id"        => @$shipment->customer->id,
                "customer_vat"       => @$shipment->customer->vat,
                "customer_country"   => @$shipment->customer->billing_country,
                "is_particular"      => @$shipment->customer->is_particular,
                "payment_condition"  => @$shipment->customer->payment_condition->code,
                "currency"           => Setting::get('app_currency'),
                "vat_rate"           => $vatRate,
                'vat_rate_id'        => $vatRateId,
                'billing_item'       => $billingItem,
                "subtotal"           => number($subtotal),
                "vat"                => number($billingVat),
                "total"              => number($subtotal + $billingVat),
            ],
            "prices" => [
                "zone"               => $shipment->zone,
                "pickup_zone"        => $shipment->pickup_zone,
                "price_row"          => $priceRow,
                "out_range"          => $outOfRange, //se o preço está fora da tabela de preços
                "base_price"         => number($shippingPrice - $extraPrice),
                "extra_price"        => number($extraPrice),
                "expenses"           => number($expensesPrice),
                "shipping"           => number($shippingPrice),
                "fuel_price"         => number($fuelPrice),
                "fuel_tax"           => $fuelTax,
                'vat_rate'           => $vatRate,
                "unity"              => @$service->unity,
                "is_pvp"             => $defaultPrice,
            ],
            "costs" => [
                "provider_id"        => $shipment->provider_id,
                "zone"               => $shipment->cost_zone,
                "pickup_zone"        => $shipment->pickup_zone,
                "price_row"          => $costPriceRow,
                "out_range"          => $costOutOfRange, //se o preço está fora da tabela de preços
                "base_price"         => number($costShippingPrice - @$costExtraPrice),
                "extra_price"        => number(@$costExtraPrice),
                "shipment"           => number($costShippingPrice),
                "expenses"           => number($expensesCost),
                "fuel_price"         => number($costFuelPrice),
                "fuel_tax"           => number($costFuelTax),
                "vat_rate"           => $vatRate,
                "subtotal"           => number($costSubtotal),
                "vat"                => number($costVat),
                "total"              => number($costSubtotal + $costVat)
            ],
            "balance" => [
                "value"              => $subtotal - $costSubtotal,
                "percent"            => $subtotal > 0.00 ? number((($subtotal - $costShippingPrice) / $subtotal) * 100) : '0.00',
            ],
            "prices_details"  => [
                "shipping" => [
                    "subtotal"      => $shippingPrice,
                    "vat"           => $shippingPrice * ($vatRate / 100),
                    "total"         => $shippingPrice * (1 + ($vatRate / 100)),
                    "cost_subtotal" => $costShippingPrice,
                    "cost_vat"      => $costShippingPrice * ($vatRate / 100),
                    "cost_total"    => $costShippingPrice * (1 + ($vatRate / 100)),
                ],
                "fuel" => [
                    "subtotal"      => $fuelPrice,
                    "vat"           => $fuelPrice * ($vatRate / 100),
                    "total"         => $fuelPrice * (1 + ($vatRate / 100)),
                    "cost_subtotal" => 0.00,
                    "cost_vat"      => 0.00,
                    "cost_total"    => 0.00,
                ],
                "expenses" => $expenses,
            ],
            "parcels" => [
                "taxable_weight"     => number($taxableWeight),
                "real_weight"        => number($originalWeight),
                'volumetric_weight'  => number($volumetricWeight),
                'fator_m3'           => $shipment->fator_m3,
                'coefficient_m3'     => $coefficientM3,
                'extra_weight'       => number($extraKg),
                'cost_extra_weight'  => number($costExtraKg),
                'max_volumes_allowed' => @$service->max_volumes,
                'max_weight_allowed' => @$service->max_weight,
                'is_avg_weight'      => $averageWeight,

                'provider_taxable_weight'     => number($shipment->provider_taxable_weight),
                'provider_volumetric_weight'  => number($volumetricWeightProvider),
                'cost_coefficient_m3' => $costCoeficientM3,
                'cost_weight_taxable' => number($shipment->provider_taxable_weight),
                'cost_weight_extra'   => number($costExtraKg),
                'has_assembly'        => $shipment->has_assembly,
                'has_sku'             => $shipment->has_sku,
            ],
            "shipment" => [
                "customer_id"        => @$shipment->customer_id,
                "provider_id"        => @$shipment->provider_id,
                "agency_id"          => @$shipment->agency_id,
                "sender_agency_id"   => @$shipment->sender_agency_id,
                "recipient_agency_id" => @$shipment->recipient_agency_id ?? $shipment->sender_agency_id,
                "sender_zip_code"    => $shipment->sender_zip_code,
                "sender_country"     => $shipment->sender_country,
                "recipient_zip_code" => $shipment->recipient_zip_code,
                "recipient_country"  => $shipment->recipient_country,
                "volumes"            => $shipment->volumes,
                "kms"                => number($shipment->kms),
                "ldm"                => number($shipment->ldm),
                "hours"              => number($shipment->hours),
                "transitTime"        => $transitTime,
                "tags"               => $tags, //lista de icones a apresentar no envio
                "volumetric_dims_min"   => $volumetricMinM3,
                "volumetric_coeficient" => $coefficientM3,
                "volumetric_provider"   => $costCoeficientM3,
            ],
            "field_errors" => []
        ];


        if ($fullResponse) {

            $customer = $shipment->customer;

            //get pickup date
            $pickupDate = $shipment->getPickupDate(true);

            if (@$customer->route && ($customer->route->type == 'pickup' || $customer->route->type == null)) {
                $pickupRoute = $customer->route;
            } else {
            $pickupRoute = Route::getRouteFromZipCode($shipment->sender_zip_code, $shipment->service_id, null, 'pickup');
            }
            
            if (@$pickupRoute && $pickupRoute->exists) {
                $pickupRouteSchedule = $pickupRoute->getSchedule($shipment->start_hour, $shipment->end_hour);
            }

            //dd($pickupDate);
            $response['pickup'] = [
                "agency_id"     => @$response['shipment']['sender_agency_id'],
                "route_id"      => @$pickupRoute->id,
                "operator_id"   => @$pickupRouteSchedule['operator']['id'],
                "hour_min"      => @$pickupDate['hour_min'],
                "hour_max"      => @$pickupDate['hour_max'],
                "hour"          => @$pickupDate['hour'],
                "date"          => @$pickupDate['date'],
                "shipping_date" => @$pickupDate['shipping_date'],
            ];

            $response['field_errors'] = array_merge($response['field_errors'], @$pickupDate['errors']);

            //get delivery date
            $deliveryDate = $shipment->getDeliveryDate(true, @$pickupDate['shipping_date']);

            if (@$customer->route && ($customer->route->type == 'delivery' || $customer->route->type == null)) {
                $deliveryRoute = $customer->route;
            } else {
                $deliveryRoute = Route::getRouteFromZipCode($shipment->recipient_zip_code, $shipment->service_id, null, 'delivery');
            }

            if (@$deliveryRoute && $deliveryRoute->exists) {
                $deliveryRouteSchedule = $deliveryRoute->getSchedule($shipment->start_hour, $shipment->end_hour);
            }

            //DESCOMENTAR SE FOR PRECISO QUE DEVOLVA A AGENCIA CORRETA
            /*$deliveryZipCode = AgencyZipCode::where(function($q) use($shipment) {
                $zp4 = explode('-', $shipment->recipient_zip_code);
                $zp4 = @$zp4[0];
                $q->where('zip_code', $shipment->recipient_zip_code);
                $q->orWhere('zip_code', $zp4);
            })->orderBy('zip_code', 'desc')->first();

            if(@$deliveryZipCode->agency_id) { //subscreve o original de acordo com a bd de codigos postais
                $response['shipment']['recipient_agency_id'] = @$deliveryZipCode->agency_id;
            }*/

            $response['delivery'] = [
                "agency_id"         => @$response['shipment']['recipient_agency_id'],
                "route_id"          => @$deliveryRoute->id,
                "operator_id"       => @$deliveryRouteSchedule['operator']['id'],
                "hour_min"          => @$deliveryDate['hour'],
                "hour_max"          => @$deliveryDate['hour'],
                "date"              => @$deliveryDate['date'],
                "transit_time"      => @$deliveryDate['transit_time'],
                "transit_time_max"  => @$deliveryDate['transit_time_max'],
                "delivery_date"     => @$deliveryDate['delivery_date'],
            ];
        }

        //fields to autofill
        $response['fillable'] = [

            "shipping_base_price" => $response['prices']['base_price'],
            "shipping_price"      => $response['prices']['shipping'],
            "fuel_tax"            => $response['prices']['fuel_tax'],
            "fuel_price"          => $response['prices']['fuel_price'],
            "expenses_price"      => $response['prices']['expenses'],

            "billing_subtotal"    => $response['billing']['subtotal'],
            "billing_vat"         => $response['billing']['vat'],
            "billing_total"       => $response['billing']['total'],
            "billing_zone"        => $response['prices']['zone'],
            "billing_pickup_zone" => $response['prices']['pickup_zone'],
            "billing_item"        => $response['billing']['billing_item'],
            "vat_rate"            => $response['billing']['vat_rate'],
            "vat_rate_id"         => $response['billing']['vat_rate_id'],

            "cost_fuel_tax"             => @$response['costs']['fuel_tax'],
            "cost_fuel_price"           => @$response['costs']['fuel_price'],
            "cost_shipping_base_price"  => @$response['costs']['base_price'],
            "cost_shipping_price"       => @$response['costs']['shipment'],
            "cost_expenses_price"       => @$response['costs']['expenses'],
            "cost_billing_subtotal" => @$response['costs']['subtotal'],
            "cost_billing_vat"      => @$response['costs']['vat'],
            "cost_billing_total"    => @$response['costs']['total'],
            "cost_billing_zone"     => @$response['costs']['zone'],

            "taxable_weight"            => $response['parcels']['taxable_weight'],
            "extra_weight"              => $response['parcels']['extra_weight'],
            "provider_taxable_weight"   => $response['parcels']['provider_taxable_weight'],

            'has_assembly'        => $shipment->has_assembly,
            'has_sku'             => $shipment->has_sku,

            'dims_bigger_weight'  => $shipment->dims_bigger_weight,
            'dims_bigger_side'    => $shipment->dims_bigger_side,
            'dims_bigger_size'    => $shipment->dims_bigger_size,

            "tags" => $tags //lista de icones a apresentar no envio
        ];

        if ($fullResponse) {
            // $shipment->original_provider_id = $originalProviderId;
            $validate = self::validateShipment($shipment);
            // $response['field_errors'] = array_merge($response['field_errors'], $validate['errors']);
            $response['errors'] = $validate['errors'];
            $response['result'] = $validate['valid'];
        }

        //descarta variaveis temporarias
        unset(
            $shipment->coefficient_m3,
            $shipment->original_provider_id,
            $shipment->coefficient_m3,
            $shipment->is_avg_weight,
            $shipment->pickup_zone,
            $shipment->cost_zone,
            $shipment->origin_remote_zone,
            $shipment->destination_remote_zone,
            $shipment->blocked_zone,
            $shipment->service,
            $shipment->customer
        );

        //memoria inicial = 468.08;
        //memoria anterior = 696.97
        /*$memory = memory_get_usage() - $startMemory; // 36640

        dd(human_filesize2($memory));*/

        return $response;
    }

    /**
     * Merge prices per dimension into single result
     * @param $pricesPerDimension
     * @return mixed
     */
    public static function mergePricesPerDimension($pricesPerDimension)
    {
        $response = @$pricesPerDimension[0];
        unset($pricesPerDimension[0]);

        $subtotal = $costShippingPrice = 0;
        foreach ($pricesPerDimension as $dimPrice) {
            //PRICES
            $response['prices']['base_price']  += @$dimPrice['prices']['base_price'];
            $response['prices']['shipping']    += @$dimPrice['prices']['shipping'];
            $response['prices']['extra_price'] += @$dimPrice['prices']['extra_price'];

            //PRICE DETAILS - SHIPPING
            $response['prices_details']['shipping']['subtotal'] += @$dimPrice['prices_details']['shipping']['subtotal'];
            $response['prices_details']['shipping']['vat']      += @$dimPrice['prices_details']['shipping']['vat'];
            $response['prices_details']['shipping']['total']    += @$dimPrice['prices_details']['shipping']['total'];
            
            //BALANCE
            $subtotal += @$dimPrice['billing']['subtotal'];
            $costShippingPrice += @$dimPrice['costs']['subtotal'];
        }

        //FUEL
        $response['prices']['fuel_price'] = ($response['prices']['shipping'] + $response['prices']['expenses']) * ($response['prices']['fuel_tax'] / 100);
        $response['prices_details']['fuel']['subtotal']      = $response['prices']['fuel_price'];
        $response['prices_details']['fuel']['vat']           = $response['prices']['fuel_price'] * ($response['prices']['vat_rate'] / 100);
        $response['prices_details']['fuel']['total']         = $response['prices_details']['fuel']['subtotal'] + $response['prices_details']['fuel']['vat'];

        //BILLING
        $response['billing']['subtotal'] = number(@$response['prices_details']['shipping']['subtotal'] + @$response['prices_details']['fuel']['subtotal'] + @$response['prices_details']['expenses']['subtotal']);
        $response['billing']['vat']      = number(@$response['prices_details']['shipping']['vat'] + @$response['prices_details']['fuel']['vat'] + @$response['prices_details']['expenses']['vat']);
        $response['billing']['total']    = number(@$response['prices_details']['shipping']['total'] + @$response['prices_details']['fuel']['total'] + @$response['prices_details']['expenses']['total']);

        //PRICES
        $response['prices']['base_price']  = number($response['prices']['base_price']);
        $response['prices']['shipping']    = number($response['prices']['shipping']);
        $response['prices']['extra_price'] = number($response['prices']['extra_price']);

        //PRICE DETAILS - SHIPPING
        $response['prices_details']['shipping']['subtotal'] = number($response['prices_details']['shipping']['subtotal']);
        $response['prices_details']['shipping']['vat']      = number($response['prices_details']['shipping']['vat']);
        $response['prices_details']['shipping']['total']    = number($response['prices_details']['shipping']['total']);

        //FILLABLE
        $response['fillable']['shipping_base_price'] = number($response['prices']['base_price']);
        $response['fillable']['shipping_price']      = number($response['prices']['shipping']);
        $response['fillable']['fuel_price']          = number($response['prices']['fuel_price']);
        $response['fillable']['billing_subtotal']    = number($response['billing']['subtotal']);
        $response['fillable']['billing_vat']         = number($response['billing']['vat']);
        $response['fillable']['billing_total']       = number($response['billing']['total']);
        $response['fillable'][''] = $response['billing']['subtotal'];

        //BALANCE
        $response['balance']['value']   = $subtotal - $costShippingPrice;
        $response['balance']['percent'] = $subtotal > 0.00 ? number((($subtotal - $costShippingPrice) / $subtotal) * 100) : '0.00';

        return $response;
    }

    /**
     * Valida informação do envio
     * @param $shipment
     * @return array
     */
    public static function validateShipment($shipment)
    {

        $isValid = true;
        $errors  = [];

        /*if(@$shipment->service->provider_id && $shipment->original_provider_id && $shipment->original_provider_id != @$shipment->service->provider_id) {
            $isValid  = false;
            $errors[] = 'O fornecedor escolhido não pode ser usado para este serviço.';
        }*/

        $packTypes = PackType::filterSource()
            ->get()
            ->keyBy('code');

        foreach ($shipment->pack_dimensions ?? [] as $key => $dimension) {
            if (empty($dimension['width']) || empty($dimension['height']) || empty($dimension['length']) || empty($dimension['weight'])) {
                continue;
            }

            $packType = @$packTypes[$dimension['type']];
                if (!$packType) {
                    continue;
                }

            if ($packType['type'] == 'pallets') {
                $packType['type'] = 'pallet';
            } else if ($packType['type'] == 'others') {
                $packType['type'] = 'boxes';
            }

            if (@$shipment->service->{'max_weight_' . $packType['type']} && $dimension['weight'] > @$shipment->service->{'max_weight_' . $packType['type']}) {
                $isValid  = false;
                $errors[] = 'Peso da dimensão nº'. ($key + 1) .' não pode ser superior a ' . @$shipment->service->{'max_weight_' . $packType['type']} . 'kg.';
            }

            if (@$shipment->service->{'max_width_' . $packType['type']} && $dimension['width'] > @$shipment->service->{'max_width_' . $packType['type']}) {
                    $isValid  = false;
                $errors[] = 'Largura da dimensão nº'. ($key + 1) .' não pode ser superior a ' . @$shipment->service->{'max_width_' . $packType['type']} . 'cm.';
                }

            if (@$shipment->service->{'max_height_' . $packType['type']} && $dimension['height'] > @$shipment->service->{'max_height_' . $packType['type']}) {
                    $isValid  = false;
                $errors[] = 'Altura da dimensão nº'. ($key + 1) .' não pode ser superior a ' . @$shipment->service->{'max_height_' . $packType['type']} . 'cm.';
                }

            if (@$shipment->service->{'max_length_' . $packType['type']} && $dimension['length'] > @$shipment->service->{'max_length_' . $packType['type']}) {
                    $isValid  = false;
                $errors[] = 'Comprimento da dimensão nº'. ($key + 1) .' não pode ser superior a ' . @$shipment->service->{'max_length_' . $packType['type']} . 'cm.';
            }
        }
        

        // if ((@$shipment->service->max_weight_docs && $shipment->dims_bigger_weight > @$shipment->service->max_weight_docs) ||
        //     (@$shipment->service->max_weight_boxes && $shipment->dims_bigger_weight > @$shipment->service->max_weight_boxes) ||
        //     (@$shipment->service->max_weight_pallet && $shipment->dims_bigger_weight && $shipment->dims_bigger_weight > @$shipment->service->max_weight_pallet)
        // ) {
        //     $isValid  = false;
        //     $errors[] = 'Peso dos volumes não pode ser superior a ' . @$shipment->service->max_weight_boxes . 'kg.';
        // }

        if (@$shipment->service->min_weight && $shipment->weight > 0.00 && $shipment->weight < @$shipment->service->min_weight) {
            $isValid  = false;
            $errors[] = 'O peso da expedição não pode ser inferior a ' . @$shipment->service->min_weight . ' para o serviço selecionado.';
        }

        if (@$shipment->service->max_weight && $shipment->weight > @$shipment->service->max_weight) {
            $isValid = false;
            $errors[] = 'O serviço selecionado apenas permite ' . @$shipment->service->max_weight . 'kg por expedição.';
        }

        if (@$shipment->service->min_volumes && $shipment->weight >= 1 && $shipment->volumes < @$shipment->service->min_volumes) {
            $isValid = false;
            $errors[] = 'Os volumes da expedição não podem ser inferiores a ' . @$shipment->service->min_volumes;
        }

        if (@$shipment->service->max_volumes && $shipment->volumes > @$shipment->service->max_volumes) {
            $isValid  = false;
            $errors[] = 'O serviço selecionado apenas permite ' . @$shipment->service->max_volumes . ' volumes por expedição.';
        }

        return [
            'valid'  => $isValid,
            'errors' => $errors
        ];
    }

    /**
     * Retorna os serviços disponíveis dependendo dos parametros disponíveis do envio
     * @param $params
     * @return mixed
     */
    public static function getAvailableServices($params)
    {

        $shipmentTypology = @$params['shipment_tipology'] ? $params['shipment_tipology'] : $params['pack_type']; //$params['shipment_tipology'] vem do budgeter
        $appCountry       = Setting::get('app_country');
        $senderCountry    = $params['sender_country'];
        $senderZipCode    = $params['sender_zip_code'];
        $recipientCountry = $params['recipient_country'];
        $recipientZipCode = $params['recipient_zip_code'];

        $volumes = $params['volumes'];
        $weight  = $params['weight'];

        $services = Service::with('provider');

        if (!empty($params['services'])) {
            //limita a pesquisa aos serviços escolhidos
            $services = $services->whereIn('id', $params['services']);
        } else {

            //limita os serviços que não tenham regras de peso e volumes válidas
            $services = $services->where(function ($q) use ($weight) {
                $q->whereNull('min_weight');
                $q->orWhere('min_weight', '<=', $weight);
            })
                ->where(function ($q) use ($weight) {
                    $q->whereNull('max_weight');
                    $q->orWhere('max_weight', '>=', $weight);
                })
                ->where(function ($q) use ($volumes) {
                    $q->whereNull('min_volumes');
                    $q->orWhere('min_volumes', '<=', $volumes);
                })
                ->where(function ($q) use ($volumes) {
                    $q->whereNull('max_volumes');
                    $q->orWhere('max_volumes', '>=', $volumes);
                });
        }

        //verifica todos os serviços que têm zona de faturação para o codigo postal indicado

        //limita os serviços por tipo de volume
        if ($shipmentTypology !== null) {
            if ($shipmentTypology == 'boxes') {
                $services = $services->where('allow_boxes', 1);
            } elseif ($shipmentTypology == 'pallets') {
                $services = $services->where('allow_pallets', 1);
            } elseif ($shipmentTypology == 'docs') {
                $services = $services->where('allow_docs', 1);
            } else {
                $services = $services->where(function ($q) {
                    $q->where('allow_docs', 0);
                    $q->where('allow_boxes', 0);
                    $q->where('allow_pallets', 0);
                });
            }
        }

   

        //limita serviços internacionais
        if (($senderCountry == $appCountry && !in_array($recipientCountry, ['es', 'pt']))
            || (!in_array($senderCountry, ['es', 'pt']) && $recipientCountry == $appCountry)) {
            $services = $services->where('is_internacional', 1);
        } else {
            $services = $services->where('is_internacional', 0);
        }

        $services = $services->ordered()
            ->get();


        //dd($services->pluck('name')->toArray());

        //valida dimensões por pack indivualmente
        $errors = [];
        foreach ($services as $serviceKey => $service) {

            foreach ($params['pack_length'] as $packKey => $lengthValue) {

                $packWeight = (float) $params['pack_weight'][$packKey];
                $packWidth  = (float) $params['pack_width'][$packKey];
                $packHeight = (float) $params['pack_height'][$packKey];
                $packLength = (float) $params['pack_length'][$packKey];
                $packDims   = $packWidth + $packHeight + $packLength;

                //so no GLS eurobusiness é que a formula é 2xAncho + 2xAlto + Largo
                if (config('app.source') == 'baltrans' && in_array($service->id, [31, 162])) {
                    $packDims = ($packWidth * 2) + ($packHeight * 2) + $packLength;
                }

                $maxWeight = (float) @$service->{"max_weight_" . $shipmentTypology};
                $maxWidth  = (float) @$service->{'max_width_' . $shipmentTypology};
                $maxHeight = (float) @$service->{'max_height_' . $shipmentTypology};
                $maxLength = (float) @$service->{'max_length_' . $shipmentTypology};
                $maxDims   = (float) @$service->{'max_dims_' . $shipmentTypology};

                if ($maxDims && $packDims > $maxDims) {
                    unset($services[$serviceKey]);
                    $errors[] = [
                        'service_id'   => $service->id,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'error'        => 'Soma lados máximo: ' . $maxDims . '. Peso item: ' . $packDims
                    ];
                } elseif ($maxWeight && $packWeight > $maxWeight) {
                    unset($services[$serviceKey]);
                    $errors[] = [
                        'service_id'   => $service->id,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'error'        => 'Peso máximo: ' . $maxWeight . '. Peso item: ' . $packWeight
                    ];
                } elseif ($maxWidth && $packWidth > $maxWidth) {
                    unset($services[$serviceKey]);
                    $errors[] = [
                        'service_id'   => $service->id,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'error'        => 'Comprimento máximo: ' . $maxWidth . '. Comprimento item: ' . $packWidth
                    ];
                } elseif ($maxHeight && $packHeight > $maxHeight) {
                    unset($services[$serviceKey]);
                    $errors[] = [
                        'service_id'   => $service->id,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'error'        => 'Altura máxima: ' . $maxHeight . '. Altura item: ' . $packHeight
                    ];
                } elseif ($maxLength && $packLength > $maxLength) {
                    unset($services[$serviceKey]);
                    $errors[] = [
                        'service_id'   => $service->id,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'error'        => 'Largura máxima: ' . $maxLength . '. Largura item: ' . $packLength
                    ];
                }
            }
        }

        //dd($services->pluck('name')->toArray());

        return $services;
    }

    /**
     * Obtem as zonas de preço
     *
     * @param $shipment
     * @param $service
     * @param $country
     * @param $fullZipCode
     * @param $zipCode
     * @return array
     */
    public static function getPricesZone($shipment)
    {

        /*        $shipment->recipient_zip_code = '4000-120';
                $shipment->sender_zip_code = '4000-120';
                $shipment->recipient_country = 'pt';*/

        $service  = $shipment->service;
        
        $isImport = $shipment->is_import || $shipment->type == 'P' ? true : false; //P=recolha

        $country            = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country, $isImport);
        $country            = empty($country) ? Setting::get('app_country') : $country;
        $senderZipCode      = $shipment->sender_zip_code;
        $recipientZipCode   = $shipment->recipient_zip_code;
        $fullZipCode        = Shipment::getBillingZipCode($senderZipCode, $recipientZipCode, @$service->is_import, true);


        if (config('app.source') == 'intercourier' && in_array($shipment->customer_id, ['7021', '7073', '7075', '7080', '7092', '7120', '7139'])) {
            $country = 'pt';
        }

        //Zona de faturação
        $billingZone = self::findBillingZone($shipment, $service->zones, $country, $fullZipCode);

        //zona de origem
        $allowedOrigins = null;
        $originZone     = null;
        if (@$shipment->customer->price_table_id) {
            //tabela de preços. verifica na tabela as configurações de zonas de origem
        } else {
            //verifica nas configurações do cliente as zonas de origem configuradas
            $allowedOrigins = $service->zones;
        }

        if ($allowedOrigins) {
            //verifica para o serviço selecionado, quais são as zonas de origem que ele tem configuradas
            //procura na tabela customer_assigned_services todas as zonas de origem para o serviço
            //e tabela preços / cliente selecionado

            $fieldName  = 'customer_id';
            $fieldValue = $shipment->customer_id;

            $priceTableId = @$shipment->customer->price_table_id;
            $priceTableId = $priceTableId ? $priceTableId : @$shipment->customer->prices_tables[$shipment->service->group];

            if ($priceTableId) {
                $fieldName  = 'price_table_id';
                $fieldValue = $priceTableId;
            }

            $allowedOrigins = CustomerService::where('service_id', $shipment->service_id)
                ->where($fieldName, $fieldValue)
                ->whereNotNull('origin_zone')
                ->groupBy('origin_zone')
                ->pluck('origin_zone')
                ->toArray();


            $originZone = self::findBillingZone($shipment, $allowedOrigins, $shipment->sender_country, $shipment->sender_zip_code, $shipment->sender_zip_code);
        }

        //zona de custo
        $costBillingZone = $billingZone;
        $costZones = @$service->zones_provider[@$shipment->provider_id]; //zonas personalizadas para o fornecedor

        if ($costZones) {
            $costBillingZone = self::findBillingZone($shipment, $costZones, $country, $fullZipCode);

            if (!$costBillingZone) {
                $costBillingZone  = $costBillingZone;
            }
        }

        return [
            'origin_zone'  => empty($originZone) ? null : $originZone,
            'billing_zone' => $billingZone,
            'cost_zone'    => $costBillingZone
        ];
    }

    /**
     * Get zip code zones (blocked, remote, ...)
     * 
     * @param Shipment $shipment
     * @return array
     */
    public static function getZipCodeZones($shipment) {
        $service = @$shipment->service;

        $country            = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country, $shipment->is_import);
        $country            = empty($country) ? Setting::get('app_country') : $country;

        $senderZipCode      = $shipment->sender_zip_code;
        $recipientZipCode   = $shipment->recipient_zip_code;

        $senderZipCode4     = zipcodeCP4(trim($senderZipCode));
        $recipientZipCode4  = zipcodeCP4(trim($recipientZipCode));

        $billingFullZipCode = Shipment::getBillingZipCode($senderZipCode, $recipientZipCode, @$service->is_import, true);
        $splitBillingZipCode = explode('-', $billingFullZipCode);
        $billingZipCode4 = @$splitBillingZipCode[0] ?? $billingFullZipCode;

        /** Find remote and blocked zones */
        $allZones = ZipCodeZone::filterSource()
            ->whereIn('type', ['blocked', 'remote'])
            ->where('country', $country)
            ->where(function ($q) use ($shipment) {
                $q->where('provider_id', $shipment->provider_id);
                $q->orWhereNull('provider_id');
            })
            ->get();

        $originRemoteZone = $allZones->filter(function ($zone) use ($shipment, $senderZipCode4) {
            $validService = empty($zone->services) || in_array($shipment->service_id, $zone->services);
            if (!$validService || $zone->type != 'remote')
                return false;

            return (empty($zone->zip_codes) && $shipment->sender_country == $zone->zone_country) || (!empty($zone->zip_codes) && in_array($senderZipCode4, $zone->zip_codes));
        })->first();

        $destinationRemoteZone = $allZones->filter(function ($zone) use ($shipment, $recipientZipCode4) {
            $validService = empty($zone->services) || in_array($shipment->service_id, $zone->services);
            if (!$validService || $zone->type != 'remote')
                return false;

            return (empty($zone->zip_codes) && $shipment->recipient_country == $zone->zone_country) || (!empty($zone->zip_codes) && in_array($recipientZipCode4, $zone->zip_codes));
        })->first();

        $blockedZone = $allZones->filter(function ($zone) use ($shipment, $billingZipCode4) {
            $validService = empty($zone->services) || in_array($shipment->service_id, $zone->services);

            $rule = $zone->type == 'blocked' && $validService;
            if($zone->zip_codes) {
                $rule = $rule && in_array($billingZipCode4, $zone->zip_codes);
            }
            
            return $rule;
        })->first();

        return [
            'blocked_zone'            => $blockedZone->code ?? null,
            'origin_remote_zone'      => $originRemoteZone->code ?? null,
            'destination_remote_zone' => $destinationRemoteZone->code ?? null,
        ];
    }

    /**
     * Procura uma zona de faturação dado um array de zonas a verificar
     * @param $shipment
     * @param $zonesToCompare Array de zonas a comparar
     * @param $country pais a verificar
     * @param $fullZipCode codigo postal a verificar
     * @return array|int|string|null
     */
    public static function findBillingZone($shipment, $zonesToVerify, $country, $fullZipCode)
    {

        $zones = $zonesToVerify;

        $billingZone = null;

        //obtem da BD as zonas de faturação a verificar
        $billingZones = BillingZone::filterSource();
        if ($zones) {
            $billingZones = $billingZones->whereIn('code', $zones);
        }
        $billingZones = $billingZones->get(['code', 'unity', 'mapping', 'pack_types', 'country', 'distance_min', 'distance_max']);

        if (!$billingZones->isEmpty()) {

            //cria um array com os tipos de zona de faturação.
            //o array tem o formato 'country' => 'pt' apenas para que
            //ao verificar se existe a zona baste apenas fazer a verificação pela chave do array
            $zonesTypes = $billingZones->filter(function ($item) use ($zones) {
                return in_array($item->code, $zones);
            })
                ->pluck('code', 'unity')
                ->toArray();


            //obtem zona de faturação por tipo mercadoria
            if ($shipment->pack_type && @$zonesTypes['pack_type']) {
                $packType = $shipment->pack_type;
                $zonesByPackType = $billingZones->filter(function ($item) use ($packType) {
                    return $item->unity == 'pack_type' && in_array($packType, $item->mapping); //verifica se o tipo de mercadoria está na lista de mapeamento
                })->first();

                if (@$zonesByPackType->code) {
                    return @$zonesByPackType->code;
                }
            }


            //obtem zona de faturação por país
            $zonesByCountry = null;
            if (@$zonesTypes['country']) {
                $zonesByCountry = $billingZones->filter(function ($item) {
                    return $item->unity == 'country';
                })->pluck('mapping', 'code')->toArray();
            }

            //obtem zona de faturação por codigo postal
            //forçando o país de faturação
            $zonesByZipCode = null;
            if (@$zonesTypes['zip_code']) {
                $zonesByZipCode = $billingZones->filter(function ($item) use ($country) {
                    return $item->unity == 'zip_code' && ($item->country == $country || empty($item->country));
                })->pluck('mapping', 'code')->toArray();
            }

            //obtem zona de faturação por codigo postal + tipo mercadoria
            //forçando o país de faturação
            if ($shipment->pack_type && @$zonesTypes['pack_zip_code']) {
                $packType = $shipment->pack_type;
                $zonesByPackZipCode = $billingZones->filter(function ($item) use ($country, $packType) {
                    return $item->unity == 'pack_zip_code'
                        && ($item->country == $country || empty($item->country))
                        && in_array($packType, $item->pack_types); //filtra tambem se o tipo da pacote está nos tipos permitidos
                })->pluck('mapping', 'code')->toArray();
                $zonesByZipCode = $zonesByPackZipCode; //atribui à variavel de zonas por codigo postal
            }

            //obtem zona de faturação por matriz de códigos postais
            //forçando o país de faturação
            $zonesByMatrix = null;
            if (@$zonesTypes['matrix']) {
                $zonesByMatrix = $billingZones->filter(function ($item) use ($country) {
                    return $item->unity == 'matrix'
                        && ($item->country == $country || empty($item->country));
                })->pluck('mapping', 'code')->toArray();
            } elseif ($shipment->pack_type && @$zonesTypes['pack_matrix']) {
                $packType = $shipment->pack_type;
                $zonesByMatrix = $billingZones->filter(function ($item) use ($country, $packType) {
                    return $item->unity == 'pack_matrix'
                        && ($item->country == $country || empty($item->country))
                        && in_array($packType, $item->pack_types); //filtra tambem se o tipo da pacote está nos tipos permitidos
                })->pluck('mapping', 'code')->toArray();
            }

            //verifica se há zonas de faturação por distancia
            $zonesByDistance = null;
            if (@$zonesTypes['distance']) {
                $zonesByDistance = @$billingZones->filter(function ($item) use ($shipment) {
                    $kms = $shipment->kms / 2; //os km vêm sempre de ida e volta
                    return $item->unity == 'distance' && valueBetween($kms, $item->distance_min, $item->distance_max);
                })->first()->code;
            }

            //verifica se os km do envio existem em alguma zona de faturação
            if (!empty($zonesByDistance) && !empty($kms)) {
                $billingZone = $zonesByDistance;
            } else {

                //1. verifica zonas por matriz
                if (!$billingZone && $zonesByMatrix) {
                    $countryUpper   = strtoupper($shipment->sender_country); //para as ranges FR40001-FR42000,DE54000-DE80000,...

                    //obtem o codigo 4 digitos da origem
                    $zipCodeParts   = explode('-', $shipment->sender_zip_code);
                    $originZipCode4 = trim(@$zipCodeParts[0]);

                    //obtem o codigo 4 digitos do destino
                    $zipCodeParts = explode('-', $shipment->recipient_zip_code);
                    $destZipCode4 = trim(@$zipCodeParts[0]);

                    // Converte códigos postais para integer
                    $originZipCode4    = ZipCode::toInteger($originZipCode4);
                    $destZipCode4      = ZipCode::toInteger($destZipCode4);
                    $originWithCountry = ZipCode::toInteger($countryUpper . $originZipCode4);
                    $destWithCountry   = ZipCode::toInteger($countryUpper . $destZipCode4);

                    //caso o país seja FR (porque tem zonas repartidas), verifica se o codigo postal corresponde a alguma zona
                    foreach ($zonesByMatrix as $zoneCode => $matrixZones) {

                        $zoneFinded = array_where($matrixZones, function ($value) use ($originZipCode4, $destZipCode4, $country, $countryUpper, $originWithCountry, $destWithCountry) {

                            $signal = '=>';
                            if (str_contains($value, '<=>')) {
                                $signal = '<=>';
                            }

                            $parts  = explode($signal, $value);
                            $origin = @$parts[0];
                            $dest   = @$parts[1];

                            //range de códigos postais (ex: 1000-1999<=>3000-3999)
                            if (str_contains($origin, '-')) {
                                $rangeOrigin = explode('-', $origin);
                                $rangeOrigin[0] = ZipCode::toInteger($rangeOrigin[0]);
                                $rangeOrigin[1] = ZipCode::toInteger($rangeOrigin[1]);

                                $rangeOriginWithCountry[0] = ZipCode::toInteger($countryUpper . $rangeOrigin[0]);
                                $rangeOriginWithCountry[1] = ZipCode::toInteger($countryUpper . $rangeOrigin[1]);
                            }

                            if (str_contains($dest, '-')) {
                                $rangeDestination = explode('-', $dest);
                                $rangeDestination[0] = ZipCode::toInteger($rangeDestination[0]);
                                $rangeDestination[1] = ZipCode::toInteger($rangeDestination[1]);

                                $rangeDestWithCountry[0] = ZipCode::toInteger($countryUpper . $rangeDestination[0]);
                                $rangeDestWithCountry[1] = ZipCode::toInteger($countryUpper . $rangeDestination[1]);
                            }

                            //valida apenas uni-direcional ORIGEM=>DESTINO
                            if ($signal == '=>') {
                                $result = ((
                                    //se codigo postal de origem entre 1000 e 1999...
                                    valueBetween($originZipCode4, $rangeOrigin[0], $rangeOrigin[1]) ||
                                    valueBetween($originWithCountry, $rangeOriginWithCountry[0], $rangeOriginWithCountry[1]) //para os casos em que há validação de país (ex: FR46001-FR45999)
                                ) && (
                                    //..e codigo postal de destino entre 3000-3999
                                    valueBetween($destZipCode4, $rangeDestination[0], $rangeDestination[1]) ||
                                    valueBetween($destWithCountry, $rangeDestWithCountry[0], $rangeDestWithCountry[1])
                                ));
                            } else {

                                //valida apenas bi-direcional ORIGEM<=>DESTINO
                                //ORIGEM => DESTINO
                                $result1 = ((
                                    //se codigo postal de origem entre 1000 e 1999...
                                    valueBetween($originZipCode4, $rangeOrigin[0], $rangeOrigin[1]) ||
                                    valueBetween($originWithCountry, $rangeOriginWithCountry[0], $rangeOriginWithCountry[1]) //para os casos em que há validação de país (ex: FR46001-FR45999)
                                ) && (
                                    //..e codigo postal de destino entre 3000-3999
                                    valueBetween($destZipCode4, $rangeDestination[0], $rangeDestination[1]) ||
                                    valueBetween($destWithCountry, $rangeDestWithCountry[0], $rangeDestWithCountry[1])
                                ));

                                //DESTINO => ORIGEM
                                $result2 = ((
                                    valueBetween($originZipCode4, $rangeDestination[0], $rangeDestination[1]) ||
                                    valueBetween($originWithCountry, $rangeDestWithCountry[0], $rangeDestWithCountry[1]) //para os casos em que há validação de país (ex: FR46001-FR45999)
                                ) && (
                                    //..e codigo postal de destino entre 3000-3999
                                    valueBetween($destZipCode4, $rangeOrigin[0], $rangeOrigin[1]) ||
                                    valueBetween($destWithCountry, $rangeOriginWithCountry[0], $rangeOriginWithCountry[1])
                                ));

                                $result = $result1 || $result2;

                                //dd($originZipCode4.' - '.$destZipCode4);
                                //dd('A->B:'. ($result1 ? 1 : 0). ' B->A='.($result2 ? 1 : 0));
                            }

                            return $result;

                            //}
                        });

                        if ($zoneFinded) {
                            $billingZone = strtolower($zoneCode);
                            //dd($billingZone);
                            break;
                        }
                    }
                }

                //2. verifica se o código postal indicado existe em alguma zona de faturação
                if ($zonesByZipCode) {

                    //prepara os codigos de barras
                    $countryUpper   = strtoupper($country); //para as ranges FR40001-FR42000,DE54000-DE80000,...
                    $zipCodeParts   = explode('-', $fullZipCode);
                    $zipCode4digits = trim(@$zipCodeParts[0]);

                    //caso o país seja FR (porque tem zonas repartidas), verifica se o codigo postal corresponde a alguma zona
                    foreach ($zonesByZipCode as $zoneCode => $zoneParts) {

                        $zoneFinded = array_where($zoneParts, function ($value) use ($fullZipCode, $zipCode4digits, $country, $countryUpper) {

                            //valida range de codigos postais, separados por espaço #####-#####
                            //ranges são validas para paises fora de PT
                            if ($country != 'pt' && str_contains($value, '-')) { //se o pais é diferente de PT e o codigo postal tem traços, considera ser uma range de codigos postais
                                $range = explode('-', $value);
                                return ($fullZipCode >= @$range[0] && $fullZipCode <= @$range[1]) //codigos postais normais 2000-3000, 5000-9000
                                    || ($countryUpper . $fullZipCode >= @$range[0] && $countryUpper . $fullZipCode <= @$range[1]); //para as ranges FR40001-FR42000,DE54000-DE80000,...
                            } else {

                                if ($value == $fullZipCode) {
                                    return $value == $fullZipCode;
                                }

                                return $value === $zipCode4digits;
                            }
                        });

                        if ($zoneFinded) {
                            $billingZone = strtolower($zoneCode);
                            break;
                        }
                    }
                }
            }

            //dd($billingZone);
            unset($zonesByZipCode);

            //3. se não encontrou a zona pelo codigo postal, tenta pelo país
            if (!$billingZone && $zonesByCountry) {
                //verifica se o país indicado existe em alguma zona de faturação
                foreach ($zonesByCountry as $zoneCode => $zoneParts) {
                    $zoneFinded = array_where($zoneParts, function ($value) use ($country) {
                        return $value === $country;
                    });


                    if ($zoneFinded) {
                        $billingZone = $zoneCode;
                        //comentado em 15/11/2022 porque na baltrans estava a retornar o país PT em vez de retornar o codigo da zona.
                        //$billingZone = strtolower(@$zoneFinded[0]);
                        break;
                    }
                }
            }

            unset($zonesByCountry);
        }

        return $billingZone;
    }

    /**
     * Obtem o coeficiente volumétrico para um envio
     *
     * @param $shipment
     * @param $service
     * @param $customer
     * @param $zone
     * @return float[]
     */
    public static function getVolumetricCoeficient($shipment)
    {

        $service  = $shipment->service;
        $customer = $shipment->customer;
        $zone     = $shipment->zone;

        //Obtem os fatores de cubicagem por defeito para o serviço, fornecedor e zona definidos
        $volumetricFactor = ServiceVolumetricFactor::where('service_id', $service->id)
            ->where('provider_id', @$shipment->provider_id)
            ->where('zone', $zone)
            ->first();

        $volumetricMinM3       = (float) @$volumetricFactor->volume_min;
        $coefficientM3         = (float) @$volumetricFactor->factor;
        $costCoeficientM3 = (float) @$volumetricFactor->factor_provider;

        //verifica se o cliente tem um valor de cubicagem personalizado e subscreve o valor default se for o caso
        $customerVolumetryMin = @$customer->custom_volumetries[$service->id]['dim_min'][$zone];
        if ($customerVolumetryMin) {
            $volumetricMinM3 = (float) $customerVolumetryMin;
        }

        $customerVolumetry = @$customer->custom_volumetries[$service->id]['coeficient'][$zone];
        if ($customerVolumetry) {
            $coefficientM3 = (float) $customerVolumetry;
        }

        return [
            'volume_min'  => (float) $volumetricMinM3,
            'coeficient'  => (float) $coefficientM3,
            'provider_coeficient' => (float) $costCoeficientM3,
        ];
    }

    /**
     * Retorna todos os pesos do envio
     * @param $shipment
     * @return array
     */
    public static function getShipmentDimensions($shipment)
    {
        $fatorM3 =
            $dimsMaxWeight =
            $dimsMaxSize =
            $dimsMaxWidth =
            $dimsMaxLength =
            $dimsMaxHeight =
            $hasSKU = false;

        if ($shipment->pack_dimensions) {

            foreach ($shipment->pack_dimensions as $packDimension) {

                $width  = (float) @$packDimension['width'];
                $height = (float) @$packDimension['height'];
                $length = (float) @$packDimension['length'];
                $qty    = (float) @$packDimension['qty'];

                $dimsSize = $width + $height + (float)$length;
                $dimsMaxWeight = valueMax(@$packDimension['weight'], $dimsMaxWeight);
                $dimsMaxWidth  = valueMax($width, $dimsMaxWidth);
                $dimsMaxLength = valueMax($length, $dimsMaxLength);
                $dimsMaxHeight = valueMax($height, $dimsMaxHeight);
                $dimsMaxSize   = valueMax($dimsSize, $dimsMaxSize);
                $fatorM3 += ($qty * (($width * $height * $length) / 1000000));

                if (!empty(@$packDimension['sku'])) {
                    $hasSKU = true;
                }
            }
        }

        return [
            'fator_m3'   => $fatorM3 ?: $shipment->fator_m3,
            'max_weight' => $dimsMaxWeight,
            'max_length' => $dimsMaxLength,
            'max_width'  => $dimsMaxWidth,
            'max_height' => $dimsMaxHeight,
            'max_size'   => $dimsMaxSize, //pacote maior
            'max_side'   => valueMax($dimsMaxWidth, valueMax($dimsMaxLength, $dimsMaxHeight)),
            'has_sku'    => $hasSKU
        ];
    }

    /**
     * Retorna todos os pesos do envio
     * @param $shipment
     * @param $service
     * @return array
     */
    public static function getShipmentWeights($shipment)
    {

        $serviceUnity = @$shipment->service->unity;

        //obtem o coeficiente volumétrico
        $volumetricCoeficients = Self::getVolumetricCoeficient($shipment);
        $volumetricMinM3       = $volumetricCoeficients['volume_min'];
        $coefficientM3         = $volumetricCoeficients['coeficient'];
        $costCoeficientM3 = $volumetricCoeficients['provider_coeficient'];

        //calcula o peso volumétrico
        $volumetricWeight         = $shipment->fator_m3 * $coefficientM3;
        $volumetricWeightProvider = $shipment->fator_m3 * $costCoeficientM3;

        //grava variaveis de peso
        $originalWeight    = $shipment->weight;
        $taxableWeight     = $originalWeight > $volumetricWeight ? $originalWeight : $volumetricWeight;
        $costTaxableWeight = $originalWeight > $volumetricWeightProvider ? $originalWeight : $volumetricWeightProvider;


        if ($serviceUnity == 'volume') {
            $taxableWeight     = $shipment->volumes;
            $costTaxableWeight = $shipment->volumes;
        } elseif ($serviceUnity == 'm3') {
            $originalWeight     = 0;
            $taxableWeight      = $shipment->volume_m3;
            $costTaxableWeight  = $shipment->volume_m3;
        } elseif ($serviceUnity == 'km') {
            $taxableWeight      = $shipment->kms;
            $costTaxableWeight  = $shipment->kms;
        } elseif ($serviceUnity == 'hours') {
            $taxableWeight      = $shipment->hours;
            $costTaxableWeight  = $shipment->hours;
        } elseif ($serviceUnity == 'ldm') {
            $taxableWeight      = $shipment->ldm;
            $costTaxableWeight  = $shipment->ldm;
        } elseif ($serviceUnity == 'advalor') {
            $taxableWeight = $shipment->goods_price;
        } elseif ($serviceUnity == 'services') {

            $startDate = date('Y-m') . '-01';
            $endDate   = date('Y-m') . '-31';

            $totalMonthShipments = Shipment::where('customer_id', $shipment->customer_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();

            $taxableWeight = $totalMonthShipments;
        }

        //Peso escalonado.
        //Divisão do peso pelos volumes.
        $averageWeight = false;
        if (@$shipment->customer->average_weight && $shipment->volumes > 0) {
            $averageWeight = true;
            $taxableWeight = $taxableWeight / $shipment->volumes;
        }

        return [
            'billing' => [
                'original'          => $originalWeight,
                'taxable'           => $taxableWeight ? $taxableWeight : 0.00,
                'fator_m3'          => $shipment->fator_m3,
                'coefficient_m3'    => $coefficientM3,
                'volumetric_min_m3' => $volumetricMinM3,
                'volumetric_weight' => $volumetricWeight,
                'is_avg_weight'     => $averageWeight
            ],
            'cost' => [
                'original'          => $originalWeight,
                'taxable'           => $costTaxableWeight,
                'fator_m3'          => $shipment->fator_m3,
                'coefficient_m3'    => $costCoeficientM3,
                'volumetric_min_m3' => $volumetricMinM3,
                'volumetric_weight' => $volumetricWeightProvider
            ]
        ];
    }

    /**
     * Retorna os preços para o cliente indicado
     * @param $service
     * @param $shipment
     * @param $zone
     * @param $originZone
     * @return mixed
     */
    public static function getPricesTable($shipment)
    {

        $customerId = $shipment->customer_id;
        $service    = $shipment->service;
        $zone       = $shipment->zone;
        $originZone = $shipment->pickup_zone;

        if (empty($customerId)) {
            return [
                'is_pvp' => true,
                'prices' => self::getDefaultPricesTable($shipment)
            ];
        }

        $servicePrices = Customer::with(['services' => function ($q) use ($service, $zone, $originZone) {
            $q->where('service_id', @$service->id);
            $q->where('zone', $zone);
            // $q->where('origin_zone', $originZone);
            $q->where(function($q) use($originZone) {
                    $q->whereNull('origin_zone');
                    if($originZone) {
                        $q->orWhere('origin_zone', $originZone);
                    }
            });
            $q->orderBy('origin_zone', 'desc');
        }])
            ->remember(5)
            ->find($customerId);

        //se o envio tem portes no destino e nas definições gerais está definido o uso de uma tabela específica para portes no destino.
        if (@$shipment->payment_at_recipient && Setting::get('cod_prices_table')) {
            @$servicePrices->price_table_id = Setting::get('cod_prices_table');
        }

        //a tabela de preços por defeito tem definida uma tabela persobalizada para este grupo de serviços
        if (@$servicePrices && !@$servicePrices->price_table_id && @$servicePrices->prices_tables[@$service->group]) { //obtem a tabela de preços definida para o grupo de serviços
            $servicePrices->price_table_id = $servicePrices->prices_tables[@$service->group];
        }

        //obtem os preços a partir da tabela geral
        if (@$servicePrices->price_table_id) {
            $servicePrices = PriceTable::with(['services' => function ($q) use ($service, $zone, $originZone) {
                $q->where('service_id', @$service->id);
                $q->where('zone', $zone);
                $q->where('origin_zone', $originZone);
            }])
                ->remember(5)
                ->find($servicePrices->price_table_id);
        }

        //não existem preços tabela de preços no cliente, substitui pela tabela de preços PVP
        if (($servicePrices && $servicePrices->services->isEmpty()) || (!$servicePrices)) {
            return [
                'is_pvp' => true,
                'prices' => self::getDefaultPricesTable($service, $shipment, $zone, $originZone)
            ];
        }

        return [
            'is_pvp' => false,
            'prices' => @$servicePrices->services
        ];
    }

    /**
     * Retorna a tabela de preços do cliente final para o serviço indicado
     * @param $service
     * @param $zone
     * @param $originZone
     * @param $cod
     * @return mixed
     */
    public static function getDefaultPricesTable($shipment)
    {

        $customerId = $shipment->customer_id;
        $service    = $shipment->service;
        $zone       = $shipment->zone;
        $originZone = $shipment->pickup_zone;

        //obtem a tabela de preços do cliente final (tabela pvp)
        $defaultServicePrices = Customer::with(['services' => function ($q) use ($service, $zone, $originZone) {
            $q->where('service_id', @$service->id);
            $q->where('zone', $zone);
            $q->where('origin_zone', $originZone);
        }])
            ->where('final_consumer', true)
            ->where('agency_id', $shipment->agency_id)
            ->remember(5)
            ->first();

        //se o envio tem portes no destino e nas definições gerais está definido o uso de uma tabela específica para portes no destino.
        if ($shipment->payment_at_recipient && Setting::get('cod_prices_table')) {
            @$defaultServicePrices->price_table_id = Setting::get('cod_prices_table');
        }

        //a tabela de preços por defeito tem definida uma tabela persobalizada para este grupo de serviços
        if ($defaultServicePrices && !$defaultServicePrices->price_table_id && @$defaultServicePrices->prices_tables[@$service->group]) { //obtem a tabela de preços definida para o grupo de serviços
            $defaultServicePrices->price_table_id  = $defaultServicePrices->prices_tables[@$service->group];
        }

        //obtem preço PVP caso a tabela do CFINAL use uma das tabelas gerais
        if (@$defaultServicePrices->price_table_id) {

            $defaultServicePrices = PriceTable::with(['services' => function ($q) use ($service, $zone, $originZone) {
                $q->where('service_id', @$service->id);
                $q->where('zone', $zone);
            }])
                ->remember(5)
                ->find($defaultServicePrices->price_table_id);
        }

        return @$defaultServicePrices->services;
    }

    /**
     * Obtem a tabela de preços do fornecedor
     * @param $shipment
     * @param $service
     * @param $costZone
     * @return bool|Carbon|float|\Illuminate\Support\Collection|int|mixed|string|null
     */
    public static function getProviderPricesTable($shipment)
    {

        $service  = $shipment->service;
        $costZone = $shipment->cost_zone;

        $providerPrices = Provider::with(['services' => function ($q) use ($service, $shipment, $costZone) {
            $q->where('service_id', $service->id);
            $q->where('zone', $costZone);
            $q->where(function ($q) use ($shipment) {
                $q->where('agency_id', $shipment->agency_id);
            });
        }])
            ->find($shipment->provider_id);

        if ($providerPrices) {

            //procura se existe o fornecedor tem tabela de custo definida para este cliente e subscreve caso exista.
            $serviceCosts = $providerPrices->services->filter(function ($item) use ($shipment) {
                return $item->pivot->customer_id == $shipment->customer_id && $item->pivot->type == 'expedition';
            });

            //se não existe tabela de custos por defeito para este cliente, usa os preços gerais do fornecedor
            if ($serviceCosts->isEmpty()) {
                $serviceCosts = $providerPrices->services->filter(function ($item) {
                    return empty($item->pivot->customer_id) && $item->pivot->type == 'expedition';
                });
            }
        }

        return [
            'percent_gain'  => @$providerPrices->percent_total_price_gain,
            'prices'        => @$serviceCosts,
            'fuel_tax'      => @$providerPrices->fuel_tax
        ];
    }

    /**
     * Calcula o preço de tabela de um envio
     * A função aplica-se ao cálculo tanto de tabela de venda como da tabela de fornecedor
     * @param $shipment
     * @param string $priceType [billing|expedition]
     * @return array
     */
    public static function calcPriceFromTable($shipment, $priceType = 'billing')
    {

        $outOfRange    = true; //caso o preço esteja fora dos valores da tabela
        $priceRow      = '0-0';
        $shippingPrice = 0.00;
        $extraPrice    = 0.00;
        $extraKg       = 0.00;
        $defaultPrice  = false;
        $providerFuel  = 0;
        $costByPercentGain = false;

        if ($priceType == 'billing') {
            //preços de venda
            $entityExists  = $shipment->customer_id;
            $taxableWeight = $shipment->taxable_weight;
            if ($shipment->pack_weight && @$shipment->service->unity == 'weight') { //se definido o pack_weight é para calcular a tabela pelo valor da mercadoria e não pelo valor total
                $packVolumetricWeight = $shipment->coefficient_m3 * $shipment->pack_fator_m3;
                $taxableWeight = $shipment->pack_weight > $packVolumetricWeight ? $shipment->pack_weight : $packVolumetricWeight;
            } elseif ($shipment->pack_qty && @$shipment->service->unity == 'volume') { //se definido o pack_qty é para calcular a tabela pelo valor da mercadoria e não pelo valor total
                $taxableWeight = $shipment->pack_qty;
            }
        } else {
            //preços de custo (expedição)
            $entityExists  = $shipment->customer_id;
            $taxableWeight = $shipment->provider_taxable_weight;
        }

        /**
         * @author Daniel Almeida
         * --
         * Ex: Prices rows are from 0.00 -> 1.00, 1.01 -> 2.00
         * so if the $taxableWeight is 1.001 the value isn't
         * inside a valid range even tho it is.
         * 
         * To fix that we round the $taxableWeight to 2 decimal digits.
         */
        $taxableWeight = round($taxableWeight, 2);


        if ($entityExists) {

            if ($priceType == 'billing') {
                //obtem a tabela de preços de venda a usar
                $servicePrices = self::getPricesTable($shipment);
                $defaultPrice  = $servicePrices['is_pvp'];
                $servicePrices = $servicePrices['prices'];
            } else {
                //obtem a tabela de preços de custo a usar
                $servicePrices = self::getProviderPricesTable($shipment);
                $costByPercentGain = $servicePrices['percent_gain']; //preço de custo é por percentagem sobre o preço de venda
                $providerFuel      = $servicePrices['fuel_tax'];
                $servicePrices     = $servicePrices['prices'];
            }


            //preço de custo é uma percentagem do preço de venda
            if ($costByPercentGain) {
                $shippingPrice = $shipment->shipping_price * ($costByPercentGain / 100);
            } else {

                if (!empty($servicePrices) && !$servicePrices->isEmpty()) {

                    $servicePrice = $servicePrices->filter(function ($q) use ($taxableWeight) {
                        return $q->pivot->min <= $taxableWeight && $q->pivot->max >= $taxableWeight;
                    })->first();


                    if ($servicePrice) {
                        $outOfRange = false;

                        //preço de custo
                        $shippingPrice = @$servicePrice->pivot->price;

                        //identifica qual o escalao de preços
                        $priceRow = $servicePrice->pivot->min . '-' . $servicePrice->pivot->max;
                    }



                    //se não existe preço para os kg indicados
                    //ou se o preço é 0.00
                    //ou se o preço encontrado é a linha de kg adicional
                    //vai tentar obter a linha anterior de preços
                    if (empty(@$servicePrice->pivot) || $servicePrice->pivot->price == 0.00 || @$servicePrice->pivot->is_adicional || ($servicePrice->pivot->price != 0.00 && $servicePrice->pivot->max >= 99999)) {

                   
                        if (Setting::get('shipment_nocalc_kg_adic_zero') && empty(@$servicePrice->pivot)) {
                            //se tem ativa a opção de não calcular preços se a taxa adicional está a zeros
                            //acontece caso o peso fique no escalão de kg adicional e nesses casos não se pretende
                            //que seja calculado preço
                            $shippingPrice = 0.00;
                        } else {
                            //memoriza temporáriamente a linha original
                            $tmpServicePrice = $servicePrice;

                            //vai procurar a ultima linha de preços onde há preço, desde que não seja a linha de kg adicional
                            $servicePrice = $servicePrices->reverse()->filter(function ($q) use ($servicePrice) {
                                return $q->pivot->max <= @$servicePrice->pivot->min && $q->pivot->price != 0.00 && !$q->pivot->is_adicional;
                            })->first();


                            //Se a linha de preços atual (gravada na variavel temporária), está a 0.00
                            //vamos procurar se a próxima linha de preços é linha a linha de kg adicional.
                            //Se a proxima linha for a linha de kg adicional, então como a linha atual 
                            //é uma linha vazia, vamos ter de calcular o peso adicional.
                            if(@$tmpServicePrice->pivot->price == 0.00) {
                                $nextServicePriceRow = $servicePrices->reverse()->filter(function ($q) use ($servicePrice) {
                                    return $q->pivot->max > @$servicePrice->pivot->min && $q->pivot->price != 0.00 && $q->pivot->is_adicional;
                                })->first();

                                $tmpServicePrice = $nextServicePriceRow;
                            }
                            
                            if ($servicePrice) {
                                //se existe linha anterior com preço

                                //Como encontrou uma linha anterior, substituiu o preço base
                                $shippingPrice = @$servicePrice->pivot->price;

                                //identifica qual o escalao de preços
                                $priceRow = $servicePrice->pivot->min . '-' . $servicePrice->pivot->max;

                                //atualiza o escalão de kg minimo para o máximo da última linha encontrada com preços
                                //Desta forma faz com que sejam ignoradas todas as linhas que estão com o preço vazio.
                                $newMinValue = (float) $servicePrice->pivot->max;
                            } else {
                                //Se não existe uma linha anterior com preços

                                if (!$servicePrice && !$tmpServicePrice) {
                                    //a tabela não tem qualquer preço ou linha para cobrir os kg inseridos.
                                    $outOfRange    = true;
                                    $shippingPrice = 0.00;
                                } else {
                                    //repoe de novo a linha de preços original
                                    $servicePrice = $tmpServicePrice; //reestabelece de novo a variavel original;
                                    $newMinValue = (float) @$tmpServicePrice->pivot->min;

                                    //identifica qual o escalao de preços
                                    $priceRow = $servicePrice->pivot->min . '-' . $servicePrice->pivot->max;
                                }
                            }

                            //se a linha original era uma linha de KG adicionais para processar os KG adicionais
                            if (@$tmpServicePrice->pivot->is_adicional || @$tmpServicePrice->pivot->max >= 9999) { //encontrou uma linha anterior com preços.

                                //atualiza o escalão de kg minimo e máximo para ignorar as linhas
                                //que estão com o preço vazio.
                                $servicePrice->pivot->min = @$newMinValue; //peso minimo = máximo da ultima linha com preços +0.01
                                $servicePrice->pivot->max = @$tmpServicePrice->pivot->max; //peso maximo = peso máximo da linha inicial calculada

                                //obtem os kg adicionais
                                $extraKg = $taxableWeight - $servicePrice->pivot->min;

                                //determina quantos kg adicionais deve calcular (por cada kg, ou por cada tonelada por exemplo)
                                $extraKgBaseUnity = @$tmpServicePrice->pivot->adicional_unity ? $tmpServicePrice->pivot->adicional_unity : 1;
                                if ($extraKgBaseUnity > 1) {
                                    $extraKg = ceil($extraKg / $extraKgBaseUnity);
                                }

                                //obtem o preço por kg extra
                                $pricePerKgExtra = @$tmpServicePrice->pivot->price;

                                //calcula o preço dos kg adicionais
                                $extraPrice = ceil($extraKg) * $pricePerKgExtra;

                                if ($servicePrice->pivot->min == 0.00 && $servicePrice->pivot->is_adicional) {
                                    //se o valor minimo de peso é 0, significa que a tabela só tem kg adicional ou a 1ª linha é 0.00
                                    //se a vaiavel "is_adicional" está ativa, signfica que a 1ª linha é kg adicional
                                    //isto evita somar o valor da linha inicial e considera só o valor os kg adicionais
                                    $shippingPrice = $extraPrice;
                                } else {
                                    $shippingPrice += $extraPrice;
                                }

                                //identifica qual o escalao de preços
                                $priceRow = $servicePrice->pivot->min . '-' . $servicePrice->pivot->max;
                            }
                        }
                    }
                }
            }
        }


        //Se o preço calculado é da tabela por defeito e se está definido
        //para não usar a tabela PVP, então coloca os valores a zero.
        if ($defaultPrice && !Setting::get('shipment_use_final_consumer_table')) {
            $defaultPrice = false;
            $shippingPrice = $extraPrice = 0;
        }

        //Processa casos especiais de preço de venda
        if ($priceType == 'billing') {

            //servico escalonado
            //(peso/volumes) * volumes
            if (@$shipment->is_avg_weight && $shipment->volumes > 0) {
                $shippingPrice = $shippingPrice * $shipment->volumes;
            }

            //Preço por volume: preço total x volumes
            if (@$shipment->service->price_per_volume) {
                if($shipment->pack_qty) {
                    $shippingPrice = $shippingPrice * $shipment->pack_qty;
                } else {
                    $shippingPrice = $shippingPrice * $shipment->volumes;
                }
            }

            //Preço xM3
            if (@$shipment->service->multiply_price) {
                $shippingPrice = $shippingPrice * $taxableWeight;
            }

            //Preço por advalor
            //o peso taxavel é o preço total dos bens transportados.
            if (@$shipment->service->unity == 'advalor') {
                $shippingPrice = (float) $taxableWeight * ((float) $shippingPrice / 100);
            }
        }

        //liberta memoria
        unset($servicePrices, $tmpServicePrice);

        return [
            'out_range'   => $outOfRange,
            'price_row'   => $priceRow,
            'price'       => $shippingPrice,
            'extra_price' => $extraPrice,
            'extra_kg'    => $extraKg,
            'is_pvp'      => $defaultPrice,
            'fuel_tax'    => $providerFuel,
            'percentual_cost' => $costByPercentGain
        ];
    }

    /**
     * Calcula o preço de venda do cliente
     * @param $shipment
     * @return string[]
     */
    public static function calcBillingPrices($shipment)
    {
        return self::calcPriceFromTable($shipment, 'billing');
    }

    /**
     * Calcula o preço de custo do fornecedor
     *
     * @param $shipment
     * @return array
     */
    public static function calcCostPrices($shipment)
    {
        return self::calcPriceFromTable($shipment, 'expedition');
    }

    /**
     * Determina quais as taxas adicionais validas/disponíveis para o envio
     *
     * @param $manualExpenses array (conforme tabela shipments_assigned_expenses da base de dados), com as taxas manuais a somar na resposta.
     * @return array
     */
    public function getAvailableExpenses($manualExpenses, $shipment)
    {
        $shipment = $this;

        // Obtém as taxas manuais previamente gravadas
        if (!$manualExpenses && $shipment->id) {
            $manualExpenses = ShipmentExpense::where('shipment_id', $shipment->id)
                ->where('auto', 0)
                ->get()
                ->toArray() ?? [];

        }

        $manualExpensesIds = [];
        foreach (($manualExpenses ?? []) as $manualExpense) {
            $manualExpensesIds[] = $manualExpense['expense_id'];
        }

        //1. obtem todas as despesas de ativação automática
        $expenses = ShippingExpense::where('trigger_fields', '<>', '')
            ->whereNotIn('id', $manualExpensesIds)
            ->get();

        //2. Percorre cada taxa e verifica se as regras se aplicam
        $validExpenses = [
            'items'     => [],
            'count'     => 0,
            'subtotal'  => 0,
            'vat'       => 0,
            'total'     => 0,
            'tags'      => []
        ];

        foreach ($expenses as $expense) {

            /* echo '=========================<br/>TAXA '.$expense->id.' - '.$expense->internal_name.'<br/>'; */

            $fieldsNames = $expense->trigger_fields;
            $operators   = $expense->trigger_operators;
            $fieldValues = $expense->trigger_values;
            $fieldJoins  = $expense->trigger_joins;

            $isValidRule    = false;
            $isValidExpense = true;
            $lastJoin       = null;

            foreach ($fieldsNames as $key => $fieldName) {

                $field = $shipment->getAvailableExpensesFieldValue($fieldName); //obtem a partir do envio, o valor do campo que queremos comparar
                $op    = @$operators[$key]; //obtem o operador de comparação
                $val   = @$fieldValues[$key]; //obtem o valor de referencia a comparar
                $join  = @$fieldJoins[$key];

                switch ($op) {
                    case '=':
                        $isValidRule = $field == $val ? true : false;
                        break;
                    case '<>':
                        $isValidRule = $field != $val ? true : false;
                        break;
                    case '>':
                        $isValidRule = $field > $val  ? true : false;
                        break;
                    case '<':
                        $isValidRule = $field < $val  ? true : false;
                        break;
                    case '>=':
                        $isValidRule = $field >= $val ? true : false;
                        break;
                    case '<=':
                        $isValidRule = $field <= $val ? true : false;
                        break;
                    case 'c':
                        if (is_array($field)) {
                            $isValidRule = in_array($val, $field);
                        } else {
                            $isValidRule = str_contains($field, $val);
                        }
                        break;
                    case 'nc':
                        if (is_array($field)) {
                            $isValidRule = !in_array($val, $field);
                        } else {
                            $isValidRule = !str_contains($field, $val);
                        }
                        break;
                    case 'e':
                        $isValidRule = empty($field);
                        break;
                    case 'ne':
                        $isValidRule = !empty($field);
                        break;
                    case 'sw':
                        $isValidRule = starts_with($field, $val);
                        break;
                    case 'ew':
                        $isValidRule = ends_with($field, $val);
                        break;
                    case 'nsw':
                        $isValidRule = !starts_with($field, $val);
                        break;
                    case 'new':
                        $isValidRule = !ends_with($field, $val);
                        break;
                }


                if ($lastJoin == 'or') {
                    $isValidExpense = $isValidExpense || $isValidRule;
                } elseif ($lastJoin == 'and') {
                    $isValidExpense = $isValidExpense && $isValidRule;
                } else {
                    $isValidExpense = $isValidRule;
                }

                $lastJoin = $join;


                /*if($expense->id == 629) {
                    if($isValidRule) {
                        echo '<div><span style="color: green">'.$fieldName.' - '.$field.' '.$op.' '.$val.'</span> ['.$join.'] - '.($isValidExpense ? '<span style="color: green">valida</span>' : '<span style="color: red">invalida</span>').'</div>';
                    } else {
                        echo '<div><span style="color: red">'.$fieldName.' - '.$field.' '.$op.' '.$val.'</span> ['.$join.'] - '.($isValidExpense ? '<span style="color: green">valida</span>' : '<span style="color: red">invalida</span>').'</div>';
                    }
                }*/
            }

            if ($isValidExpense) {
                //se a taxa é válida, calcula o preço
                $expenseDetails  = $shipment->calcExpensePrice($expense);

                if ($expenseDetails) { //só adiciona a taxa se esta se retornar preços
                    $expenseDetails['fillable']['auto'] = true;
                    $validExpenses['items'][]  = $expenseDetails['fillable'];
                    $validExpenses['count']    = @$validExpenses['count'] + 1;
                    $validExpenses['subtotal'] = @$validExpenses['subtotal'] + $expenseDetails['fillable']['subtotal'];
                    $validExpenses['vat']      = @$validExpenses['vat'] + $expenseDetails['fillable']['vat'];
                    $validExpenses['total']    = @$validExpenses['total'] + $expenseDetails['fillable']['total'];

                    $validExpenses['cost_subtotal'] = @$validExpenses['cost_subtotal'] + $expenseDetails['fillable']['cost_subtotal'];
                    $validExpenses['cost_vat']      = @$validExpenses['cost_vat'] + $expenseDetails['fillable']['cost_vat'];
                    $validExpenses['cost_total']    = @$validExpenses['cost_total'] + $expenseDetails['fillable']['cost_total'];

                    if (@$expenseDetails['expense']['type'] != 'other') {
                        $validExpenses['tags'][] = $expenseDetails['expense']['type'];
                    }
                }
            }

            /* if($isValidExpense) {
                 echo 'TAXA VÁLIDA ====<br/>';
             } else {
                 echo 'TAXA INVÁLIDA ====<br/>';
             }*/
        }

        if ($manualExpenses) {

            foreach ($manualExpenses as $manualExpense) {

                if (!is_null(@$manualExpense['subtotal'])) { //so adiciona a taxa se tiver o subtotal
                    if (empty(@$manualExpense['code'])) {
                        $expense = ShippingExpense::filterSource()->find($manualExpense['expense_id']);
                        $manualExpense['code'] = @$expense->code;
                        $manualExpense['name'] = @$expense->name;
                        //$manualExpense['unity'] = @$expense->unity;
                        $manualExpense['type'] = @$expense->type;
                        $manualExpense['billing_item_id'] = @$expense->billing_item_id;
                    }

                    $manualExpense['customer_id'] = @$shipment->customer_id;
                    $manualExpense['provider_id'] = @$shipment->provider_id;
                    $manualExpense['type']        = @$manualExpense['type'] ? $manualExpense['type'] : 'other';
                    $manualExpense['unity']       = @$manualExpense['unity'] ? $manualExpense['unity'] : 'euro';
                    $manualExpense['vat_rate_id'] = @$manualExpense['vat_rate_id'] ? $manualExpense['vat_rate_id'] : '';

                    if (empty($manualExpense['vat_rate_id'])) {
                        $manualExpense['vat_rate'] = $shipment->vat_rate;
                    } else {
                        $vatRate = VatRate::filterSource()->find($manualExpense['vat_rate_id']);
                        $manualExpense['vat_rate'] = @$vatRate->value;
                    }

                    $manualExpense['subtotal'] = @$manualExpense['subtotal'] ? $manualExpense['subtotal'] : 0;
                    $manualExpense['total'] = @$manualExpense['subtotal'] * (1 + ((float) $manualExpense['vat_rate'] / 100));
                    $manualExpense['vat']   = @$manualExpense['total'] - @$manualExpense['subtotal'];

                    $manualExpense['cost_total'] = @$manualExpense['cost_subtotal'] * (1 + ((float) $manualExpense['vat_rate'] / 100));
                    $manualExpense['cost_vat']   = @$manualExpense['cost_total'] - @$manualExpense['cost_subtotal'];
                    $manualExpense['auto']       = false;

                    $validExpenses['items'][]   = $manualExpense;
                    $validExpenses['count']     = @$validExpenses['count'] + 1;
                    $validExpenses['subtotal']  = @$validExpenses['subtotal'] + @$manualExpense['subtotal'];
                    $validExpenses['vat']       = @$validExpenses['vat'] + @$manualExpense['vat'];
                    $validExpenses['total']     = @$validExpenses['total'] + @$manualExpense['total'];

                    $manualExpense['cost_subtotal'] = @$manualExpense['cost_subtotal'] ? $manualExpense['cost_subtotal'] : 0;
                    $validExpenses['cost_subtotal'] = @$validExpenses['cost_subtotal'] + $manualExpense['cost_subtotal'];
                    $validExpenses['cost_vat']      = @$validExpenses['cost_vat'] + $manualExpense['cost_vat'];
                    $validExpenses['cost_total']    = @$validExpenses['cost_total'] + $manualExpense['cost_total'];

                    if (@$manualExpense['type'] != 'other') {
                        $validExpenses['tags'][] = $manualExpense['type'];
                    }
                }
            }
        }

        return $validExpenses;
    }

    /**
     * Calcula uma taxa adicional
     *
     * @param $expense
     * @return null || array
     */
    public function calcExpensePrice($expense)
    {

        $expense = is_integer($expense) ? ShippingExpense::find($expense) : $expense;

        if ($expense) {
            return $expense->calcExpensePrice($this, $expense->qty ? $expense->qty : 1);
        }

        return null;
    }

    /**
     * Get all available expenses for shipment
     * @return array
     */
    public function getAvailableExpensesFieldValue($fieldName)
    {

        if ($fieldName == 'weekday') {
            $date = new Carbon($this->date);
            return $date->dayOfWeek;
        }

        return $this->$fieldName;
    }

    /**
     * Get Fuel Tax percent
     * @return mixed
     */
    public function getFuelTaxRate()
    {
        return Setting::get('fuel_tax');
    }

    /**
     * (Calcula e insere ou atualiza) os preços de um envio
     * 
     * @return array|null
     */
    public function updatePrices($prices = null)
    {
        if (!$prices) {
            $prices = self::calcPrices($this);
        }

        if (@$prices['fillable']) {
            $this->fill($prices['fillable']);
            $this->storeExpenses($prices);
                $this->save();
            }

        return $prices;
    }

    /**
     * Set shipment expenses from calc prices
     * @param $expensesArr
     */
    public function setCalculatedExpenses($expensesArr)
    {
        if ($expensesArr) {
            foreach ($expensesArr as $expense) {
                $shipmentExpense = new ShipmentExpense();
                $shipmentExpense->fill($expense);
                $shipmentExpense->shipment_id = $this->id;
                $shipmentExpense->date        = $this->billing_date;
                $shipmentExpense->save();
            }
        }
    }

    /**
     * Adiciona taxas devolvidas pelo calculador de preços no envio
     * Só funciona se o envio já tiver id (estiver gravado)
     * @param $prices
     * @return array|bool
     */
    public function storeExpenses($pricesArr)
    {

        if ($this->id) {
            //apaga taxas automaticas
            ShipmentExpense::where('shipment_id', $this->id)
                // ->where('auto', 1) // o calculador agora também retorna as taxas manuais
                ->forceDelete();

            //adiciona novas taxas
            $expensesArr = [];
            $now = date('Y-m-d H:i:s');
            foreach ($pricesArr['expenses'] as $expense) {

                // if ($expense['auto']) {

                    $expense['shipment_id'] = $this->id;
                    $expense['date']        = $this->billing_date;
                    $expense['created_at']  = $now;
                    $expense['updated_at']  = $now;

                    unset($expense['name'], $expense['code'], $expense['customer_id'], $expense['type']);
                    $expensesArr[] = $expense;
                // }
            }

            ShipmentExpense::insert($expensesArr);

            return $expensesArr;
        }

        return false;
    }

    /**
     * Obtem a data de recolha do envio
     * @return array|string
     */
    public function getPickupDate($returnDetails = false)
    {

        $outZone    = $this->out_zone; //se o envio permite kms a seu encargo, adiciona 1 dia
        $holidays   = CalendarEvent::getHolidays();
        $pickupDate = new Date($this->date);
        $service    = $this->service;
        $errors     = [];

        $startHour = date('H:i');
        $endHour   = $startHour;
        if ($this->start_hour) {
            $startHour = $this->start_hour;
        } else if ($this->start_hour_pickup) {
            $startHour = $this->start_hour_pickup;
            $endHour   = !empty($this->end_hour_pickup) ? $this->end_hour_pickup : $startHour;
        } else if ($this->end_hour_pickup) {
            $endHour   = $this->end_hour_pickup;
            $startHour = !empty($this->start_hour_pickup) ? $this->start_hour_pickup : $endHour;
        }

        $pickupLimitHour = $this->getPickupLimitHour();
        if ($pickupDate->isToday()) {
            if (date('H:i') >= $pickupLimitHour || $outZone) {
                $pickupDate = $this->getNextPickupDate($pickupDate, @$service->pickup_weekdays, $holidays);

                $errors[] = [
                    'parent' => '#alert-date',
                    'fields' => ['.shipment-date'],
                    'feedback' => 'Não é possível fazer a recolha hoje. Por favor selecione outro dia.'
                ];
            }
        }

        // Verify if the start hour is valid
        if (!valueBetween($startHour, $service->min_hour, $pickupLimitHour)) {
            $startHour = $service->min_hour;
            $errors[] = [
                'parent' => '#alert-date',
                'fields' => ['.shipment-start-hour'],
                'feedback' => 'Hora mínima fora dos limites do serviço.'
            ];
        }

        // Verify if the end hour is valid
        if (!valueBetween($endHour, $service->min_hour, $pickupLimitHour)) {
            $endHour = $pickupLimitHour;
            $errors[] = [
                'parent' => '#alert-date',
                'fields' => ['.shipment-end-hour'],
                'feedback' => 'Hora máxima fora dos limites do serviço.'
            ];
        }

        if ($startHour > $endHour) {
            $errors[] = [
                'parent' => '#alert-date',
                'fields' => ['.shipment-start-hour'],
                'feedback' => 'Hora mínima não pode ser maior que a máxima.'
            ];
        } else if (@$service->pickup_hour_difference) {
            $startHourInt   = str_replace(':', '', $startHour);
            $endHourInt     = str_replace(':', '', $endHour);
            $hourDifference = str_replace(':', '', $service->pickup_hour_difference);

            if (($endHourInt - $startHourInt) < $hourDifference) {
                $errors[] = [
                    'parent' => '#alert-date',
                    'fields' => ['.shipment-start-hour', '.shipment-end-hour'],
                    'feedback' => 'Diferença horária mínima de '. @$service->pickup_hour_difference .' requerida.'
                ];
            }
        }

        $formatedDate = $pickupDate->format('Y-m-d');

        if (!Setting::get('customers_shipment_hours')) {
            $errors = [];
        }

        if ($returnDetails) {
            return [
                'date'     => $formatedDate,
                'hour'     => $startHour,
                'hour_min' => $service->min_hour,
                'hour_max' => $pickupLimitHour,
                'weekday'  => $pickupDate->dayOfWeek,
                'shipping_date' => $formatedDate . ' ' . $startHour . ':00',
                'errors'   => $errors
            ];
        }

        return $formatedDate . ' ' . $startHour . ':00';
    }

    /**
     * Obtem a proxima data de recolha
     *
     * @param $date
     * @param null $allowedDays
     * @param false $returnDays
     * @return mixed
     */
    public function getNextPickupDate($date, $allowedDays = null, $ignoreDates = [], $returnDays = false)
    {

        //[dom seg ter qua qui sex sab]
        //[ 0   1   2   3   4   5   6 ]

        $allowedDays = $allowedDays ? $allowedDays : [0, 1, 2, 3, 4, 5, 6];

        $dateFinded  = 0;
        $countDays   = 0;

        do {
            $date = $date->addDay();
            $countDays++;

            if (
                array_search($date->dayOfWeek, $allowedDays) !== false &&
                !in_array($date->format('Y-m-d'), $ignoreDates)
            ) {
                $dateFinded = 1;
            }
        } while (!$dateFinded);


        return $returnDays ? $countDays : $date;
    }

    /**
     * Obtem a data prevista de entrega
     * @return array|string
     */
    public function getDeliveryDate($returnDetails = false, $pickupDate = null)
    {

        $holidays   = CalendarEvent::getHolidays();
        $transit    = $this->getTransitTime();
        $service    = $this->service;
        $countDays  = 0;

        $deliveryHour = $service->delivery_hour;
        if ($this->end_hour) {
            $deliveryHour = $this->end_hour;
        }

        if (empty($deliveryHour)) {
            $deliveryHour = '19:00';
        }

        if (!$pickupDate) {
            $pickupDate = new Date($this->getPickupDate());
        } else {
            $pickupDate = new Date($pickupDate);
        }


        if ($transit < 24.00) { //menos de 24h

            $deliveryDate = $pickupDate->addHours($transit);

            if ($deliveryDate->format('H:i') > $service->delivery_hour) { //ultrapassou a data maxima de recolha
                $deliveryDate->addDay();
            }
        } else {

            $transitDays = $transit / 24; //converte horas em dias transito

            do {
                $deliveryDate = $pickupDate->addDay(); //percorre dia após dia e valida se não precisa de saltar dias

                if (
                    array_search($deliveryDate->dayOfWeek, $service->delivery_weekdays) !== false &&
                    !in_array($deliveryDate->format('Y-m-d'), $holidays)
                ) {
                    $countDays++;
                }
            } while ($countDays < $transitDays);
        }


        $dateFormated = $deliveryDate->format('Y-m-d');

        if ($returnDetails) {
            return [
                'date'             => $dateFormated,
                'hour'             => $deliveryHour,
                'weekday'          => $deliveryDate->dayOfWeek,
                'transit_time'     => $transit,
                'transit_time_max' => $this->getTransitTimeMax(),
                'delivery_date'    => $dateFormated . ' ' . $deliveryHour . ':00'
            ];
        }

        return $dateFormated . ' ' . $deliveryHour . ':00';
    }

    /**
     * Devolve a hora limite de recolha
     *
     * @return string
     */
    public function getPickupLimitHour()
    {

        $pickupLimitHour = Setting::get('shipments_daily_limit_hour', @$this->service->max_hour);

        // if (@$this->service->max_hour < '23:50') {
        //     $pickupLimitHour = @$this->service->max_hour;
        // }

        if (@$this->customer->shipments_daily_limit_hour) {
            $pickupLimitHour = @$this->customer->shipments_daily_limit_hour;
        }

        return $pickupLimitHour ? $pickupLimitHour : '19:00';
    }

    /**
     * Obtem o tempo de transito (em horas)
     * @return float
     */
    public function getTransitTime()
    {

        $transitTime = @$this->service->transit_time;

        if (@$this->service->zones_transit[$this->zone]) {
            $transitTime = @$this->service->zones_transit[$this->zone];
        }

        return (float) $transitTime;
    }

    /**
     * Get shipment transit time max (in hours)
     * @return float
     */
    public function getTransitTimeMax()
    {

        $transitTime = @$this->service->transit_time_max;

        if (@$this->service->transit_time_max[$this->zone]) {
            $transitTime = @$this->service->zones_transit_max[$this->zone];
        }

        return (float) $transitTime;
    }

    /**
     * Return shipments ids from selected query string filters
     *
     * @param Request $request
     * @return mixed
     */
    public static function getIdsFromFilters(Request $request)
    {

        $ids = [];
        if ($request->id || $request->ids) {
            if ($request->id) {
                $ids = $request->id;
            } else {
                $ids = $request->ids;
            }
        } else {

            $data = Shipment::select(['id']);

            if (Auth::user()->is_developer) {
                $data = $data->whereIn('customer_id', [205]);
            }

            //limit search
            $value = $request->limit_search;
            if ($request->has('limit_search') && !empty($value)) {
                $minId = (int) CacheSetting::get('shipments_limit_search');
                if ($minId) {
                    $data = $data->where('id', '>=', $minId);
                }
            }

            //filter hide final status
            $value = $request->hide_final_status;
            if ($request->has('hide_final_status') && !empty($value)) {
                $finalStatus = ShippingStatus::where('is_final', 1)->pluck('id')->toArray();
                if (in_array(config('app.source'), ['corridadotempo'])) {
                    $finalStatus[] = 9;
                }
                $data = $data->whereNotIn('status_id', $finalStatus);
            }

            //show hidden
            $value = $request->hide_scheduled;
            if ($request->has('hide_scheduled') && !empty($value)) {
                $data = $data->where('date', '<=', date('Y-m-d'));
            }

            //filter period
            $value = $request->period;
            if ($request->has('period')) {
                if ($value == "1") { //MANHA
                    $data = $data->where(function ($q) {
                        $q->whereRaw('HOUR(created_at) between "00:00:00" and "13:00:00"');
                        $q->orWhereRaw('HOUR(created_at) between "18:00:00" and "23:59:59"');
                    });
                } else {
                    $data = $data->where(function ($q) {
                        $q->whereRaw('HOUR(created_at) between "13:00:00" and "18:00:00"');
                    });
                }
            }

            //filter customer
            $value = $request->customer;
            if ($request->has('customer')) {
                $data = $data->where('customer_id', $value);
            }

            //filter customer
            $value = $request->dt_customer;
            if ($request->has('dt_customer')) {
                $data = $data->where('customer_id', $value);
            }

            //filter status
            $value = $request->get('status');
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('status_id', $value);
            }

            //filter service
            $value = $request->get('service');
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('service_id', $value);
            }

            //filter provider
            $value = $request->get('provider');
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('provider_id', $value);
            }

            //filter route
            $value = $request->route;
            if ($request->has('route')) {
                $data = $data->where('route_id', $value);
            }

            //filter agency
            $value = $request->agency;
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('agency_id', $value);
            }

            //filter agency
            $value = $request->sender_agency;
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('sender_agency_id', $value);
            }

            //filter recipient agency
            $value = $request->recipient_agency;
            if (!empty($value)) {
                $value = explode(',', $value);
                $data = $data->whereIn('recipient_agency_id', $value);
            }

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {

                $dtMax = $dtMin;

                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }

                if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                    $dtMin = $dtMin . ' 00:00:00';
                    $dtMax = $dtMax . ' 23:59:59';
                    $statusId = $request->get('date_unity');

                    if (in_array($statusId, ['3', '4', '5', '9', '36'])) {
                        $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                            $q->where('status_id', $statusId)->whereBetween('created_at', [$dtMin, $dtMax]);
                        });
                    } elseif ($statusId == 'delivery') {
                        $data->whereBetween('delivery_date', [$dtMin, $dtMax]);
                    } elseif ($statusId == 'billing') {
                        $data->whereBetween('billing_date', [$dtMin, $dtMax]);
                    } elseif ($statusId == 'creation') {
                        $data->whereBetween('created_at', [$dtMin, $dtMax]);
                    }
                } else { //filter by shipment date
                    $data = $data->whereBetween('date', [$dtMin, $dtMax]);
                }
            }

            //filter operator
            $value = $request->operator;
            if ($request->has('operator')) {
                $value = explode(',', $value);
                if (in_array('not-assigned', $value)) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->whereNull('operator_id')
                            ->orWhereIn('operator_id', $value);
                    });
                } else {
                    $data = $data->whereIn('operator_id', $value);
                }
            }

            //filter charge
            $value = $request->charge;
            if ($request->has('charge')) {
                if ($value == 0) {
                    $data = $data->whereNull('charge_price');
                } elseif ($value == 1) {
                    $data = $data->whereNotNull('charge_price');
                }
            }

            //filter payment at recipient
            $value = $request->payment_recipient;
            if ($request->has('payment_recipient')) {
                if ($value == '0') {
                    $data = $data->where('payment_at_recipient', 0);
                } elseif ($value == '1') {
                    $data = $data->where('payment_at_recipient', 1);
                }
            }

            //show is blocked
            $value = $request->blocked;
            if ($request->has('blocked')) {
                $data = $data->where('is_blocked', $value);
            }

            //show printed
            $value = $request->printed;
            if ($request->has('printed')) {
                $data = $data->where('is_printed', $value);
            }

            //filter invoice
            $value = $request->get('invoice');
            if ($request->has('invoice')) {
                if ($value == '0') {
                    $data = $data->whereNull('invoice_doc_id');
                } else {
                    $data = $data->whereNotNull('invoice_doc_id');
                }
            }

            //filter expenses
            $value = $request->get('expenses');
            if ($request->has('expenses')) {
                if ($value == '0') {
                    $data = $data->where(function ($q) {
                        $q->whereNull('total_expenses');
                        $q->orWhere('total_expenses', 0.00);
                    });
                } else {
                    $data = $data->where('total_expenses', '>', 0.00);
                }
            }

            //show hidden
            $value = $request->deleted;
            if ($request->has('deleted') && !empty($value)) {
                $data = $data->withTrashed();
            }

            //filter type
            $value = $request->get('shp_type');
            if ($request->has('shp_type')) {
                if ($value == Shipment::TYPE_SHIPMENT) {
                    $data = $data->whereNull('type');
                } else if ($value == 'sync-error') {
                    $data = $data->whereNotNull('webservice_method')
                        ->whereNull('submited_at');
                } else if ($value == 'sync-no') {
                    $data = $data->whereNull('webservice_method');
                } else if ($value == 'sync-yes') {
                    $data = $data->whereNotNull('webservice_method')
                        ->whereNotNull('submited_at');
                } else if ($value == 'noprice') {
                    $data = $data->where(function ($q) {
                        $q->whereNull('total_price');
                        $q->orWhere('total_price', '0.00');
                    });
                } else if ($value == 'pod_signature') {
                    $data = $data->whereHas('last_history', function ($q) {
                        $q->where('signature', '<>', '');
                    });
                } else if ($value == 'pod_file') {
                    $data = $data->whereHas('last_history', function ($q) {
                        $q->where('filepath', '<>', '');
                    });
                } else if ($value == 'pudo') {
                    $data = $data->whereNotNull('recipient_pudo_id');
                } else {
                    $data = $data->where('type', $value);
                }
            }

            //filter vehicle
            $value = $request->vehicle;
            if ($request->has('vehicle')) {
                if ($value == '-1') {
                    $data = $data->where(function ($q) {
                        $q->whereNull('vehicle');
                        $q->orWhere('vehicle', '');
                    });
                } else {
                    $data = $data->where('vehicle', $value);
                }
            }

            //filter route
            /*$value = $request->route;
            if($request->has('route')) {
                if($value == '-1') {
                    $data = $data->whereNull('route_id');
                } else {
                    $data = $data->where('route_id', $value);
                }
            }*/

            //filter trailer
            $value = $request->trailer;
            if ($request->has('trailer')) {
                if ($value == '-1') {
                    $data = $data->where(function ($q) {
                        $q->whereNull('trailer');
                        $q->orWhere('trailer', '');
                    });
                } else {
                    $data = $data->where('trailer', $value);
                }
            }

            //filter sender country
            $value = $request->get('sender_country');
            if ($request->has('sender_country')) {
                $data = $data->where('sender_country', $value);
            }

            //filter recipient country
            $value = $request->get('recipient_country');
            if ($request->has('recipient_country')) {
                $data = $data->where('recipient_country', $value);
            }

            //filter recipient zip code
            $value = $request->get('recipient_zip_code');
            if (!empty($value)) {

                $values = explode(',', $value);
                $zipCodes = array_map(function ($item) {
                    return str_contains($item, '-') ? $item : substr($item, 0, 4) . '%';
                }, $values);

                $data = $data->where(function ($q) use ($zipCodes) {
                    foreach ($zipCodes as $zipCode) {
                        $q->orWhere('recipient_zip_code', 'like', $zipCode . '%');
                    }
                });
            }

            //filter workgroups
            $value = $request->get('workgroups');
            if ($request->has('workgroups')) {

                $workgroup = UserWorkgroup::remember(config('cache.query_ttl'))
                    ->cacheTags(UserWorkgroup::CACHE_TAG)
                    ->filterSource()
                    ->whereIn('id', $value)
                    ->get(['services'])
                    ->toArray();

                $serviceIds = [];
                foreach ($workgroup as $group) {
                    if (is_array(@$group['services'])) {
                        $serviceIds = array_merge($serviceIds, $group['services']);
                    }
                }

                if ($serviceIds) {
                    $data = $data->whereIn('service_id', $serviceIds);
                }
            }

            //filter recipient district
            $district = $request->get('recipient_district');
            $county   = $request->get('recipient_county');
            if ($request->has('recipient_district') || $request->has('recipient_county')) {

                $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                    ->cacheTags(ShippingStatus::CACHE_TAG)
                    ->where('district_code', $district)
                    ->where('country', 'pt');

                if ($county) {
                    $zipCodes = $zipCodes->where('county_code', $county);
                }

                $zipCodes = $zipCodes->groupBy('zip_code')
                    ->pluck('zip_code')
                    ->toArray();

                $data = $data->where(function ($q) use ($zipCodes) {
                    $q->where('recipient_country', 'pt');
                    $q->whereIn(DB::raw('SUBSTRING(`recipient_zip_code`, 1, 4)'), $zipCodes);
                });
            }

            $ids = $data->pluck('id')->toArray();
        }

        return $ids;
    }

    /**
     * Cria um retorno diretamente de um envio
     *
     * @param null $data exemplo: [volumes=>1, weight=>23]
     * @param false $generatedByOperator
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createDirectReturn($customFields = null, $generatedByOperator = false, $autoSave = true)
    {

        $defaultStatus     = Setting::get('shipment_status_after_create') ?? ShippingStatus::ACCEPTED_ID;
        $statusAfterPickup = Setting::get('mobile_app_status_after_pickup') ?? ShippingStatus::IN_TRANSPORTATION_ID;
        $statusId          = $generatedByOperator ? $statusAfterPickup : $defaultStatus;

        $originalShipment = $this;

        $shipment = $originalShipment->replicate();

        $shipment->reset2replicate();
        $shipment->tracking_code         = null;
        $shipment->parent_tracking_code  = $originalShipment->tracking_code;
        $shipment->type                  = Shipment::TYPE_RETURN;
        $shipment->sender_agency_id      = $originalShipment->recipient_agency_id;
        $shipment->recipient_agency_id   = empty($originalShipment->sender_agency_id) ? $originalShipment->agency_id : $originalShipment->sender_agency_id;
        $shipment->provider_id           = $originalShipment->provider_id;

        $shipment->sender_name           = $originalShipment->recipient_name;
        $shipment->sender_address        = $originalShipment->recipient_address;
        $shipment->sender_zip_code       = $originalShipment->recipient_zip_code;
        $shipment->sender_city           = $originalShipment->recipient_city;
        $shipment->sender_country        = $originalShipment->recipient_country;
        $shipment->sender_phone          = $originalShipment->recipient_phone;

        $shipment->recipient_name        = $originalShipment->sender_name;
        $shipment->recipient_address     = $originalShipment->sender_address;
        $shipment->recipient_zip_code    = $originalShipment->sender_zip_code;
        $shipment->recipient_city        = $originalShipment->sender_city;
        $shipment->recipient_country     = $originalShipment->sender_country;
        $shipment->recipient_phone       = $originalShipment->sender_phone;

        $shipment->charge_price          = null;
        $shipment->has_return            = null;
        $shipment->cod                   = null;
        $shipment->obs                   = null;
        $shipment->obs_internal          = null;
        $shipment->price_fixed           = 0;

        $shipment->complementar_services = null;
        $shipment->status_id             = $statusId;


        //subscreve valores personalizados
        if (!empty($customFields)) {
            $shipment->fill($customFields);
        }


        //calcula preços e informação envio
        $prices = Shipment::calcPrices($shipment);

        if (@$prices) {
            if (@$prices['pickup']) {
                $shipment->pickup_route_id = $prices['pickup']['route_id'];
            }

            if (@$prices['delivery']) {
                $shipment->delivery_date = $prices['delivery']['delivery_date'];
                $shipment->end_hour = $prices['delivery']['hour_max'];
                $shipment->route_id = $prices['delivery']['route_id'];
            }

            if (@$prices['fillable']) {
                $shipment->fill($prices['fillable']);
            }
        }



        if ($autoSave) {
            $shipment->setTrackingCode();

            //add expenses to shipment
            $shipment->setCalculatedExpenses(@$prices['expenses']);

            //add tracking history
            $history = new ShipmentHistory();
            $history->status_id = $defaultStatus;
            $history->shipment_id = $shipment->id;
            $history->operator_id = $shipment->operator_id;
            $history->agency_id = $shipment->sender_agency_id;
            $history->save();

            if ($generatedByOperator) {
                $history = new ShipmentHistory();
                $history->status_id = $statusAfterPickup;
                $history->shipment_id = $shipment->id;
                $history->operator_id = $shipment->operator_id;
                $history->agency_id = $shipment->sender_agency_id;
                $history->save();
            }

            //add story on original Shipment History
            $history = new ShipmentHistory();
            $history->status_id = ShippingStatus::DELIVERED_ID;
            $history->shipment_id = $originalShipment->id;
            $history->operator_id = $shipment->operator_id;
            $history->agency_id = $originalShipment->recipient_agency_id;
            $history->save();

            $originalShipment->status_id = ShippingStatus::DELIVERED_ID;
            $originalShipment->save();
        }

        return $shipment;
    }

    /**
     * Cria um retorno diretamente de um envio
     *
     * @param null $data exemplo: [volumes=>1, weight=>23]
     * @param false $generatedByOperator
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createDirectDevolution($autoSave = true)
    {
        $originalShipment = $this;

        /**
         * Avoid creating a new devolution if there is one already created
         */
        $devolutionShipment = Shipment::where('parent_tracking_code', $originalShipment->tracking_code)
            ->where('type', Shipment::TYPE_DEVOLUTION)
            ->first();

        if ($devolutionShipment) {
            return $devolutionShipment;
        }
        /**-- */

        $shipment = $originalShipment->replicate();
        $shipment->reset2replicate();


        $statusId  = Setting::get('shipment_status_after_create') ?? ShippingStatus::ACCEPTED_ID;
        $isSending = @$originalShipment->customer->vat == 'A85508299' ? true : false;

        if ($isSending) {
            $parts = explode('#', $shipment->reference3); //0=codigo serviço, 1=codigo cliente
            $shipment->reference3                 = '99#DEV#' . @$parts[1] . '#' . $originalShipment->provider_tracking_code;
            $shipment->provider_sender_agency     = $originalShipment->provider_recipient_agency;
            $shipment->provider_recipient_agency  = $originalShipment->provider_sender_agency;
        }

        $shipment->parent_tracking_code  = $originalShipment->tracking_code;
        $shipment->type                  = Shipment::TYPE_DEVOLUTION;
        $shipment->sender_agency_id      = $originalShipment->recipient_agency_id;
        $shipment->recipient_agency_id   = empty($originalShipment->sender_agency_id) ? $originalShipment->agency_id : $originalShipment->sender_agency_id;

        $shipment->sender_name           = $originalShipment->recipient_name;
        $shipment->sender_address        = $originalShipment->recipient_address;
        $shipment->sender_zip_code       = $originalShipment->recipient_zip_code;
        $shipment->sender_city           = $originalShipment->recipient_city;
        $shipment->sender_country        = $originalShipment->recipient_country;
        $shipment->sender_phone          = $originalShipment->recipient_phone;

        $shipment->recipient_name        = $originalShipment->sender_name;
        $shipment->recipient_address     = $originalShipment->sender_address;
        $shipment->recipient_zip_code    = $originalShipment->sender_zip_code;
        $shipment->recipient_city        = $originalShipment->sender_city;
        $shipment->recipient_country     = $originalShipment->sender_country;
        $shipment->recipient_phone       = $originalShipment->sender_phone;

        $shipment->charge_price          = null;
        $shipment->has_return            = null;
        $shipment->cod                   = null;
        $shipment->obs                   = null;
        $shipment->obs_internal          = null;
        $shipment->price_fixed           = 0;

        $shipment->complementar_services = null;
        $shipment->status_id             = $statusId;


        //calcula preços e informação envio
        $prices = Shipment::calcPrices($shipment);

        //dd($prices);
        if (@$prices) {
            if (@$prices['pickup']) {
                $shipment->pickup_route_id = $prices['pickup']['route_id'];
            }

            if (@$prices['delivery']) {
                $shipment->delivery_date = $prices['delivery']['delivery_date'];
                $shipment->end_hour = $prices['delivery']['hour_max'];
                $shipment->route_id = $prices['delivery']['route_id'];
            }

            if (@$prices['fillable']) {
                $shipment->fill($prices['fillable']);
            }
        }


        if ($autoSave) {
            $shipment->provider_id = $originalShipment->provider_id;
            $shipment->setTrackingCode();

            //add expenses to shipment
            $shipment->setCalculatedExpenses(@$prices['expenses']);

            //add tracking history
            $history = new ShipmentHistory();
            $history->status_id     = $statusId;
            $history->shipment_id   = $shipment->id;
            $history->operator_id   = $shipment->operator_id;
            $history->agency_id     = $shipment->sender_agency_id;
            $history->save();

            //add story on original Shipment History
            $history = new ShipmentHistory();
            $history->status_id     = ShippingStatus::DEVOLVED_ID;
            $history->shipment_id   = $originalShipment->id;
            $history->operator_id   = $shipment->operator_id;
            $history->agency_id     = $originalShipment->recipient_agency_id;
            $history->save();

            $originalShipment->status_id = ShippingStatus::DEVOLVED_ID;
            $originalShipment->save();
        }

        if ($isSending) {
            $webservice = new Webservice\Base();
            $webservice->submitShipment($shipment);
        }

        return $shipment;
    }


    /**
     * Cria um  envio automático a partir de uma recolha
     *
     * @param false $failed
     * @param null $serviceId
     * @return bool|Carbon|float|\Illuminate\Support\Collection|int|mixed|string|null
     */
    public function createShipmentFromPickup($failed = false, $serviceId = null)
    {

        $originalShipment = $this;
        $shipment = $originalShipment->replicate();

        if (is_null($serviceId)) {
            if ($failed) {
                $serviceId = null;
                $service = Service::remember(config('cache.query_ttl'))
                    ->cacheTags(Service::CACHE_TAG)
                    ->filterSource()
                    ->where('code', 'RFAIL')
                    ->first();

                if ($service) {
                    $serviceId = $service->assigned_service_id;
                }
            } else {
                $service = Service::remember(config('cache.query_ttl'))
                    ->cacheTags(Service::CACHE_TAG)
                    ->filterSource()
                    ->where(function ($q) use ($originalShipment) {
                        $q->whereId($originalShipment->service_id);
                        $q->orWhere('code', '24H');
                    })
                    ->get();

                //get assigned service id

                $serviceId = $service->filter(function ($item) use ($originalShipment) {
                    return $item->id == $originalShipment->service_id;
                })->first();
                $serviceId = @$serviceId->assigned_service_id;

                //if dont have assigned service id, get service 24 by default
                if (!$serviceId) {
                    $serviceId = $service->filter(function ($item) use ($originalShipment) {
                        return $item->code == '24H';
                    })->first()->id;
                }
            }
        }

        $shipment->resetWebserviceError();
        $shipment->tracking_code        = null;
        $shipment->parent_tracking_code = $originalShipment->tracking_code;
        $shipment->type                 = Shipment::TYPE_PICKUP;
        $shipment->service_id           = $serviceId;
        $shipment->is_collection        = 0;
        $shipment->created_by_customer  = 0;

        if ($failed) {
            $shipment->ignore_billing  = 0;
            $shipment->price_fixed     = 0;
            $shipment->charge_price    = 0;
            $shipment->charge_price    = null;
            $shipment->has_return      = null;
            $shipment->optional_fields = null;
            $shipment->status_id       = ShippingStatus::PICKUP_FAILED_ID;
            $shipment->complementar_services = null;
        } else {
            $shipment->status_id = Setting::get('shipment_status_after_create') ? Setting::get('shipment_status_after_create') : ShippingStatus::ACCEPTED_ID;
        }

        $prices = Shipment::calcPrices($shipment);
        $shipment->cost_price     = @$prices['cost'];
        $shipment->total_price    = @$prices['total'];
        $shipment->fuel_tax       = @$prices['fuelTax'];
        $shipment->extra_weight   = @$prices['extraKg'];
        $shipment->setTrackingCode();

        //add expense to shipment
        $shipment->insertOrUpdadePickupExpense($originalShipment);

        //add story to shipment
        $history = new ShipmentHistory();
        $history->status_id   = $shipment->status_id;
        $history->shipment_id = $shipment->id;
        $history->agency_id   = $shipment->agency_id;
        $history->save();

        //store children trk
        $originalShipment->update([
            'children_tracking_code' => $shipment->tracking_code,
            'children_type'          => Shipment::TYPE_PICKUP,
            'status_id'              => $failed ? ShippingStatus::PICKUP_FAILED_ID : ShippingStatus::PICKUP_CONCLUDED_ID
        ]);

        return $shipment->tracking_code;
    }

    /**
     * Create shipment from pickup
     */
    public function addPickupFailedExpense()
    {

        $expense = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->where('type', 'recfail')
            ->first();

        if ($expense) {
            $price = $expense->calcExpensePrice($this);
            if (!@$price['fillable']) {
                return false;
            }

            $shipmentExpense = ShipmentExpense::firstOrNew([
                'shipment_id' => $this->id,
                'expense_id'  => $expense->id
            ]);

            $shipmentExpense->fill($price['fillable']);
            $shipmentExpense->date = date('Y-m-d');
            $shipmentExpense->save();

            ShipmentExpense::updateShipmentTotal($this->id);

            return $shipmentExpense->subtotal;
        }

        return false;
    }

     /**
     * List shipment multiple addresses
     *
     * @return void
     */
    public function listAllAddresses() {

        if($this->type == Shipment::TYPE_MASTER) { //envio filho
            $masterTrk = $this->parent_tracking_code;
        } else {
            $masterTrk = $this->tracking_code;
        }

        $allShipments = Shipment::with('pack_dimensions')
            ->where(function($q) use($masterTrk) {
                $q->where('parent_tracking_code', $masterTrk);
                $q->orWhere('tracking_code', $masterTrk);
            })
            ->orderBy('type', 'asc')
            ->get([
                'id', 'tracking_code', 'type',
                'sender_name', 'sender_address', 'sender_zip_code','sender_city','sender_state','sender_country','sender_attn',
                'sender_vat','sender_phone','sender_latitude', 'sender_longitude',
                'recipient_name', 'recipient_address','recipient_zip_code','recipient_city','recipient_state','recipient_country',
                'recipient_attn','recipient_vat','recipient_phone','recipient_email','recipient_latitude','recipient_longitude',
                'shipper_name','shipper_address','shipper_zip_code','shipper_city','shipper_country','shipper_vat','shipper_phone',
                'receiver_name','receiver_address','receiver_zip_code','receiver_city','receiver_country','receiver_vat','receiver_phone',
                'volumes','weight','fator_m3','ldm','kms', 'operator_id', 'vehicle', 'trailer', 'trip_id', 'service_id',
                'date','shipping_date','delivery_date', 'reference', 'reference2', 'reference3', 'obs', 'obs_delivery', 'obs_internal'
            ]);

        return $allShipments;
    }

    /**
     * Store logistic Shipping Order
     * @param $input
     * @return array
     */
    public function storeShippingOrder()
    {

        $shipment = $this;
        $products = [];
        $totalQty   = 0;
        $totalItems = 0;
        $totalPrice = 0;

        unset($shipment->pack_dimensions);
        if (!$shipment->pack_dimensions->isEmpty()) {

            $productIds = [];
            foreach ($shipment->pack_dimensions as $item) {
                if ($item->sku) {
                    $totalQty += $item->qty;
                    $totalItems++;
                    $totalPrice += @$item->price;

                    $products[] = [
                        'product_id' => @$item->product_id,
                        'qty'        => @$item->qty,
                        'price'      => @$item->price,
                        'serial_no'  => @$item->serial_no,
                        'lote'       => @$item->lote,
                        'sku'        => @$item->sku,
                    ];

                    $productIds[] = @$item->product_id;
                }
            }

            $productsCollection = App\Models\Logistic\Product::with('locations')->whereIn('id', $productIds)->get();

            //create logistic shipping order
            if (!empty($products)) {

                $shippingOrder = App\Models\Logistic\ShippingOrder::firstOrNew(['shipment_id' => $shipment->id]);
                if ($shippingOrder->status_id == App\Models\Logistic\ShippingOrder::STATUS_CONCLUDED) {
                    return [
                        'result' => true
                    ];
                }

                if (!$shippingOrder->exists) {
                    $shippingOrder->source      = config('app.source');
                    $shippingOrder->shipment_id = $shipment->id;
                    $shippingOrder->customer_id = $shipment->customer_id;
                    $shippingOrder->date        = $shipment->date;
                    $shippingOrder->document    = $shipment->reference;
                    $shippingOrder->shipment_trk = $shipment->tracking_code;
                    $shippingOrder->obs         = br2nl('TRK' . $shipment->tracking_code . '<br/>' . $shipment->obs);
                    $shippingOrder->status_id   = App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING;
                    $shippingOrder->user_id     = null;
                    $shippingOrder->total_items = $totalItems;
                    $shippingOrder->qty_total   = $totalQty;
                    $shippingOrder->total_price = $totalPrice;
                    $shippingOrder->setCode();
                } else {
                    $shippingOrder->date        = $shipment->date;
                    $shippingOrder->document    = $shipment->reference;
                    $shippingOrder->shipment_trk = $shipment->tracking_code;
                    $shippingOrder->obs         = br2nl('TRK' . $shipment->tracking_code . '<br/>' . $shipment->obs);
                    $shippingOrder->total_items = $totalItems;
                    $shippingOrder->qty_total   = $totalQty;
                    $shippingOrder->total_price = $totalPrice;
                    $shippingOrder->save();
                }

                //if(in_array($shippingOrder->status_id, [App\Models\Logistic\ShippingOrder::STATUS_PENDING, App\Models\Logistic\ShippingOrder::STATUS_PROCESSING])) {

                $insertArr  = [];
                $datetime   = date('Y-m-d H:i:s');
                $productIds = [];
                foreach ($products as $product) {

                    $curProduct = $productsCollection->find($product['product_id']);
                    $productLocations  = [];
                    if ($curProduct) {
                        // $locations  = $curProduct->autoselectLocationId(@$product['qty']);
                        $productLocations = App\Models\Logistic\ProductLocation::getAutomaticLocation($product['product_id'], @$product['qty']);
                    }

                    if (!empty($productLocations)) {
                        $qtyToSatisfy = $product['qty'];
                        foreach ($productLocations as $productLocation) {
                            $qty = ($qtyToSatisfy > $productLocation['qty']) ? $productLocation['qty'] : $qtyToSatisfy;

                            $insertArr[] = [
                                'shipping_order_id'   => $shippingOrder->id,
                                'product_id'          => $product['product_id'],
                                'qty'                 => $qty,
                                'price'               => $qty * @$product['price'],
                                'barcode'             => $product['sku'],
                                'serial_no'           => $product['serial_no'],
                                'lote'                => $product['lote'],
                                'location_id'         => $productLocation['location_id'],
                                'product_location_id' => $productLocation['id'],
                                'created_at'          => $datetime
                            ];

                            $qtyToSatisfy -= $qty;
                        }

                        $productIds[] = $product['product_id'];
                    }
                }

                if (!empty($insertArr)) {
                    App\Models\Logistic\ShippingOrderLine::deleteByShippingOrderId($shippingOrder->id);
                    App\Models\Logistic\ShippingOrderLine::insert($insertArr);

                    //aloca e reserva os stocks
                    foreach ($insertArr as $arrayItem) {
                        $productId         = $arrayItem['product_id'];
                        $locationId        = $arrayItem['location_id'];
                        $productLocationId = $arrayItem['product_location_id'];
                        $qty               = $arrayItem['qty'];

                        $product         = App\Models\Logistic\Product::find($productId);
                        $productLocation = App\Models\Logistic\ProductLocation::where(function ($q) use ($productId, $locationId, $productLocationId) {
                            $q->where(function ($q) use ($productId, $locationId) {
                                $q->where('location_id', $locationId)
                                    ->where('product_id', $productId);
                            });

                            $q->orWhere('id', $productLocationId);
                        })->first();

                        $product->update([
                            'stock_allocated' => $product->stock_allocated + $qty,
                            'stock_available' => $product->stock_available - $qty
                        ]);

                        if ($productLocation) {
                            $productLocation->update([
                                'stock_allocated' => $productLocation->stock_allocated + $qty,
                                'stock_available' => $productLocation->stock_available - $qty
                            ]);
                        }
                    }
                } else {
                    App\Models\Logistic\ShippingOrderLine::deleteByShippingOrderId($shippingOrder->id);
                    $shippingOrder->delete();
                }

                
                //cativa os artigos envolvidos
                //procura na lista de ordens de saida todas as ordens que estejam pendentes e soma o total de quantidade de cada artigo
                /*$allocatedTotals = App\Models\Logistic\ShippingOrderLine::whereHas('shipping_order', function ($q) {
                        $q->where('status_id', App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING);
                    })
                    ->whereIn('product_id', $productIds)
                    ->groupBy('product_id')
                    ->select(['product_id', DB::raw('sum(qty) as qty')])
                    ->pluck('qty', 'product_id')
                    ->toArray();

                foreach ($allocatedTotals as $id => $qty) {
                    //aloca o stock do produto
                    App\Models\Logistic\Product::where('id', $id)->update(['stock_allocated' => $qty]);
                }*/


                //SUBMIT BY WEBSERVICE
                if (!empty($insertArr) && (config('app.source') == 'activos24')) {

                    try {

                        if (@$shipment->customer->customer_id) {
                            $shipment->customer = Customer::find(@$shipment->customer->customer_id);
                        }

                        $shippingOrder->customer     = $shipment->customer;
                        $shippingOrder->trk          = $shipment->tracking_code;
                        $shippingOrder->provider_trk = $shipment->provider_tracking_code;
                        $onS3 = new \App\Models\InvoiceGateway\OnSearch\Document();
                        $onS3->insertOrUpdateDocument($shippingOrder);
                    } catch (\Exception $e) {
                        return [
                            'result'   => false,
                            'feedback' => 'Erro submissão OnS3: ' . $e->getMessage()
                        ];
                    }
                }
                //}
            }

            return [
                'result'   => true,
                'feedback' => 'Submetido com sucesso.'
            ];
        }
    }

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setTrackingCode()
    {
        
        $source = config('app.source');

        if (empty($this->tracking_code)) {
            $this->save();

            if ($this->is_collection) {
                $code = str_pad($this->id, 9, "0", STR_PAD_LEFT);
                $code .= '/' . date('y');
            } else {

                if ($source == 'massivepurple') {
                    $code = 'MP' . str_pad($this->id, 8, '0', STR_PAD_LEFT) . 'PT';
                } elseif ($source == '2660express') {
                    $shpToday = Shipment::withTrashed()
                                ->where('is_collection', 0)
                                ->whereRaw('DATE(created_at)="'.date('Y-m-d').'"')
                                ->count(); //nesta altura já fez save do envio no inicio da função, pelo que o count já vem atualizado.

                    $code = date('ymd'). str_pad($shpToday, 6, '0', STR_PAD_LEFT);
                } else {
                    $code = trk_algorithm($this->agency_id, $this->id);
                }
            }

            $this->tracking_code = $code;
        }

        $this->save();
    }

    /**
     * Check if shipment has sync or has sync error. Return error string if shipment has error.
     * @return bool|mixed|string
     */
    public function hasSyncError()
    {
        return $this->hasSync(true);
    }

    /**
     * Check if shipment has webservice sync
     * @param bool $checkError if true, check if webservice retuns error
     * @return bool|mixed|string
     */
    public function hasSync($checkError = false)
    {
        if ($checkError) {
            return !empty($this->webservice_method) && empty($this->submited_at) ? (empty($this->webservice_error) ? 'Error undefined.' : $this->webservice_error) : false;
        } else {
            return !empty($this->webservice_method) && !empty($this->submited_at) ? true : false;
        }
    }

    /**
     * Check if shipment is grouped
     * @return bool|string [master tracking]
     */
    public function isGrouped()
    {

        if ($this->children_type == 'M') {
            return $this->tracking_code;
        } elseif ($this->type == 'M') {
            return $this->parent_tracking_code;
        }

        return false;
    }

    /**
     * Reset all fields to replicate shipment
     */
    public function reset2replicate()
    {
        $shipment = $this;
        $shipment->is_blocked                = 0;
        $shipment->invoice_type              = null;
        $shipment->invoice_draft             = 0;
        $shipment->invoice_id                = null;
        $shipment->invoice_doc_id            = null;
        $shipment->invoice_key               = null;
        $shipment->purchase_invoice_id       = null;
        $shipment->conferred                 = null;
        $shipment->provider_conferred        = null;
        $shipment->tracking_code             = null;
        $shipment->provider_tracking_code    = null;
        $shipment->provider_cargo_agency     = null;
        $shipment->provider_sender_agency    = null;
        $shipment->provider_recipient_agency = null;
        $shipment->parent_tracking_code      = null;
        $shipment->children_tracking_code    = null;
        $shipment->type                      = null;
        $shipment->webservice_error          = null;
        $shipment->webservice_method         = null;
        $shipment->submited_at               = null;
        $shipment->created_by_customer       = 0;
        $shipment->created_by                = null;
        $shipment->is_printed                = 0;
        $shipment->ignore_billing            = 0;
        $shipment->without_pickup            = false;

        $shipment->operator_id               = null;
        $shipment->date                      = date('Y-m-d');
        $shipment->start_hour                = null;
        $shipment->billing_date              = date('Y-m-d');

        $shipment->pickuped_date             = null;
        $shipment->inbound_date              = null;
        $shipment->distribution_date         = null;
        $shipment->incidence_date            = null;
        $shipment->delivered_date            = null;
    }

     /**
     * Reset shipment prices
     * @param bool $save
     */
    public function resetPrices()
    {
        if(!$this->invoice_id) {
            $this->price_kg                  = null;
            $this->shipping_price            = null;
            $this->expenses_price            = null;
            $this->fuel_price                = null;
            $this->fuel_tax                  = null;
            $this->vat_rate                  = null;
            $this->vat_rate_id               = null;
            $this->billing_subtotal          = null;
            $this->billing_vat               = null;
            $this->billing_total             = null;
            $this->cost_shipping_base_price  = null;
            $this->cost_shipping_price       = null;
            $this->cost_expenses_price       = null;
            $this->cost_billing_subtotal     = null;
            $this->cost_billing_vat          = null;
            $this->cost_billing_total        = null;
            $this->total_price_for_recipient = null;
            $this->payment_at_recipient      = null;
            $this->cod                       = null;
            $this->price_fixed               = false;
        }
    }

    /*==================================================================================================================
     *  WEBSERVICE ALIAS FUNCTIONS
     ==================================================================================================================*/

    /**
     * Submit webservice connection
     * @return
     */
    public function submitWebservice($debug = false)
    {
        if(@$this->provider->webservice_method == 'sending' && $this->provider_tracking_code && empty($this->submited_at)) { //envios de distribuição sending
            $submitWebservice = false;
        } else {

            $serviceAllowsAutoSubmit = false;
            if (!$this->is_collection && @$this->service->settings['webservices_auto_submit']) {
                $serviceAllowsAutoSubmit = true;
            } else if ($this->is_collection && @$this->service->assignedService->settings['webservices_auto_submit']) {
                $serviceAllowsAutoSubmit = true;
            }

            if (Setting::get('webservices_auto_submit') && $serviceAllowsAutoSubmit && !@$this->status->is_final) {

                //sistemas que permitem edição do envio
                $allowEdition = ['envialia', 'tipsa', 'nacex', 'wexpress', 'lfm', 'sending'];

                try {
                    if (
                        empty($this->webservice_method)
                        || (!empty($this->webservice_method) && empty($this->submited_at))
                        || in_array($this->webservice_method, $allowEdition)
                    ) {

                        $webservice = new Webservice\Base($debug);
                        $webservice->submitShipment($this);
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * Destroy webservice connection
     * @return bool|string
     */
    public function destroyWebservice()
    {
        try {
            $webservice = new Webservice\Base();
            $webservice->deleteShipment($this, null);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * Reset webservice error
     * @param bool $save
     */
    public function resetWebserviceError($save = false)
    {

        $this->provider_tracking_code       = null;
        $this->provider_cargo_agency        = null;
        $this->provider_sender_agency       = null;
        $this->provider_recipient_agency    = null;
        $this->webservice_method            = null;
        $this->webservice_error             = null;
        $this->submited_at                  = null;

        if ($save) {
            $this->save();
        }
    }


    /*==================================================================================================================
     *  NOTIFICATIONS
     ==================================================================================================================*/
    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setOperatorNotification($operatorId = null)
    {
        if ($operatorId) {
            $data['id'] = time(); //ID da notificação = time
        } else {
            $data['id'] = 0; //agrupa as notificações
        }

        $data['title']   = 'Novo Serviço';
        $data['message'] = 'Solicitado novo serviço por ' . @$this->customer->name;

        $channel = BroadcastPusher::getChannel($operatorId);
        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel = null)
    {
        $source = null;

        $message = 'Novo envio de ' . $this->customer->name;
        $sourceClass = 'ShipmentCustomer';
        $sourceId = $this->customer_id;

        if ($this->is_collection) {
            $sourceClass = 'Shipment';
            $message  = 'Recolha solicitada por ' . $this->customer->name;
            $sourceId = $this->id;
            $source   = @$this->senderAgency->source;
            $agencies = $this->senderAgency->source != config('app.source') ? [$this->customer->agency_id, $this->sender_agency_id] : [$this->customer->agency_id];
        } else {
            $agencies = [$this->customer->agency_id];
        }

        //get notification recipients
        $recipients = \App\Models\User::where(function ($q) use ($agencies) {
            $q->where(function ($q) use ($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                }
            });
        })
            ->where(function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->whereName('administrator');
                })
                    ->orWhereHas('roles.perms', function ($query) {
                        $query->whereName('shipments');
                    });
            })
            ->get(['id']);

        //dd($recipients->pluck('id')->toArray());
        foreach ($recipients as $user) {
            $notification = Notification::firstOrNew([
                'source_class'  => $sourceClass,
                'source_id'     => $sourceId,
                'recipient'     => $user->id
            ]);

            $notification->source_class = $sourceClass;
            $notification->source_id    = $sourceId;
            $notification->recipient    = $user->id;
            $notification->message      = $message;
            $notification->alert_at     = date('Y-m-d H:i:s');
            $notification->read         = false;
            $notification->save();
        }

        if ($notification) {
            $notification->setPusher(BroadcastPusher::getGlobalChannel($source));
        }

        //dd($recipients->pluck('id')->toArray());
        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        return Notification::where('source_class', 'Shipment')
            ->where('source_id', $this->id)
            ->delete();
    }

    /**
     * Schedule notification
     * @param $type
     * @return bool true
     */
    public function scheduleNotification()
    {
        $notificaiton = new ScheduledTask();
        $notificaiton->source     = config('app.source');
        $notificaiton->action     = 'notification';
        $notificaiton->target     = 'Shipment';
        $notificaiton->target_id  = $this->id;
        return $notificaiton->save();
    }

    /**
     * Execute wallet auto payment
     * @param $emails
     * @return array
     */
    public function walletPayment($customerCollection = null, $autoSave = true, $customPrice = null, $force = false)
    {

        if (!$customerCollection) {
            $customerCollection = $this->customer;
        }

        $paymentSuccess = true;
        $walletPayment  = false;
        if (hasModule('account_wallet') && !$customerCollection->is_mensal && !$this->ignore_billing) {

            $walletPayment = App\Models\GatewayPayment\Base::storeShipmentPayment($this, $customerCollection, $customPrice, $force);

            if ($walletPayment['result']) {
                $this->ignore_billing = 1;
                $this->vat_rate       = @$walletPayment['vat_rate'];
            } else {
                $this->ignore_billing = 0;
                $this->status_id     = ShippingStatus::PAYMENT_PENDING_ID;
                $this->vat_rate      = @$walletPayment['vat_rate'];
                $paymentSuccess      = false;
            }

            if ($autoSave) {
                $this->save();
            }
        }

        return [
            'success'        => $paymentSuccess,
            'walletPayment'  => $walletPayment
        ];
    }

    /**
     * Execute wallet auto refund
     * @param $emails
     * @return array
     */
    public function walletRefund($customerCollection = null, $autoSave = true, $customPrice = null)
    {

        if (!$customerCollection) {
            $customerCollection = $this->customer;
        }

        $paymentSuccess = true;
        $walletPayment  = false;
        if (hasModule('account_wallet') && !$customerCollection->is_mensal && $this->ignore_billing) {
            $walletPayment = App\Models\GatewayPayment\Base::refundShipmentPayment($this, $customerCollection, $customPrice);
            if ($walletPayment['result']) {
                $this->ignore_billing = 0;
                $this->vat_rate       = null;
            } else {
                $paymentSuccess = false;
            }

            if ($autoSave) {
                $this->save();
            }
        }

        return [
            'success'        => $paymentSuccess,
            'walletPayment'  => $walletPayment
        ];
    }


    /*==================================================================================================================
     *  PDF & DOCUMENTATION FUNCTIONS
     ==================================================================================================================*/

    /**
     * Create adhesive labels
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printAdhesiveLabels($shipmentsIds, $useAgenciesLogo = true, $source = 'admin', $returnMode = 'pdf')
    {

        try {
            $customerBlockProviderLabels = Setting::get('customer_block_provider_labels');

            if ($source == 'customer' || $source == 'api' || $source == 'partnersApi') {

                if ($source == 'customer') {
                    $customer = Auth::guard('customer')->user();
                } elseif ($source == 'api') {
                    $customer = Auth::guard('api')->user();
                }

                $shipments = self::whereIn('id', $shipmentsIds);

                if (@$customer && ($source == 'customer' || $source == 'api')) {
                    $shipments = $shipments->where(function ($q) use ($customer) {
                        $q->where(function ($q) use ($customer) {
                            $q->where('customer_id', $customer->id);
                            $q->orWhere('requested_by', $customer->id);
                            if ($customer->customer_id) {
                                $q->orWhere('customer_id', $customer->customer_id);
                                $q->orWhere('requested_by', $customer->customer_id);
                            }
                        });
                    });
                }

                $shipments = $shipments->get();
            } else {
                $sortOrder = implode(',', $shipmentsIds);
                $shipments = self::filterAgencies()
                    ->whereIn('id', $shipmentsIds)
                    ->orderByRaw('FIELD(id,' . $sortOrder . ')')
                    ->get();
            }
            if ($shipments->isEmpty()) {
                return App::abort(404);
            }

            //construct pdf
            ini_set("memory_limit", "-1");

            $concatLabels = false;
            $forceCancelAutoPrint = false;
            $totalShipments = count($shipments);
            if ($totalShipments > 1) {
                $concatLabels = [];
            }

            Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => true]);


            $hasNativeLabels  = false;
            $printNativeLabel = true;
            foreach ($shipments as $shipment) {

                if (@$shipment->webservice_method == 'sending') { //etiqueta Sending

                    $doc = new Webservice\Sending();
                    $doc = $doc->getEtiqueta(null, $shipment->provider_tracking_code);

                    if ($returnMode == 'string') {
                        return $doc;
                    }

                    header('Content-Type: application/pdf');
                    echo base64_decode($doc); //show label on screen
                    exit;
                } elseif ($shipment->webservice_method && $shipment->submited_at && !($source == 'customer' && $customerBlockProviderLabels)) {
                    $printNativeLabel = false;

                    $doc = new Webservice\Base;
                    $doc = $doc->getAdhesiveLabel($shipment, null, $totalShipments > 1 ? 'S' : 'I');

                    if (in_array($shipment->webservice_method, ['wexpress'])) { //etiqueta nao é possivel obter por PDF. Redireciona para URL de impressão
                        return ['external_url' => $doc];
                    }

                    //força etiquetas enovo tms a sair sempre a etiqurta nativa
                    if (!$doc) { //when adhesive label return false (due to not find webservice method)
                        $printNativeLabel = true;
                    } else {

                        try {
                            ob_start();
                            $data = base64_decode($doc);

                            if (is_array($concatLabels)) {
                                if (!File::exists(public_path() . '/uploads/labels/mass_print/')) {
                                    File::makeDirectory(public_path() . '/uploads/labels/mass_print/');
                                }
                                $filepath = public_path() . '/uploads/labels/mass_print/mass_' . $shipment->id . '.pdf';
                                File::put($filepath, $data);

                                $concatLabels[$filepath] = [
                                    'filepath'    => $filepath,
                                    'orientation' => in_array($shipment->webservice_method, ['envialia', 'tipsa', 'via_directa', 'correos_express', 'ctt']) ? 'L' : 'P'
                                ];
                            } else {
                                if ($returnMode == 'string') {
                                    return $data;
                                }

                                header('Content-Type: application/pdf');
                                echo $data; //show label on screen
                                exit;
                            }
                        } catch (\Exception $e) {
                            Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => false]);
                            throw new Exception('Erro na etiqueta do envio ' . $shipment->tracking_code . '. Motivo: ' . $e->getMessage());
                        }
                    }
                } elseif ($shipment->webservice_method && !$shipment->submited_at) {

                    Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => false]);
                    $printNativeLabel = false;
                    $forceCancelAutoPrint  = true;

                    $data = [
                        'shipment'  => $shipment,
                        'source'    => $source,
                        'view'      => 'admin.printer.shipments.labels.label_error',
                    ];

                    $mpdf = new Mpdf(getLabelFormat('full-horizontal'));
                    $mpdf->showImageErrors = true;
                    $mpdf->SetAuthor("ENOVO");
                    $mpdf->shrink_tables_to_fit = 0;
                    $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
                    $mpdf->forcePortraitHeaders = true;
                }

                if ($printNativeLabel) {

                    $customerLabel = @$shipment->customer->settings['label_template'];
                    $labelFormat = getLabelFormat($customerLabel);

                    if ($shipment->webservice_method == 'ctt_correios' || (config('app.source') == 'entregaki' && $shipment->provider_id == 8)) { //CTT Correios
                        $labelFormat = [
                            'orientation'   => 'P',
                            'format'        => [100, 145],
                            'margin_left'   => 2,
                            'margin_right'  => 2,
                            'margin_top'    => 2,
                            'margin_bottom' => 2,
                            'margin_header' => 0,
                            'margin_footer' => 0,
                        ];
                    } elseif (@$shipment->service->is_mail) { //Etiqueta correio
                        //elseif (config('app.source') == 'massivepurple' && $shipment->provider_id == '1') { //Etiqueta correio
                        $labelFormat = [
                            'orientation'   => 'P',
                            'format'        => [80, 15],
                            'margin_left'   => 0,
                            'margin_right'  => 0,
                            'margin_top'    => 0,
                            'margin_bottom' => 0,
                            'margin_header' => 0,
                            'margin_footer' => 0,
                        ];
                    }

                    $mpdf = new Mpdf($labelFormat);
                    $mpdf->showImageErrors = true;
                    $mpdf->SetAuthor("ENOVO");
                    $mpdf->shrink_tables_to_fit = 0;

                    $data = [
                        'shipment' => $shipment,
                        'useAgenciesLogo' => $useAgenciesLogo,
                    ];

                    $data['view'] = getLabelView($customerLabel ? 'label_' . $customerLabel : null);

                    if ($shipment->webservice_method == 'ctt_correios' || (config('app.source') == 'entregaki' && $shipment->provider_id == 8)) { //CTT Correios
                        $data['view'] = 'admin.printer.shipments.labels.label_15x10-correios';
                    } elseif (@$shipment->service->is_mail) {
                        $data['view'] = 'admin.printer.shipments.labels.label_correio_80x15';
                    }

                    for ($i = 1; $i <= $shipment->volumes; $i++) {
                        $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT));
                        $qrCode->disableBorder();
                        $output = new \Mpdf\QrCode\Output\Png();
                        $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 70));

                        $data['count']  = $i;
                        $data['qrCode'] = $qrCode;
                        $mpdf->addPage();
                        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
                        $mpdf->forcePortraitHeaders = true;
                    }

                    if (is_array($concatLabels)) {

                        $labelContent = $mpdf->Output('Etiquetas.pdf', 'S');

                        if (!File::exists(public_path() . '/uploads/labels/mass_print/')) {
                            File::makeDirectory(public_path() . '/uploads/labels/mass_print/');
                        }

                        $filepath = public_path() . '/uploads/labels/mass_print/mass_' . $shipment->id . '.pdf';
                        File::put($filepath, $labelContent);

                        $labelSize = Setting::get('shipment_label_size') ? Setting::get('shipment_label_size') : 'default';

                        if (@$shipment->service->is_mail) {
                            $orientation = 'L';
                        } else {
                            $orientation = config('labels.' . $labelSize . '.orientation');
                            $orientation = in_array($shipment->webservice_method, ['envialia', 'tipsa', 'via_directa', 'correos_express', 'ctt']) ? 'L' : $orientation;
                        }

                        $concatLabels[$filepath] = [
                            'filepath'    => $filepath,
                            'orientation' => $orientation
                        ];
                    }
                }

                $printNativeLabel = true;
            }

            //dd($concatLabels);

            //mass print all other labels
            if (!empty($concatLabels) && is_array($concatLabels)) {

                // Merge files
                $pdf = new PdfManage();
                foreach ($concatLabels as $label) {

                    if (File::exists($label['filepath'])) {
                        $orientation = $label['orientation'];
                        $pdf->addPDF($label['filepath'], 'all', $orientation);
                    }
                }

                // Save Merged Files
                //$orientation = in_array($shipment->webservice_method, ['envialia','viadirecta']) ? 'L' : 'P';
                $outputFilepath = public_path() . '/uploads/labels/mass_print/' . config('app.source') . '_' . date('Y') . date('m') . '.pdf';

                if ($returnMode == 'string') {
                    return $pdf->merge('S', $outputFilepath); //return string
                }

                $pdf->merge('browser', $outputFilepath);

                foreach ($concatLabels as $label) {
                    File::delete($label);
                }

                exit;
            } else {

                if ($returnMode == 'string') {
                    return $mpdf->Output('Etiquetas.pdf', 'S'); //string
                }

                if (Setting::get('open_print_dialog_labels') && !$forceCancelAutoPrint) {
                    $mpdf->SetJS('this.print();');
                }

                $mpdf->debug = true;
                return $mpdf->Output('Etiquetas.pdf', 'I'); //output to screen

                exit;
            }
        } catch (\Exception $e) {
            Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => false]);
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
    }

    /**
     * Create adhesive labels A4
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printAdhesiveLabelsA4($shipmentsIds, $firstPosition = 1, $labelsPerPage = null, $source = 'admin', $returnMode = 'pdf')
    {

        try {

            $labelsPerPage = $labelsPerPage ? $labelsPerPage : (Setting::get('shipment_label_a4') ? Setting::get('shipment_label_a4') : 4);

            if ($source == 'customer' || $source == 'api') {

                if ($source == 'customer') {
                    $customer = Auth::guard('customer')->user();
                } elseif ($source == 'api') {
                    $customer = Auth::guard('api')->user();
                }

                $shipments = self::whereIn('id', $shipmentsIds)
                    ->where(function ($q) use ($customer) {
                        $q->where('customer_id', $customer->id);
                        if ($customer->customer_id) {
                            $q->orWhere('customer_id', $customer->customer_id);
                        }
                    })
                    ->get();
            } else {
                $shipments = self::filterAgencies()
                    ->whereIn('id', $shipmentsIds)
                    ->get();
            }

            if ($shipments->isEmpty()) {
                return App::abort(404);
            }

            //construct pdf
            ini_set("memory_limit", "-1");

            Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => true]);

            $mpdf = new Mpdf([
                'format'        => 'A4',
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_left'   => 0,
                'margin_right'  => 0,
            ]);
            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("ENOVO");
            $mpdf->shrink_tables_to_fit = 0;

            $view = 'admin.printer.shipments.labels.label_a4_' . $labelsPerPage;

            $data = [
                'firstPosition' => $firstPosition,
                'shipments'     => $shipments,
                'view'          => $view,
                'documentTitle' => ''
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write


            if ($returnMode == 'string') {
                return $mpdf->Output('Etiquetas.pdf', 'S'); //string
            }

            if (Setting::get('open_print_dialog_labels')) {
                $mpdf->SetJS('this.print();');
            }

            $mpdf->debug = true;
            return $mpdf->Output('Etiquetas.pdf', 'I'); //output to screen

            exit;
        } catch (\Exception $e) {
            Shipment::whereIn('id', $shipmentsIds)->update(['is_printed' => false]);
            throw new \Exception($e->getMessage() . ' file ' . $e->getFile() . ' linha ' . $e->getLine());
        }
    }

    /**
     * Print transportation guides
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printTransportGuide($shipmentsIds, $shipments = null, $extraData = null, $grouped = false, $returnMode = 'pdf')
    {

        if (Setting::get('shipment_guide_type') == 'cmr') {
            return self::printCmr($shipmentsIds, $shipments = null, $extraData = null, $returnMode = 'pdf');
        }

        try {
            if (!$shipments) {
                $shipments = self::filterAgencies()
                    ->whereIn('id', $shipmentsIds)
                    ->get();
                if ($shipments->isEmpty()) {
                    return App::abort(404);
                }

                if (count($shipmentsIds) == 1 && $masterTrk = $shipments->first()->isGrouped()) { //devolve todas as guias no serviço agrupado
                    $shipments = Shipment::where(function ($q) use ($masterTrk) {
                        $q->where(function ($q) use ($masterTrk) {
                            $q->where('tracking_code', $masterTrk);
                            $q->where('children_type', 'M');
                        });
                        $q->orWhere(function ($q) use ($masterTrk) {
                            $q->where('type', 'M');
                            $q->where('parent_tracking_code', $masterTrk);
                        });
                    })
                        ->orderBy('id')
                        ->get();
                }
            }

            ini_set("memory_limit", "-1");

            if (Setting::get('shipment_guide_type') == 'guide_custom01') {
                $mpdf = new Mpdf([
                    'format'        => 'A4',
                    'margin_top'    => 0,
                    'margin_bottom' => 0,
                    'margin_left'   => 0,
                    'margin_right'  => 0,
                ]);
            } else {
                $mpdf = new Mpdf([
                    'format'        => 'A4',
                    'margin_top'    => 10,
                    'margin_bottom' => 10,
                    'margin_left'   => 20,
                    'margin_right'  => 20,
                ]);
            }

            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("ENOVO");
            $mpdf->shrink_tables_to_fit = 0;

            if ($grouped) {

                $volumes = 0;
                $weight = 0;
                foreach ($shipments as $key => $shipment) {
                    $volumes += $shipment->volumes;
                    $weight += $shipment->weight;
                }

                $shipment = $shipments->first();

                $shipment->volumes = $volumes;
                $shipment->weight = $weight;
                $shipment->recipient_name = 'Vários Destinatários';
                $shipment->recipient_address = 'Várias moradas';
                $shipment->recipient_city = 'Várias localidades';
                $shipments = collect([$shipment]);
            }

            foreach ($shipments as $key => $shipment) {

                $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
                $qrCode->disableBorder();
                $output = new \Mpdf\QrCode\Output\Png();
                $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 49));

                /*if ($shipment->webservice_method == 'ctt' && $shipment->volumes > 1) {
                    $doc = new Webservice\Base;
                    $file = $doc->getTransportationGuide($shipment);

                    $data = base64_decode($file);
                    header('Content-Type: application/pdf');
                    echo $data;
                    exit;
                } else {*/

                if (@$extraData['vehicle']) {
                    $vehicleKg = @$extraData['vehicle_kg'];
                    $vehicleUsefullKg = @$extraData['vehicle_kg_usefull'];
                } else {
                    $shipment->vehicle = $shipment->vehicle ? $shipment->vehicle : @$shipment->operator->vehicle;

                    $vehicle = self::getVehicle($shipment->vehicle);

                    $licensePlate = @$vehicle->license_plate;
                    $vehicleKg = @$vehicle->gross_weight;
                    $vehicleUsefullKg = @$vehicle->usefull_weight;
                }
                $defaultPackingType = 'DIVERSOS';
                if ($shipment->packaging_type) {
                    
                    if(!empty($shipment->packaging_type) && count($shipment->packaging_type) > 1) { //varios tipos de materiais
                        $defaultPackingDesc = '';
                        foreach ($shipment->packaging_type as $packCode => $packQty) {
                            $defaultPackingDesc.= $packQty.strtoupper($packCode).' ';
                        }
                    } else { //so um tipo de material
                        $type = @$shipment->pack_dimensions->first()->type;
                        if ($type) {
                            $defaultPackingType = strtoupper(trans('admin/global.packages_types.' . $type));
                        }
    
                        $defaultPackingDesc = str_limit(@$shipment->pack_dimensions->first()->description, 50);
                    }
                }

                if (empty($defaultPackingDesc)) {
                    $defaultPackingDesc = 'ITENS DIVERSOS';
                }

                $shipment->car_registration    = @$extraData['vehicle'] ? @$extraData['vehicle'] : $licensePlate;
                $shipment->vehicle_kg          = $vehicleKg;
                $shipment->vehicle_kg_usefull  = $vehicleUsefullKg;
                $shipment->packing             = @$extraData['packing'] ? @$extraData['packing'] : $defaultPackingType;
                $shipment->packing_description = @$extraData['description'] ? @$extraData['description'] : $defaultPackingDesc;

                $data = ['shipment' => $shipment];
                $guideType = empty(Setting::get('shipment_guide_type')) ? 'guide' : Setting::get('shipment_guide_type');
                $data['view'] = 'admin.printer.shipments.guides.' . $guideType;
                $data['customerAccount'] = false;
                $data['qrCode'] = $qrCode;
                for ($i = 0; $i < 3; $i++) {
                    $data['copy'] = $i + 1;
                    if ($i == 0) {
                        $data['copyId'] = 1;
                        $data['copyNumber'] = trans('admin/docs.all.original'); //'Cópia do Expedidor - ORIGINAL';
                    } else if ($i == 1) {
                        $data['copyId'] = 2;
                        $data['copyNumber'] = trans('admin/docs.all.second'); //'Cópia do Transportador - DUPLICADO';
                    } else if ($i == 2) {
                        $data['copyId'] = 3;
                        $data['copyNumber'] = trans('admin/docs.all.third'); //'Cópia do Destinatário - TRIPLICADO';
                    }


                    if (Setting::get('shipment_guide_type') == 'guide_goods') {
                        $mpdf->AddPage('P', '', '', '', '', 0, 0, 0, 0);
                    }
                    $mpdf->WriteHTML(view('admin.printer.shipments.layouts.transportation_guide', $data)->render()); //write
                }

                if (Setting::get('guides_show_conditions')) {
                    $data = [
                        'documentTitle'     => '',
                        'documentSubtitle'  => '',
                        'view'              => 'admin.printer.shipments.docs.geral_conditions',
                        'locale'            => 'pt'
                    ];
                    $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
                }
            }
            /*}*/
            if ($returnMode == 'string') {
                return $mpdf->Output('Guia Transporte.pdf', 'S'); //string
            }

            if (Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print()');
            }

            $mpdf->debug = true;

            if ($shipments->count() == 1) {
                return $mpdf->Output('Guia de Transporte - ' . @$shipment->first()->tracking_code . '.pdf', 'I'); //output to screen
            }
            return $mpdf->Output('Guias de Transporte.pdf', 'I'); //output to screen

            exit;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Print pickup manifest
     *
     * @param $pickupId
     * @return string
     */
    public static function printPickupManifest($pickupsIds, $pickups = null, $returnMode = 'pdf')
    {

        if (empty($pickups)) {
            $pickups = Shipment::filterAgencies()
                ->isPickup()
                ->whereIn('id', $pickupsIds)
                ->get();
        }

        if ($pickups->isEmpty()) {
            throw new \Exception('Nenhuma recolha selecionada.');
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 10,
            'margin_bottom' => 10,
            'margin_left'   => 20,
            'margin_right'  => 20,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [];
        foreach ($pickups as $key => $pickup) {
            $data['shipment'] = $pickup;
            $data['view']     =  'admin.printer.shipments.docs.pickup_manifest';
            for ($i = 0; $i < 2; $i++) {
                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.pickup_manifest', $data)->render());
            }
        }

        if ($returnMode == 'string') {
            return $mpdf->Output('Manifesto de Recolha.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        if ($pickups->count() == 1) {
            return $mpdf->Output('Manifesto de Recolha - ' . $pickups->first()->tracking_code . '.pdf', 'I'); //output to screen
        }

        return $mpdf->Output('Manifestos de Recolha.pdf', 'I'); //output to screen

        exit;
    }

     /**
     * Print e-CMR To international transport
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printECMR($shipmentsIds, $extraData = null, $returnMode = 'pdf')
    {
        $extraData = $extraData ?? [];
        $extraData['ecmr'] = 1;
        return Shipment::printCmr($shipmentsIds, $extraData, $returnMode);
    }

    /**
     * Print CMR To international transport
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printCmr($shipmentsIds, $extraData = null, $returnMode = 'pdf')
    {

        $shipments = self::filterAgencies()
            ->whereIn('id', $shipmentsIds)
            ->get();

        if ($shipments->isEmpty()) {
            return App::abort(404);
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 2.5,
            'margin_bottom' => 10,
            'margin_left'   => 20,
            'margin_right'  => 5,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("QuickBox");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shipments as $key => $shipment) {

            $data = ['shipment' => $shipment];

            if(@$extraData['ecmr']) { //e-CMR
                $pickupHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                ->where('status_id', ShippingStatus::SHIPMENT_PICKUPED) //RECOLHIDO
                ->orderBy('created_at', 'desc')
                ->first();

                $deliveryHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                    ->where('status_id', ShippingStatus::DELIVERED_ID) //ENTREGUE
                    ->orderBy('created_at', 'desc')
                    ->first();

                $data['pickupHistory']   = $pickupHistory;
                $data['deliveryHistory'] = $deliveryHistory;

                $data['view'] =  'admin.printer.shipments.guides.ecmr';
                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.cmr', $data)->render()); //write
            } else {
                //cmr convencional
                $data['view'] =  'admin.printer.shipments.guides.cmr';
                $data['customerAccount'] = false;

                for ($i = 0; $i < 4; $i++) {
                    $data['copy'] = $i + 1;

                    if ($i == 0) {
                        $data['copyDesignationPt'] = 'Expedidor';
                        $data['copyDesignationEn'] = 'Sender';
                    } else if ($i == 1) {
                        $data['copyDesignationPt'] = 'Destinatário';
                        $data['copyDesignationEn'] = 'Consignee';
                    } else if ($i >= 2) {
                        $data['copyDesignationPt'] = 'Transportador';
                        $data['copyDesignationEn'] = 'Transporter';
                    }
                    $mpdf->WriteHTML(view('admin.printer.shipments.layouts.cmr', $data)->render()); //write
                }
            }
        }

        if ($returnMode == 'string') {
            return $mpdf->Output('Declaração Expedição Internacional.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        if ($shipments->count() == 1) {
            return $mpdf->Output('CMR - ' . $shipment->first()->tracking_code . '.pdf', 'I'); //output to screen
        }
        return $mpdf->Output('Declaração Expedição Internacional.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print shipment proof
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printShipmentProof($shipmentsIds, $shipments = null, $extraData = null, $returnMode = 'pdf')
    {

        ini_set("pcre.backtrack_limit", "500000000");
        
        if (!$shipments) { //complementar_services
            $shipments = self::filterAgencies()
                ->with(['service' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Service::CACHE_TAG);
                }])
                ->with(['customer' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Customer::CACHE_TAG);
                }])
                ->with(['agency' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Agency::CACHE_TAG);
                }])
                ->whereIn('id', $shipmentsIds)
                ->get();

            if ($shipments->isEmpty()) {
                return App::abort(404);
            }
        }

        $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->get();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 20,
            'margin_bottom' => 0,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shipments as $key => $shipment) {

            $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
            $qrCode->disableBorder();
            $output = new \Mpdf\QrCode\Output\Png();
            $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 70));

            $data = [
                'shipment'      => $shipment,
                'complementarServices' => $complementarServices,
                'documentTitle' => 'Resumo de Serviço',
                'qrCode'        => $qrCode,
                'view'          => 'admin.printer.shipments.docs.proof'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write
        }

        if (Setting::get('shipment_proof_show_conditions')) {
            $data = [
                'documentTitle'     => '',
                'documentSubtitle'  => '',
                'view'              => 'admin.printer.shipments.docs.geral_conditions',
                'locale'            => 'pt'
            ];
            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
        }

        if ($returnMode == 'string' || $returnMode == 'S') {
            return $mpdf->Output('Comprovativo de Envio.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        if ($shipments->count() == 1) {
            return $mpdf->Output('Comprovativo de Envio - ' . $shipment->first()->tracking_code . '.pdf', 'I'); //output to screen
        }
        return $mpdf->Output('Comprovativo de Envio.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print shipments POD
     *
     * @param Request $request
     * @param null $shipments
     * @param $returnMode [i = output para o ecra | s=string]
     * @return \Illuminate\Http\Response|string
     */
    public static function printPod($shipmentsIds, $returnMode = 'I', $historyId = null)
    {
        $appMode    = Setting::get('app_mode');

        $shipments = Shipment::with('last_history', 'operator')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->whereIn('id', $shipmentsIds)
            ->get();

        ini_set("memory_limit", "-1");

        $mpdfOptions = [
            'format'        => [210, 99],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ];
        
        if($appMode == 'cargo') {

            $mpdfOptions = [
                'format'        => 'A4',
                'margin_top'    => 2.5,
                'margin_bottom' => 10,
                'margin_left'   => 20,
                'margin_right'  => 5,
            ];
        }

        $mpdf = new Mpdf($mpdfOptions);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO TMS");
        $mpdf->shrink_tables_to_fit = 0;

       

        foreach ($shipments as $shipment) {

            if ($historyId) {
                $shipment->last_history = ShipmentHistory::find($historyId);
            }

            $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
            $qrCode->disableBorder();
            $output = new \Mpdf\QrCode\Output\Png();
            $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 49));

            if($appMode == 'cargo') {

                $pickupHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                                    ->where('status_id', ShippingStatus::SHIPMENT_PICKUPED) //RECOLHIDO
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                $deliveryHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                                    ->where('status_id', ShippingStatus::DELIVERED_ID) //ENTREGUE
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                $data = [
                    'shipment'          => $shipment,
                    'pickupHistory'     => $pickupHistory,
                    'deliveryHistory'   => $deliveryHistory,
                    'qrCode'            => $qrCode,
                    'copy'              => 1,
                    'documentTitle'     => 'e-CMR #' . $shipment->tracking_code,
                    'documentSubtitle'  => '',
                    'view'              => 'admin.printer.shipments.guides.cmr'
                ];

                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.cmr', $data)->render()); //write

            } else if($shipment->last_history->status_id == \App\Models\ShippingStatus::SHIPMENT_PICKUPED){
                
                $data = [
                    'shipment'          => $shipment,
                    'qrCode'            => $qrCode,
                    'documentTitle'     => 'Prova Recolha - TRK' . $shipment->tracking_code,
                    'documentSubtitle'  => '',
                    'view'              => 'admin.printer.shipments.docs.pod_pickup'
                ];
                
                $mpdf->WriteHTML(view('admin.layouts.pdf_blank', $data)->render()); //write
            }else{
                
                $data = [
                    'shipment'          => $shipment,
                    'qrCode'            => $qrCode,
                    'documentTitle'     => 'Prova Entrega - TRK' . $shipment->tracking_code,
                    'documentSubtitle'  => '',
                    'view'              => 'admin.printer.shipments.docs.pod'
                ];
                
                $mpdf->WriteHTML(view('admin.layouts.pdf_blank', $data)->render()); //write
            }

            
        }


        $mpdf->debug = true;
        return $mpdf->Output('Prova de Entrega.pdf', $returnMode); //output to screen

        exit;
    }

    /**
     * Print Value Statement
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printValueStatement($shipmentsIds, $shipments = null, $extraData = null, $returnMode = 'pdf')
    {

        if (!$shipments) { //complementar_services
            $shipments = self::filterAgencies()
                ->with(['customer' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Customer::CACHE_TAG);
                }])
                ->whereIn('id', $shipmentsIds)
                ->get();

            if ($shipments->isEmpty()) {
                return App::abort(404);
            }
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 24,
            'margin_bottom' => 0,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shipments as $key => $shipment) {

            $data = [
                'shipment'      => $shipment,
                'documentTitle' => 'Declaração de Valores',
                'view'          => 'admin.printer.shipments.docs.value_statement'
            ];
            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write
        }

        if ($returnMode == 'string') {
            return $mpdf->Output('Declaração de Valores.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        if ($shipments->count() == 1) {
            return $mpdf->Output('Declaração de Valores - ' . $shipment->first()->tracking_code . '.pdf', 'I'); //output to screen
        }
        return $mpdf->Output('Declaração de Valores.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print shipping instructions
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printShippingInstructions($shipmentsIds, $shipments = null, $extraData = null, $output = 'I')
    {

        if (!$shipments) {
            $shipments = self::filterAgencies()
                ->with(['service' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Service::CACHE_TAG);
                }])
                ->with(['customer' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Customer::CACHE_TAG);
                }])
                ->with(['agency' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Agency::CACHE_TAG);
                }])
                ->whereIn('id', $shipmentsIds)
                ->get();

            if ($shipments->isEmpty()) {
                return App::abort(404);
            }
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 20,
            'margin_bottom' => 0,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO TMS");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shipments as $key => $shipment) {

            $addresses = null;

            if ($shipment->children_type == 'M') {
                $addresses = Shipment::where('parent_tracking_code', $shipment->tracking_code)->get();
            }

            $shipment->addresses      = $addresses;
            $shipment->loading_notes  = @$extraData['loading_notes'];
            $shipment->delivery_notes = @$extraData['delivery_notes'];

            $locale = $shipment->provider->locale;
            $locale = empty($locale) ? Setting::get('app_country') : $locale;

            $data = [
                'shipment'      => $shipment,
                'documentTitle' => transLocale('admin/global.word.charging_instructions', $locale),
                'page'          => 1,
                'view'          => 'admin.printer.shipments.docs.' . (Setting::get('charging_instructions_model') ? Setting::get('charging_instructions_model') : 'charging_instructions')
            ];
            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

            if (Setting::get('prices_table_general_conditions')) {
                $data['page'] = 2;
                $mpdf->AddPage();
                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write
            }
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        if ($shipments->count() == 1) {
            return $mpdf->Output('Instruções de Carga - ' . $shipment->first()->tracking_code . '.pdf', $output); //output to screen
        }
        return $mpdf->Output('Instruções de Carga.pdf', $output); //output to screen

        exit;
    }

    /**
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \Throwable
     */
    public static function printDeliveryMap($shipmentsGrouped, $params, $outputFormat = 'I')
    {

        $date         = @$params['date'];
        $manifestCode = @$params['code'];

        $bgExists = File::exists(public_path() . '/uploads/pdf/bg_v.png');

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 12,
            'margin_top'    => 30,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $lastOperator = null;
        foreach ($shipmentsGrouped as $operatorId => $shipments) {

            $operator    = @$shipments->first()->operator;
            $curOperator = @$operator->id;

            $data = [
                'shipments'         => $shipments,
                'documentTitle'     => 'Manifesto de Entrega' . ($manifestCode ? ' #' . $manifestCode : ''),
                'documentSubtitle'  => 'Operador ' . $operator ? @$operator->name . ' // ' . $date : 'Sem operador // ' . $date,
                'view' => 'admin.printer.shipments.docs.delivery_manifest'
            ];


            if (!empty($lastOperator) && $lastOperator != $curOperator) {

                $data['bgExists'] = $bgExists;
                $mpdf->defHTMLHeaderByName(
                    'pageHeader',
                    view('admin.layouts.pdf.header', $data)->render()
                );

                $mpdf->addPage();
            }

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

            $lastOperator = $curOperator;
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Manifesto de Entrega.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print cargo manifest
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printShipmentsCargoManifest($shipmentIds, $outputFormat = 'I', $groupBy = 'customer')
    {

        ini_set("pcre.backtrack_limit", "500000000");
        ini_set("memory_limit", "-1");

        $shipments = Shipment::with('expenses')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
            }])
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->filterAgencies()
            ->whereIn('id', $shipmentIds)
            ->orderBy('customer_id', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();


        switch ($groupBy) {
            case 'customers':
                $data = $shipments->groupBy('customer_id');
                $subtitle  = count($data) == 1 ? @$shipments->first()->customer->code . ' - ' . @$shipments->first()->customer->name : '';
                break;
            case 'providers':
                $data = $shipments->groupBy('provider_id');
                $subtitle  = count($data) == 1 ? @$shipments->first()->provider->code . ' - ' . @$shipments->first()->provider->name : '';
                break;
            default:
                $data = ['1' => $shipments];

                $totalCustomers = $shipments->groupBy('customer_id')->count();
                $subtitle  = $totalCustomers == 1 ? @$shipments->first()->customer->code . ' - ' . @$shipments->first()->customer->name : '';
                $subtitle .= '<br/><div style="font-size: 8pt">' . date('Y-m-d H:i') . '</div>';
                break;
        }

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 28,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;


        if ($groupBy == 'customers') {
            $page = 1;
            $dataItems = $data;
            foreach ($dataItems as $groupId => $groupItems) {

                $data = [
                    'customers'         => [$groupId => $groupItems],
                    'groupBy'           => $groupBy,
                    'documentTitle'     => trans('account/shipments.selected.print-manifest'), //'Manifesto de Carga',
                    'documentSubtitle'  => $subtitle,
                    'view'              => 'admin.printer.shipments.docs.cargo_manifest'
                ];

                $layout = 'pdf';

                if ($page > 1) {
                    $mpdf->addPage(); //nao adiciona pagina na 1ª interação
                }
                $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write
                $page++;
            }
        } else {
            $data = [
                'customers'         => $data,
                'groupBy'           => $groupBy,
                'documentTitle'     => trans('account/shipments.selected.print-manifest'), //'Manifesto de Carga',
                'documentSubtitle'  => $subtitle,
                'view'              => 'admin.printer.shipments.docs.cargo_manifest'
            ];

            if (config('app.source') == 'baltrans' && $groupBy == 'providers') {
                $data['view']              = 'admin.printer.shipments.docs.cargo_manifest_baltrans';
                $data['documentSubtitle'] .= '<div style="font-size: 8pt">' . date('Y-m-d H:i') . '</div>';
            }

            $layout = 'pdf';
            $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Manifesto de Carga.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print cargo manifest
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printShipmentsColdManifest($shipmentIds, $outputFormat = 'I', $groupByCustomer = true, $temperature = 21, $humidity = 15)
    {

        $shipments = Shipment::with('expenses')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
            }])
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->filterAgencies()
            ->whereIn('id', $shipmentIds)
            ->orderBy('customer_id', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($groupByCustomer) {
            $customers = $shipments->groupBy('customer_id');
        } else {
            $customers = ['1' => $shipments];
        }

        //construct pdf
        ini_set("pcre.backtrack_limit", "500000000000");
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 28,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'customers'         => $customers,
            'groupByCustomer'   => 1,
            'documentTitle'     => 'Manifesto de Frio e Humidade',
            'documentSubtitle'  => '',
            'view'              => 'admin.printer.shipments.docs.cold_manifest',
            'temperature'       => @$temperature,
            'humidity'          => @$humidity
        ];

        $layout = 'pdf';

        $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Manifesto de Carga.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print itenerary manifest
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printIteneraryManifest($shipmentIds, $outputFormat = 'I')
    {

        ini_set("pcre.backtrack_limit", "500000000");
        ini_set("memory_limit", "-1");

        $shipments = Shipment::with('expenses')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
            }])
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->filterAgencies()
            ->whereIn('id', $shipmentIds)
            ->orderBy('customer_id', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();


        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shipments as $shipment) {
            $data = [
                'documentTitle' => 'Mapa de Itenerário',
                'documentSubtitle' => 'Viagem ' . $shipment->tracking_code,
                'view' => 'admin.printer.shipments.docs.itenerary_manifest',
                'shipment' => $shipment
            ];

            $layout = 'pdf';

            $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Manifesto de Carga.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print shipments
     *
     * @param Request $request
     * @param null $shipments
     * @return \Illuminate\Http\Response|string
     */
    public static function printShipments($shipmentsIds, $outputFormat = 'I', $groupByCustomer = false)
    {

        $shipments = Shipment::with('expenses')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->whereIn('id', $shipmentsIds)
            ->orderBy('date', 'asc')
            ->get();


        $countCustomers = count(array_unique($shipments->pluck('customer_id')->toArray()));
        $minDate  = $shipments->min('date');
        $maxDate  = $shipments->max('date');

        $docTitle     = trans('admin/global.billing.pdf.title');
        $customerName = '';
        if ($countCustomers == 1) {
            $customer = @$shipments->first()->customer;
            $customerName = '<br/>' . @$customer->code . ' - ' . @$customer->name;
        }

        $docSubtitle  = '<span style="color: #000">' . trans('admin/global.word.period_of') . ' ' . $minDate . ' a ' . $maxDate . '</span>' . $customerName;

        if ($minDate == $maxDate) {
            $docSubtitle = trans('admin/global.word.service_of') . ' ' . $minDate . $customerName;
        }

        if ($groupByCustomer) {
            $shipments = $shipments->groupBy('customer.name');
        } else {
            $shipments = [@$customer->name => $shipments];
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'groupedResults'    => $shipments,
            'groupByCustomer'   => $groupByCustomer,
            'documentTitle'     => $docTitle,
            'documentSubtitle'  => $docSubtitle,
            'view'              =>  'admin.printer.shipments.docs.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Envios.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Print shipments
     *
     * @param Request $request
     * @param null $shipments
     * @return \Illuminate\Http\Response|string
     */
    public static function printGoodsManifest($shipmentsIds, $orderBy = 'date', $outputMode = 'I', $groupByCustomer = false)
    {

        $shipments = Shipment::with('pack_dimensions')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->whereIn('id', $shipmentsIds);

        if ($orderBy) {
            $shipments = $shipments->orderBy($orderBy, 'asc');
        } else {
            $shipments = $shipments->orderByRaw('FIND_IN_SET(id, "' . implode(',', $shipmentsIds) . '")');
        }

        $shipments = $shipments->get();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'shipments'         => $shipments,
            'documentTitle'     => 'Listagem de Artigos',
            'documentSubtitle'  => '',
            'view'              =>  'admin.printer.shipments.docs.goods_manifest'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Envios.pdf', $outputMode); //output to screen

        exit;
    }

    /**
     * Print Reimbursement Guide
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printReimbursementGuide($shipmentsIds, $useAgenciesLogo = true, $source = 'admin')
    {

        if ($source == 'customer') {
            $customer = Auth::guard('customer')->user();

            $shipments = self::whereIn('id', $shipmentsIds)
                ->where('customer_id', $customer->id)
                ->get();
        } else {
            $shipments = self::filterAgencies()
                ->whereIn('id', $shipmentsIds)
                ->get();
        }

        //construct pdf
        foreach ($shipments as $shipment) {

            if ($shipment->webservice_method && $shipment->submited_at) {

                $doc = new Webservice\Base;
                $doc = $doc->getReimbursementGuide($shipment);

                $data = base64_decode($doc);
                header('Content-Type: application/pdf');
                echo $data;
            }
        }
        exit;
    }

    /**
     * @param $shipmentsIds
     * @param string $outputMode
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \Throwable
     */
    public static function printDevolutionsSummary($shipmentsIds, $outputMode = 'I')
    {

        ini_set("memory_limit", "-1");

        $shipments = Shipment::filterAgencies()
            ->with('last_history')
            ->whereIn('id', $shipmentsIds)
            ->get();

        $subtitle = '';
        if ($shipments->groupBy('customer_id')->count() == 1) {
            $subtitle = @$shipments->first()->customer->code . ' - ' . @$shipments->first()->customer->name;
        }

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'shipments'         => $shipments,
            'documentTitle'     => 'Resumo de Devoluções',
            'documentSubtitle'  => $subtitle,
            'view'              => 'admin.printer.refunds.summary_devolutions'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo deDevoluções.pdf', $outputMode); //output to screen

        exit;
    }

    /**
     * Download excel file
     *
     * @param $data
     * @param null $filename
     * @param bool $exportString [true|false] Return excel as base64 string
     * @return bool|string
     */
    public static function exportExcel($data, $filename = null, $exportString = false, $ignoreFields = [], $docSource = 'admin', $alternative = false)
    {

        ini_set("memory_limit", "-1");

        $docSource = empty($docSource) ? 'admin' : $docSource;
        $maxRows = 5000;

        if ($data->count() > 5000) {
            throw new Exception('Só é permitido exportar um máximo de ' . $maxRows . ' registos de cada vez.');
        }

        $filename = $filename ? $filename : 'Listagem de Envios';

        $user = Auth::user();
        if ($user) {
            $customer = null;
            $myAgencies = $user->agencies;
        } else {
            $customer = Auth::guard('customer')->user();
            $myAgencies = [$customer->agency_id];
        }


        $shipmentTypes = [
            Shipment::TYPE_PICKUP       => 'RECOLHA',
            Shipment::TYPE_MASTER       => 'AGRUPADO',
            Shipment::TYPE_RECANALIZED  => 'RECANALIZAÇÃO',
            Shipment::TYPE_RETURN       => 'RETORNO',
            Shipment::TYPE_DEVOLUTION   => 'DEVOLUCAO',
            Shipment::TYPE_SHIPMENT     => 'ENVIO',
        ];

        //get adicional expenses to include on excel file
        $expenses = ShippingExpense::filterSource()
            ->whereIn('type', [
                ShippingExpense::TYPE_WAINTING_TIME,
                ShippingExpense::TYPE_OUT_HOUR,
                ShippingExpense::TYPE_PEAGES,
                ShippingExpense::TYPE_RETURN
            ])
            ->pluck('name', 'id')
            ->toArray();


        $header = [];
        $header[] = 'Tipo';
        $header[] = 'TRK';
        if ($docSource == 'admin' || Setting::get('customer_show_provider_trk')) {
            $header[] = 'TRK Secundário';
        }
        $header[] = 'Data Carga';
        $header[] = 'Data Descarga';
        $header[] = 'Cod. Serviço';
        $header[] = 'Serviço';
        /* $header[] = 'Hora Início';
         $header[] = 'Hora Fim';*/
        $header[] = 'Referência';

        if (Setting::get('shipments_reference2')) {
            $header[] = Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Referência 2';
        }

        if (Setting::get('shipments_reference3')) {
            $header[] = Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Referência 3';
        }

        if ($docSource == 'admin') {
            $header[] = 'Agência Recolha';
            $header[] = 'Agência Destino';
            $header[] = 'Fornecedor';
        }


        $header[] = 'Cod. Cliente';
        $header[] = 'Nome Cliente';
        $header[] = 'Departamento';
        if (Setting::get('shipments_requester_name')) {
            $header[] = 'Solicitado Por';
        }
        $header[] = 'A/C remetente';
        $header[] = 'Remetente';
        $header[] = 'Morada Remetente';
        $header[] = 'CP Remetente';
        $header[] = 'Localidade Remetente';
        $header[] = 'Pais Remetente';
        $header[] = 'Contacto Remetente';
        $header[] = 'A/C Destinatário';
        $header[] = 'Destinatário';
        $header[] = 'Morada Destinatário';
        $header[] = 'CP Destinatário';
        $header[] = 'Localidade Destinatário';
        $header[] = 'País Destinatário';
        $header[] = 'Contacto Destinatário';
        $header[] = 'E-mail Destinatário';
        $header[] = 'Volumes';
        $header[] = 'Peso (Kg)';
        $header[] = 'Peso Vol. (Kg)';
        $header[] = 'Kms';
        $header[] = 'Cobrança';
        $header[] = 'Retorno';
        $header[] = 'Último Estado';
        $header[] = 'Data Último Estado';
        $header[] = 'Motivo Incidência';
        $header[] = 'Observações Estado';
        $header[] = 'Data Recolha';
        $header[] = 'Data Distribuição';
        $header[] = 'Data Entrega';
        $header[] = 'Nome Receptor';

        if ($docSource == 'admin') {
            $header[] = 'Cód. Motorista';
            $header[] = 'Nome Motorista';
        }

        $header[] = 'Solicitado por';
        $header[] = 'Observações Carga';
        $header[] = 'Observações Descarga';
        $header[] = 'Viatura';
        $header[] = 'Reboque';

        if ($docSource == 'admin') {
            $header[] = 'Obs. Internas';
        }

        if ($user && Auth::user()->showPrices() && !in_array('cost_price', $ignoreFields)) {
            $header[] = 'Preço Custo';
        }

        $settingsShippingExpenses = Setting::get('export_shipping_expenses') ?? [];
        $shippingExpenses = ShippingExpense::ordered()
            ->whereIn('id', $settingsShippingExpenses)
            ->get();

        if ((($user && Auth::user()->showPrices()) || $docSource == 'customer' && @$customer->show_billing) && !in_array('price', $ignoreFields)) {
            $header[] = 'Preço';

            if ($settingsShippingExpenses && $alternative) {
                foreach ($shippingExpenses as $shippingExpense) {
                    $header[] = $shippingExpense->name;
                }
            }

            $header[] = 'Encargos';
            $header[] = 'Taxa Combustível';
            $header[] = 'Total';
        }

        $header[] = 'Taxa IVA';
        $header[] = 'Data de Registo em Sistema';
        $header[] = 'Data de Entrada Armazém';

        $rowCounter = 1;
        $excel = Excel::create($filename, function ($file) use (&$rowCounter, $data, $header, $myAgencies, $expenses, $shipmentTypes, $ignoreFields, $user, $customer, $docSource, $settingsShippingExpenses, $shippingExpenses, $alternative) {

            $file->sheet('Listagem', function ($sheet) use (&$rowCounter, $data, $header, $myAgencies, $expenses, $shipmentTypes, $ignoreFields, $user, $customer, $docSource, $settingsShippingExpenses, $shippingExpenses, $alternative) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                $sheet->setColumnFormat(array(
                    'B' => '@', //tracking
                    'C' => '@', //tracking
                    'L' => '@', //codigo servico
                    'R' => '@', //codigo postal
                    'Y' => '@', //codigo postal
                    'AD' => '0.00', //weight
                    'AE' => '0.00', //kms
                    'AJ' => '0.00', //price,
                    'AK' => '0.00', //price,
                    'AL' => '0.00', //price,
                    'AM' => '0.00', //subtotal PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
                ));

                foreach ($data as $shipment) {

                    $shippingDate = new Carbon($shipment->shipping_date);
                    $shippingDate = $shippingDate->format('Y-m-d H:i');

                    $deliveryDate = '';
                    if ($shipment->delivery_date != null && $shipment->delivery_date != "") {
                        $deliveryDate = new Carbon($shipment->delivery_date);
                        $deliveryDate = $deliveryDate->format('Y-m-d H:i');
                    }

                    $totalPrice = '';
                    if ($customer || ($user && Auth::user()->isAdmin() || (!empty($myAgencies) && in_array($shipment->agency_id, $myAgencies)))) {  //show total price only if sender agency_id is one of the user agencies
                        $totalPrice = $shipment->billing_subtotal;
                    }

                    $shipmentPrice = '';
                    if ($shipment->shipping_price > 0.00) {
                        $shipmentPrice = $shipment->shipping_price;
                    }

                    $expensesPrice = 0;
                    if ($shipment->expenses_price > 0.00) {
                        $expensesPrice = $shipment->expenses_price;
                    }

                    $costPrice = '';
                    if ($shipment->cost_billing_subtotal > 0.00) {
                        $costPrice = $shipment->cost_billing_subtotal;
                    }

                    $vatRate = $shipment->vat_rate ? $shipment->vat_rate : $shipment->getVatRate(true);

                    if ($shipment->cod == 'D' || $shipment->cod == 'S') {
                        $totalPrice = '';
                    }

                    if (in_array('price', $ignoreFields)) {
                        $totalPrice = '';
                        $vatRate = '';
                    }

                    $totalPrice = $totalPrice > 0.00 ? $totalPrice : '';
                    $vatRate    = $totalPrice > 0.00 ? $vatRate : '';

                    $rowData = [];
                    $rowData[] = @$shipmentTypes[$shipment->type] ? $shipmentTypes[$shipment->type] : 'ENVIO';
                    $rowData[] = $shipment->tracking_code . ' ';
                    if ($docSource == 'admin' || Setting::get('customer_show_provider_trk')) {
                        $rowData[] = $shipment->provider_tracking_code . ' ';
                    }

                    $rowData[] = $shippingDate;
                    /*$rowData[] = $shipment->start_hour ? $shipment->start_hour : '';
                    $rowData[] = $shipment->end_hour ? $shipment->end_hour : '';*/
                    $rowData[] = $deliveryDate;
                    $rowData[] = @$shipment->service->display_code;
                    $rowData[] = @$shipment->service->name;

                    $rowData[] = $shipment->reference . ' ';
                    if (Setting::get('shipments_reference2')) {
                        $rowData[] = $shipment->reference2;
                    }
                    if (Setting::get('shipments_reference3')) {
                        $rowData[] = $shipment->reference3;
                    }

                    if ($docSource == 'admin') {
                        $rowData[] = @$shipment->senderAgency->name;
                        $rowData[] = @$shipment->recipientAgency->name;
                        $rowData[] = in_array('provider_id', $ignoreFields) ? '' : @$shipment->provider->name;
                    }

                    $rowData[] = $shipment->customer ? @$shipment->customer->code : '';
                    $rowData[] = $shipment->customer ? @$shipment->customer->name : '';
                    $rowData[] = $shipment->department_id ? @$shipment->department->name : '';
                    if (Setting::get('shipments_requester_name')) {
                        $rowData[] = $shipment->requester_name;
                    }
                    $rowData[] = $shipment->sender_attn;
                    $rowData[] = $shipment->sender_name;
                    $rowData[] = $shipment->sender_address;
                    $rowData[] = $shipment->sender_zip_code;
                    $rowData[] = $shipment->sender_city;
                    $rowData[] = $shipment->sender_country;
                    $rowData[] = $shipment->sender_phone;
                    $rowData[] = $shipment->recipient_attn;
                    $rowData[] = $shipment->recipient_name;
                    $rowData[] = $shipment->recipient_address;
                    $rowData[] = $shipment->recipient_zip_code;
                    $rowData[] = $shipment->recipient_city;
                    $rowData[] = $shipment->recipient_country;
                    $rowData[] = $shipment->recipient_phone;
                    $rowData[] = $shipment->recipient_email;
                    $rowData[] = $shipment->volumes;
                    $rowData[] = $shipment->weight;
                    $rowData[] = $shipment->volumetric_weight;
                    $rowData[] = $shipment->kms;
                    $rowData[] = $shipment->charge_price ? money($shipment->charge_price) : '';
                    $rowData[] = ''; //APAGAR QUANDO POSSIVEL. ATENCAO AS LINHAS DE ENCARGO
                    $rowData[] = @$shipment->status->name;
                    $rowData[] = @$shipment->lastHistory->created_at;
                    $rowData[] = @$shipment->lastIncidence->incidence->name;
                    $rowData[] = @$shipment->lastHistory->obs;
                    $rowData[] = @$shipment->pickuped_date;
                    $rowData[] = @$shipment->distribution_date;
                    $rowData[] = @$shipment->delivered_date ? @$shipment->delivered_date : (@$shipment->lastHistory->status_id == ShippingStatus::DELIVERED_ID ? @$shipment->lastHistory->created_at : '');
                    $rowData[] = @$shipment->lastHistory->receiver;

                    if ($docSource == 'admin') {
                        $rowData[] = @$shipment->operator->code;
                        $rowData[] = @$shipment->operator->name;
                    }

                    $rowData[] = $shipment->requester_name;
                    $rowData[] = $shipment->obs . ' ' . ($shipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? '#### RECOLHA FALHADA ####' : '');
                    $rowData[] = $shipment->obs_delivery;
                    $rowData[] = $shipment->vehicle;
                    $rowData[] = $shipment->trailer;

                    if ($docSource == 'admin') {
                        $rowData[] = $shipment->obs_internal;
                    }

                    if ($user && Auth::user()->showPrices() && !in_array('cost_price', $ignoreFields)) {
                        $rowData[] = $costPrice;
                    }

                    if ((($user && Auth::user()->showPrices()) || $docSource == 'customer' && @$customer->show_billing) && !in_array('price', $ignoreFields)) {
                        $rowData[] = $shipmentPrice;

                        if ($settingsShippingExpenses && $alternative) {
                            foreach ($shippingExpenses as $shippingExpense) {
                                $shipmentExpense = $shipment->expenses()->where('expense_id', $shippingExpense->id)->first();

                                if (!$shipmentExpense || !$shipmentExpense->pivot) {
                                    $rowData[] = 0;
                                    continue;
                                }

                                $rowData[]      = $shipmentExpense->pivot->subtotal ?? 0;
                                $expensesPrice -= $shipmentExpense->pivot->subtotal ?? 0;
                            }
                        }

                        $rowData[] = excelNumber($expensesPrice);
                        $rowData[] = $shipment->fuel_tax;
                        $rowData[] = $totalPrice;
                    }

                    $rowData[] = $vatRate;

                    $registerSystem = "";
                    $enterInArmazem = "";
                    $registerSystem = $shipment->created_at;
                    foreach ($shipment->history as $row) {
                        // if($row->status_id == 5){
                        //     $registerSystem = $row->created_at;
                        // }
                        if ($row->status_id == 17) {
                            $enterInArmazem = $row->created_at;
                        }
                    }
                    $rowData[] = $registerSystem;
                    $rowData[] = $enterInArmazem;

                    $sheet->appendRow($rowData);
                    $rowCounter++;


                    if (Setting::get('billing_customers_excel_expenses') && !empty($shipment->expenses) && !$alternative) {

                        foreach ($shipment->expenses as $expense) {

                            $expenseData = [
                                'ENCARGO',
                                $shipment->tracking_code,
                            ];

                            if ($docSource == 'admin' || Setting::get('customer_show_provider_trk')) {
                                $expenseData[] =  '';
                            }

                            $expenseData[] =  $expense->pivot->date;
                            $expenseData[] =  '';
                            $expenseData[] =  $expense->code;
                            $expenseData[] =  $expense->name;
                            $expenseData[] =  ''; //reference

                            if (Setting::get('shipments_reference2')) {
                                $expenseData[] = '';
                            }
                            if (Setting::get('shipments_reference3')) {
                                $expenseData[] = '';
                            }

                            if ($docSource == 'admin') {
                                $expenseData[] = '';
                                $expenseData[] = '';
                                $expenseData[] = '';
                            }

                            $expenseData[] = $shipment->customer ? @$shipment->customer->code : '';
                            $expenseData[] = $shipment->customer ? @$shipment->customer->name : '';
                            $expenseData[] = $shipment->department_id ? @$shipment->department->name : '';
                            if (Setting::get('shipments_requester_name')) {
                                $expenseData[] = '';
                            }
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = ''; //volumes
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';

                            if ($docSource == 'admin') {
                                $expenseData[] = @$shipment->operator->code;
                                $expenseData[] = @$shipment->operator->name;
                            }

                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';
                            $expenseData[] = '';

                            if ($docSource == 'admin') {
                                $expenseData[] = '';
                            }

                            if ($user && Auth::user()->showPrices() && !in_array('cost_price', $ignoreFields)) {
                                $expenseData[] = @$expense->pivot->cost_price;
                            }

                            if ((($user && Auth::user()->showPrices()) || $docSource == 'customer' && @$customer->show_billing) && !in_array('price', $ignoreFields)) {
                                $expenseData[] = @$expense->pivot->subtotal;
                                $expenseData[] = '';
                                $expenseData[] = '';
                                $expenseData[] = '';
                            }
                            $expenseData[] = $vatRate;


                            $sheet->appendRow($expenseData);
                            $rowCounter++;

                            $sheet->row($rowCounter, function ($row) {
                                //$row->setBackground('#eeeeee');
                                $row->setFontColor('#777777');
                            });
                        }

                        $shipment->expenses;
                    }
                }
            });
        });

        if ($exportString) {
            return file_get_contents($excel->store("xlsx", false, true)['full']);
        }

        $excel->export('xlsx');
    }


    /**
     * Download excel file
     *
     * @param $data
     * @param null $filename
     * @param bool $exportString [true|false] Return excel as base64 string
     * @return bool|string
     */
    public static function exportExcelDimensions($data, $filename = null, $exportString = false, $ignoreFields = [], $docSource = 'admin', $alternative = false)
    {

        ini_set("memory_limit", "-1");

        $docSource = empty($docSource) ? 'admin' : $docSource;
        $maxRows = 5000;

        if ($data->count() > 5000) {
            throw new Exception('Só é permitido exportar um máximo de ' . $maxRows . ' registos de cada vez.');
        }

        $filename = $filename ? $filename : 'Listagem de Envios';

        $user = Auth::user();
        if ($user) {
            $customer = null;
            $myAgencies = $user->agencies;
        } else {
            $customer = Auth::guard('customer')->user();
            $myAgencies = [$customer->agency_id];
        }
        
        $header = [];
        //$header[] = 'Tipo';
        $header[] = 'TRK';
        if ($docSource == 'admin' || Setting::get('customer_show_provider_trk')) {
            $header[] = 'TRK Provider';
        }
                        
        $header[] = 'Data Recolha';
        $header[] = 'Data Descarga';
        $header[] = 'Cod. Serviço';
        $header[] = 'Serviço';
        /* $header[] = 'Hora Início';
         $header[] = 'Hora Fim';*/
        $header[] = 'Referência';

        if (Setting::get('shipments_reference2')) {
            $header[] = Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Referência 2';
        }

        if (Setting::get('shipments_reference3')) {
            $header[] = Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Referência 3';
        }

       /*  if ($docSource == 'admin') {
            $header[] = 'Agência Recolha';
            $header[] = 'Agência Destino';
            $header[] = 'Fornecedor';
        } */


        $header[] = 'Cod. Cliente';
        $header[] = 'Nome Cliente';
        $header[] = 'Departamento';
        if (Setting::get('shipments_requester_name')) {
            $header[] = 'Solicitado Por';
        }
        $header[] = 'Remetente';
        $header[] = 'Morada Remetente';
        $header[] = 'CP Remetente';
        $header[] = 'Localidade Remetente';
        $header[] = 'Pais Remetente';
        $header[] = 'Contacto Remetente';
        $header[] = 'Destinatário';
        $header[] = 'Morada Destinatário';
        $header[] = 'CP Destinatário';
        $header[] = 'Localidade Destinatário';
        $header[] = 'País Destinatário';
        $header[] = 'Contacto Destinatário';
        $header[] = 'E-mail Destinatário';
        $header[] = 'Qtd';
        $header[] = 'SKU';
        $header[] = 'Descrição';
        $header[] = 'Peso';
        $header[] = 'Comprimento';
        $header[] = 'Largura';
        $header[] = 'Altura';
        $header[] = 'M3';
        $header[] = 'Montagem';
        $header[] = 'Cobrança';
        $header[] = 'Retorno';
        $header[] = 'Último Estado';
        $header[] = 'Data Último Estado';
        $header[] = 'Motivo Incidência';
        $header[] = 'Observações Estado';
        $header[] = 'Data Recolha';
        $header[] = 'Data Distribuição';
        $header[] = 'Data Entrega';
        $header[] = 'Nome Receptor';

        if ($docSource == 'admin') {
            $header[] = 'Cód. Motorista';
            $header[] = 'Nome Motorista';
        }

        $header[] = 'Solicitado por';
        $header[] = 'Observações Carga';
        $header[] = 'Observações Descarga';
        $header[] = 'Viatura';
        $header[] = 'Reboque';

        if ($docSource == 'admin') {
            $header[] = 'Obs. Internas';
        }

        if ($user && Auth::user()->showPrices() && !in_array('cost_price', $ignoreFields)) {
            $header[] = 'Preço Custo';
        }

        $settingsShippingExpenses = Setting::get('export_shipping_expenses') ?? [];
        $shippingExpenses = ShippingExpense::ordered()
            ->whereIn('id', $settingsShippingExpenses)
            ->get();

        if ((($user && Auth::user()->showPrices()) || $docSource == 'customer' && @$customer->show_billing) && !in_array('price', $ignoreFields)) {
            $header[] = 'Preço';

            if ($settingsShippingExpenses && $alternative) {
                foreach ($shippingExpenses as $shippingExpense) {
                    $header[] = $shippingExpense->name;
                }
            }

            $header[] = 'Encargos';
            $header[] = 'Taxa Combustível';
            $header[] = 'Total';
        }

        $header[] = 'Taxa IVA';
        $header[] = 'Data de Registo em Sistema';
        $header[] = 'Data de Entrada Armazém';

        $rowCounter = 1;
        $excel = Excel::create($filename, function ($file) use (&$rowCounter, $data, $header, $myAgencies, $ignoreFields, $user, $customer, $docSource, $settingsShippingExpenses, $shippingExpenses, $alternative) {

            $file->sheet('Listagem', function ($sheet) use (&$rowCounter, $data, $header, $myAgencies, $ignoreFields, $user, $customer, $docSource, $settingsShippingExpenses, $shippingExpenses, $alternative) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                $sheet->setColumnFormat(array(
                    'B' => '@', //tracking
                    'C' => '@', //tracking
                    'L' => '@', //codigo servico
                    'R' => '@', //codigo postal
                    'Y' => '@', //codigo postal
                    'AD' => '0.00', //weight
                    'AE' => '0.00', //kms
                    'AJ' => '0.00', //price,
                    'AK' => '0.00', //price,
                    'AL' => '0.00', //price,
                    'AM' => '0.00', //subtotal PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
                ));

                foreach ($data as $shipment) {

                    $shippingDate = new Carbon($shipment->shipping_date);
                    $shippingDate = $shippingDate->format('Y-m-d H:i');

                    $deliveryDate = '';
                    if ($shipment->delivery_date != null && $shipment->delivery_date != "") {
                        $deliveryDate = new Carbon($shipment->delivery_date);
                        $deliveryDate = $deliveryDate->format('Y-m-d H:i');
                    }

                    $totalPrice = '';
                    if ($customer || ($user && Auth::user()->isAdmin() || (!empty($myAgencies) && in_array($shipment->agency_id, $myAgencies)))) {  //show total price only if sender agency_id is one of the user agencies
                        $totalPrice = $shipment->billing_subtotal;
                    }

                    $shipmentPrice = '';
                    if ($shipment->shipping_price > 0.00) {
                        $shipmentPrice = $shipment->shipping_price;
                    }

                    $expensesPrice = 0;
                    if ($shipment->expenses_price > 0.00) {
                        $expensesPrice = $shipment->expenses_price;
                    }

                    $costPrice = '';
                    if ($shipment->cost_billing_subtotal > 0.00) {
                        $costPrice = $shipment->cost_billing_subtotal;
                    }

                    $vatRate = $shipment->vat_rate ? $shipment->vat_rate : $shipment->getVatRate(true);

                    if ($shipment->cod == 'D' || $shipment->cod == 'S') {
                        $totalPrice = '';
                    }

                    if (in_array('price', $ignoreFields)) {
                        $totalPrice = '';
                        $vatRate = '';
                    }

                    $totalPrice = $totalPrice > 0.00 ? $totalPrice : '';
                    $vatRate    = $totalPrice > 0.00 ? $vatRate : '';

            
                    if($shipment->pack_dimensions->isEmpty()) {
                         $dimensions = [(object)[
                            "type"        => "box",
                            "qty"         => $shipment->volumes,
                            "weight"      => $shipment->weight,
                            "length"      => "0.00",
                            "width"       => "0.00",
                            "height"      => "0.00",
                            "volume"      => "0.00",
                            "description" => "",
                            "adr_class"   => null,
                            "adr_letter"  => null,
                            "adr_number"  => null,
                            "price"       => null,
                            "total_price" => null,
                            "total_cost"  => null,
                            "product_id"  => "",
                            "sku"         => "",
                            "serial_no"   => null,
                            "lote"        => null,
                            "optional_fields" => null
                        ]];
                    
                    } else {
                        $dimensions = $shipment->pack_dimensions;
                    }
                   

                    foreach($dimensions as $dimension) {

                        $rowData = [];
                        //$rowData[] = @$shipmentTypes[$shipment->type] ? $shipmentTypes[$shipment->type] : 'ENVIO';
                        $rowData[] = $shipment->tracking_code . ' ';

                        if ($docSource == 'admin' || Setting::get('customer_show_provider_trk')) {
                            $rowData[] = $shipment->provider_tracking_code . ' ';
                        }

                        $rowData[] = $shippingDate;
                        $rowData[] = $deliveryDate;
                        /*$rowData[] = $shipment->start_hour ? $shipment->start_hour : '';
                        $rowData[] = $shipment->end_hour ? $shipment->end_hour : '';*/
                        
                        $rowData[] = @$shipment->service->display_code;
                        $rowData[] = @$shipment->service->name;

                        $rowData[] = $shipment->reference . ' ';
                        if (Setting::get('shipments_reference2')) {
                            $rowData[] = $shipment->reference2;
                        }
                        if (Setting::get('shipments_reference3')) {
                            $rowData[] = $shipment->reference3;
                        }

                    /*  if ($docSource == 'admin') {
                            $rowData[] = @$shipment->senderAgency->name;
                            $rowData[] = @$shipment->recipientAgency->name;
                            $rowData[] = in_array('provider_id', $ignoreFields) ? '' : @$shipment->provider->name;
                        } */

                        $rowData[] = $shipment->customer ? @$shipment->customer->code : '';
                        $rowData[] = $shipment->customer ? @$shipment->customer->name : '';
                        $rowData[] = $shipment->department_id ? @$shipment->department->name : '';
                        if (Setting::get('shipments_requester_name')) {
                            $rowData[] = $shipment->requester_name;
                        }
                        $rowData[] = $shipment->sender_name;
                        $rowData[] = $shipment->sender_address;
                        $rowData[] = $shipment->sender_zip_code;
                        $rowData[] = $shipment->sender_city;
                        $rowData[] = $shipment->sender_country;
                        $rowData[] = $shipment->sender_phone;
                        $rowData[] = $shipment->recipient_name;
                        $rowData[] = $shipment->recipient_address;
                        $rowData[] = $shipment->recipient_zip_code;
                        $rowData[] = $shipment->recipient_city;
                        $rowData[] = $shipment->recipient_country;
                        $rowData[] = $shipment->recipient_phone;
                        $rowData[] = $shipment->recipient_email;
                        $rowData[] = $dimension->qty;
                        $rowData[] = $dimension->sku;
                        $rowData[] = $dimension->description;
                        $rowData[] = $dimension->weight;
                        $rowData[] = $dimension->width;
                        $rowData[] = $dimension->length;
                        $rowData[] = $dimension->height;
                        $rowData[] = $dimension->volume;
                        $rowData[] = @$dimension->optional_fields['Montagem'] ? 'S' : '';
                        $rowData[] = $shipment->charge_price ? money($shipment->charge_price) : '';
                        $rowData[] = ''; //APAGAR QUANDO POSSIVEL. ATENCAO AS LINHAS DE ENCARGO
                        $rowData[] = @$shipment->status->name;
                        $rowData[] = @$shipment->lastHistory->created_at;
                        $rowData[] = @$shipment->lastIncidence->incidence->name;
                        $rowData[] = @$shipment->lastHistory->obs;
                        $rowData[] = @$shipment->pickuped_date;
                        $rowData[] = @$shipment->distribution_date;
                        $rowData[] = @$shipment->delivered_date ? @$shipment->delivered_date : (@$shipment->lastHistory->status_id == ShippingStatus::DELIVERED_ID ? @$shipment->lastHistory->created_at : '');
                        $rowData[] = @$shipment->lastHistory->receiver;

                        if ($docSource == 'admin') {
                            $rowData[] = @$shipment->operator->code;
                            $rowData[] = @$shipment->operator->name;
                        }

                        $rowData[] = $shipment->requester_name;
                        $rowData[] = $shipment->obs . ' ' . ($shipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? '#### RECOLHA FALHADA ####' : '');
                        $rowData[] = $shipment->obs_delivery;
                        $rowData[] = $shipment->vehicle;
                        $rowData[] = $shipment->trailer;

                        if ($docSource == 'admin') {
                            $rowData[] = $shipment->obs_internal;
                        }

                        if ($user && Auth::user()->showPrices() && !in_array('cost_price', $ignoreFields)) {
                            $rowData[] = $costPrice;
                        }

                        if ((($user && Auth::user()->showPrices()) || $docSource == 'customer' && @$customer->show_billing) && !in_array('price', $ignoreFields)) {
                            $rowData[] = $shipmentPrice;

                            if ($settingsShippingExpenses && $alternative) {
                                foreach ($shippingExpenses as $shippingExpense) {
                                    $shipmentExpense = $shipment->expenses()->where('expense_id', $shippingExpense->id)->first();

                                    if (!$shipmentExpense || !$shipmentExpense->pivot) {
                                        $rowData[] = 0;
                                        continue;
                                    }

                                    $rowData[]      = $shipmentExpense->pivot->subtotal ?? 0;
                                    $expensesPrice -= $shipmentExpense->pivot->subtotal ?? 0;
                                }
                            }

                            $rowData[] = excelNumber($expensesPrice);
                            $rowData[] = $shipment->fuel_tax;
                            $rowData[] = $totalPrice;
                        }

                        $rowData[] = $vatRate;

                        $registerSystem = "";
                        $enterInArmazem = "";
                        $registerSystem = $shipment->created_at;
                        foreach ($shipment->history as $row) {
                            // if($row->status_id == 5){
                            //     $registerSystem = $row->created_at;
                            // }
                            if ($row->status_id == 17) {
                                $enterInArmazem = $row->created_at;
                            }
                        }
                        $rowData[] = $registerSystem;
                        $rowData[] = $enterInArmazem;

                        $sheet->appendRow($rowData);
                        $rowCounter++;
                    }

                    $shipment->expenses;
                    
                }
            });
        });

        if ($exportString) {
            return file_get_contents($excel->store("xlsx", false, true)['full']);
        }

        $excel->export('xlsx');
    }

    /**
     * Append keyword to list of shipment keywords
     */
    public function addKeyword($key, $value) {
       
        $keywords = json_decode($this->keywords, true);
        
        if($keywords && isset($keywords[$key])) {
            $keywords[$key] = $value;
        } else {
            $keywords = [$key => $value];
        }

        $this->keywords = json_encode($keywords);

        return $this->keywords;
    }

    /**
     * Remove keyword key from shipment
     */
    public function removeKeyword($key) {
       
        $keywords = json_decode($this->keywords, true);
    
        unset($keywords[$key]);

        $this->keywords = json_encode($keywords);

        return $this->keyword;
    }

    /**
     * Return provider weight
     *
     * @param $originalWeight
     * @return float|int
     */
    public static function getProvierWeight($originalWeight)
    {

        if ($originalWeight < 2.00) {
            $kg = 1.00;
        } else if (valueBetween($originalWeight, 2.00, 4.99)) {
            $kg = 2.00;
        } else if (valueBetween($originalWeight, 5.00, 9.99)) {
            $kg = 5.00;
        } else if (valueBetween($originalWeight, 10.00, 14.99)) {
            $kg = 10.00;
        } else if (valueBetween($originalWeight, 15.00, 19.99)) {
            $kg = 15.00;
        } else if (valueBetween($originalWeight, 20.00, 24.99)) {
            $kg = 20.00;
        } else if (valueBetween($originalWeight, 25.00, 29.99)) {
            $kg = 25.00;
        } else if (valueBetween($originalWeight, 30.00, 34.99)) {
            $kg = 30.00;
        } else if (valueBetween($originalWeight, 30.00, 39.99)) {
            $kg = 35.00;
        } else {
            $kg = $originalWeight - 10;
        }

        return $kg;
    }


    /**
     * Detect recipient agency by given zip code
     *
     * @param type $zipCode
     * @return boolean
     */
    public static function getAgencyByZipCode($zipCode, $providerId = null, $country = null, $service = null, $senderZipCode = null, $recipientZipCode = null, $shipment = null)
    {

        $originalCountry   = $country;
        $appDefaultCountry = Setting::get('app_country');

        $originalZp = $zipCode;
        $zipCodeExtension = null;
        if (str_contains($originalZp, '-')) {
            $zipCode = explode('-', trim($zipCode));
            if (strlen($zipCode[0]) == 2 || $zipCode[0] == 'L' || $zipCode[0] == 'LV') {
                $zipCode = trim($zipCode[0] . '-' . $zipCode[1]);
                $zipCodeExtension = null;
            } else {
                $zipCodeExtension = trim(@$zipCode[1]);
                $zipCode = trim($zipCode[0]);

                if ($zipCodeExtension) {
                    $extensionLength = strlen($zipCodeExtension);
                    if ($extensionLength == 3 && strlen($zipCode) == 4) {
                        $country = 'pt';
                    } else if ($extensionLength == 3 && strlen($zipCode) == 5) {
                        $country = 'br';
                    }
                }
            }
        } else {
            $zp = new ZipCode();
            if ($zp->isValid('GB', $zipCode)) {
                $zipCode = explode(' ', $zipCode);
                $zipCode = @$zipCode[0];
            } else {
            }
        }

        $allServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->where('source', config('app.source'))
            ->get();

        $agency = AgencyZipCode::remember(config('cache.query_ttl'))
            ->cacheTags(AgencyZipCode::CACHE_TAG)
            ->filterSource()
            ->where(function ($q) use ($zipCode, $originalZp) {
                $q->where('zip_code', [$zipCode]);
                $q->orWhereIn('zip_code', [$originalZp]);
            })
            ->select(['agency_id', 'zone', 'provider_id', 'kms', 'services', 'is_regional', 'country', 'zip_code'])
            ->orderBy('zip_code', 'desc') //faz com que em 1º apareçam os codigos postais de 7 digitos se existir
            ->first();

        if (!$agency) {
            $agency = new AgencyZipCode;
            $zone   = strlen($zipCode) == 4 ? 'pt' : '';

            if (empty($zone)) {
                $zone = $appDefaultCountry;
            }

            $agency->zone = $zone;
        }

        $isInternacional = false;
        if ($service) {
            $service = @$allServices->find($service);
            $isInternacional = @$service->is_internacional;
        }

        //get cities assigned to zip code
        $cities = ZipCode::remember(config('cache.query_ttl'))
            ->cacheTags(ZipCode::CACHE_TAG)
            ->where('zip_code', $zipCode);

        if ($zipCodeExtension) {
            $cities = $cities->where('zip_code_extension', $zipCodeExtension);
        }

        if ($appDefaultCountry == 'us' || $appDefaultCountry == 'br' || $appDefaultCountry == 'ca') { //filtra só os codigos postais de US e BR e CA
            $cities = $cities->whereIn('country', ['us', 'br', 'ca']);
        } else { //ignora para a europa os codigos US e BR e CA
            $cities = $cities->whereNotIn('country', ['us', 'br', 'ca']);
        }

        if ($country) {
            $cities = $cities->where('country', $country);
        }

        $cities = $cities->orderBy('id', 'asc')
            ->groupBy('postal_designation')
            ->select(['id', 'postal_designation', 'country', 'state'])
            ->get();

        $arr = $arrCountry = [];
        $singleZone = true;

        $firstCity   = @$cities->first();
        $lastCountry = @$firstCity->country;
        $state       = @$firstCity->state;
        foreach ($cities as $city) {
            $arr[] = ['value' => $city->postal_designation, 'data' => $city->postal_designation, 'country' => $city->country];
            $arrCountry[] = ['value' => '[' . strtoupper($city->country) . '] ' . $city->postal_designation, 'data' => $city->postal_designation, 'country' => $city->country];

            if ($lastCountry != $city->country) {
                $singleZone = false;
            }
        }

        if ($singleZone) {
            $agency->zone = @$city->country ? $city->country : '';
            $agency->cities = $arr;
        } else {
            $agency->zone = '';
            $agency->cities = $arrCountry; //usa o array com informação do país
        }

        if ($country) {
            $agency->zone = $country;
        }

        if (empty($agency->provider_id) && $providerId) {
            $agency->provider_id = $providerId; //substitui o provider_id inicial
        }

        if (empty($agency->provider_id)) {
            $agency->provider_id = Setting::get('shipment_default_provider');
        }


        /*if (!empty($agency->provider_id)) {
            $provider = Provider::remember(config('cache.query_ttl'))
                ->cacheTags(Provider::CACHE_TAG)
                ->whereId($agency->provider_id)
                ->first();

            if (@$provider->autodetect_agencies) {
                $agency->agencies = $provider->agencies;
            } else {
                $agency->agencies = @Agency::remember(config('cache.query_ttl'))
                    ->cacheTags(Agency::CACHE_TAG)
                    ->whereSource(config('app.source'))
                    ->pluck('id')
                    ->toArray();
            }
        }*/

        $arrAgenciesHtml = '';
        if (!empty($agency->agencies)) {

            $arrAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->whereIn('id', $agency->agencies)->get();

            if (!$arrAgencies->isEmpty()) {
                foreach ($arrAgencies as $item) {
                    $arrAgenciesHtml .= '<option value="' . $item->id . '">' . $item->name . '</option>';
                }
            }
        } else {

            $arrAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterSource()->get();

            if (!$arrAgencies->isEmpty()) {
                foreach ($arrAgencies as $item) {
                    $arrAgenciesHtml .= '<option value="' . $item->id . '">' . $item->name . '</option>';
                }
            }
        }

        $agency->agenciesHtml = $arrAgenciesHtml;


        //Processa serviços autorizados para o codigo postal indicado
        if (empty($agency->services)) { //nao ha servicos definidos para os codigos postais
            $agency->services = null;

            $internacionalServices = $allServices->filter(function ($item) {
                return $item->is_internacional == 1;
            })->pluck('id')->toArray();

            if ($agency->zone && !in_array($agency->zone, [$appDefaultCountry, 'es', 'pt'])) { //apenas internacionais

                $agency->services = $internacionalServices;
            } else {

                $fullZipCode = $zipCodeExtension ? $zipCode . '-' . $zipCodeExtension : $zipCode;
                $matchedZones = [];

                //obtem as zonas de faturação por pais, codigo postal, pack_type ou distance.
                //obtem todas as zonas do tipo pack_type, pois todos estes serviços estão autorizados, visto que não têm codigo postal
                $country = $originalCountry;
                $matchedZones = BillingZone::where(function($q) use($country) {
                        $q->where('country', $country);
                        $q->orWhere('country', '');
                        $q->orWhereNull('country');
                    })
                    ->where(function($q) use($country, $zipCode, $fullZipCode){
                        $q->whereIn('unity', ['zip_code', 'country']);
                        $q->where(function($q) use($country, $zipCode, $fullZipCode) {
                            $q->where('mapping', 'like', '%"'.$country.'"%');
                            $q->orWhere('mapping', 'like', '%"'.$zipCode.'"%');
                            $q->orWhere('mapping', 'like', '%"'.$fullZipCode.'"%');
                        });
                    })
                    ->orWhereIn('unity', ['pack_type', 'distance'])
                    ->pluck('code')
                    ->toArray();

                //verifica as zonas de matriz
                $allMatrixZones = BillingZone::whereIn('unity', ['matrix', 'pack_matrix'])
                    ->where(function($q) use($country) {
                        $q->where('country', $country);
                        $q->orWhere('country', '');
                        $q->orWhereNull('country');
                    })
                    ->pluck('mapping', 'code')
                    ->toArray();

                if(!empty($allMatrixZones)) {
                    foreach($allMatrixZones as $zoneCode => $zonesArr) {
                        foreach(($zonesArr ?? []) as $matrix) {
                            $matrixParts = explode('<=>', $matrix);
                            $matrixParts1 = explode('-', @$matrixParts[0]);

                            if(@$matrixParts[1]) {
                                $matrixParts2 = explode('-', @$matrixParts[1]);
                            } 
                            
                            if(valueBetween($zipCode, @$matrixParts1[0], @$matrixParts1[1]) || valueBetween($zipCode, @$matrixParts2[0], @$matrixParts2[1])) { //codigo postal contido na matriz
                                $matchedZones[] = $zoneCode;
                                break; //interrompe o ciclo atual pois já encontrou uma correspondencia
                            }
                        }
                    }
                }
                
                //obtem os serviços disponiveis para estas zonas
                $availableServices = $allServices->filter(function ($item) use ($matchedZones) {
                    return !empty(array_intersect($item->zones, $matchedZones));
                })->pluck('id')->toArray();

                $agency->services = $availableServices;
            }
        } else {

            if (!in_array($originalCountry, [$appDefaultCountry, 'es', 'pt'])) { //força a que se apliquem apenas serviços internacionais
                $internacionalServices = $allServices->filter(function ($item) {
                    return $item->is_internacional == 1;
                })->pluck('id')->toArray();

                $agency->services = $internacionalServices;
            } else {
                $agency->services = array_map('intval', $agency->services);
            }
        }

        if (empty($shipment) || $shipment == null) {
            $agency->service_allowed = is_array($agency->services);
        } else {
            $agency->service_allowed = is_array($agency->services) ? in_array($shipment->service_id, $agency->services) : null;
        }


        /* $xxx = Service::whereIn('id', $agency->services)->pluck('name', 'id')->toArray();
        dd($xxx); */

        $agency->zone    = $agency->zone ? $agency->zone : $appDefaultCountry; //para remover
        $agency->country = $agency->zone;

        //list States
        $states = Self::listStates($agency->zone);
        $statesSelect2 = [];

        if (!empty($states)) {
            $statesSelect2[] = [
                'id'   => '',
                'text' => ''
            ];

            foreach ($states as $key => $value) {
                $statesSelect2[] = [
                    'id'   => $key,
                    'text' => $value
                ];
            }
        }

        $agency->states        = $states;
        $agency->states_select = $statesSelect2;
        $agency->state         = $state;

        //Se existe array de codigos postais locais configurado e se o codigo postal de destino é fora da zona de atuação
        //vai buscar os KM à base de dados com base nos dados do remetente em vez do destinatário
        if (Setting::get('postal_codes_of_operation')) {

            $recipientZipCode4 = explode('-', $recipientZipCode);
            $recipientZipCode4 = $recipientZipCode4[0];
            $senderZipCode4 = explode('-', $senderZipCode);
            $senderZipCode4 = $senderZipCode4[0];

            if (
                in_array($recipientZipCode4, explode(',', Setting::get('postal_codes_of_operation'))) &&
                !in_array($senderZipCode4, explode(',', Setting::get('postal_codes_of_operation')))
            ) {

                $kms = ZipCode\AgencyZipCode::where('zip_code', $senderZipCode)
                    ->orWhere('zip_code', $senderZipCode4)
                    ->orderBy('zip_code', 'desc')
                    ->first(['zip_code', 'kms']);

                $agency->kms = empty(@$kms->kms) ? '0' : $kms->kms;
            }
        }

        return $agency;
    }

    /**
     * @param $zipCode Codigo postal a comparar
     * @param $country País a comparar
     * @param $shipment
     * @return AgencyZipCode|bool
     */
    public static function getAgency($zipCode, $country, $shipment)
    {

        return self::getAgencyByZipCode(
            $zipCode,
            $shipment->provider_id,
            $country,
            $shipment->service_id,
            $shipment->sender_zip_code,
            $shipment->recipient_zip_code,
            $shipment
        );
    }
    /**
     * Detect correct country to billing the shipments
     *
     * @param type $zipCode
     * @return boolean
     */
    public static function getBillingZone($senderZone = 'pt', $recipientZone = 'pt', $isImport = false)
    {
        return self::getBillingCountry($senderZone, $recipientZone, $isImport);
    }

    public static function getBillingCountry($senderZone = 'pt', $recipientZone = 'pt', $isImport = false)
    {

        $defaultZone = Setting::get('app_country');

        //fora portugal
        if (!in_array($defaultZone, [$senderZone, $recipientZone])) {
            if ($isImport) {
                return $senderZone;
            }
            return $recipientZone;
        }

        //pt -> pt
        if ($senderZone == $defaultZone && $recipientZone == $defaultZone) {
            return $defaultZone;
        }

        //es -> pt
        if ($senderZone != $defaultZone && $recipientZone == $defaultZone) {
            return $senderZone;
        }

        //pt -> es
        if ($senderZone == $defaultZone && $recipientZone != $defaultZone) {
            if ($isImport == '1') {
                return $senderZone;
            }
            return $recipientZone;
        }

        //es -> es
        if ($senderZone == $recipientZone && $senderZone != $defaultZone) {
            return $senderZone;
        }


        return $defaultZone;
    }

    /**
     * Detect correct zip code to billing the shipments
     *
     * @param $senderZipCode
     * @param $recipientZipCode
     * @param bool $isCollection
     * @return mixed
     */
    public static function getBillingZipCode($senderZipCode, $recipientZipCode, $isCollection = false, $returnFullZipCode = false)
    {
        $fullSenderZipCode      = $senderZipCode;
        $fullRecipientZipCode   = $recipientZipCode;
        $senderZipCode          = zipcodeCP4(trim($senderZipCode));
        $recipientZipCode       = zipcodeCP4(trim($recipientZipCode));

        $postalCodesOperation = Setting::get('postal_codes_of_operation');
        if (!empty($postalCodesOperation)) {
            $postalCodesOperation = explode(',', $postalCodesOperation);
            if (in_array($recipientZipCode, $postalCodesOperation)) {
                return $returnFullZipCode ? $fullSenderZipCode : $senderZipCode;
            }
        }

        if ($isCollection) {
            return $returnFullZipCode ? $fullSenderZipCode : $senderZipCode;
        }

        return $returnFullZipCode ? $fullRecipientZipCode : $recipientZipCode;
    }


    /**
     * Automatic generate shipment from pickup
     * @param null $date
     * @throws \Exception
     */
    public static function generateShipmentsFromPickups($date = null, $customerId = null)
    {

        try {

            $date = $date ?? date('Y-m-d');

            $sourceAgencies = Agency::filterSource()->pluck('id')->toArray();

            $finalStatus = [
                ShippingStatus::PICKUP_CONCLUDED_ID,
                ShippingStatus::PICKUP_DONE_ID
            ];

            //obtem todas as recolhas sem envio gerado
            $pickups = Shipment::whereIn('agency_id', $sourceAgencies)
                ->where('is_collection', 1)
                ->whereNull('children_tracking_code')
                ->whereIn('status_id', $finalStatus)
                ->where('date', '>=', $date);

            if ($customerId) {
                $pickups->where('customer_id', $customerId);
            }

            $pickups = $pickups->get();

            foreach ($pickups as $pickup) {
                $pickupTRK = $pickup->tracking_code;

                //verifica se já existe envio gerado mas que não esteja associado
                $generatedShipment = Shipment::where('parent_tracking_code', $pickupTRK)->first();

                if (empty($generatedShipment)) { //se o envio gerado ainda não existe, cria-o
                    $pickup->createShipmentFromPickup();
                }

                if ($generatedShipment) {

                    //set children type on pickup
                    $pickup->children_type = Shipment::TYPE_PICKUP;
                    $pickup->children_tracking_code = $generatedShipment->tracking_code;
                    $pickup->status_id = ShippingStatus::PICKUP_CONCLUDED_ID;
                    $pickup->save();

                    //set pickup expense
                    $generatedShipment->insertOrUpdadePickupExpense($pickup); //add expense

                    //update history
                    $history = new ShipmentHistory();
                    $history->shipment_id = $pickup->id;
                    $history->status_id = ShippingStatus::PICKUP_CONCLUDED_ID;
                    $history->obs = 'Gerado envio TRK' . $generatedShipment->tracking_code;
                    $history->agency_id = $pickup->agency_id;
                    $history->save();
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
        }
    }

    /**
     * Insert or update pickup expense
     * This method is for shipments and not for pickups
     *
     * @return mixed
     */
    public function insertOrUpdadePickupExpense($pickupCollection = null)
    {

        if ($this->type == Shipment::TYPE_PICKUP) {

            $expenseId = ShippingExpense::getPickupExpense(true);

            if (is_null($pickupCollection)) {
                $pickupCollection = Shipment::where('tracking_code', $this->parent_tracking_code)->first();
            }


            $expense = ShipmentExpense::firstOrNew([
                'shipment_id' => $this->id,
                'expense_id'  => $expenseId
            ]);

            $result = true;
            if ($pickupCollection->billing_subtotal > 0.00) {
                $expense->shipment_id       = $this->id;
                $expense->expense_id        = $expenseId;
                $expense->qty               = 1;
                $expense->price             = $pickupCollection->billing_subtotal;
                $expense->subtotal          = $pickupCollection->billing_subtotal;
                $expense->vat               = $pickupCollection->billing_vat;
                $expense->total             = $pickupCollection->billing_total;
                $expense->vat_rate          = $pickupCollection->vat_rate;
                $expense->vat_rate_id       = $pickupCollection->vat_rate_id;

                $expense->cost_price        = $pickupCollection->cost_billing_subtotal;
                $expense->cost_subtotal     = $pickupCollection->cost_billing_subtotal;
                $expense->cost_vat          = $pickupCollection->cost_billing_vat;
                $expense->cost_total        = $pickupCollection->cost_billing_total;
                $expense->cost_vat_rate     = $pickupCollection->vat_rate;
                $expense->cost_vat_rate_id  = $pickupCollection->vat_rate_id;

                $expense->created_by        = Auth::check() ? Auth::user()->id : null;
                $expense->date              = date('Y-m-d');
                //$expense->auto              = true;//Se ativo como automático, ao editar as taxas manualmente na janela de envio, desaparece
                $result = $expense->save();
            } elseif ($expense->exists) {
                $result = $expense->delete(); //se taxa é 0, apaga a taxa
            }

            //atualiza preços totais do envio
            $expensesManual = ShipmentExpense::where('shipment_id', $this->id)
                ->where('auto', 0)
                ->get()
                ->toArray();

            $priceFixed = $this->price_fixed;
            $this->price_fixed = true;
            $prices = Shipment::calcPrices($this, true, $expensesManual);
           
            if(@$prices['fillable']) {
                $this->price_fixed = $priceFixed;
                $this->fill($prices['fillable']);
                $this->save();
            }
            
            return $result;
        }

        return null;
    }

    /**
     * Detect is shipment is Import or Export
     *
     * @param type $zipCode
     * @return boolean
     */
    public function isExport()
    {

        if ($this->sender_country == Setting::get('app_country') && ($this->recipient_country != Setting::get('app_country') || $this->recipientIsIsland())) {
            return true;
        } elseif ($this->sender_country != Setting::get('app_country') && $this->recipient_country != Setting::get('app_country')) {
            return true;
        }

        return false;
    }

    /**
     * Detect is shipment Export to spain
     *
     * @param type $zipCode
     * @return boolean
     */
    public function isExportSpain()
    {

        if ($this->isExport() && $this->recipient_country == 'es') {
            return true;
        }

        return false;
    }

    /**
     * Detect is shipment is import
     *
     * @param type $zipCode
     * @return boolean
     */
    public function isImport()
    {

        if ($this->recipient_country == Setting::get('app_country') && $this->sender_country != Setting::get('app_country')) {
            return true;
        }
        return false;
    }

    /**
     * Detect is shipment is nacional (PT-PT)
     *
     * @param type $zipCode
     * @return boolean
     */
    public function isNacional()
    {

        if ($this->recipient_country == Setting::get('app_country') && $this->sender_country == Setting::get('app_country')) {
            return true;
        }
        return false;
    }

    /**
     * Retorna o estado por defeito quando se cria um envio.
     * @return int
     */
    public function getDefaultStatus($isCustomer = false)
    {
        if ($this->is_collection) {
            if ($isCustomer) {
                $defaultStatusId = ShippingStatus::PICKUP_REQUESTED_ID;
            } else {
                $defaultStatusId = empty(Setting::get('pickup_status_after_create')) ? ShippingStatus::PICKUP_ACCEPTED_ID : Setting::get('pickup_status_after_create');
            }
        } else {
            if ($isCustomer) {
                $defaultStatusId = ShippingStatus::PENDING_ID;
            } else {
                $defaultStatusId = empty(Setting::get('shipment_status_after_create')) ? ShippingStatus::ACCEPTED_ID : Setting::get('shipment_status_after_create');
            }
        }


        return $defaultStatusId;
    }

    /**
     * List states
     * @param null $country
     * @return array|\Illuminate\Foundation\Application|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public static function listStates($country = null)
    {

        $acceptCountries = ['us', 'ca', 'br'];

        if (in_array($country, $acceptCountries)) {
            $states = trans('districts_codes.districts.' . $country);
            $states = is_array($states) ? $states : [];
            return $states;
        }

        return [];
    }

    /**
     * Get Vehicle
     */
    public static function getVehicle($licensePlate)
    {

        if (hasModule('fleet')) {
            if ($licensePlate) {
                $vehicle = App\Models\FleetGest\Vehicle::filterSource()->where('license_plate', $licensePlate)->first();
            } else {
                $vehicle = App\Models\FleetGest\Vehicle::filterSource()->where('is_default', 1)->first();
            }
        } else {
            if ($licensePlate) {
                $vehicle = Vehicle::filterSource()->filterAgencies()->where('license_plate', $licensePlate)->first();
            } else {
                $vehicle = Vehicle::filterSource()->filterAgencies()->where('is_default', 1)->first();
            }
        }

        return $vehicle;
    }

    /**
     * Return Out Of Standard Expense
     *
     * @param $expenses
     * @return null
     */
    public static function getSMSExpense($expenses)
    {

        if (!empty($expenses)) {
            $expense = $expenses->filter(function ($item) {
                return $item->type == 'sms';
            })->first();

            if ($expense) {
                return $expense->id;
            }
        }

        return null;
    }

    /**
     * Return country from a given zip code
     *
     * @param $zipCode
     * @return null
     */
    public static function countryFromZipCode($zipCode)
    {

        if (empty($zipCode) || strlen($zipCode) <= 4) {
            return Setting::get('app_country');
        }

        if (strlen($zipCode) == 4) {
            return 'pt';
        } else {
            return 'es';
        }

        return Setting::get('app_country');
    }

    /**
     * Send notification e-mail
     *
     * @param bool $forceSendCustomer force email to be sent to customer. Ignore default status
     * @param null $forceSendRecipient
     * @return bool
     */
    public function sendEmail()
    {

        $shipment = $this;

        $emails = validateNotificationEmails($shipment->recipient_email);
        if (!empty($emails['valid'])) {

            try {
                $codeServices = Setting::get('shipment_notify_pickup_code_services');
                if ($codeServices && in_array($this->service_id, $codeServices)) {
                    $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
                    $qrCode->disableBorder();
                    $output = new \Mpdf\QrCode\Output\Png();
                    $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 200));

                    Mail::send(transEmail('emails.shipments.pickup_code', $shipment->recipient_country), compact('shipment', 'qrCode'), function ($message) use ($emails, $shipment) {
                        $message->to($emails['valid'])
                            ->subject('Código Levantamento Encomenda #' . $shipment->tracking_code);
                    });
                    return true;
                } else {

                    //GET GROUPED IDS
                    if ($shipment->children_type == 'M') {
                        $shipments = Shipment::where('type', 'M')
                            ->where('parent_tracking_code', $shipment->tracking_code)
                            ->pluck('tracking_code')
                            ->toArray();

                        $shipment->tracking_code = $shipment->tracking_code . ',' . implode(',', $shipments);
                    }

                    Mail::send(transEmail('emails.shipments.tracking', $shipment->recipient_country), compact('shipment'), function ($message) use ($emails, $shipment) {
                        $message->to($emails['valid'])
                            ->subject(transLocale('admin/email.subjects.shipments.create', $shipment->getRecipientLocale(), ['trk' => $shipment->tracking_code]));
                    });
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Envia email com etiqueta, CRM e guia
     *
     * @return bool
     */

    public function sendEmailWithDocs($data)
    {
        $shipment = $this;
        $selectedAttachments = $data['attachments'];
        $shipmentsIds = [$shipment->id];

        $emails = validateNotificationEmails($data['email']);

        if (!empty($emails['valid'])) {
            $data['subject']    = 'Envio de Documentos: ' . $shipment->tracking_code;
            $data['email_view'] = 'emails.shipments.documents';

            try {

                //Attach label
                if (in_array('label', $selectedAttachments)) {
                    $content = $this->printAdhesiveLabels($shipmentsIds, true, 'admin', 'string');


                    $attachments[] = [
                        'mime'      => 'application/pdf',
                        'filename'  => 'Etiquetas ' . $this->tracking_code . '.pdf',
                        'content'   => $content
                    ];
                }

                //Attach guide
                if (in_array('guide', $selectedAttachments)) {
                    $content = $this->printTransportGuide($shipmentsIds, null, null, false, 'string');
                    $attachments[] = [
                        'mime'      => 'application/pdf',
                        'filename'  => 'Guia Transporte ' . $this->tracking_code . '.pdf',
                        'content'   => $content
                    ];
                }

                //Attach CRM
                if (in_array('crm', $selectedAttachments)) {
                    $content = $this->printCmr($shipmentsIds, null, null, 'string');
                    $attachments[] = [
                        'mime'      => 'application/pdf',
                        'filename'  => 'CRM ' . $this->tracking_code . '.pdf',
                        'content'   => $content
                    ];
                }

                $emails = $emails['valid'];

                //SEND EMAIL
                Mail::send($data['email_view'], compact('data', 'shipment'), function ($message) use ($data, $emails, $attachments) {

                    $message->to($emails);


                    $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($data['subject']);

                    if ($attachments) {
                        foreach ($attachments as $attachment) {

                            if (isset($attachment['content'])) {
                                $message->attachData(
                                    $attachment['content'],
                                    $attachment['filename'],
                                    $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                                );
                            }
                        }
                    }
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Envia email com dados de pagamento
     *
     * @return bool
     */
    public function sendPaymentEmail()
    {

        $shipment = $this;

        $emails = validateNotificationEmails($shipment->recipient_email);
        if (!empty($emails['valid'])) {

            try {

                //GET GROUPED IDS
                if ($shipment->children_type == 'M') {
                    $shipments = Shipment::where('type', 'M')
                        ->where('parent_tracking_code', $shipment->tracking_code)
                        ->pluck('tracking_code')
                        ->toArray();

                    $shipment->tracking_code = $shipment->tracking_code . ',' . implode(',', $shipments);
                }

                Mail::send(transEmail('emails.shipments.tracking', $shipment->recipient_country), compact('shipment'), function ($message) use ($emails, $shipment) {
                    $message->to($emails['valid'])
                        ->subject(transLocale('admin/email.subjects.shipments.create', $shipment->getRecipientLocale(), ['trk' => $shipment->tracking_code]));
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function sendEmailTimeWindow()
    {

        $shipment = $this;

        $emails = Shipment::validateNotificationEmails($shipment->recipient_email);
        if (!empty($emails['valid'])) {

            try {
                $codeServices = Setting::get('shipment_notify_pickup_code_services');
                if ($codeServices && in_array($this->service_id, $codeServices)) {
                    $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
                    $qrCode->disableBorder();
                    $output = new \Mpdf\QrCode\Output\Png();
                    $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 200));

                    Mail::send(transEmail('emails.shipments.pickup_code', $shipment->recipient_country), compact('shipment', 'qrCode'), function ($message) use ($emails, $shipment) {
                        $message->to($emails['valid'])
                            ->subject('Código Levantamento Encomenda #' . $shipment->tracking_code);
                    });
                    return true;
                } else {

                    //GET GROUPED IDS
                    if ($shipment->children_type == 'M') {
                        $shipments = Shipment::where('type', 'M')
                            ->where('parent_tracking_code', $shipment->tracking_code)
                            ->pluck('tracking_code')
                            ->toArray();

                        $shipment->tracking_code = $shipment->tracking_code . ',' . implode(',', $shipments);
                    }
                    Mail::send(transEmail('emails.shipments.customer_delivery_time_window', $shipment->recipient_country), compact('shipment'), function ($message) use ($emails, $shipment) {
                        $message->to($emails['valid'])
                            ->subject(transLocale('admin/email.subjects.shipments.time_window', $shipment->getRecipientLocale(), ['trk' => $shipment->tracking_code]));
                    });
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }


    /**
     * Send notification e-mail
     *
     * @param bool $forceSendCustomer force email to be sent to customer. Ignore default status
     * @param null $forceSendRecipient
     * @return bool
     */
    public function sendSms($forceSmsRecipient = false, $forceSmsCustomer = false)
    {

        $shipment        = $this;
        $customer        = @$this->customer;
        $customerMobile  = @$shipment->customer->mobile;
        $recipientMobile = @$shipment->recipient_phone;

        if ($forceSmsCustomer || $forceSmsCustomer || empty($customer->shipping_services_notify) || in_array($customer->shipping_services_notify, [$shipment->service_id])) {

            //notify recipient
            if ($forceSmsRecipient || !empty(@$customer->customer_sms_text['sms_text_registered_recipient'])) {
                $mobiles = validateNotificationMobiles($recipientMobile);
                $mobiles = $mobiles['valid'];

                if (!empty($mobiles)) {
                    try {
                        $sms = new Sms();
                        $sms->to = implode(';', $mobiles);
                        $sms->message = $shipment->getSmsText(true);
                        $sms->source_id = $shipment->id;
                        $sms->source_type = 'Shipment';
                        $sms->send();
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
            }

            //notify customer
            if ($forceSmsCustomer || !empty(@$customer->customer_sms_text['sms_text_registered_sender'])) {
                $mobiles = validateNotificationMobiles($customerMobile);
                $mobiles = $mobiles['valid'];

                if (!empty($mobiles)) {
                    try {
                        $sms = new Sms();
                        $sms->to = implode(';', $mobiles);
                        $sms->message = $shipment->getSmsText();
                        $sms->source_id = $shipment->id;
                        $sms->source_type = 'Shipment';
                        $sms->send();
                    } catch (\Exception $e) {
                        throw new \Exception('Falhou o envio da SMS para o cliente. Tente de novo.');
                    }
                }
            }
        }
        return true;
    }

    /**
     * Return SMS Message Text
     * @param bool $toRecipient
     * @return mixed
     */
    public function getSmsText($toRecipient = true)
    {

        $url = request()->getHttpHost() . '/trk/' . $this->tracking_code;
        $customer = $this->customer;

        if ($toRecipient) {
            $defaultMsg = 'Registado envio ' . $this->tracking_code . '. De: ' . substr($this->sender_name, 0, 20) . '. Siga a entrega em ' . $url;
            $msg = $customer->getSmsText('registered_recipient', $defaultMsg);
        } else {
            $defaultMsg = 'Registado envio ' . $this->tracking_code . '. Para: ' . substr($this->recipient_name, 0, 20) . '. Siga a entrega em ' . $url;
            $msg = $customer->getSmsText('registered_sender', $defaultMsg);
        }

        $shipment = $this;
        $history = new ShipmentHistory();
        $msg = $history->replaceMsgVars($msg, $shipment);

        /*$msg = str_replace(':trk', $this->tracking_code, $msg);
        $msg = str_replace(':ptrk', $this->provider_tracking_code, $msg);
        $msg = str_replace(':ddate', $this->delivery_date, $msg);
        $msg = str_replace(':price', $this->charge_price, $msg);
        $msg = str_replace(':sender', substr($this->sender_name, 0, 20), $msg);
        $msg = str_replace(':recipient', substr($this->recipient_name, 0, 20), $msg);
        $msg = str_replace(':url', $url, $msg);*/

        return $msg;
    }

    /**
     * Notify operators

     * @param bool $sendToMobile
     * @return bool
     */
    public function notifyOperators($sendToMobile = true, $validate = true, $forceNotify = false) {

        if ($validate && 
            ($this->without_pickup 
             || !Setting::get('mobile_app_menu_tasks') 
             || !Setting::get('mobile_app_notifications') 
             || $this->hasSyncError())) {
            return false;
        }
        
        $notifyOperator = false;
        try {
            /**
             * Separate tasks by customer/deparment
             */
            $taskCustomerId = $this->customer_id;

            // Only separate if shipment was made by a departement
            if ($this->department_id && $this->department_id != $this->customer_id) {
                // Only separate if deparment is independent
                if ($this->department->is_independent) {
                    $taskCustomerId = $this->department_id;
                }
            }

            
            $task = OperatorTask::filterSource()
                ->where('concluded', 0)
                ->where('customer_id', $taskCustomerId)
                ->where('date', $this->date)
                ->where('transport_type_id', @$this->service->transport_type_id);

            if($this->is_pickup) {
                $task = $task->where('is_pickup', 1)
                             ->where('shipments', '['.$this->id.']'); //só válido para esta recolha. não se pode juntar recolhas
            } else {
                $task = $task->where('is_pickup', 0);
            }

            $task = $task->first();


            $taskExists = true;
            if (!$task) {
                $task           = new OperatorTask;
                $taskExists     = false;
                $notifyOperator = true;
            }

            /**
             * Get operators to be notified
             */
            if (!$this->pickup_operator_id) {
                /**
                 * @author Daniel Almeida
                 * 
                 * This should be a setting but for now it's a if statement
                 */
                if (in_array(config('app.source'), ['baltrans'])) {
                    $task->operators = [];
                } else {
                    $operators = User::where('agencies', 'like', '%"' . $this->agency_id . '"%')
                        ->isOperator();

                    if (!Setting::get('mobile_app_notify_all_operators') && $this->pickup_operator_id) { //notifica apenas o motorista que faz a recolha
                        $operators = $operators->where('id', $this->pickup_operator_id);
                    }

                    $operators = $operators->orderBy('name', 'asc')
                        ->pluck('id')
                        ->toArray();

                    $task->operators = $operators;
                }
            } else {
                $task->operators = [$this->pickup_operator_id];
            }

           /*  if($this->is_pickup && $this->operator_id) {
                $task->operators = [$this->pickup_operator_id];
            } */
            /**-- */

            $shipmentsArr = empty($task->shipments) ? [] : $task->shipments;
            if (!in_array($this->id, $shipmentsArr)) {
                array_push($shipmentsArr, $this->id);
            }

            //vai buscar informacao atualizada das recolhas
            $allPickupShipments = Shipment::whereIn('id', $shipmentsArr)
                ->where('customer_id', $this->customer_id)
                ->get(['customer_id', 'volumes', 'weight', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_phone', 'recipient_name', 'recipient_zip_code', 'recipient_city']);

            $details = '';
            foreach ($allPickupShipments as $shp) {
                if($this->is_pickup) {
                    $details .= 'RECOLHA DE '.@$shp->customer->name.'<br/>';
                    $details .= $shp->volumes . ' Obj. - '. $shp->recipient_name .' ('. $shp->recipient_city .')<br/>';
                } else {
                    $details .= $shp->volumes . ' Obj. - '. $shp->recipient_city .'<br/>';
                }
            }

            $task->source      = config('app.source');
            $task->last_update = date('Y-m-d H:i:s');
            $task->is_pickup   = $this->is_pickup;
            $task->name        = $this->is_pickup ? '[REC] ' . $this->sender_name : $this->sender_name;
            $task->description = '';
            $task->details     = br2nl($details);
            $task->customer_id = $taskCustomerId;
            $task->volumes   = $allPickupShipments->sum('volumes');
            $task->weight    = $allPickupShipments->sum('weight');
            $task->deleted   = 0;
            $task->shipments = $shipmentsArr;
            $task->date      = $this->date;
            $task->transport_type_id = @$this->service->transport_type_id;
            
            if (!$task->exists) {
                $task->address  = $this->sender_address;
                $task->zip_code = $this->sender_zip_code;
                $task->city     = $this->sender_city;
                $task->phone    = $this->sender_phone;

                $task->start_hour = $this->start_hour;
                $task->end_hour   = $this->is_collection ? $this->end_hour : $this->end_hour_pickup;
            }

            if (count($task->operators) == 1) {
                $task->operator_id = $task->operators[0];
            }

            $task->save();

            if ($forceNotify || ($sendToMobile && $notifyOperator && Setting::get('mobile_app_notifications'))) {
                if ($this->operator_id) {
                    $task->setNotification(BroadcastPusher::getChannel($this->operator_id));
                } else if (Setting::get('mobile_app_notify_all_operators')) {
                    $task->notifyAllOperators();
                }
            }

            //se a tarefa existe e já foi aceite, marca o envio criado como "em recolha"
            if ($taskExists && $task->readed && !$task->concluded && !$task->deleted) {
                $history = new ShipmentHistory();
                $history->shipment_id = $this->id;
                $history->agency_id   = $this->agency_id;
                $history->status_id   = ShippingStatus::IN_PICKUP_ID;
                $history->operator_id = @$task->operator_id;
                $history->save();

                $this->status_id = ShippingStatus::IN_PICKUP_ID;
                $this->save();
            }

            return true;
        } catch (\Exception $e) {
            Log::error($e);
        }

        return false;
    }

    public function scopeIsFinalStatus($query, $isFinal = true)
    {
        return $this->whereHas('status', function($q) use($isFinal) {
            $q->where('is_final', $isFinal);
        });
    }



    /**
     * Undocumented function
     *
     * @param [type] $query
     * @param [type] $request
     * @return void
     */
    public function scopeApplyRequestFilters($query, $request) {

        $data = $query;

         //limit search
         $value = $request->limit_search;
         if ($request->has('limit_search') && !empty($value)) {
             $minId = (int) CacheSetting::get('shipments_limit_search');
             if ($minId) {
                 $data = $data->where('id', '>=', $minId);
             }
         }
 
         //filter hide final status
         $value = $request->hide_final_status;
         if ($request->has('hide_final_status') && !empty($value)) {
             $finalStatus = ShippingStatus::where('is_final', 1)->pluck('id')->toArray();
             if (in_array(config('app.source'), ['corridadotempo'])) {
                 $finalStatus[] = 9;
             }
             $data = $data->whereNotIn('status_id', $finalStatus);
         }
 
         //show hidden
         $value = $request->hide_scheduled;
         if ($request->has('hide_scheduled') && !empty($value)) {
             $data = $data->where('date', '<=', date('Y-m-d'));
         }
 
         //filter period
         $value = $request->billing_period;
         if ($request->has('delivery_period')) { //se usar só como variavel "period" vai entrar em conflito com a  
             if ($value == "1") { //MANHA
                 $data = $data->where(function ($q) {
                     $q->whereRaw('HOUR(created_at) between "00:00:00" and "13:00:00"');
                     $q->orWhereRaw('HOUR(created_at) between "18:00:00" and "23:59:59"');
                 });
             } else {
                 $data = $data->where(function ($q) {
                     $q->whereRaw('HOUR(created_at) between "13:00:00" and "18:00:00"');
                 });
             }
         }
 
         //filter customer
         $value = $request->customer;
         if ($request->has('customer')) {
             $data = $data->where('customer_id', $value);
         }
 
         //filter customer
         $value = $request->dt_customer;
         if ($request->has('dt_customer')) {
             $data = $data->where('customer_id', $value);
         }
 
         //filter status
         $value = $request->get('status');
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('status_id', $value);
         }
 
         //filter service
         $value = $request->get('service');
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('service_id', $value);
         }
 
         //filter provider
         $value = $request->get('provider');
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('provider_id', $value);
         }
 
         //filter route
         $value = $request->route;
         if ($request->has('route')) {
             $data = $data->where('route_id', $value);
         }
 
         //filter agency
         $value = $request->agency;
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('agency_id', $value);
         }
 
         //filter agency
         $value = $request->sender_agency;
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('sender_agency_id', $value);
         }
 
         //filter recipient agency
         $value = $request->recipient_agency;
         if (!empty($value)) {
             $value = explode(',', $value);
             $data = $data->whereIn('recipient_agency_id', $value);
         }
 
         //filter date min
         $dtMin = $request->get('date_min');
         if ($request->has('date_min')) {
 
             $dtMax = $dtMin;
 
             if ($request->has('date_max')) {
                 $dtMax = $request->get('date_max');
             }
 
             if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                 $dtMin = $dtMin . ' 00:00:00';
                 $dtMax = $dtMax . ' 23:59:59';
                 $statusId = $request->get('date_unity');
 
                 if (in_array($statusId, ['3', '4', '5', '9', '36'])) {
                     $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                         $q->where('status_id', $statusId)->whereBetween('created_at', [$dtMin, $dtMax]);
                     });
                 } elseif ($statusId == 'delivery') {
                     $data->whereBetween('delivery_date', [$dtMin, $dtMax]);
                 } elseif ($statusId == 'billing') {
                     $data->whereBetween('billing_date', [$dtMin, $dtMax]);
                 } elseif ($statusId == 'creation') {
                     $data->whereBetween('created_at', [$dtMin, $dtMax]);
                 }
             } else { //filter by shipment date
                 $data = $data->whereBetween('date', [$dtMin, $dtMax]);
             }
         }

         //filter volumes min
         $volMin = $request->get('volumes_min');
         if($request->has('volumes_min')) {

             $volMax = $volMin;

             if($request->has('volumes_max')) {
                 $volMax = $request->get('volumes_max');
             }

             $data = $data->whereBetween('volumes', [$volMin, $volMax]);
         }
         
         //filter weight min
         $weightMin = $request->get('weight_min');
         if($request->has('weight_min')) {

             $weightMax = $weightMin;

             if($request->has('weight_max')) {
                 $weightMax = $request->get('weight_max');
             }

             $data = $data->whereBetween('weight', [$weightMin, $weightMax]);
         }
 
         //filter operator
         $value = $request->operator;
         if ($request->has('operator')) {
             $value = explode(',', $value);
             if (in_array('not-assigned', $value)) {
                 $data = $data->where(function ($q) use ($value) {
                     $q->whereNull('operator_id')
                         ->orWhereIn('operator_id', $value);
                 });
             } else {
                 $data = $data->whereIn('operator_id', $value);
             }
         }
 
         //filter charge
         $value = $request->charge;
         if ($request->has('charge')) {
             if ($value == 0) {
                 $data = $data->whereNull('charge_price');
             } elseif ($value == 1) {
                 $data = $data->whereNotNull('charge_price');
             }
         }
 
         //filter payment at recipient
         $value = $request->payment_recipient;
         if ($request->has('payment_recipient')) {
             if ($value == '0') {
                 $data = $data->where('payment_at_recipient', 0);
             } elseif ($value == '1') {
                 $data = $data->where('payment_at_recipient', 1);
             }
         }
 
         //show is blocked
         $value = $request->blocked;
         if ($request->has('blocked')) {
             $data = $data->where('is_blocked', $value);
         }
 
         //show printed
         $value = $request->printed;
         if ($request->has('printed')) {
             $data = $data->where('is_printed', $value);
         }
 
         //filter invoice
         $value = $request->get('invoice');
         if ($request->has('invoice')) {
             if ($value == '0') {
                 $data = $data->whereNull('invoice_doc_id');
             } else {
                 $data = $data->whereNotNull('invoice_doc_id');
             }
         }
 
         //filter expenses
         $value = $request->get('expenses');
         if ($request->has('expenses')) {
             if ($value == '0') {
                 $data = $data->where(function ($q) {
                     $q->whereNull('total_expenses');
                     $q->orWhere('total_expenses', 0.00);
                 });
             } else {
                 $data = $data->where('total_expenses', '>', 0.00);
             }
         }
 
         //show hidden
         $value = $request->deleted;
         if ($request->has('deleted') && !empty($value)) {
             $data = $data->withTrashed();
         }
 
         //filter type
         $value = $request->get('shp_type');
         if ($request->has('shp_type')) {
             if ($value == Shipment::TYPE_SHIPMENT) {
                 $data = $data->whereNull('type');
             } else if ($value == 'sync-error') {
                 $data = $data->whereNotNull('webservice_method')
                     ->whereNull('submited_at');
             } else if ($value == 'sync-no') {
                 $data = $data->whereNull('webservice_method');
             } else if ($value == 'sync-yes') {
                 $data = $data->whereNotNull('webservice_method')
                     ->whereNotNull('submited_at');
             } else if ($value == 'noprice') {
                 $data = $data->where(function ($q) {
                     $q->whereNull('total_price');
                     $q->orWhere('total_price', '0.00');
                 });
             } else if ($value == 'pod_signature') {
                 $data = $data->whereHas('last_history', function ($q) {
                     $q->where('signature', '<>', '');
                 });
             } else if ($value == 'pod_file') {
                 $data = $data->whereHas('last_history', function ($q) {
                     $q->where('filepath', '<>', '');
                 });
             } else if ($value == 'pudo') {
                 $data = $data->whereNotNull('recipient_pudo_id');
             } else {
                 $data = $data->where('type', $value);
             }
         }
 
         //filter vehicle
         $value = $request->vehicle;
         if ($request->has('vehicle')) {
             if ($value == '-1') {
                 $data = $data->where(function ($q) {
                     $q->whereNull('vehicle');
                     $q->orWhere('vehicle', '');
                 });
             } else {
                 $data = $data->where('vehicle', $value);
             }
         }
 
         //filter route
         /*$value = $request->route;
         if($request->has('route')) {
             if($value == '-1') {
                 $data = $data->whereNull('route_id');
             } else {
                 $data = $data->where('route_id', $value);
             }
         }*/
 
         //filter trailer
         $value = $request->trailer;
         if ($request->has('trailer')) {
             if ($value == '-1') {
                 $data = $data->where(function ($q) {
                     $q->whereNull('trailer');
                     $q->orWhere('trailer', '');
                 });
             } else {
                 $data = $data->where('trailer', $value);
             }
         }
 
         //filter sender country
         $value = $request->get('sender_country');
         if ($request->has('sender_country')) {
             $data = $data->where('sender_country', $value);
         }
 
         //filter recipient country
         $value = $request->get('recipient_country');
         if ($request->has('recipient_country')) {
             $data = $data->where('recipient_country', $value);
         }
 
         //filter recipient zip code
         $value = $request->get('recipient_zip_code');
         if (!empty($value)) {
 
             $values = explode(',', $value);
             $zipCodes = array_map(function ($item) {
                 return str_contains($item, '-') ? $item : substr($item, 0, 4) . '%';
             }, $values);
 
             $data = $data->where(function ($q) use ($zipCodes) {
                 foreach ($zipCodes as $zipCode) {
                     $q->orWhere('recipient_zip_code', 'like', $zipCode . '%');
                 }
             });
         }
 
         //filter workgroups
         $value = $request->get('workgroups');
         if ($request->has('workgroups')) {
 
             $workgroup = UserWorkgroup::remember(config('cache.query_ttl'))
                 ->cacheTags(UserWorkgroup::CACHE_TAG)
                 ->filterSource()
                 ->whereIn('id', $value)
                 ->get(['services'])
                 ->toArray();
 
             $serviceIds = [];
             foreach ($workgroup as $group) {
                 if (is_array(@$group['services'])) {
                     $serviceIds = array_merge($serviceIds, $group['services']);
                 }
             }
 
             if ($serviceIds) {
                 $data = $data->whereIn('service_id', $serviceIds);
             }
         }
 
         //filter recipient district
         $district = $request->get('recipient_district');
         $county   = $request->get('recipient_county');
         if ($request->has('recipient_district') || $request->has('recipient_county')) {
 
             $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                 ->cacheTags(ShippingStatus::CACHE_TAG)
                 ->where('district_code', $district)
                 ->where('country', 'pt');
 
             if ($county) {
                 $zipCodes = $zipCodes->where('county_code', $county);
             }
 
             $zipCodes = $zipCodes->groupBy('zip_code')
                 ->pluck('zip_code')
                 ->toArray();
 
             $data = $data->where(function ($q) use ($zipCodes) {
                 $q->where('recipient_country', 'pt');
                 $q->whereIn(DB::raw('SUBSTRING(`recipient_zip_code`, 1, 4)'), $zipCodes);
             });
         }

         return $data;
    }

    /**
     * Apply filters datatable customers billing
     *
     * @param [type] $query
     * @param [type] $request
     * @return void
     */
    public function scopeApplyCustomerBillingRequestFilters($query, $request) {

        $data = $query;

        if (Setting::get('billing_ignored_services')) {
            $data = $data->whereNotIn('service_id', Setting::get('billing_ignored_services'));
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $request->get('date_unity');

                $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $data = $data->whereBetween('billing_date', [$dtMin, $dtMax]);
            }
        }

        //filter department
        $value = $request->department;
        if ($request->has('department')) {
            if ($value == '-1') {
                $data = $data->whereNull('department_id');
            } else {
                $data = $data->where('department_id', $value);
            }
        }

        //filter invoice
        $value = $request->invoice;
        if ($request->has('invoice')) {
            $data = $data->where('invoice_doc_id', $value);
        }

        //filter expenses
        $value = $request->get('expenses');
        if ($request->has('expenses')) {
            if ($value == '0') {
                $data = $data->where(function ($q) {
                    $q->whereNull('total_expenses');
                    $q->orWhere('total_expenses', 0.00);
                });
            } else {
                $data = $data->where('total_expenses', '>', 0.00);
            }
        }

        //filter expense type
        $value = $request->expense_type;
        if ($request->has('expense_type')) {
            $data = $data->whereHas('expenses', function ($q) use ($value) {
                $q->whereIn('expense_id', $value);
            });
        }

        //filter zone
        $value = $request->zone;
        if ($request->has('zone')) {
            $data = $data->where('zone', $value);
        }

        //filter service
        $value = $request->get('service');
        if ($request->has('service')) {
            if ($value == '-1') {
                $data = $data->whereNull('service_id');
            } else {
                $data = $data->where('service_id', $value);
            }
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            //$data = $data->where('agency_id', $value);
            $data = $data->where('sender_agency_id', $value);
        }

        //filter agency
        $value = $request->recipient_agency;
        if ($request->has('recipient_agency')) {
            $data = $data->where('recipient_agency_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            if ($value == 'not-assigned') {
                $data = $data->whereNull('operator_id');
            } else {
                $data = $data->where('operator_id', $value);
            }
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        //filter conferred
        $value = $request->get('conferred');
        if ($request->has('conferred')) {
            if ($value == 0) {
                $data = $data->where(function ($q) {
                    $q->whereNull('customer_conferred');
                    $q->orWhere('customer_conferred', 0);
                });
            } else {
                $data = $data->where('customer_conferred', 1);
            }
        }

        //filter billed
        $value = $request->billed;
        if ($request->has('billed')) {
            if ($value == 1) {
                $data = $data->whereNotNull('invoice_doc_id');
            } else {
                $data = $data->whereNull('invoice_doc_id');
            }
        }

        //filter charge
        $value = $request->charge;
        if ($request->has('charge')) {
            if ($value == 0) {
                $data = $data->whereNull('charge_price');
            } elseif ($value == 1) {
                $data = $data->whereNotNull('charge_price');
            }
        }

        //filter price_fixed
        $value = $request->price_fixed;
        if ($request->has('price_fixed')) {
            $data = $data->where('price_fixed', $value);
        }

        //filter return
        $value = $request->return;
        if ($request->has('return')) {
            if ($value) {
                $data = $data->whereNotNull('has_return');
            } else {
                $data = $data->whereNull('has_return');
            }
        }

        //filter sender country
        $value = $request->get('sender_country');
        if ($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->get('recipient_country');
        if ($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
        }

        //filter price 0
        $value = $request->price;
        if ($request->has('price')) {
            if ($value == 1) {
                $data = $data = $data->where(function ($q) {
                    $q->whereNull('total_price');
                    $q->orWhere('total_price', 0.00);
                });
            } else {
                $data = $data->where('total_price', '>', 0.00);
            }
        }

        //filter empty country
        $value = $request->empty_country;
        if ($request->has('empty_country')) {
            if ($value == 1) {
                $data = $data = $data->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereNull('sender_country');
                        $q->orWhere('sender_country', '');
                    });
                    $q->orWhere(function ($q) {
                        $q->whereNull('recipient_country');
                        $q->orWhere('recipient_country', '');
                    });
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNotNull('sender_country');
                    $q->orWhereNotNull('recipient_country');
                });
            }
        }

        $value = $request->empty_children;
        if ($request->has('empty_children')) {
            if ($value == 1) {
                $data = $data->whereNull('children_tracking_code');
            } else {
                $data = $data->whereNotNull('children_tracking_code');
            }
        }

        return $data;
    }

    /**
     * Apply filters datatable customers refunds
     *
     * @param [type] $query
     * @param [type] $request
     * @return void
     */
    public function scopeApplyRefundsRequestFilters($query, $request) {

        $data = $query;

        //filter status
        $value = $request->get('refund_status');
        if ($request->has('refund_status')) {
            if ($value == '1') { //pending
                $data = $data->has('refund_control', '=', 0);
            } else if ($value == '2') { //received and not paid
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->whereNull('payment_method');
                    $q->whereNull('payment_date');
                    $q->whereNotNull('received_method');
                    $q->whereNotNull('received_date');
                    $q->where('received_method', '<>', 'claimed');
                    $q->where('canceled', 0);
                });
            } else if ($value == '3') { //received and paid
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->whereNotNull('received_method');
                    $q->whereNotNull('received_date');
                    $q->whereNotNull('payment_method');
                    $q->whereNotNull('payment_date');
                    $q->where('canceled', 0);
                });
            } else if ($value == '4') { //paid and not received
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->whereNull('received_method');
                    $q->whereNull('received_date');
                    $q->whereNotNull('payment_method');
                    $q->whereNotNull('payment_date');
                    $q->where('canceled', 0);
                });
            } else if ($value == '5') { //claimed
                $data = $data->whereHas('refund_control', function ($q) {
                    /*$q->whereNotNull('payment_method');
                    $q->whereNotNull('payment_date');*/
                    $q->where('received_method', 'claimed');
                    $q->where('canceled', 0);
                });
            } else if ($value == '6') { //canceled
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->where('canceled', '1');
                });
            } else if ($value == '7') { //requested
                $data = $data->whereHas('refund_control', function ($q) {
                    $q->where('canceled', 0);
                    $q->whereNotNull('requested_method');
                    $q->whereNotNull('requested_date');
                });
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $request->get('date_unity');

                $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }
        }

        //filter delayed reception
        $value = $request->get('delayed_reception');
        if (!empty($value)) {
            $data = $data->where(function ($q) {
                $q->whereHas('last_history', function ($q) {
                    $date = Date::now()->subDays(5)->format('Y-m-d');
                    $q->where('status_id', ShippingStatus::DELIVERED_ID);
                    $q->where('last_history.created_at', '<=', $date . ' 00:00:00');
                })
                    ->has('refund_control', '=', 0)
                    ->orWhereHas('refund_control', function ($q) {
                        $q->whereNull('received_method');
                        $q->whereNull('received_date');
                        $q->where('canceled', 0);
                    });
            });
        }

        //limit search
        $value = $request->limit_search;
        if ($request->has('limit_search') && !empty($value) && !empty(Setting::get('shipments_limit_search'))) {
            $today   = Date::today();
            $minDate = $today->subMonth(Setting::get('shipments_limit_search'))->format('Y-m-d');
            if ($minDate) {
                $data = $data->where('shipments.created_at', '>=', $minDate . ' 00:00:00');
            }
        }

        //filter shipment status
        $value = $request->get('shipment_status');
        if (!empty($value)) {
            $data = $data->whereIn('status_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if (!empty($value)) {
            $data = $data->whereIn('service_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if (!empty($value)) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter agency
        $value = $request->agency;
        if (!empty($value)) {
            $data = $data->whereIn('shipments.agency_id', $value);
        }

        //filter agency
        $value = $request->sender_agency;
        if (!empty($value)) {
            $data = $data->whereIn('sender_agency_id', $value);
        }

        //filter recipient agency
        $value = $request->recipient_agency;
        if (!empty($value)) {
            $data = $data->whereIn('recipient_agency_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            if (in_array('not-assigned', $value)) {
                $data = $data->where(function ($q) use ($value) {
                    $q->whereNull('operator_id');
                    $q->orWhereIn('operator_id', $value);
                });
            } else {
                $data = $data->whereIn('operator_id', $value);
            }
        }

        //filter route
        $value = $request->route;
        if ($request->has('route')) {
            if (in_array('not-assigned', $value)) {
                $data = $data->where(function ($q) use ($value) {
                    $q->whereNull('route_id');
                    $q->orWhereIn('route_id', $value);
                });
            } else {
                $data = $data->whereIn('route_id', $value);
            }
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where(function ($q) use ($value) {
                $q->where(function ($q) use ($value) {
                    $q->where('shipments.customer_id', $value);
                    $q->where(function ($q) use ($value) {
                        $q->where('requested_by', $value)
                            ->orWhereNull('requested_by');
                    });
                })
                    ->orWhere(function ($q) use ($value) {
                        $q->where('shipments.customer_id', '<>', $value)
                            ->where('requested_by', $value);
                    });
            });
        }

        //filter sender country
        $value = $request->get('sender_country');
        if ($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->get('recipient_country');
        if ($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
        }

        //filter received_method
        $value = $request->get('received_method');
        if ($request->has('received_method')) {
            $data = $data->whereHas('refund_control', function ($q) use ($value) {
                $q->whereIn('received_method', $value);
            });
        }

        $value = $request->get('operator_received_method');
        if ($request->has('operator_received_method')) {
            $data = $data->whereIn('shipments.refund_method', $value);
        }

        //filter received date
        $dtMin = $request->get('received_date_min');
        if ($request->has('received_date_min')) {

            $dtMax = $dtMin;

            if ($request->has('received_date_max')) {
                $dtMax = $request->get('received_date_max');
            }

            $data = $data->whereHas('refund_control', function ($q) use ($dtMin, $dtMax) {
                $q->whereBetween('received_date',  [$dtMin, $dtMax]);
            });
        }

        //filter payment date
        $dtMin = $request->get('payment_date_min');
        if ($request->has('payment_date_min')) {

            $dtMax = $dtMin;

            if ($request->has('payment_date_max')) {
                $dtMax = $request->get('payment_date_max');
            }

            $data = $data->whereHas('refund_control', function ($q) use ($dtMin, $dtMax) {
                $q->whereBetween('payment_date',  [$dtMin, $dtMax]);
            });
        }

        //filter payment_method
        $value = $request->get('payment_method');
        if ($request->has('payment_method')) {
            $data = $data->whereHas('refund_control', function ($q) use ($value) {
                $q->whereIn('payment_method', $value);
            });
        }
 
        //filter confirmed
        $value = $request->get('confirmed');
        if ($request->has('confirmed')) {
            $data = $data->whereHas('refund_control', function ($q) use ($value) {
                $q->where('confirmed', $value);
            });
        }

        return $data;
    }
    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterAgencies($query, $agencies = null)
    {

        $user = Auth::user();
        $customer = Auth::guard('customer')->user();

        if (empty($agencies)) {
            if ($user) {
                $agencies = $user->agencies;
            } elseif ($customer) {
                $agencies = [$customer->agency_id];
            }
        }


        if (($user && !$user->hasRole([config('permissions.role.admin')])) || !empty($agencies)) {

            return $query->where(function ($q) use ($agencies) {
                $q->whereIn('agency_id', $agencies)
                    ->orWhereIn('sender_agency_id', $agencies)
                    ->orWhereIn('recipient_agency_id', $agencies);
            });
        } elseif ($user && $user->isSeller()) {

            return $query->whereHas('customer', function ($q) use ($user) {
                $q->whereNull('seller_id')
                    ->orWhere('seller_id', $user->id);
            });
        }
    }


    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterMyAgencies()
    {

        $user = Auth::user();
        $agencies = @$user->agencies;

        if ($user && !$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            //return $this->whereIn($this->table.'.agency_id', Auth::user()->agencies); //original
            return $this->where(function ($q) {
                $q->whereIn($this->table . '.agency_id', Auth::user()->agencies);
                $q->orWhereIn($this->table . '.sender_agency_id', Auth::user()->agencies);
            });
        }
    }

    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterCustomer($query, $customer = null)
    {

        if (empty($customer)) {
            $customer = @Auth::guard('customer')->user();
        }

        if ($customer) {
            if ($customer->customer_id) {
                if (!empty($customer->view_parent_shipments)) {
                    return $query->where(function ($q) use ($customer) {
                        $q->where('customer_id', $customer->customer_id);
                        $q->orWhere('requested_by', $customer->customer_id);
                    });
                } else {
                    return $query->where('department_id', $customer->id);
                }
            } else {
                return $query->where(function ($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                    $q->orWhere('requested_by', $customer->id);
                });
            }
        }
    }

    /**
     * Store devolution expense if expense exists
     *
     * @param $expenses
     * @return null
     */
    public function storeDevolutionExpenseIfExists()
    {
        $shipment = $this;

        $expense = ShippingExpense::filterSource()
            ->where('type', 'devolution')
            ->where(function ($q) use ($shipment) {
                $q->whereNull('trigger_services');
                $q->orWhere('trigger_services', 'like', '%"' . $shipment->service_id . '"%');
            })
            ->first();

        if ($expense) {
            $price = $expense->getPrice($this, null, 1);

            $shipmentExpense = ShipmentExpense::firstOrNew([
                'shipment_id' => $this->id,
                'expense_id'  => $expense->id,
            ]);

            $shipmentExpense->shipment_id   = $this->id;
            $shipmentExpense->expense_id    = $expense->id;
            $shipmentExpense->qty           = 1;
            $shipmentExpense->price         = @$price['price'];
            $shipmentExpense->subtotal      = @$price['price'];
            $shipmentExpense->cost_price    = @$price['cost'];
            $shipmentExpense->date          = $this->billing_date;
            $shipmentExpense->save();

            ShipmentExpense::updateShipmentTotal($this->id);

            return $shipmentExpense;
        }

        return false;
    }


    /*public static function storeExpenseByShipmentId($shipmentId, $data)
    {

        $success = true;

        $expenseId = $data['id'];

        $shipmentExpense = ShipmentExpense::firstOrNew([
            'shipment_id' => $shipmentId,
            'id' => $expenseId
        ]);

        if (!$shipmentExpense->exists) { //se não encontrou através do ID, tenta através do expense_id
            $expenseId = $data['expense_id'];
            $shipmentExpense = ShipmentExpense::firstOrNew([
                'shipment_id' => $shipmentId,
                'expense_id'  => $expenseId
            ]);
        }

        $data['date'] = $shipmentExpense->exists ? $shipmentExpense->date : date('Y-m-d');

        if ($shipmentExpense->validate($data)) {
            $shipmentExpense->fill($data);
            $shipmentExpense->save();

            //update shipment total_expenses field
            ShipmentExpense::updateShipmentTotal($shipmentId);
        } else {
            $success = false;
        }

        return $success;
    }*/

    public function senderIsIsland()
    {
        $zipCode = explode('-', $this->sender_zip_code);
        return $this->sender_country == 'pt' && in_array(@$zipCode[0], $this->getIslandsZipCodes());
    }

    public function recipientIsIsland()
    {
        $zipCode = explode('-', $this->recipient_zip_code);
        return $this->recipient_country == 'pt' && in_array(@$zipCode[0], $this->getIslandsZipCodes());
    }

    public function scopeIsBlocked($query, $blocked = true)
    {
        return $this->where('is_blocked', $blocked);
    }

    public function scopeIsPickup($query, $pickup = true)
    {
        return $this->where('is_collection', $pickup);
    }

    public function getIslandsZipCodes()
    {
        return [
            9000, 9004, 9020, 9024, 9030, 9050, 9054, 9060, 9064, 9100, 9125, 9135, 9200, 9225,
            9230, 9240, 9270, 9300, 9304, 9325, 9350, 9360, 9370, 9374, 9385, 9400, 9580, 9500,
            9504, 9545, 9555, 9560, 9600, 9625, 9630, 9650, 9675, 9680, 9684, 9700, 9701, 9760,
            9880, 9800, 9804, 9850, 9875, 9930, 9934, 9940, 9944, 9950, 9900, 9901, 9904, 9960,
            9970, 9980
        ];
    }

    /**
     * Return shipment vat tax
     * 
     * @param bool $returnOnlyValue
     * @return array|float
     */
    public function getVatRate($returnOnlyValue = false)
    {
        if ($this->vat_rate_id) {
            $vatRate = VatRate::filterSource()->find($this->vat_rate_id);
            return [
                'id'     => @$vatRate->id,
                'type'   => @$vatRate->exemption_reason ? 'exempt' : 'normal',
                'code'   => @$vatRate->code,
                'value'  => @$vatRate->value,
                'reason' => @$vatRate->exemption_reason,
                'item'   => $this->getBillingItem(),
            ];
        }

        if (Setting::get('app_country') == 'pt') {
            $details = $this->getVatRatePT();
        } else {
            $defaultValue = Setting::get('vat_rate_normal');

            $details = [
                'id'     => $defaultValue,
                'type'   => 'normal',
                'code'   => $defaultValue,
                'value'  => $defaultValue,
                'reason' => null,
                'item'   => $this->getBillingItem()
            ];
        }

        if ($returnOnlyValue) {
            return $details['value'];
        }

        return $details;
    }

    public function getVatRatePT($customer = null)
    {

        $defaultVatRate = VatRate::getDefaultRate();
        // $vatNormal = Setting::get('vat_rate_normal');

        $shipment  = $this;
        $customer  = @$shipment->customer;

        $vatType  = 'normal';
        $vatValue = @$defaultVatRate->value;
        $vatId    = @$defaultVatRate->id;
        $vatCode  = @$defaultVatRate->code;
        $reason   = null;

        //1. verifica se o serviço força iva
        if (@$this->service->vat_rate) {
            $vatRate  = VatRate::getByCode(@$this->service->vat_rate);

            $vatType  = $vatRate->subclass == 'ise' ? 'exempt' : 'normal';
            $vatValue = $vatRate->value;
            $vatId    = @$vatRate->id;
            $vatCode  = @$vatRate->code;
            $reason   = @$vatRate->exemption_reason;

        //2. verifica se cliente é nif estrangeiro
        } else if (@$customer->billing_country && @$customer->billing_country != 'pt') {
            $vatRate  = VatRate::getByCode('M40');

            $vatType  = 'exempt';
            $vatValue = 0;
            $vatId    = @$vatRate->id;
            $vatCode  = @$vatRate->code;
            $reason   = @$vatRate->exemption_reason;
        } else {

            //3. verifica se o cliente é particular ou consumidor final
            if (
                @$customer->is_particular
                || empty(@$customer->vat)
                || @$customer->vat == '999999990'
                || @$customer->vat == '999999999'
            ) {
                $vatType  = 'normal';
                $vatValue = @$defaultVatRate->value;
                $vatId    = @$defaultVatRate->id;
                $vatCode  = @$vatRate->code;
                $reason   = null;
            }

            //3. verifica se o serviço é isento de IVA
            elseif (@$shipment->service->is_mail) {
                $vatRate  = VatRate::getByCode('M99');
                
                $vatType  = 'exempt';
                $vatValue = 0;
                $vatId    = @$vatRate->id;
                $reason   = @$vatRate->exemption_reason;
                $vatCode  = @$vatRate->code;
                $billingItem = 'mail';
            }

            //4. verifica país origem e destino do envio
            elseif (($shipment->sender_country == 'pt' && $shipment->recipient_country != 'pt')
                || ($shipment->sender_country != 'pt' && $shipment->recipient_country != 'pt')
                || ($shipment->sender_country == 'pt' && $shipment->recipient_country == 'pt' && ($shipment->recipientIsIsland() || $shipment->senderIsIsland()))
            ) {
                $vatRate  = VatRate::getByCode('M05');

                $vatType  = 'exempt';
                $vatValue = 0;
                $vatId    = @$vatRate->id;
                $vatCode  = @$vatRate->code;
                $reason   = @$vatRate->exemption_reason;
            }
        }

        $result = [
            'id'     => $vatId,
            'type'   => $vatType,
            'code'   => $vatId,
            'value'  => $vatValue,
            'reason' => $reason,
            'item'   => $this->getBillingItem(),
        ];

        return $result;
    }

    /**
     * Retorna o artigo de faturação a usar
     * @param false $returnOnlyValue
     * @return string
     */
    public function getBillingItem()
    {
        $shipment  = $this;
        $sender    = $shipment->sender_country;
        $recipient = $shipment->recipient_country;

        $itemAbrv = 'NAC'; //nacional - variavel abreviada
        $item     = 'nacional'; //envios nacionais

        //verifica por serviço
        if (@$shipment->service->is_mail) {
            $item = 'mail';
        }

        //estafetagem
        elseif (@$shipment->service->is_courier) {
            $item = 'courier';
        }

        //espanha
        elseif (($sender == 'pt' && $recipient == 'es')
            || ($sender == 'es' && $recipient == 'es')) {
                $itemAbrv = 'ESP';
                $item     = 'spain';
        }

        //importação
        elseif ($sender != 'pt' && $recipient == 'pt') {
            $itemAbrv = 'IMP';
            $item     = 'import';
        }

        //internacional
        elseif (($sender == 'pt' && !in_array($recipient, ['pt', 'es']))
            || ($sender != 'pt' && $recipient != 'pt')
        ) {
            $itemAbrv = 'EXP'; //exportação
            $item     = 'internacional';
        }

        if(Setting::get('invoice_items_method') == 'services') {
            return $itemAbrv.$this->service_id;
        }

        return $item;
    }

    public function isVatExempt()
    {
  
        if ($this->vat_rate === null) {

            //metodo antigo (anterior a 15/09/2022)
            //permite garantir que envios anteriores à versao 2.6 do sistema continuam a funcionar normalmente
            $appCountry = Setting::get('app_country');

            //if (@$this->customer->is_particular) { //serviços CTT correio (isento iva)
            if(str_contains(@$this->service->vat_rate, 'M')) {
                return true;
            }

            return !(($this->sender_country == $appCountry && $this->recipient_country == $appCountry && !$this->recipientIsIsland() && !$this->senderIsIsland())
                || ($this->sender_country != $appCountry && $this->recipient_country == $appCountry));
        }

        return $this->vat_rate > 0 ? false : true;
        
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

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setSenderIdAttribute($value)
    {
        $this->attributes['sender_id'] = empty($value) ? null : $value;
    }

    public function setDepartmentIdAttribute($value)
    {
        $this->attributes['department_id'] = empty($value) ? null : $value;
    }

    public function setRecipientIdAttribute($value)
    {
        $this->attributes['recipient_id'] = empty($value) ? null : $value;
    }

    public function setSenderPudoIdAttribute($value)
    {
        $this->attributes['sender_pudo_id'] = empty($value) ? null : $value;
    }

    public function setRecipientPudoIdAttribute($value)
    {
        $this->attributes['recipient_pudo_id'] = empty($value) ? null : $value;
    }
    
    public function setDispatcherIdAttribute($value)
    {
        $this->attributes['dispatcher_id'] = empty($value) ? null : $value;
    }

    public function setServiceIdAttribute($value)
    {
        $this->attributes['service_id'] = empty($value) ? null : $value;
    }

    public function setTripIdAttribute($value)
    {
        $this->attributes['trip_id'] = empty($value) ? null : $value;
    }

    public function setTransportTypeIdAttribute($value)
    {
        $this->attributes['transport_type_id'] = empty($value) ? null : $value;
    }
    
    public function setReferenceAttribute($value)
    {
        $this->attributes['reference'] = empty($value) ? null : $value;
    }

    public function setReference2Attribute($value)
    {
        $this->attributes['reference2'] = empty($value) ? null : $value;
    }

    public function setReference3Attribute($value)
    {
        $this->attributes['reference3'] = empty($value) ? null : $value;
    }

    public function setSenderAttnAttribute($value)
    {
        $this->attributes['sender_attn'] = empty($value) ? null : $value;
    }

    public function setSenderVatAttribute($value)
    {
        $this->attributes['sender_vat'] = empty($value) ? null : $value;
    }

    public function setSenderZipCodeCountryAttribute($value)
    {
        $this->attributes['sender_zip_code'] = strtoupper(trim($value));
    }

    public function setSenderStateAttribute($value)
    {
        $this->attributes['sender_state'] = empty($value) ? null : $value;
    }

    public function setSenderCountryAttribute($value)
    {
        $this->attributes['sender_country'] = strtolower(trim($value));
    }

    public function setSenderPhoneAttribute($value)
    {
        $this->attributes['sender_phone'] = empty($value) ? null : $value;
    }

    public function setRecipientAttnAttribute($value)
    {
        $this->attributes['recipient_attn'] = empty($value) ? null : $value;
    }

    public function setRecipientVatAttribute($value)
    {
        $this->attributes['recipient_vat'] = empty($value) ? null : $value;
    }

    public function setRecipientZipCodeCountryAttribute($value)
    {
        $this->attributes['recipient_zip_code'] = strtoupper(trim($value));
    }

    public function setRecipientStateAttribute($value)
    {
        $this->attributes['recipient_state'] = empty($value) ? null : $value;
    }

    public function setRecipientCountryAttribute($value)
    {
        $this->attributes['recipient_country'] = strtolower(trim($value));
    }

    public function setRecipientPhoneAttribute($value)
    {
        $this->attributes['recipient_phone'] = empty($value) ? null : $value;
    }

    public function setRecipientEmailAttribute($value)
    {
        $this->attributes['recipient_email'] = empty($value) ? null : strtolower(trim($value));
    }

    public function setShipperNameAttribute($value)
    {
        return $this->attributes['shipper_name'] = empty($value) ? null : trim($value);
    }

    public function setShipperAddressAttribute($value)
    {
        return $this->attributes['shipper_address'] = empty($value) ? null : trim($value);
    }

    public function setShipperZipCodeAttribute($value)
    {
        return $this->attributes['shipper_zip_code'] = empty($value) ? null : trim($value);
    }

    public function setShipperCityAttribute($value)
    {
        return $this->attributes['shipper_city'] = empty($value) ? null : trim($value);
    }

    public function setShipperCountryAttribute($value)
    {
        return $this->attributes['shipper_country'] = empty($value) ? null : trim($value);
    }

    public function setShipperVatAttribute($value)
    {
        return $this->attributes['shipper_vat'] = empty($value) ? null : trim($value);
    }

    public function setShipperPhoneAttribute($value)
    {
        return $this->attributes['shipper_phone'] = empty($value) ? null : trim($value);
    }

    public function setReceiverNameAttribute($value)
    {
        return $this->attributes['receiver_name'] = empty($value) ? null : trim($value);
    }

    public function setReceiverAddressAttribute($value)
    {
        return $this->attributes['receiver_address'] = empty($value) ? null : trim($value);
    }

    public function setReceiverZipCodeAttribute($value)
    {
        return $this->attributes['receiver_zip_code'] = empty($value) ? null : trim($value);
    }

    public function setReceiverCityAttribute($value)
    {
        return $this->attributes['receiver_city'] = empty($value) ? null : trim($value);
    }

    public function setReceiverCountryAttribute($value)
    {
        return $this->attributes['receiver_country'] = empty($value) ? null : trim($value);
    }

    public function setReceiverVatAttribute($value)
    {
        return $this->attributes['receiver_vat'] = empty($value) ? null : trim($value);
    }

    public function setReceiverPhoneAttribute($value)
    {
        return $this->attributes['receiver_phone'] = empty($value) ? null : trim($value);
    }

    public function setIncotermAttribute($value)
    {
        $this->attributes['incoterm'] = empty($value) ? null : $value;
    }

    public function setStartHourAttribute($value)
    {
        $this->attributes['start_hour'] = empty($value) ? null : $value;
    }

    public function setEndHourAttribute($value)
    {
        $this->attributes['end_hour'] = empty($value) ? null : $value;
    }

    public function setClosedAtAttribute($value)
    {
        $this->attributes['closed_at'] = empty($value) ? null : $value;
    }

    public function setVehicleAttribute($value)
    {
        $this->attributes['vehicle'] = empty($value) ? null : $value;
    }

    public function setTrailerAttribute($value)
    {
        $this->attributes['trailer'] = empty($value) ? null : $value;
    }

    public function setObsAttribute($value)
    {
        $this->attributes['obs'] = empty($value) ? null : $value;
    }

    public function setObsDeliveryAttribute($value)
    {
        $this->attributes['obs_delivery'] = empty($value) ? null : $value;
    }

    public function setObsInternalAttribute($value)
    {
        $this->attributes['obs_internal'] = empty($value) ? null : $value;
    }

    public function setProviderCargoAgencyAttribute($value)
    {
        $this->attributes['provider_cargo_agency'] = empty($value) ? null : $value;
    }

    public function setProviderSenderAgencyAttribute($value)
    {
        $this->attributes['provider_sender_agency'] = empty($value) ? null : $value;
    }

    public function setProviderRecipientAgencyAttribute($value)
    {
        $this->attributes['provider_recipient_agency'] = empty($value) ? null : $value;
    }

    public function setChargePriceAttribute($value)
    {
        $this->attributes['charge_price'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setCountDischargesAttribute($value)
    {
        $this->attributes['count_discharges'] = empty($value) ? 1 : $value;
    }

    public function setCodAttribute($value)
    {
        //C CLIENTE
        //P PAGO
        //S REMETENTE
        //D DESTINATARIO
        $this->attributes['cod'] = empty($value) ? null : $value;
    }

    public function setShippingPriceAttribute($value)
    {
        $this->attributes['shipping_price'] = empty($value) ? 0.00 : $value; //comentado em 20/12 porque ao editar o serviço, ficava o campo totalmente vazio e dava erro.
        $this->attributes['total_price'] = $this->attributes['shipping_price']; //atualiza campo antigo.
    }

    public function setShippingBasePriceAttribute($value)
    {
        $this->attributes['shipping_base_price'] = empty($value) ? 0.00 : $value;
        $this->attributes['base_price'] = $this->attributes['shipping_base_price']; //atualiza campo antigo.
    }

    public function setExpensesPriceAttribute($value)
    {
        $this->attributes['expenses_price'] = (empty($value) || $value == 0.00) ? null : $value;
        $this->attributes['total_expenses'] = $this->attributes['expenses_price']; //atualiza campo antigo.
    }

    public function setTotalPriceWhenCollectingAttribute($value)
    {
        $this->attributes['total_price_when_collecting'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setFatorM3Attribute($value)
    {
        $this->attributes['fator_m3'] = (empty($value) ||  $value == 0.00) ? null : $value;
    }

    public function setVolumeM3Attribute($value)
    {
        $this->attributes['volume_m3'] = (empty($value) ||  $value == 0.00) ? null : $value;
    }

    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setLitersAttribute($value)
    {
        $this->attributes['liters'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setVolumetricWeightAttribute($value)
    {
        $this->attributes['volumetric_weight'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setProviderWeightAttribute($value)
    {
        $this->attributes['provider_weight'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setKmsAttribute($value)
    {
        $this->attributes['kms'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setFuelTaxAttribute($value)
    {
        $this->attributes['fuel_tax'] = empty($value) ? null : $value;
    }

    public function setInsurancePriceAttribute($value)
    {
        $this->attributes['insurance_price'] = (empty($value) || $value == 0.00) ? null : $value;
    }

    public function setComplementarServicesAttribute($value)
    {
        $this->attributes['complementar_services'] = empty($value) ? null : json_encode($value);
    }

    public function setStatusDateAttribute($value)
    {
        $this->attributes['status_date'] = empty($value) ? null : $value;

        if (Setting::get('shipments_billing_date') == 'delivery' && $this->attributes['status_id'] == ShippingStatus::DELIVERED_ID) {
            $this->attributes['billing_date'] = $this->attributes['status_date'];
            ShipmentExpense::where('shipment_id', $this->attributes['id'])->update(['date' => $this->attributes['billing_date']]);
        }
    }

    public function setOptionalFieldsAttribute($value)
    {
        $this->attributes['optional_fields'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomFieldsAttribute($value)
    {
        $this->attributes['custom_fields'] = empty($value) ? null : json_encode($value);
    }

    public function setCodMethodsAttribute($value)
    {
        $this->attributes['cod_methods'] = empty($value) ? null : json_encode($value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = empty($value) ? null : json_encode($value);
    }

    public function setPackagingTypeAttribute($value)
    {
        $this->attributes['packaging_type'] = empty($value) ? null : json_encode($value);
    }

    public function setHasReturnAttribute($value)
    {
        if (!empty($value) && is_array($value)) {
            $value = array_filter($value);
            $value = array_unique($value);
            $value = array_values($value);

            $this->attributes['has_return'] = empty($value) ? null : json_encode($value);
        } else {
            $this->attributes['has_return'] = null;
        }
    }

    public function setGuideRequiredAttribute($value)
    {
        $this->attributes['guide_required'] = empty($value) ? null : $value;
    }

    public function setInvoiceIdAttribute($value)
    {
        $this->attributes['invoice_id'] = empty($value) ? null : $value;
    }

    public function setInvoiceTypeAttribute($value)
    {
        $this->attributes['invoice_type'] = empty($value) ? null : $value;
    }

    public function setStartHourPickupAttribute($value) {
        $this->attributes['start_hour_pickup'] = empty($value) ? null : $value;
    }

    public function setEndHourPickupAttribute($value) {
        $this->attributes['end_hour_pickup'] = empty($value) ? null : $value;
    }

    public function getIsPickupAttribute($value)
    {
        return $this->is_collection;
    }

    public function getSenderIntcodeAttribute($value)
    {
        return strtoupper($this->sender_country) . substr($this->sender_zip_code, 0, 2);
    }

    public function getRecipientIntcodeAttribute($value)
    {
        return strtoupper($this->recipient_country) . substr($this->recipient_zip_code, 0, 2);
    }

    public function getSenderFullAddressAttribute($value)
    {
        return $this->sender_address.', '. $this->sender_zip_code. ' '.$this->sender_city.', '.trans('country.'.$this->sender_country);
    }

    public function getRecipientFullAddressAttribute($value)
    {
        return $this->recipient_address.', '. $this->recipient_zip_code. ' '.$this->recipient_city.', '.trans('country.'.$this->recipient_country);
    }

    /* public function getShipperNameAttribute($value)
    {
        return $this->shipper_name ?? @$this->customer->name;
    }

    public function getShipperAddressAttribute($value)
    {
        return $this->shipper_address ?? @$this->customer->address;
    }

    public function getShipperZipCodeAttribute($value)
    {
        return $this->shipper_zip_code ?? @$this->customer->zip_code;
    }

    public function getShipperCityAttribute($value)
    {
        return $this->shipper_city ?? @$this->customer->city;
    }

    public function getShipperCountryAttribute($value)
    {
        return $this->shipper_country ?? @$this->customer->country;
    }

    public function getShipperVatAttribute($value)
    {
        return $this->shipper_vat ?? @$this->customer->vat;
    }

    public function getShipperPhoneAttribute($value)
    {
        return $this->shipper_phone ?? @$this->customer->phone;
    }

    public function getReceiverNameAttribute($value)
    {
        return $this->receiver_name ?? @$this->recipient_name;
    }

    public function getReceiverAddressAttribute($value)
    {
        return $this->receiver_address ?? @$this->recipient_address;
    }

    public function getReceiverZipCodeAttribute($value)
    {
        return $this->receiver_zip_code ?? @$this->recipient_zip_code;
    }

    public function getReceiverCityAttribute($value)
    {
        return $this->receiver_city ?? @$this->recipient_city;
    }

    public function getReceiverCountryAttribute($value)
    {
        return $this->receiver_country ?? @$this->recipient_country;
    }

    public function getReceiverVatAttribute($value)
    {
        return $this->receiver_vat ?? @$this->recipient_vat;
    }

    public function getReceiverPhoneAttribute($value)
    {
        return $this->receiver_phone ?? @$this->recipient_phone;
    } */

    public function getTotalPriceForRecipientAttribute($value)
    {
        return $this->cod == 'D' || $this->cod == 'S' ? $this->shipping_price : null;
    }

    public function getShippingPriceVatAttribute($value)
    {
        return $this->shipping_price * ((float) ($this->vat_rate ? $this->vat_rate : Setting::get('vat_rate_normal')) / 100);
    }

    public function getShippingPriceTotalAttribute($value)
    {
        return $this->shipping_price * (1 + ((float) ($this->vat_rate ? $this->vat_rate : Setting::get('vat_rate_normal')) / 100));
    }

    public function getFuelPriceVatAttribute($value)
    {
        return $this->fuel_price * ((float) ($this->vat_rate ? $this->vat_rate : Setting::get('vat_rate_normal')) / 100);
    }

    public function getFuelPriceTotalAttribute($value)
    {
        return $this->fuel_price * (1 + ((float) ($this->vat_rate ? $this->vat_rate : Setting::get('vat_rate_normal')) / 100));
    }

    public function getComplementarServicesAttribute($value)
    {
        return json_decode($value);
    }

    public function getCustomFieldsAttribute($value)
    {
        return json_decode($value);
    }

    public function getCodMethodsAttribute($value)
    {
        return json_decode($value);
    }

    public function getTagsAttribute($value)
    {
        return $value ? json_decode($value) : [];
    }

    public function getHasReturnAttribute($value)
    {
        return json_decode($value);
    }

    public function getPackagingTypeAttribute($value)
    {
        return empty($value) ? null : json_decode($value, true);
    }

    public function getVolumesAttribute($value)
    {
        return empty($value) ? null : $value;
    }

    public function getIteneraryAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getTagsHtmlAttribute($value)
    {
        $tagsIcons = [
            'charge'    => '<span class="label bg-purple m-r-3" data-toggle="tooltip" title="Cobrança: ' . $this->charge_price . $this->currency . '"><i class="fas fa-euro-sign"></i></span>',
            'rpack'     => '<span class="label bg-green m-r-3" data-toggle="tooltip" title="Retorno Encomenda"><i class="fas fa-undo"></i></span>',
            'rguide'    => '<span class="label bg-green m-r-3" data-toggle="tooltip" title="Comprovativo Entrega Assinado"><i class="fas fa-file-contract"></i></span>',
            'out_hour'  => '<span class="label bg-maroon m-r-3" style="padding: 2px 3px 3px 5px;" data-toggle="tooltip" title="Fora Horas"><i class="fas fa-business-time"></i></span>',
            'weight'    => '<span class="label bg-orange m-r-3" style="padding: 2px 3px 3px 3px;" data-toggle="tooltip" title="Sobretaxa Peso">KG</span>',
            'night'     => '<span class="label bg-maroon m-r-3" style="padding: 2px 3px 3px 5px;" data-toggle="tooltip" title="Noturno"><i class="fas fa-moon"></i></span>',
            'weekend'   => '<span class="label bg-maroon m-r-3" style="padding: 2px 3px 3px 5px;" data-toggle="tooltip" title="Fim Semana"><i class="fas fa-umbrella-beach"></i> </span>',
            'assembly'  => '<span class="label bg-brown m-r-3" style="padding: 2px 4px;" data-toggle="tooltip" title="Montagem"><i class="fas fa-wrench"></i></span>',
            'sku'       => '<span class="label bg-brown m-r-3" style="padding: 2px 4px;" data-toggle="tooltip" title="Pedido Artigos"><i class="fas fa-tshirt"></i></span>'
        ];

        if ($this->tags) {
            $tags = '';
            foreach ($this->tags as $tagId) {
                $tags .= @$tagsIcons[$tagId];
            }

            return $tags;
        }

        return '';
    }

    //define se o envio é um envio de retorno/devolução/recolha
    public function getIsBackShipmentAttribute($value)
    {
        return !($this->type == \App\Models\Shipment::TYPE_RETURN ||
            $this->type == \App\Models\Shipment::TYPE_DEVOLUTION ||
            $this->type == \App\Models\Shipment::TYPE_PICKUP ||
            $this->type == \App\Models\Shipment::TYPE_RECANALIZED);
    }

    public function getCurrencyAttribute($value)
    {
        return $value ? $value : Setting::get('app_currency');
    }

    public function getGainMoneyAttribute()
    {
        $costs = $this->cost_billing_subtotal;
        $gains = $this->billing_subtotal;
        return $gains - $costs;
    }

    public function getGainPercentAttribute()
    {
        $costs = $this->cost_billing_subtotal;
        $gains = $this->billing_subtotal;
        $balance = $gains - $costs;

        if ($balance > 0.00) {
            return $gains > 0.00 ? ($balance * 100) / $gains : 0;
        } else {
            $balance = $balance * -1;
            return $gains > 0.00 ? ($balance * 100) / $gains : 0;
        }
    }

    public function isPaymentRequired()
    {

        return false;

        if (@$this->customer->payment_ && !$this->ignore_billing && !$this->invoice_id) {
            return false;
        }

        return false;
    }

    public function getTransitTimeAttribute($value)
    {
        $startDate = new Date($this->created_at);
        $endDate   = new Date(@$this->last_history->created_at);

        $days    = $startDate->diffInDays($endDate);
        $hours   = $startDate->copy()->addDays($days)->diffInHours($endDate);
        $minutes = $startDate->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);

        $str = $days ? $days . 'd ' : '';
        $str .= $hours . 'h ' . $minutes . 'min';
        return $str;
    }

    public function getLiveTrackingAttribute($value)
    {
        if (hasModule('live_tracking')) {
            if (in_array($this->status_id, ShippingStatus::OPERATORS_DELIVERY_DEFAULT_STATUS)) {
                return 'active';
            }

            return 'disabled';
        }

        return false;
    }

    public function setEcommerceGatewayIdAttribute($value) {
        $this->attributes['ecommerce_gateway_id'] = empty($value) ? null : $value;
    }

    public function setEcommerceGatewayOrderCodeAttribute($value) {
        $this->attributes['ecommerce_gateway_order_code'] = empty($value) ? null : $value;
    }
}
