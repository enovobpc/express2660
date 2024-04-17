<?php

namespace App\Models;

use App\Http\Controllers\Admin\Invoices\SalesController;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;
use Mail, Setting, DB, Auth;


class CustomerBilling extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_billing';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_billing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billing_type', 'month', 'year', 'period', 'billed', 'api_key',
        'total_month', 'total_month_vat', 'total_month_no_vat', 'total_month_cost',
        'total_discount', 'fuel_tax', 'irs_tax', 'insurance_tax', 'invoice_type', 'shipments', 'covenants', 'products'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'total_month'        => 'required',
        'total_month_vat'    => 'required',
        'total_month_no_vat' => 'required',
        'total_month_cost'   => 'required',
    );

    /**
     *
     * Relashionships
     *
     */
    public function customers()
    {
        return $this->hasMany('App\Models\Customer');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    /**
     * Get customer billing
     *
     * @param $customerId
     * @param null $month
     * @param null $year
     * @return collection customer
     */
    public static function getBilling($customerId, $month = null, $year = null, $period = '30d', $dataIds = null, $billedIds = [], $ignoreCOD = false)
    {

        $model = new self();
       // return  $model->getServicesMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD);
       
       // return self::getDefaultMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD);

        $original = self::getDefaultMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD);
        $original = $original->toArray();


        $novo = $model->getServicesMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD);
        $novo = $novo->toArray();

        $ignoreArr =  [
            'total_shipments_spain', 'count_shipments_spain', 'count_shipments_volumes_spain', 'total_shipments_cost_spain', 'total_expenses_spain',
            'total_shipments_internacional', 'count_shipments_internacional', 'count_shipments_volumes_internacional', 'total_shipments_cost_internacional',
            'total_shipments_cost_islands', 'total_shipments_islands', 'count_shipments_islands', 'count_shipments_volumes_islands'
        ];

        $camposCorretosAgora = [
            'total_shipments_cost', 'total_export'
        ];
      
        echo '<table>';
        echo '<tr>';
        echo '<td>Field</td>';
        echo '<td>Antigo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        echo '<td>Novo</td>';
        foreach($original as $fieldName => $fieldValue) {
            
            if(@$novo[$fieldName] != $fieldValue && !in_array($fieldName, $ignoreArr) && !in_array($fieldName, $camposCorretosAgora)) {
                echo '<tr>';
                echo '<td>'.$fieldName.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                echo '<td>'.(is_array($fieldValue) || is_object($fieldValue)  ? 'array' : $fieldValue).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                echo '<td>'.(is_array($fieldValue) || is_object($fieldValue)  ? 'array' : @$novo[$fieldName]).'</td>';
            }

            echo '</tr>';
        }
        echo '</table>';

        dd($novo['billing']);
        
        /* if(Setting::get('invoice_items_method') == 'services') {
            $model = new self();
            return $model->getServicesMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD);
        }

        return self::getDefaultMethodBilling($customerId, $month, $year, $period, $dataIds, $billedIds, $ignoreCOD); */
    }

    /**
     * Calculate billing prices by shipment services
     *
     * @param [type] $customerId
     * @param [type] $month
     * @param [type] $year
     * @param string $period
     * @param [type] $dataIds
     * @param array $billedIds
     * @param boolean $ignoreCOD
     * @return object
     */
    public function getServicesMethodBilling($customerId, $month = null, $year = null, $period = '30d', $dataIds = null, $billedIds = [], $ignoreCOD = false)
    {

        $customer = Customer::filterAgencies()->find($customerId);
        $customer->customer_id = $customer->id;
        if (!$customer->exists) {
            $customer = new Customer();
            $customer->id   = $customerId;
            $customer->name = 'Sem cliente associado';
        }

        $response   = new \stdClass();
        $unbilled   = new \stdClass();
        $appCountry = Setting::get('app_country');
        $month      = is_null($month) ? date('m') : $month;
        $year       = is_null($year) ? date('Y') : $year;

        $data = $this->prepareData($dataIds, $billedIds);
        $shipmentsIds       = $data['shipments'];
        $covenentsIds       = $data['covenants'];
        $productsIds        = $data['products'];
        $billedShipmentsArr = $data['billed_shipments'];
        $billedCovenantsArr = $data['billed_covenants'];
        $billedProductsArr  = $data['billed_products'];

        $periodDates        = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay     = $periodDates['first'];
        $periodLastDay      = $periodDates['last'];
        $periodDates        = [$periodFirstDay, $periodLastDay];

        //===================================================
        // FATURAÇÃO AVENÇAS MENSAIS
        //===================================================
        $allMonthCovenants   = $this->getAllMonthCovenants($customer, $periodDates, $covenentsIds);
        $allBillingCovenants = $allMonthCovenants->filter(function($item) use($billedCovenantsArr) { 
            return !in_array($item->id, $billedCovenantsArr);
        });

        $response->covenants = $this->getCovenantResponseObject($allMonthCovenants);
        $unbilled->covenants = $this->getCovenantResponseObject($allBillingCovenants);
        $customer->total_covenants = $unbilled->covenants->total;
        $customer->count_covenants = $unbilled->covenants->count;

        //===================================================
        // FATURAÇÃO ARTIGOS
        //===================================================
        $allMonthProducts   = $this->getAllMonthProducts($customer, $periodDates, $productsIds);
        $allBillingProducts = $allMonthProducts->filter(function($item) use($billedProductsArr) { 
            return !in_array($item->id, $billedProductsArr);
        });
        $productsBoughtNoVat = $allBillingProducts->filter(function ($item) {
            return $item->vat_rate == 'is';
        });
        
        $response->products = $this->getProductResponseObject($allMonthProducts);
        $unbilled->products = $this->getProductResponseObject($allBillingProducts);

       
        $customer->total_products               = $allBillingProducts->sum('subtotal');
        $customer->count_products               = $allBillingProducts->count('subtotal');
        $customer->total_products_cost          = $allBillingProducts->sum('cost_price');

        $customer->total_products_tax_0         = $productsBoughtNoVat->sum('subtotal');
        $customer->count_products_tax_0         = $productsBoughtNoVat->count('subtotal');
        $customer->total_products_cost_tax_0    = $productsBoughtNoVat->sum('cost_price');

        $customer->total_products_tax_normal      = $customer->total_products - $customer->total_products_tax_0;
        $customer->count_products_tax_normal      = $customer->count_products - $customer->count_products_tax_0;
        $customer->total_products_cost_tax_normal = $customer->total_products_cost - $customer->total_products_cost_tax_0;
    

        //===================================================
        // FATURAÇÃO ENVIOS
        //===================================================
        //envios totais do periodo selecionado e envios por faturar  
        $allPeriodShipments  = $this->getAllMonthShipments($customer, $periodDates, $shipmentsIds, $ignoreCOD);
        $allBillingShipments = $this->getAllBillingShipments($customer, $periodDates, $shipmentsIds, $billedShipmentsArr);

        //===================================================
        // filtra e separa os envios por tipo de destino
        //===================================================
        $nacionalShipments  = $this->getNacionalShipments($allBillingShipments, $appCountry);
        $importShipments    = $this->getImportShipments($allBillingShipments, $appCountry);
        $exportShipments    = $this->getExportShipments($allBillingShipments, $appCountry);
        $spainShipments     = $this->getSpainShipments($exportShipments, $appCountry);
        $islandsShipments   = $this->getIslandsShipments($exportShipments, $appCountry);
        $vatNormalShipments = $this->getVatShipments($allBillingShipments, $appCountry);
        $vatExemptShipments = $this->getExemptShipments($allBillingShipments, $appCountry);

        //===================================================
        // envios com portes no destino
        //===================================================
        $codShipments  = $allPeriodShipments->filter(function($item) { return $item->cod == 'D' || $item->cod == 'S'; });
        $response->cod = $this->getShipmentResponseObject($codShipments);
        $customer->total_cod = $response->cod->subtotal;
        $customer->count_cod = $response->cod->count;
        
        //===================================================
        // obtem lista pedidos recolha
        //===================================================
        $allPickupShipments = $allPeriodShipments->filter(function($item) { return $item->is_collection; });
        $response->pickups  = $this->getShipmentResponseObject($allPickupShipments);
        $customer->total_pickups = $response->pickups->subtotal;
        $customer->count_pickups = $response->pickups->count;

        //===================================================
        // Envios totais do mês (faturado ou não faturado)
        //===================================================
        $response->shipments = $this->getShipmentResponseObject($allPeriodShipments);
        $customer->total_shipments              = $response->shipments->subtotal;
        $customer->count_shipments_volumes      = $response->shipments->volumes;
        $customer->count_shipments              = $response->shipments->count;
        $customer->total_expenses               = $response->shipments->expenses_price;

        $customer->total_shipments_vat          = $vatNormalShipments->sum('billing_subtotal');
        $customer->count_shipments_vat          = $vatNormalShipments->count();
        $customer->count_shipments_vat_volumes  = $vatNormalShipments->sum('volumes');
        $customer->total_shipments_cost         = $vatNormalShipments->sum('cost_billing_subtotal');

        //===================================================
        // Envios por faturar
        //===================================================
        $unbilled->shipments = $this->getShipmentResponseObject($allBillingShipments);
        $customer->total_shipments         = $unbilled->shipments->subtotal; //antiga considfera shipping_price
        $customer->count_shipments_volumes = $unbilled->shipments->volumes;
        $customer->count_shipments         = $unbilled->shipments->count;
        $customer->total_expenses          = $unbilled->shipments->expenses_price;

        //nacional
        $unbilled->shipments->shipments_nacional = $this->getShipmentResponseObject($nacionalShipments);

        $customer->count_shipments_nacional         = $nacionalShipments->count();
        $customer->total_shipments_nacional         = $nacionalShipments->sum('billing_subtotal');
        $customer->count_shipments_nacional_volumes = $nacionalShipments->sum('volumes');
        $customer->total_shipments_nacional_cost    = $nacionalShipments->sum('cost_billing_subtotal');
        $customer->total_expenses_nacional          = $nacionalShipments->sum('expenses_price');

        //import
        $customer->total_shipments_import           = $importShipments->sum('billing_subtotal');
        $customer->count_shipments_import           = $importShipments->count();
        $customer->count_shipments_import_volumes   = $importShipments->sum('volumes');
        $customer->total_shipments_import_cost      = $importShipments->sum('cost_billing_subtotal');
        $customer->total_expenses_import            = $importShipments->sum('expenses_price');

        //export
        $customer->total_export                     = $exportShipments->sum('billing_subtotal');
        $customer->count_export                     = $exportShipments->count();
        $customer->count_export_volumes             = $exportShipments->sum('volumes');
        $customer->total_export_cost                = $exportShipments->sum('cost_billing_subtotal');
        $customer->total_expenses_export            = $exportShipments->sum('expenses_price');

        /**
         * Update values if customer is not PT
         */
        if ($appCountry == 'pt' && $customer->billing_country && $customer->billing_country != 'pt') {
            $customer->total_shipments_vat = 0;
            $customer->count_shipments_vat = 0;
            $customer->total_expenses_vat  = 0;
            $customer->exemption_reason    = 'M40';
        }

        $customer->total_shipments_no_vat = (float) number_format($customer->total_shipments - $customer->total_shipments_vat, 2, '.', '');
        $customer->total_expenses_no_vat  = $customer->total_expenses - $customer->total_expenses_vat;
        $customer->count_shipments_no_vat = (float) number_format($customer->count_shipments - $customer->count_shipments_vat, 2, '.', '');

        //===================================================
        // Totais no mês por faturar
        //===================================================
        $response->totals = (object) [
            'subtotal'      => $response->shipments->subtotal + $response->covenants->subtotal  + $response->products->subtotal,
            'vat'           => $response->shipments->vat + $response->covenants->vat  + $response->products->vat,
            'total'         => $response->shipments->total + $response->covenants->total  + $response->products->total,
            'cost_subtotal' => $response->shipments->cost_subtotal + $response->products->cost_subtotal,
            'cost_vat'      => $response->shipments->cost_vat + $response->products->cost_vat,
            'cost_total'    => $response->shipments->cost_total + $response->products->cost_total,
        ];

        $unbilled->totals = (object) [
            'subtotal'      => $unbilled->shipments->subtotal + $unbilled->covenants->subtotal  + $unbilled->products->subtotal,
            'vat'           => $unbilled->shipments->vat + $unbilled->covenants->vat  + $unbilled->products->vat,
            'total'         => $unbilled->shipments->total + $unbilled->covenants->total  + $unbilled->products->total,
            'cost_subtotal' => $unbilled->shipments->cost_subtotal + $unbilled->products->cost_subtotal,
            'cost_vat'      => $unbilled->shipments->cost_vat + $unbilled->products->cost_vat,
            'cost_total'    => $unbilled->shipments->cost_total + $unbilled->products->cost_total,
        ];

        $customer->total_month_absolute = $response->totals->subtotal; 
        $customer->total_month          = $unbilled->totals->subtotal;
        $customer->total_month_cost     = $unbilled->totals->cost_subtotal;

        $customer->total_month_vat      = $customer->total_shipments_vat + $customer->total_expenses_vat + $customer->total_products_tax_normal + $customer->total_covenants;
        $customer->total_month_no_vat   = (float) number_format($customer->total_month - $customer->total_month_vat, 2, '.', '');
        
        $customer->total_month_profit   = (float) number_format($customer->total_month_absolute - $customer->total_month_cost, 2, '.', '');

        //comentado em 8 junho - os particulares podem ter isencao de iva
        if ($customer->is_particular) {
            $customer->total_shipments_vat    = $customer->total_shipments_vat + $customer->total_shipments_no_vat;
            $customer->total_expenses_vat     = $customer->total_expenses_vat + $customer->total_expenses_no_vat;
            $customer->total_shipments_no_vat = 0;
            $customer->total_expenses_no_vat  = 0;
            
            $customer->total_month_vat = $customer->total_month_vat + $customer->total_month_no_vat;
            $customer->total_month_no_vat = 0;
        }
        

        //===================================================
        // retorna variaveis com os ID de todos os items a faturar
        //===================================================
        $customer->shipments = $allBillingShipments->pluck('id')->toArray();
        if (empty($dataIds) || (!empty($dataIds) && (isset($dataIds['covenants']) || isset($dataIds['products'])))) {
            $customer->covenants = $allBillingCovenants->pluck('id')->toArray();
            $customer->products  = $allBillingProducts->pluck('id')->toArray();
        } else {
            $customer->covenants = $customer->products = [];
        }

        //===================================================
        // PAREPARA RESPOSTA
        //===================================================
        $customer->billing_subtotal_with_vat   = 0;
        $customer->billing_subtotal_with_novat = 0;


        //novas variaveis
        $response->lines             = $this->getBillingLinesArr($customer, $allBillingShipments);
        $response->fuel_taxes        = $this->getFuelTaxesArr($customer, $allBillingShipments);
        $response->billing_warnings  = $this->getBillingWarningsArr($customer, $allBillingShipments, $allPickupShipments, $allBillingCovenants, $allBillingProducts);
        $response->billing_closed    = $customer->doc_subtotal > 0.00 ? false : true;
        
        $response->period   = (object)[
            'code'       => $period,
            'year'       => $year,
            'month'      => $month,
            'first_date' => $periodFirstDay,
            'last_date'  => $periodLastDay
        ];

        $response->invoice = (object)[
            'doc_type'          => $customer->default_invoice_type,
            'payment_condition' => $customer->payment_method,
            'reference'         => $customer->invoice_reference,
            'obs'               => $customer->obs_billing,
        ];


        //variaveis antigas
        $customer->lines             = $response->lines;
        $customer->fuel_taxes        = $response->fuel_taxes;
        $customer->billing_warnings  = $response->billing_warnings;
        $customer->billing_closed    = $response->billing_closed;
        $customer->period            = $response->period->code;
        $customer->invoice_type      = $response->invoice->doc_type;
    
        //nova variavel para trabalho futuro
        $customer->billing  = collect($response); 
        $customer->unbilled = collect($unbilled); 

        return $customer;
    }


    /**
     * Confere os dados recebidos pelo parametro dataIds e retorna o array validado e corrigido
     *
     * @param [type] $dataIds
     * @return void
     */
    public function prepareData($dataIds, $billedIds) {

        $shipmentsIds = []; //por defeito é tudo envios
        $covenentsIds = [];
        $productsIds  = [];

        if(!is_null($dataIds)) {

            //envios
            if (isset($dataIds['shipments'])) {
                $shipmentsIds = $dataIds['shipments'];
            } else {
                $shipmentsIds = [0]; //os ID selecionados não são de envios (não há nenhum envio para faturar). sem isto ele vai assumir todos os envios do periodo
            }

            //avenças mensais
            if (isset($dataIds['covenants'])) {
                $covenentsIds = $dataIds['covenants'];
            }

            //artigos vendidos neste periodo
            if (!is_null($dataIds) && isset($dataIds['products'])) {
                $productsIds = $dataIds['products'];
            }
        }

        return [
            'shipments' => $shipmentsIds,
            'covenants' => $covenentsIds,
            'products'  => $productsIds,
            'billed_shipments' => @$billedIds['shipments']?:[],
            'billed_covenants' => @$billedIds['covenants']?:[],
            'billed_products'  => @$billedIds['products']?:[],
        ];
    }

    /**
     * Retorna uma resposta em formato objecto com os atributos totais para um envio 
     *
     * @param [type] $shipmentCollection
     * @return object
     */
    public function getShipmentResponseObject($shipmentCollection) {

        $responseArr = [
            'count'          => $shipmentCollection->count(),
            'volumes'        => $shipmentCollection->sum('volumes'),
            'subtotal'       => $shipmentCollection->sum('billing_subtotal'),
            'vat'            => $shipmentCollection->sum('billing_vat'),
            'total'          => $shipmentCollection->sum('billing_total'),
            'cost_subtotal'  => $shipmentCollection->sum('cost_billing_subtotal'),
            'cost_vat'       => $shipmentCollection->sum('cost_billing_vat'),
            'cost_total'     => $shipmentCollection->sum('cost_billing_total'),
            'shipping_price' => $shipmentCollection->sum('shipping_price'),
            'expenses_price' => $shipmentCollection->sum('expenses_price'),
            'fuel_price'     => $shipmentCollection->sum('fuel_price'),
            'shipments'      => $shipmentCollection
        ];

        return (object) $responseArr;
    }

    /**
     * Retorna uma resposta em formato objecto com os atributos totais para uma avença 
     *
     * @param [type] $shipmentCollection
     * @return object
     */
    public function getCovenantResponseObject($covenantCollection) {

        $responseArr = [
            'count'     => $covenantCollection->count(),
            'subtotal'  => $covenantCollection->sum('amount'),
            'vat'       => 0,
            'total'     => $covenantCollection->sum('amount'),
            'covenants' => $covenantCollection
        ];

        return (object) $responseArr;
    }

    /**
     * Retorna uma resposta em formato objecto com os atributos totais para um produto 
     *
     * @param [type] $shipmentCollection
     * @return object
     */
    public function getProductResponseObject($productCollection) {

        $responseArr = [
            'count'         => $productCollection->count(),
            'subtotal'      => $productCollection->sum('subtotal'),
            'vat'           => $productCollection->sum('vat'),
            'total'         => $productCollection->sum('total'),
            'cost_subtotal' => $productCollection->sum('subtotal'),
            'cost_vat'      => $productCollection->sum('vat'),
            'cost_total'    => $productCollection->sum('total'),
            'products'      => $productCollection
        ];

        return (object) $responseArr;
    }

    /**
     * Undocumented function
     *
     * @param [type] $customer
     * @param [type] $periodDates
     * @param [type] $covenentsIds
     * @return collection
     */
    public function getAllMonthCovenants($customer, $periodDates, $covenentsIds) {

        $covenants = CustomerCovenant::where('customer_id', $customer->id);

        if (!empty($covenentsIds)) {
            $covenants->whereIn('id', $covenentsIds);
        } else {
            $covenants->filterBetweenDates($periodDates[0], $periodDates[1]);
        }

        $covenants = $covenants->get();

        return $covenants;
    }

    /**
     * Undocumented function
     *
     * @param [type] $customer
     * @param [type] $periodDates
     * @param [type] $covenentsIds
     * @return collection
     */
    public function getAllMonthProducts($customer, $periodDates, $covenentsIds) {

        $productsBought = ProductSale::where('customer_id', $customer->id);

        if (empty($productsIds)) {
            $productsBought->whereBetween('date', $periodDates);
        } else {
            $productsBought->whereIn('id', $productsIds);
        }

        return $productsBought->get();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getShipmentBindings() {
        return [
            'id','is_collection', 'service_id', 'status_id', 'type', 'date', 'ignore_billing', 'charge_price', 'children_tracking_code',
            'sender_country', 'recipient_country', 'sender_zip_code', 'recipient_zip_code', 'cod', 'volumes', 'shipping_price', 'expenses_price', 'fuel_price', 
            'fuel_tax', 'billing_item', 'billing_subtotal', 'billing_vat', 'billing_total', 'vat_rate', 'vat_rate_id', 
            'cost_billing_subtotal', 'cost_billing_vat', 'cost_billing_total', 'invoice_id'
        ];
    }

    /**
     * Undocumented function
     *
     * @param [type] $customer
     * @param [type] $periodDates
     * @param [type] $shipmentsIds
     * @param [type] $ignoreCOD
     * @return object
     */
    public function getAllMonthShipments($customer, $periodDates, $shipmentsIds, $ignoreCOD) {

        $allShipments = Shipment::filterAgencies()
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where(function($q){
                $q->where('is_collection', 0);
                $q->orWhere(function($q){
                    $q->where('is_collection', 1);
                    $q->where('status_id', ShippingStatus::PICKUP_FAILED_ID);
                });
            });

        if ($shipmentsIds) {
            $allShipments->whereIn('id', $shipmentsIds);
        } else {
            $allShipments->whereBetween('billing_date', $periodDates);
        }

        if ($customer->exists) { //filtra cliente
            $allShipments->where('customer_id', $customer->id);
        } else {
            $allShipments->whereNull('customer_id');
        }

        if ($ignoreCOD) { //ignora pagamentos no destino  
            $allShipments->whereNotIn('cod', ['D', 'S']);
        }

        return $allShipments->get($this->getShipmentBindings());
    }

    /**
     * Undocumented function
     *
     * @param [type] $customer
     * @param [type] $periodDates
     * @param [type] $shipmentsIds
     * @param [type] $billedShipmentsArr
     * @return object
     */
    public function getAllBillingShipments($customer, $periodDates, $shipmentsIds, $billedShipmentsArr) {

        $allBillingShipments = Shipment::filterAgencies()
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereNull('invoice_id')
            ->where('ignore_billing', 0)
            ->whereNull('cod')
            ->where(function($q){
                $q->where('is_collection', 0);
                $q->orWhere(function($q){
                    $q->where('is_collection', 1);
                    $q->where('status_id', ShippingStatus::PICKUP_FAILED_ID);
                });
            });

            if($billedShipmentsArr) {
                $allBillingShipments->whereNotIn('id', $billedShipmentsArr);
            }

            if ($shipmentsIds) {
                $allBillingShipments->whereIn('id', $shipmentsIds);
            } else {
                $allBillingShipments->whereBetween('billing_date', $periodDates);
            }

            if ($customer->exists) { //filtra cliente
                $allBillingShipments->where('customer_id', $customer->id);
            } else {
                $allBillingShipments->whereNull('customer_id');
            }

        return $allBillingShipments->get($this->getShipmentBindings());
    }

    /**
     *  Retorna array com todos os envios nacionais
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getNacionalShipments($allShipments, $country = 'pt') {
        return $allShipments->filter(function ($item) use($country) {
            return $item->sender_country == $country && $item->recipient_country == $country;
        });
    }

    /**
     *  Retorna array com todos os envios de importação
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getImportShipments($allShipments, $country = 'pt') {
        return $allShipments->filter(function ($item) use ($country) {
            return $item->sender_country != $country && $item->recipient_country == $country;
        });
    }

    /**
     *  Retorna array com todos os envios de exportação
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getExportShipments($allShipments,  $country = 'pt') {
        return $allShipments->filter(function ($item) use ($country) {
            return ($item->sender_country == $country && $item->recipient_country != $country)
                || ($item->sender_country != $country && $item->recipient_country != $country);
        });
    }

    /**
     *  Retorna array com todos os envios de exportação para espanha
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getIslandsShipments($allShipments,  $country = 'pt') {
        return $allShipments->filter(function ($item) use ($country) {
            return $item->sender_country == 'pt' && $item->recipientIsIsland() 
                || $item->sender_country == 'pt' && $item->senderIsIsland();
        });
    }
    
    /**
     *  Retorna array com todos os envios de exportação para espanha
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getSpainShipments($allShipments,  $country = 'pt') {
        return $allShipments->filter(function ($item) use ($country) {
            return $item->sender_country == 'es' || $item->recipient_country == 'es';
        });
    }
    
    /**
     * Retorna array com todos os envios com iva
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getVatShipments($allShipments, $country = 'pt') {
        return $allShipments->filter(function ($item) use($country) {
            return $item->sender_country == $country && $item->recipient_country == $country;
        });
    }

    /**
     *  Retorna array com todos os envios isentos de iva
     *
     * @param [type] $allShipments
     * @param string $country
     * @return object
     */
    public function getExemptShipments($allShipments, $country = 'pt') {
        return $allShipments->filter(function ($item) use($country) {
            return $item->sender_country == $country && $item->recipient_country == $country;
        });
    }

    /**
     * Cria um array com as taxas de combustível do cliente
     *
     * @param [type] $customer
     * @param [type] $allFuelTaxes
     * @return array
     */
    public function getFuelTaxesArr(&$customer, $allBillingShipments) {

        //obtem todas as taxas de IVA
        $allFuelTaxes = $allBillingShipments->groupBy('fuel_tax');

        $fuelTotalWithVat = 0;
        $fuelTotal = 0;
        $fuelTaxes = [];

        if (!empty(Setting::get('fuel_tax'))) {

            foreach ($allFuelTaxes as $fuelTax => $fuelShipments) {

                $fuelWithVat = $shipmentsWithVat->filter(function ($item) use ($fuelTax) {
                    return $item->fuel_tax == $fuelTax;
                });

                $basePrice = $fuelShipments->sum('total_price') + $fuelShipments->sum('total_expenses');
                $subtotal  = $fuelShipments->sum('fuel_price');
                $subtotalWithVat = $fuelWithVat->sum('fuel_price');

                $fuelTotal += $subtotal;
                $fuelTotalWithVat += $subtotalWithVat;

                $vat = $subtotalWithVat * (Setting::get('vat_rate_normal') / 100);

                $fuelTaxes[] = [
                    'tax'             => $fuelTax,
                    'count'           => $fuelShipments->count(),
                    'count_with_vat'  => $fuelWithVat->count(),
                    'incidence_price' => $basePrice,
                    'incidence_vat_price' => $subtotalWithVat,
                    'total_with_vat'  => $subtotalWithVat,
                    'total_no_vat'    => $subtotal - $subtotalWithVat,
                    'subtotal'        => $subtotal,
                    'vat'             => $vat,
                    'total'           => $subtotal + $vat,
                ];
            }
        }

        //dd($fuelTaxes);
        $customer->fuel_tax_total        = $fuelTotal;
        $customer->fuel_tax_total_vat    = $fuelTotalWithVat;
        $customer->fuel_tax_total_no_vat = $fuelTotal - $fuelTotalWithVat;

        return $fuelTaxes;
    }

    /**
     * Cria array com as linhas da fatura
     *
     * @param [type] $customer
     * @return object
     */
    public function getBillingLinesArr(&$customer, $allBillingShipments) {

        $docSubtotal = $docVat = $docTotal = 0;

        $lines = [];
        
        $allIds = $allBillingShipments->pluck('id')->toArray();

        $billingItems = ShipmentExpense::whereIn('shipment_id', $allIds)
            ->where(function ($q) {
                $q->whereNotNull('billing_item_id');
                $q->orWhere('billing_item_id', '>', 0);
            })
            ->whereHas('billingItem')
            ->get();

        foreach ($billingItems as $item) {
            $key = 'item_' . $item->billingItem->id;

            if (@$lines[$key]) {
                $line = (array) $lines[$key];
                $line['qty']         += $item->qty;
                $line['qty_real']    += $item->qty;
                $line['total_price'] += $item->price;
                $line['subtotal']    += $item->subtotal;
            } else {
                $line = [
                    'key'           => $key,
                    'billing_product_id' => $item->billingItem->id,
                    'reference'     => $item->billingItem->reference,
                    'description'   => $item->billingItem->name,
                    'qty'           => $item->qty,
                    'qty_real'      => $item->qty,
                    'total_price'   => $item->price,
                    'subtotal'      => $item->subtotal,
                    'exemption_reason' => $item->vat_rate
                ];
            }

            $docSubtotal += $line['subtotal'];
            $docVat      += $exemptionReason ? 0 : $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

            $lines[$key] = (object) $line;
        }
        

        $customer->document_subtotal = (float) number($docSubtotal);
        $customer->document_vat      = (float) number($docVat);
        $customer->document_total    = (float) number($docSubtotal + $docVat);

        return (object) $lines;
    }

    /**
     * Cria array com as linhas da fatura - Modo por defeito
     *
     * @param [type] $customer
     * @return object
     */
    public static function getBillingLinesArrDefaultMode(&$customer, $covenants, $fuelTaxes, $allIds, &$docSubtotal, &$docVat) {

        $vars = [
            'courier' => [
                'ref'   => 'invoice_item_courier_ref',
                'desc'  => 'invoice_item_courier_desc',
                'count'     => 'count_shipments_courier',
                'shipments' => 'total_shipments_courier',
                'expenses'  => 'total_expenses_courier',
            ],
            'mail' => [
                'ref'       => 'invoice_item_mail_ref',
                'desc'      => 'invoice_item_mail_desc',
                'count'     => 'count_shipments_mail',
                'shipments' => 'total_shipments_mail',
                'expenses'  => 'total_expenses_mail',
            ],
            'nacional' => [
                'ref'   => 'invoice_item_nacional_ref',
                'desc'  => 'invoice_item_nacional_desc',
                'count'     => 'count_shipments_nacional',
                'shipments' => 'total_shipments_nacional',
                'expenses'  => 'total_expenses_nacional',
            ],
            'import' => [
                'ref'   => 'invoice_item_import_ref',
                'desc'  => 'invoice_item_import_desc',
                'count'     => 'count_shipments_import',
                'shipments' => 'total_shipments_import',
                'expenses'  => 'total_expenses_import',
            ],
            'islands' => [
                'ref'   => 'invoice_item_nacional_ref',
                'desc'  => 'invoice_item_nacional_desc',
                'count'     => 'count_shipments_islands',
                'shipments' => 'total_shipments_islands',
                'expenses'  => 'total_expenses_islands',
            ],
            'spain' => [
                'ref'   => 'invoice_item_spain_ref',
                'desc'  => 'invoice_item_spain_desc',
                'count'     => 'count_shipments_spain',
                'shipments'  => 'total_shipments_spain',
                'expenses' => 'total_expenses_spain',
            ],
            'internacional' => [
                'ref'   => 'invoice_item_internacional_ref',
                'desc'  => 'invoice_item_internacional_desc',
                'count'     => 'count_shipments_internacional',
                'expenses' => 'total_expenses_internacional',
                'shipments'  => 'total_shipments_internacional',
            ],
            'covenants' => [
                'ref'   => 'invoice_item_covenants_ref',
                'desc'  => 'invoice_item_covenants_desc',
                'count'     => 'count_covenants',
                'shipments'  => 'total_covenants',
            ],
            'products_tax_normal' => [
                'ref'   => 'invoice_item_products_ref',
                'desc'  => 'invoice_item_products_desc',
                'count'     => 'count_products_tax_normal',
                'shipments'  => 'total_products_tax_normal',
            ],
            'products_tax_0' => [
                'ref'   => 'invoice_item_products_ref',
                'desc'  => 'invoice_item_products_desc',
                'count'     => 'count_products_tax_0',
                'shipments'  => 'total_products_tax_0',
            ],
        ];

        $lines = [];
        foreach ($vars as $key => $value) {
            $subtotal = $customer->{@$value['shipments']} + $customer->{@$value['expenses']};

            if ($subtotal > 0.00) {

                //nas avenças escreve cada linha de avença individualmente
                if ($key == 'covenants') {

                    foreach ($covenants as $cKey => $covenant) {

                        $exemptionReason = self::getItemExemptionReason($key, $customer);

                        $line = [
                            'key'           => $key . '_' . ($cKey + 1),
                            'reference'     => Setting::get(@$value['ref']),
                            'description'   => $covenant->description,
                            'qty'           => 1,
                            'qty_real'      => 1,
                            'total_price'   => $covenant->amount,
                            'subtotal'      => $covenant->amount,
                            'exemption_reason' => $exemptionReason
                        ];

                        $docSubtotal += $line['subtotal'];
                        $docVat += $exemptionReason ? 0 : $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

                        $lines[] = (object) $line;
                    }
                }

                //todos os outros casos escreve só a linha
                else {

                    $exemptionReason = self::getItemExemptionReason($key, $customer);

                    $line = [
                        'key'           => $key,
                        'reference'     => Setting::get(@$value['ref']),
                        'description'   => self::getItemDescription($key, $value),
                        'qty'           => 1,
                        'qty_real'      => $customer->{@$value['count']},
                        'total_price'   => $subtotal,
                        'subtotal'      => $subtotal,
                        'exemption_reason' => $exemptionReason
                    ];

                    /* if($key == 'mail') {
                        dd($line);
                    }*/

                    $docSubtotal += $line['subtotal'];
                    $docVat += $exemptionReason ? 0 : $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

                    $lines[] = (object) $line;
                }
            }
        }

        /**
         * FUEL TAX
         */
        if (!empty($fuelTaxes) && Setting::get('fuel_tax_invoice_detail')) {

            foreach ($fuelTaxes as $key => $fuelTaxItem) {

                if ($fuelTaxItem['total_with_vat'] > 0.00) {
                    $line = [
                        'key'           => 'fuel_vat_' . $key,
                        'reference'     => Setting::get('invoice_item_fuel_ref'),
                        'description'   => Setting::get('invoice_item_fuel_desc') . ' - ' . money($fuelTaxItem['tax'], '%'),
                        'qty'           => 1,
                        'qty_real'      => 1,
                        'total_price'   => $fuelTaxItem['total_with_vat'],
                        'subtotal'      => $fuelTaxItem['total_with_vat'],
                        'exemption_reason' => null
                    ];

                    $docSubtotal += $line['subtotal'];
                    $docVat += $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

                    $lines[] = (object) $line;
                }

                if ($fuelTaxItem['total_no_vat'] > 0.00) {
                    $exemptionReason = self::getItemExemptionReason($key, $customer);
                    $line = [
                        'key'           => 'fuel_nvat_' . $key,
                        'reference'     => Setting::get('invoice_item_fuel_ref'),
                        'description'   => Setting::get('invoice_item_fuel_desc') . ' - ' . money($fuelTaxItem['tax'], '%'),
                        'qty'           => 1,
                        'qty_real'      => 1,
                        'total_price'   => $fuelTaxItem['total_no_vat'],
                        'subtotal'      => $fuelTaxItem['total_no_vat'],
                        'exemption_reason' => $exemptionReason
                    ];

                    $docSubtotal += $line['subtotal'];

                    $lines[] = (object) $line;
                }
            }
        }

        /**
         * INSURANCE TAX
         */
        if (!empty(Setting::get('insurance_tax'))) {

            if ($customer->insurance_tax_total_vat) {
                $line = [
                    'key'           => 'insurance_vat',
                    'reference'     => Setting::get('invoice_item_insurance_ref'),
                    'description'   => Setting::get('invoice_item_insurance_desc') . ' - ' . money($customer->insurance_tax, '%'),
                    'qty'           => 1,
                    'qty_real'      => 1,
                    'total_price'   => $customer->insurance_tax_total_vat,
                    'subtotal'      => $customer->insurance_tax_total_vat,
                    'exemption_reason' => null
                ];

                $docSubtotal += $line['subtotal'];
                $docVat += $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

                $lines[] = (object) $line;
            }

            if ($customer->insurance_tax_total_no_vat) {
                $exemptionReason = self::getItemExemptionReason($key, $customer);
                $line = [
                    'key'           => 'insurance_nvat',
                    'reference'     => Setting::get('invoice_item_insurance_ref'),
                    'description'   => Setting::get('invoice_item_insurance_desc') . ' - ' . money($customer->insurance_tax, '%'),
                    'qty'           => 1,
                    'qty_real'      => 1,
                    'total_price'   => $customer->insurance_tax_total_no_vat,
                    'subtotal'      => $customer->insurance_tax_total_no_vat,
                    'exemption_reason' => $exemptionReason
                ];

                $docSubtotal += $line['subtotal'];

                $lines[] = (object) $line;
            }
        }

        /**
         * Billing items from shipment expenses
         */
        $billingItems = ShipmentExpense::whereIn('shipment_id', $allIds)
            ->where(function ($q) {
                $q->whereNotNull('billing_item_id');
                $q->orWhere('billing_item_id', '>', 0);
            })
            ->whereHas('billingItem')
            ->get();

        foreach ($billingItems as $item) {
            $key = 'item_' . $item->billingItem->id;

            if (@$lines[$key]) {
                $line = (array) $lines[$key];
                $line['qty']         += $item->qty;
                $line['qty_real']    += $item->qty;
                $line['total_price'] += $item->price;
                $line['subtotal']    += $item->subtotal;
            } else {
                $line = [
                    'key'           => $key,
                    'billing_product_id' => $item->billingItem->id,
                    'reference'     => $item->billingItem->reference,
                    'description'   => $item->billingItem->name,
                    'qty'           => $item->qty,
                    'qty_real'      => $item->qty,
                    'total_price'   => $item->price,
                    'subtotal'      => $item->subtotal,
                    'exemption_reason' => $item->vat_rate
                ];
            }

            $docSubtotal += $line['subtotal'];
            $docVat      += $exemptionReason ? 0 : $line['subtotal'] * (Setting::get('vat_rate_normal') / 100);

            $lines[$key] = (object) $line;
        }

        $customer->document_subtotal = (float) number($docSubtotal);
        $customer->document_vat      = (float) number($docVat);
        $customer->document_total    = (float) number($docSubtotal + $docVat);

        return (object) $lines;
    }

    /**
     * Retorna array com os avisos de faturação
     *
     * @param [type] $customer
     * @param [type] $allFuelTaxes
     * @return void
     */
    public function getBillingWarningsArr(&$customer, $allBillingShipments, $allPickupShipments, $allBillingCovenants, $allBillingProducts) {

        $totalWarnings = 0;

        $countEmptyPrices = $allBillingShipments->filter(function ($item) {
            return (empty($item->billing_subtotal) || $item->billing_subtotal == 0.00 || empty($item->shipping_price) || $item->shipping_price == 0.00)
                && !in_array($item->cod, ['D', 'S'])
                && $item->type != Shipment::TYPE_MASTER;
        })->count();
        $totalWarnings+= $countEmptyPrices;

        $countEmptyBillingItem = $allBillingShipments->filter(function ($item) {
            return empty($item->billing_item);
        })->count();
        $totalWarnings+= $countEmptyBillingItem;

        $countEmptyServices = $allBillingShipments->filter(function ($item) {
            return empty($item->service_id);
        })->count();
        $totalWarnings+= $countEmptyPrices;

        $countEmptyPricesPickups = $allPickupShipments->filter(function ($item) {
            return (empty($item->total_price) || ($item->total_price + $item->total_expenses) == 0.00);
        })->count();
        $totalWarnings+= $countEmptyPrices;

        $countEmptyAssignedShipment = $allPickupShipments->filter(function ($item) {
            return empty($item->children_tracking_code) && $item->status_id != ShippingStatus::PICKUP_FAILED_ID;
        })->count();
        $totalWarnings+= $countEmptyPrices;


        /**
         * Obtem os envios que possam ter taxas mas sem as ter de verdade
         */
       /*  $shipmentsWithExpenses = $allExpenses->pluck('shipment_id')->toArray(); //obtem todos os envios com despesas reais
        //filtra os envios que por ventura possam ter encargos no campo "total_expenses" mas que não os tenham realmente.
        $shipmentsToClean = $allBillingShipments->filter(function ($item) use ($shipmentsWithExpenses) {
            return $item->total_expenses > 0.00 && !in_array($item->id, $shipmentsWithExpenses);
        }); */

        $response = (object) [
            'empty_prices'                   => $countEmptyPrices,
            'empty_billing_item'             => $countEmptyBillingItem,
            'empty_services'                 => $countEmptyServices,
            'empty_prices_pickups'           => $countEmptyPricesPickups,
            'empty_pickup_assigned_shipment' => $countEmptyAssignedShipment,
        ];

        $customer->billing_warnings     = $response;
        $customer->has_billing_warnings = $totalWarnings ? true : false;

        return $response;
    }

    /**
     * Calculate billing prices by system default option
     *
     * @param [type] $customerId
     * @param [type] $month
     * @param [type] $year
     * @param string $period
     * @param [type] $dataIds
     * @param array $billedIds
     * @param boolean $ignoreCOD
     * @return object
     */
    public static function getDefaultMethodBilling($customerId, $month = null, $year = null, $period = '30d', $dataIds = null, $billedIds = [], $ignoreCOD = false)
    {
        $month = is_null($month) ? date('m') : $month;
        $year  = is_null($year) ? date('Y') : $year;

        $shipmentsIds = $dataIds;
        if (!is_null($dataIds) && isset($dataIds['shipments'])) {
            $shipmentsIds = $dataIds['shipments'];
        }
        if (isset($shipmentsIds['covenants']) || isset($dataIds['products'])) {
            $shipmentsIds = @$dataIds['shipments']; //por defeito
        }

        if (!empty($dataIds) && empty($shipmentsIds)) {
            $shipmentsIds = [0]; //os ID selecionados não são de envios (não há nenhum envio para faturar)
            //se não indicado isto, vai selecionar todos os envios do mês
        }

        $covenentsIds = null;
        if (!is_null($dataIds) && isset($dataIds['covenants'])) {
            $covenentsIds = $dataIds['covenants'];
        }

        $productsIds = null;
        if (!is_null($dataIds) && isset($dataIds['products'])) {
            $productsIds = $dataIds['products'];
        }

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];


        $specialServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->where(function ($q) {
                $q->where('is_courier', 1);
                $q->orWhere('is_mail', 1);
            })
            ->get();

        $courierServices = $specialServices->filter(function ($item) {
            return $item->is_courier == 1;
        })->pluck('id')->toArray();

        $mailServices = $specialServices->filter(function ($item) {
            return $item->is_mail == 1;
        })->pluck('id')->toArray();

        $customer = Customer::filterAgencies()
            ->with(['billing' => function ($q) use ($year, $month, $period) {
                $q->where('year', $year)
                    ->where('month', $month)
                    ->where('period', $period);
            }])
            ->with(['productsBought' => function ($q) use ($periodFirstDay, $periodLastDay, $productsIds) {
                if (!empty($productsIds)) {
                    $q->whereIn('id', $productsIds); //faturar só as covenants indicadas
                }
                $q->whereBetween('date', [$periodFirstDay, $periodLastDay]);
            }])
            ->with(['covenants' => function ($q) use ($periodFirstDay, $periodLastDay, $covenentsIds) {
                if (!empty($covenentsIds)) {
                    $q->whereIn('id', $covenentsIds); //faturar só as covenants indicadas
                }
                $q->filterBetweenDates($periodFirstDay, $periodLastDay);
            }])
            ->firstOrNew(['id' => $customerId]);

        if (!$customer->exists) {
            $customer->id = $customerId;
            $customer->code = '';
            $customer->name = 'Faturação sem cliente associado';
        }

        $bindings = [
            'id',
            'volumes',
            'total_price',
            'total_expenses',
            'cost_price',
            'total_price_for_recipient',
            'cod',
            'payment_at_recipient',
            'recipient_zip_code',
            'recipient_country',
            'sender_country',
            'service_id',
            'ignore_billing',
            'charge_price',
            'date',
            'type',
            'is_collection',
            'children_tracking_code',
            'status_id',
            'fuel_tax',
            'fuel_price',
            'billing_subtotal',
            'billing_vat',
            'billing_total',
            'cost_shipping_price',
            'cost_expenses_price'
        ];

        $allShipments = Shipment::filterAgencies()
            //->where('is_collection', 0)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID);

        if ($shipmentsIds) {
            $allShipments = $allShipments->whereIn('id', $shipmentsIds);
        } else {
            $allShipments = $allShipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
        }

        if (!$customer->exists) {
            $allShipments = $allShipments->whereNull('customer_id');
        } else {
            $allShipments = $allShipments->where('customer_id', $customer->id);
        }

        $allShipments = $allShipments->get($bindings);

        if ($ignoreCOD) { //ignora pagamentos no destino
            $arr = [];
            foreach ($allShipments as $shp) {
                if ($shp->payment_at_recipient) {
                    $shp->payment_at_recipient = 0;
                    $shp->total_price = $shp->total_price_for_recipient;
                }
                $arr[] = $shp;
            }

            $allShipments = collect($arr);
        }


        //get shipments with payment at the recipient
        $shipmentsWithPaymentRecipient = $allShipments->filter(function ($item) {
            return $item->payment_at_recipient == 1;
        });

        //get shipments with payment at the recipient
        $allPickups = $allShipments->filter(function ($item) use ($billedIds) {
            return $item->is_collection == 1  && $item->status_id != ShippingStatus::PICKUP_FAILED_ID;
        });

        $allFuelTaxes = $allShipments->filter(function ($item) use ($billedIds) {
            if (@$billedIds['shipments']) {
                return ($item->payment_at_recipient == 0 && $item->ignore_billing == 0) && !in_array($item->id, @$billedIds['shipments']) && (!$item->is_collection || ($item->is_collection && $item->status_id == ShippingStatus::PICKUP_FAILED_ID));
            } else {
                return ($item->payment_at_recipient == 0 && $item->ignore_billing == 0) && (!$item->is_collection || ($item->is_collection && $item->status_id == ShippingStatus::PICKUP_FAILED_ID));
            }
        })->groupBy('fuel_tax');

        //get shipments with payment at the recipient
        $allShipments = $allShipments->filter(function ($item) {
            return $item->is_collection == 0 || ($item->is_collection == 1 && $item->status_id == ShippingStatus::PICKUP_FAILED_ID);
        });

        
        //get all other shipments
        $shipmentsNormal = $allShipments->filter(function ($item) use ($billedIds) {
            if (@$billedIds['shipments']) {
                return ($item->payment_at_recipient == 0 && $item->ignore_billing == 0) && !in_array($item->id, @$billedIds['shipments']);
            } else {
                return ($item->payment_at_recipient == 0 && $item->ignore_billing == 0);
            }
        });



        //dd($allShipments->toArray());




        $shipmentsAbsolute = $allShipments->filter(function ($item) use ($billedIds) {
            return $item->payment_at_recipient == 0;
        });

        //get nacional shipments (with vat)
        $shipmentsWithVat = $shipmentsNormal->filter(function ($item) use ($mailServices) {
            return ((in_array($item->sender_country, ['pt']) && in_array($item->recipient_country, ['pt'])  && !$item->recipientIsIsland())
                || (!in_array($item->sender_country, ['pt']) && in_array($item->recipient_country, ['pt'])  && !$item->recipientIsIsland()))
                && !in_array($item->service_id, $mailServices);
        });

        //get import
        $shipmentsImport = $shipmentsNormal->filter(function ($item) use ($mailServices) {
            return !in_array($item->sender_country, ['pt'])  && in_array($item->recipient_country, ['pt']) && !in_array($item->service_id, $mailServices);
        });

        //get nacional
        $shipmentsNacional = $shipmentsNormal->filter(function ($item) use ($courierServices, $mailServices) {
            return in_array($item->sender_country, ['pt'])
                && in_array($item->recipient_country, ['pt'])
                && !$item->recipientIsIsland()
                && !in_array($item->service_id, $courierServices)
                && !in_array($item->service_id, $mailServices);
        });

        //get nacional (courier)
        $shipmentsCourier = $shipmentsNormal->filter(function ($item) use ($courierServices) {
            return in_array($item->sender_country, ['pt'])
                && in_array($item->recipient_country, ['pt'])
                && !$item->recipientIsIsland()
                && in_array($item->service_id, $courierServices);
        });

        //get nacional (correios)
        $shipmentsMail = $shipmentsNormal->filter(function ($item) use ($mailServices) {
            return in_array($item->sender_country, ['pt'])
                && in_array($item->recipient_country, ['pt'])
                && in_array($item->service_id, $mailServices);
        });


        //get export
        $shipmentsExport = $shipmentsNormal->filter(function ($item) use ($mailServices) {
            return (in_array($item->sender_country, ['pt', 'es'])  && !in_array($item->recipient_country, ['pt']))
                || (!in_array($item->sender_country, ['pt'])  && !in_array($item->recipient_country, ['pt']))
                || (in_array($item->sender_country, ['pt'])  && $item->recipientIsIsland() && !in_array($item->service_id, $mailServices));
        });

        //export islands
        $shipmentsExportIslands = $shipmentsNormal->filter(function ($item) use ($mailServices) {
            return !in_array($item->service_id, $mailServices) && in_array($item->sender_country, ['pt']) && $item->recipientIsIsland();
        });

        //export spain
        $shipmentsExportSpain = $shipmentsNormal->filter(function ($item) {
            return in_array($item->sender_country, ['pt', 'es'])  && $item->recipient_country == 'es';
        });

        //$customer->exemption_reason = 'M05';

        //pickups
        //$customer->total_pickups = $allPickups->sum('total_price');
        //$customer->count_pickups = $allPickups->count('total_price');
        //$customer->total_pickups_cost = $allPickups->sum('cost_price'); 

        //shipments
       // $customer->total_shipments_absolute = $shipmentsAbsolute->sum('total_price');
        //$customer->count_shipments_absolute = $shipmentsAbsolute->count('total_price');

        $customer->total_shipments = $shipmentsNormal->sum('total_price');
        $customer->count_shipments_volumes = $shipmentsNormal->sum('volumes');
        $customer->count_shipments = $shipmentsNormal->count('total_price');
        //$customer->count_volumes = $shipmentsNormal->sum('volumes');

        $customer->total_cod = $shipmentsWithPaymentRecipient->sum('billing_subtotal');
        $customer->count_cod = $shipmentsWithPaymentRecipient->count();

        $customer->total_shipments_vat = $shipmentsWithVat->sum('total_price');
        $customer->count_shipments_vat = $shipmentsWithVat->count('total_price');
        $customer->count_shipments_vat_volumes = $shipmentsWithVat->sum('volumes');
        $customer->total_shipments_cost = $allShipments->sum('cost_shipping_price');

        //all export
        $customer->total_export = $shipmentsExport->sum('total_price');
        $customer->count_export = $shipmentsExport->count('total_price');
        $customer->count_export_volumes = $shipmentsExport->sum('volumes');
        $customer->total_export_cost = $shipmentsExport->sum('cost_shipping_price');


        //export spain
        $customer->total_shipments_spain = $shipmentsExportSpain->sum('total_price');
        $customer->count_shipments_spain = $shipmentsExportSpain->count('total_price');
        $customer->count_shipments_volumes_spain = $shipmentsExportSpain->sum('volumes');
        $customer->total_shipments_cost_spain = $shipmentsExportSpain->sum('cost_shipping_price');

        //export islands
        $customer->total_shipments_islands = $shipmentsExportIslands->sum('total_price');
        $customer->count_shipments_islands = $shipmentsExportIslands->count('total_price');
        $customer->count_shipments_volumes_islands = $shipmentsExportIslands->sum('volumes');
        $customer->total_shipments_cost_islands = $shipmentsExportIslands->sum('cost_shipping_price');

        //courier
        $customer->total_shipments_courier = $shipmentsCourier->sum('total_price');
        $customer->count_shipments_volumes_courier = $shipmentsCourier->sum('volumes');
        $customer->count_shipments_courier = $shipmentsCourier->count('total_price');
        $customer->total_shipments_cost_courier = $shipmentsCourier->sum('cost_shipping_price');

        //correio
        $customer->total_shipments_mail = $shipmentsMail->sum('total_price');
        $customer->count_shipments_volumes_mail = $shipmentsMail->sum('volumes');
        $customer->count_shipments_mail = $shipmentsMail->count('total_price');
        $customer->total_shipments_cost_mail = $shipmentsMail->sum('cost_shipping_price');

        //export internacional
        $customer->total_shipments_internacional = (float) number_format($shipmentsExport->sum('total_price') - ($customer->total_shipments_spain + $customer->total_shipments_islands), 2, '.', '');
        $customer->count_shipments_internacional = $shipmentsExport->count('total_price') - ($customer->count_shipments_spain + $customer->count_shipments_islands);
        $customer->count_shipments_volumes_internacional = $shipmentsExport->sum('volumes') - ($customer->count_shipments_volumes_spain + $customer->count_shipments_volumes_islands);
        $customer->total_shipments_cost_internacional = (float) number_format($shipmentsExport->sum('cost_price') - ($customer->total_shipments_cost_spain + $customer->total_shipments_cost_islands), 2, '.', '');

        //import
        $customer->total_shipments_import = $shipmentsImport->sum('total_price');
        $customer->count_shipments_import = $shipmentsImport->count('total_price');
        $customer->count_shipments_import_volumes = $shipmentsImport->sum('volumes');
        $customer->total_shipments_import_cost = $shipmentsImport->sum('cost_price');

        //nacional
        $customer->total_shipments_nacional = $shipmentsNacional->sum('total_price');
        $customer->count_shipments_nacional = $shipmentsNacional->count('total_price');
        $customer->count_shipments_nacional_volumes = $shipmentsNacional->sum('volumes');
        $customer->total_shipments_nacional_cost = $shipmentsNacional->sum('cost_shipping_price');

        /**
         * FUEL TAX // IF INVOICE DETAIL IS OFF
         */
        if (!Setting::get('fuel_tax_invoice_detail')) {

            //$customer->total_pickups += $allPickups->sum('fuel_price');

            //shipments
            //$customer->total_shipments_absolute += $shipmentsAbsolute->sum('fuel_price');
            $customer->total_shipments += $shipmentsNormal->sum('fuel_price');
            $customer->total_shipments_vat += $shipmentsWithVat->sum('fuel_price');

            //all export
            $customer->total_export += $shipmentsExport->sum('fuel_price');

            //export spain
            $customer->total_shipments_spain += $shipmentsExportSpain->sum('fuel_price');

            //export islands
            $customer->total_shipments_islands += $shipmentsExportIslands->sum('fuel_price');

            //courier
            $customer->total_shipments_courier += $shipmentsCourier->sum('fuel_price');

            //correio
            $customer->total_shipments_mail += $shipmentsMail->sum('fuel_price');

            //export internacional
            $customer->total_shipments_internacional += (float) number_format($shipmentsExport->sum('fuel_price') - ($shipmentsExportIslands->sum('fuel_price') + $shipmentsExportSpain->sum('fuel_price')), 2, '.', ''); //adicionado $shipmentsExportIslands->sum('fuel_price') por paulo em 2023-04-09

            //import
            $customer->total_shipments_import += $shipmentsImport->sum('fuel_price');

            //nacional
            $customer->total_shipments_nacional += $shipmentsNacional->sum('fuel_price');
        }

        /**
         * EXPENSES
         */
        //get month expenses
        $allIds      = $shipmentsNormal->pluck('id')->toArray();
        $exportIds   = $shipmentsExport->pluck('id')->toArray();
        $importIds   = $shipmentsImport->pluck('id')->toArray();
        $spainIds    = $shipmentsExportSpain->pluck('id')->toArray();
        $islandsIds  = $shipmentsExportIslands->pluck('id')->toArray();
        $nacionalIds = $shipmentsNacional->pluck('id')->toArray();
        $courierIds  = $shipmentsCourier->pluck('id')->toArray();
        $mailIds     = $shipmentsMail->pluck('id')->toArray();

        $allExpenses = ShipmentExpense::whereIn('shipment_id', $allIds)
            ->where(function ($q) {
                $q->whereNull('billing_item_id');
                $q->orWhere('billing_item_id', 0);
            })
            ->get();

        $expensesExport   = $allExpenses->filter(function ($item) use ($exportIds) {
            return in_array($item->shipment_id, $exportIds);
        });
        $expensesImport   = $allExpenses->filter(function ($item) use ($importIds) {
            return in_array($item->shipment_id, $importIds);
        });
        $expensesSpain    = $allExpenses->filter(function ($item) use ($spainIds) {
            return in_array($item->shipment_id, $spainIds);
        });
        $expensesIslands  = $allExpenses->filter(function ($item) use ($islandsIds) {
            return in_array($item->shipment_id, $islandsIds);
        });
        $expensesNacional = $allExpenses->filter(function ($item) use ($nacionalIds) {
            return in_array($item->shipment_id, $nacionalIds);
        });
        $expensesCourier  = $allExpenses->filter(function ($item) use ($courierIds) {
            return in_array($item->shipment_id, $courierIds);
        });
        $expensesMail     = $allExpenses->filter(function ($item) use ($mailIds) {
            return in_array($item->shipment_id, $mailIds);
        });


        //all expenses
        $customer->total_expenses = $allExpenses->sum('subtotal');
        $customer->count_expenses = $allExpenses->count('subtotal');
        $customer->total_expenses_cost = $allExpenses->sum('cost_price');

        //all export
        $customer->total_expenses_export = $expensesExport->sum('subtotal');
        $customer->count_expenses_export = $expensesExport->count('subtotal');
        $customer->total_expenses_cost_export = $expensesExport->sum('cost_price');

        //spain
        $customer->total_expenses_spain = $expensesSpain->sum('subtotal');
        $customer->count_expenses_spain = $expensesSpain->count('subtotal');
        $customer->total_expenses_cost_spain = $expensesSpain->sum('cost_price');

        //islands
        $customer->total_expenses_islands = $expensesIslands->sum('subtotal');
        $customer->count_expenses_islands = $expensesIslands->count('subtotal');
        $customer->total_expenses_cost_islands = $expensesIslands->sum('cost_price');

        //courier
        $customer->total_expenses_courier = $expensesCourier->sum('subtotal');
        $customer->count_expenses_courier = $expensesCourier->count('subtotal');
        $customer->total_expenses_cost_courier = $expensesCourier->sum('cost_price');

        //correio
        $customer->total_expenses_mail = $expensesMail->sum('subtotal');
        $customer->count_expenses_mail = $expensesMail->count('subtotal');
        $customer->total_expenses_cost_mail = $expensesMail->sum('cost_price');

        //internacional
        $customer->total_expenses_internacional = (float) number_format($customer->total_expenses_export - $customer->total_expenses_spain - $customer->total_expenses_islands, 2, '.', '');
        $customer->count_expenses_internacional = $customer->count_expenses_export - $customer->count_expenses_spain - $customer->count_expenses_islands;
        $customer->total_expenses_cost_internacional = (float) number_format($customer->total_expenses_cost_export - $customer->total_expenses_cost_spain - $customer->total_expenses_cost_islands, 2, '.', '');

        //import
        $customer->total_expenses_import = $expensesImport->sum('subtotal');
        $customer->count_expenses_import = $expensesImport->count('subtotal');
        $customer->total_expenses_cost_import = $expensesImport->sum('cost_price');

        //nacional
        $customer->total_expenses_nacional = $expensesNacional->sum('subtotal');
        $customer->count_expenses_nacional = $expensesNacional->count('subtotal');
        $customer->total_expenses_cost_nacional = $expensesNacional->sum('cost_price');

        //total expenses vat
        $customer->total_expenses_vat = $customer->total_expenses_nacional + $customer->total_expenses_courier + $customer->total_expenses_import;
        $customer->billing_subtotal_with_vat = ($customer->total_shipments_nacional + $customer->total_shipments_import + $customer->total_expenses_nacional + $customer->total_expenses_import);
        $customer->billing_subtotal_with_novat = $customer->total_export + $customer->total_expenses_export;


        /**
         * FUEL TAXES
         */
        $customer->fuel_tax_total        = 0;
        $customer->fuel_tax_total_vat    = 0;
        $customer->fuel_tax_total_no_vat = 0;
        $fuelTaxes = [];
        if (!empty(Setting::get('fuel_tax'))) {

            $fuelTotalWithVat = $fuelTotal = 0;
            foreach ($allFuelTaxes as $fuelTax => $fuelShipments) {

                $fuelWithVat = $shipmentsWithVat->filter(function ($item) use ($fuelTax) {
                    return $item->fuel_tax == $fuelTax;
                });

                $basePrice = $fuelShipments->sum('total_price') + $fuelShipments->sum('total_expenses');
                $subtotal  = $fuelShipments->sum('fuel_price');
                $subtotalWithVat = $fuelWithVat->sum('fuel_price');

                $fuelTotal += $subtotal;
                $fuelTotalWithVat += $subtotalWithVat;

                $vat = $subtotalWithVat * (Setting::get('vat_rate_normal') / 100);

                $fuelTaxes[] = [
                    'tax'             => $fuelTax,
                    'count'           => $fuelShipments->count(),
                    'count_with_vat'  => $fuelWithVat->count(),
                    'incidence_price' => $basePrice,
                    'incidence_vat_price' => $subtotalWithVat,
                    'total_with_vat'  => $subtotalWithVat,
                    'total_no_vat'    => $subtotal - $subtotalWithVat,
                    'subtotal'        => $subtotal,
                    'vat'             => $vat,
                    'total'           => $subtotal + $vat,
                ];
            }

            //dd($fuelTaxes);
            $customer->fuel_tax_total        = $fuelTotal;
            $customer->fuel_tax_total_vat    = $fuelTotalWithVat;
            $customer->fuel_tax_total_no_vat = $fuelTotal - $fuelTotalWithVat;
        }

        /**
         * Update values if customer is not PT
         */
        if ($customer->billing_country && $customer->billing_country != 'pt') {
            $customer->total_shipments_vat = 0;
            $customer->count_shipments_vat = 0;
            $customer->total_expenses_vat  = 0;
            $customer->exemption_reason    = 'M40';
        }

        $customer->total_shipments_no_vat = (float) number_format($customer->total_shipments - $customer->total_shipments_vat, 2, '.', '');
        $customer->total_expenses_no_vat  = $customer->total_expenses - $customer->total_expenses_vat;
        $customer->count_shipments_no_vat = (float) number_format($customer->count_shipments - $customer->count_shipments_vat, 2, '.', '');

        //dd($customer->total_expenses_vat);
        $covenants = $customer->covenants;
        $productsBought = $customer->productsBought;
        if (empty($dataIds) || (!empty($dataIds) && (isset($dataIds['covenants']) || isset($dataIds['products'])))) {
            /**
             * PRODUCTS
             */
            $productsBought = $customer->productsBought->filter(function ($item) use ($billedIds) {
                if (@$billedIds['products']) {
                    return !in_array($item->id, $billedIds['products']);
                } else {
                    return $item;
                }
            });

            $productsBoughtNoVat = $productsBought->filter(function ($item) {
                return $item->vat_rate == 'is';
            });

            $customer->total_products               = $productsBought->sum('subtotal');
            $customer->count_products               = $productsBought->count('subtotal');
            $customer->total_products_cost          = $productsBought->sum('cost_price');

            $customer->total_products_tax_0         = $productsBoughtNoVat->sum('subtotal');
            $customer->count_products_tax_0         = $productsBoughtNoVat->count('subtotal');
            $customer->total_products_cost_tax_0    = $productsBoughtNoVat->sum('cost_price');

            $customer->total_products_tax_normal      = $customer->total_products - $customer->total_products_tax_0;
            $customer->count_products_tax_normal      = $customer->count_products - $customer->count_products_tax_0;
            $customer->total_products_cost_tax_normal = $customer->total_products_cost - $customer->total_products_cost_tax_0;

            /**
             * COVENANTS
             */
            $covenants = $customer->covenants->filter(function ($item) use ($billedIds) {
                if (@$billedIds['covenants']) {
                    return !in_array($item->id, $billedIds['covenants']);
                } else {
                    return $item;
                }
            });

            $customer->total_covenants = $covenants->sum('amount');
            $customer->count_covenants = $covenants->count('amount');
        }

        /**
         * MONTH TOTALS
         */
        $customer->total_month      = $customer->total_shipments + $customer->total_expenses + $customer->total_products + $customer->total_covenants;
        $customer->total_month_vat  = $customer->total_shipments_vat + $customer->total_expenses_vat + $customer->total_products_tax_normal + $customer->total_covenants;
        $customer->total_month_no_vat  = (float) number_format($customer->total_month - $customer->total_month_vat, 2, '.', '');


        if (Setting::get('fuel_tax_invoice_detail')) {
            //Soma ao total do mes as taxas combistivel.
            //caso a opção não ativa, o valor ja vai incluida nos valores gerais
            $customer->total_month      += $customer->fuel_tax_total;
            $customer->total_month_vat  += $customer->fuel_tax_total_vat;
            $customer->total_month_no_vat  = (float) number_format($customer->total_month - $customer->total_month_vat, 2, '.', '');
        }

        //comentado em 8 junho - os particulares podem ter isencao de iva
        if ($customer->is_particular) {
            $customer->total_shipments_vat    = $customer->total_shipments_vat + $customer->total_shipments_no_vat;
            $customer->total_expenses_vat     = $customer->total_expenses_vat + $customer->total_expenses_no_vat;
            $customer->total_shipments_no_vat = 0;
            $customer->total_expenses_no_vat  = 0;
            
            $customer->total_month_vat = $customer->total_month_vat + $customer->total_month_no_vat;
            $customer->total_month_no_vat = 0;
        }

        $customer->total_month_absolute = $allShipments->sum('total_price') +
            $allShipments->sum('total_expenses') +
            $allShipments->sum('fuel_price') +
            $customer->total_at_recipient +
            $customer->productsBought->sum('subtotal') +
            $customer->covenants->sum('amount');

        $customer->total_month_cost     = $customer->total_shipments_cost + $customer->total_expenses_cost + $customer->total_products_cost;
        $customer->total_month_profit   = (float) number_format($customer->total_month_absolute - $customer->total_month_cost, 2, '.', '');


        /**
         * INSURANCE TAX
         */
        if (!empty(Setting::get('insurance_tax'))) {


            if (empty($customer->insurance_tax)) {
                $customer->insurance_tax = (float) Setting::get('insurance_tax');
            }

            $customer->insurance_tax = (float) $customer->insurance_tax;
            $tax = $customer->insurance_tax / 100;
            $customer->insurance_tax_total     = $customer->total_month * $tax;
            $customer->insurance_tax_total_vat = $customer->total_month_vat * $tax;
            $customer->insurance_tax_total_no_vat = $customer->total_month_no_vat * $tax;
        }

        $customer->invoice_type = $customer->default_invoice_type;

        //dd($customer->toArray());

        /**
         * Get warnings
         */
        $countEmptyPrices = $allShipments->filter(function ($item) {
            return (empty($item->total_price) || $item->total_price == 0.00)
                && !$item->payment_at_recipient
                && $item->type != Shipment::TYPE_MASTER;
        })->count();

        $countEmptyCountries = $allShipments->filter(function ($item) {
            return empty($item->sender_country) || empty($item->recipient_country);
        })->count();

        $countEmptyServices = $allShipments->filter(function ($item) {
            return empty($item->service_id);
        })->count();

        $countEmptyPricesPickups = $allPickups->filter(function ($item) {
            return (empty($item->total_price) || ($item->total_price + $item->total_expenses) == 0.00);
        })->count();

        $countEmptyAssignedShipment = $allPickups->filter(function ($item) {
            return empty($item->children_tracking_code) && $item->status_id != ShippingStatus::PICKUP_FAILED_ID;
        })->count();

        /**
         * Obtem os envios que possam ter taxas mas sem as ter de verdade
         */
        $shipmentsWithExpenses = $allExpenses->pluck('shipment_id')->toArray(); //obtem todos os envios com despesas reais
        //filtra os envios que por ventura possam ter encargos no campo "total_expenses" mas que não os tenham realmente.
        $shipmentsToClean = $allShipments->filter(function ($item) use ($shipmentsWithExpenses) {
            return $item->total_expenses > 0.00 && !in_array($item->id, $shipmentsWithExpenses);
        });
        //dd($shipmentsToClean->pluck('id'));

        $customer->billing_warnings = [
            'empty_prices'    => $countEmptyPrices,
            'empty_countries' => $countEmptyCountries,
            'empty_services'  => $countEmptyServices,
            'empty_prices_pickups'           => $countEmptyPricesPickups,
            'empty_pickup_assigned_shipment' => $countEmptyAssignedShipment,
            'empty_expenses'    => $shipmentsToClean->count()
        ];

        $customer->has_billing_warnings = false;
        if (
            $countEmptyPrices || $countEmptyCountries || $countEmptyServices
            || $countEmptyPricesPickups || $countEmptyAssignedShipment
        ) {
            $customer->has_billing_warnings = true;
        }


        
        $customer->shipments = $shipmentsNormal->pluck('id')->toArray();
        if (empty($dataIds) || (!empty($dataIds) && (isset($dataIds['covenants']) || isset($dataIds['products'])))) {
            $customer->covenants = $covenants->pluck('id')->toArray();
            $customer->products  = $productsBought->pluck('id')->toArray();
        } else {
            $customer->covenants = $customer->products = [];
        }

        $customer->lines             = self::getBillingLinesArrDefaultMode($customer, $covenants, $fuelTaxes, $allIds, $docSubtotal, $docVat);
        $customer->fuel_taxes        = (object) @$fuelTaxes;
        $customer->document_subtotal = (float) number($docSubtotal);
        $customer->document_vat      = (float) number($docVat);
        $customer->document_total    = (float) number($customer->document_subtotal + $customer->document_vat);
        $customer->billing_closed    = $customer->total_month == 0.00 ? true : false;
        $customer->period            = $period;
        $customer->customer_id       = $customer->id;
        $customer->obs               = '';
        //dd($customer->toArray());
        return $customer;
    }

    public static function getDefaultObs($docDate, $reference)
    {
        $tmpInvoice = new Invoice();
        $tmpInvoice->doc_date = $docDate;

        $obs = Invoice::prefillObs(Setting::get('invoice_obs'), $tmpInvoice);
        $obs = trans('admin/global.word.period') . ' ' . $reference . "\n" . $obs;
        ' ' . $reference . "\n" . $obs;
        return $obs;
    }

    public static function getDefaultReference($year, $month, $period)
    {
        return (trans('datetime.month-tiny.' . $month) . '/' . $year . ($period != '30d' ? '/' . strtoupper($period) : ''));
    }

    /**
     * Fatura automático o mês ou período a um cliente
     *
     * @param $customerId
     * @param $month
     * @param $year
     * @param $period
     */
    public static function autoBillingMonth($customerId, $month, $year, $period, $params = null, $paymentCondition = null)
    {
        $billedItems     = CustomerBilling::getBilledShipments($customerId, $year, $month, $period);
        $customerBilling = CustomerBilling::getBilling($customerId, $month, $year, $period, null, @$billedItems['ids']);

        if ($customerBilling->has_billing_warnings) {
            return [
                'result'          => false,
                'feedback'        => 'Impossível faturar massivo. Existem avisos a corrigir antes de faturar. <a href="' . route('admin.billing.customers.show', [$customerId, 'month' => $month, 'year' => $year, 'period' => $period]) . '" target="_blank">Ver Avisos</a>',
                'invoice_id'      => null,
                'invoice_doc_id' => null
            ];
        }

        $paymentCondition = empty($paymentCondition) ? $customerBilling->payment_method : $paymentCondition;
        $paymentCondition = $paymentCondition ?: '30d';

        if (in_array($paymentCondition, ['prt', 'dbt'])) {
            $invoiceLimitDays = PaymentCondition::getDays($paymentCondition);
        } else {
            $invoiceLimitDays = str_replace('d', '', $paymentCondition);
        }

        $docDate = @$params['docdate'];
        $docDate = $docDate ? $docDate : date('Y-m-d');
        if (Setting::get('billing_force_today')) {
            $docDate = date('Y-m-d');
        }

        $dueDate = new Carbon($docDate);
        $dueDate = $dueDate->addDays($invoiceLimitDays)->format('Y-m-d');

        $docRef  = CustomerBilling::getDefaultReference($year, $month, $period);
        $obs     = CustomerBilling::getDefaultObs($year . '-' . $month . '-01', $docRef);

        $linesArr = [];
        foreach ($customerBilling->lines as $line) {
            $linesArr[$line->key] = [
                "reference"     => @$line->reference,
                "description"   => @$line->description,
                "obs"           => @$line->obs,
                "qty"           => @$line->qty,
                "total_price"   => @$line->subtotal,
                "discount"      => "0.00",
                "subtotal"      => @$line->subtotal,
                "tax_rate"      => @$line->exemption_reason ? @$line->exemption_reason : Setting::get('vat_rate_normal'),
            ];
        }

        $request = new \Illuminate\Http\Request([
            'year'              => $year,
            'month'             => $month,
            'period'            => $period,
            'docref'            => @$params['reference'] ? @$params['reference'] : $docRef,
            'api_key'           => @$params['api_key'],
            'final_consumer'    => $customerBilling->final_consumer,
            'customer_id'       => $customerBilling->id,
            'billing_type'      => 'month',
            'target'            => 'CustomerBilling',
            'billing_code'      => $customerBilling->code,
            'billing_name'      => $customerBilling->billing_name,
            'billing_address'   => $customerBilling->billing_address,
            'billing_zip_code'  => $customerBilling->billing_zip_code,
            'billing_city'      => $customerBilling->billing_city,
            'billing_country'   => $customerBilling->billing_country,
            'billing_email'     => $customerBilling->billing_email,
            'agency_id'         => $customerBilling->agency_id,
            'vat'               => $customerBilling->vat,
            'send_email'        => @$params['send_email'],
            'doc_type'          => $customerBilling->default_invoice_type ? $customerBilling->default_invoice_type : 'invoice',
            'docdate'           => $docDate,
            'duedate'           => $dueDate,
            'payment_condition' => $paymentCondition,
            'payment_method'    => '',
            'total'             => $customerBilling->doc_total,
            'total_discount'    => $customerBilling->total_discount,
            'fuel_tax'          => $customerBilling->fuel_tax,
            'irs_tax'           => $customerBilling->irs_tax,
            'document_subtotal' => $customerBilling->document_subtotal,
            'document_vat'      => $customerBilling->document_vat,
            'document_total'    => $customerBilling->document_total,
            'total_month'       => $customerBilling->total_month,
            'total_month_vat'   => $customerBilling->total_month_vat,
            'total_month_no_vat' => $customerBilling->total_month_no_vat,
            'obs'               => $obs,
            'line'              => $linesArr,
            'shipments'         => $customerBilling->shipments ? implode(',', $customerBilling->shipments) : '',
            'covenants'         => $customerBilling->covenants ? implode(',', $customerBilling->covenants) : '',
            'products'          => $customerBilling->products ? implode(',', $customerBilling->products) : '',
            'attachments'       => @$params['attachments']
        ]);

        $invoiceController = new SalesController();
        $result = $invoiceController->update($request, null);
        $result = json_decode($result->content(), true);

        if (!$result['result']) {
            return [
                'result'         => false,
                'feedback'       => @$result['feedback'],
                'invoice_id'     => @$result['invoice_id'],
                'invoice_doc_id' => @$result['invoice_doc_id']
            ];
        }
        return [
            'result'         => true,
            'feedback'       => @$result['feedback'],
            'invoice_id'     => @$result['invoice_id'],
            'invoice_doc_id' => @$result['invoice_doc_id']
        ];
    }

    /**
     * Return item description
     * @param $customer
     * @return string
     */
    public static function getItemDescription($key, $value)
    {

        $description = Setting::get(@$value['desc']);

        if ($key == 'islands') {
            $description .= ' (ILHAS)';
        }

        return $description;
    }

    /**
     * Return item exemption reason
     * @param $customer
     * @return string
     */
    public static function getItemExemptionReason($key, $customer)
    {

        if(Setting::get('app_country') != 'pt') {
            return ''; //não existem motivos de isenção fora de PT
        }

        if (in_array($key, ['nacional', 'courier', 'import', 'covenants', 'products_tax_normal']) && $customer->billing_country == 'pt') {
            $exemptionReason = '';
        } else if ($key == 'mail') {
            $exemptionReason = 'M99';
        } else {
            $exemptionReason = $customer->exemption_reason;
        }

        if ($customer->is_particular && $key != 'mail') {
            $exemptionReason = '';
        }

        return $exemptionReason;
    }

    /**
     * Return customer billing billed shipments
     * @param $customerId
     * @param $year
     * @param $month
     * @param $period
     * @return array
     */
    public static function getBilledShipments($customerId, $year, $month, $period, $billingCollection = null)
    {

        if (is_null($billingCollection)) {
            $billingCollection = CustomerBilling::where('customer_id', $customerId)
                ->where('month', $month)
                ->where('year', $year)
                ->where('period', $period)
                ->get(['invoice_type', 'invoice_id', 'invoice_doc_id', 'shipments', 'covenants', 'products', 'billing_type', 'total_month']);
        }

        $billedIds    = [];
        $invoicesIds  = [];
        $billingTypes = [];
        $noDocIds     = [];
        $draftIds     = [];
        $invoicesDetails = [];
        $totalBilled = 0;
        foreach ($billingCollection as $row) {

            $totalBilled += $row->total_month;
            if ($row->invoice_type == 'nodoc') {
                $invoicesIds[$row->invoice_doc_id] = trans('admin/billing.types-list.' . $row->invoice_type);
                $noDocIds[] = $row->invoice_doc_id;
                $invoicesDetails[$row->invoice_doc_id] = [
                    'invoice_id' => $row->invoice_id,
                    'doc_id'     => $row->invoice_doc_id,
                    'doc_type'   => $row->invoice_type,
                    'name'       => $invoicesIds[$row->invoice_doc_id],
                    'key'        => $row->api_key
                ];
            } else {
                if ($row->invoice_draft) {
                    $draftIds[] = $row->invoice_doc_id;
                }

                $invoicesIds[$row->invoice_doc_id] = trans('admin/billing.types_code.' . $row->invoice_type) . ' ' . $row->invoice_doc_id;

                $invoicesDetails[$row->invoice_doc_id] = [
                    'invoice_id' => $row->invoice_id,
                    'doc_id'     => $row->invoice_doc_id,
                    'doc_type'   => $row->invoice_type,
                    'name'       => $invoicesIds[$row->invoice_doc_id],
                    'key'        => $row->api_key
                ];
            }

            $billingTypes[] = $row->billing_type;

            if ($row->shipments) {
                foreach ($row->shipments as $id) {
                    $billedIds['shipments'][] = $id;
                }
            }

            if ($row->covenants) {
                foreach ($row->covenants as $id) {
                    $billedIds['covenants'][] = $id;
                }
            }

            if ($row->products) {
                foreach ($row->products as $id) {
                    $billedIds['products'][] = $id;
                }
            }
        }

        return [
            'billing_types'   => $billingTypes,
            'invoices'        => $invoicesIds,
            'nodoc_ids'       => $noDocIds,
            'draft_ids'       => $draftIds,
            'total'           => $totalBilled,
            'ids'             => $billedIds,
            'invoicesDetails' => $invoicesDetails
        ];
    }


    /**
     * Detect covenant
     * @param $shipment
     */
    public static function detectCovenant($shipment = null, $month = null, $year = null, $customerId = null)
    {

        if (is_null($month)) {
            $dt = new Date($shipment->date);
            $month = $dt->month;
        } else {
            $month = date('m');
        }

        if (is_null($year)) {
            $dt = new Date($shipment->date);
            $year  = $dt->year;
        } else {
            $year  = date('Y');
        }

        if (is_null($customerId)) {
            $customerId = $shipment->customer_id;
        }


        $customerCovenant = CustomerCovenant::where('customer_id', $customerId)
            ->where('type', 'variable')
            ->where('service_id', $shipment->service_id)
            ->where('start_date', '<=', $shipment->date)
            ->where('end_date', '>=', $shipment->date)
            ->first();

        if ($customerCovenant && $customerCovenant->max_shipments > 0) {

            Shipment::where('customer_id', $customerCovenant->customer_id)
                ->whereRaw('YEAR(date)', $year)
                ->whereRaw('MONTH(date)', $month)
                ->update(['ignore_billing' => 0]);

            //DB::enableQueryLog();
            /*if(config('app.source') == 'westroutes') {

                $shipments = Shipment::whereRaw('id in (SELECT id FROM (select id from shipments where customer_id = '.$customerCovenant->customer_id.' and service_id = '.$customerCovenant->service_id.' and payment_at_recipient = 0 and YEAR(date) = '.$year.' and MONTH(date) = '.$month.' and deleted_at is null order by date, id asc limit '.$customerCovenant->max_shipments.') as tmp)')
                    ->get();

                foreach ($shipments as $shipment) {
                    $zipCode = zipcodeCP4($shipment->recipient_zip_code);

                    if(in_array($zipCode, Setting::get('postal_codes_of_operation'))) {
                        $shipment->ignore_billing = 1;
                        $shipment->save();
                    }
                }

            } else {*/
            Shipment::whereRaw('id in (SELECT id FROM (select id from shipments where customer_id = ' . $customerCovenant->customer_id . ' and service_id = ' . $customerCovenant->service_id . ' and payment_at_recipient = 0 and YEAR(date) = ' . $year . ' and MONTH(date) = ' . $month . ' and deleted_at is null order by date, id asc limit ' . $customerCovenant->max_shipments . ') as tmp)')
                ->update(['ignore_billing' => 1]);
            /* }*/

            //dd(DB::getQueryLog());
        }
    }

    public static function getCustomersToBilling($year, $month, $period, $customersIds = null)
    {

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        //1. OBTEM TODOS OS CLIENTES A FATURAR NO MÊS
        $myAgencies = Auth::user()->agencies;

        if (Auth::user()->hasRole(config('permissions.role.admin'))) {
            $myAgencies = Agency::whereSource(config('app.source'))->pluck('id')->toArray();
        }

        $covenantsCustomers = CustomerCovenant::leftJoin('customers', 'customers.id', '=', 'customers_covenants.customer_id')
            ->where('start_date', '<=', $periodFirstDay)
            ->where('end_date', '>=', $periodLastDay)
            ->where('type', 'fixed')
            ->whereIn('customers.agency_id', $myAgencies);

        if ($customersIds) {
            $covenantsCustomers = $covenantsCustomers->whereIn('customers.id', $customersIds);
        }

        $covenantsCustomers = $covenantsCustomers->pluck('customers.id')
            ->toArray();

        $customersBilling = CustomerBilling::where('month', $month)
            ->where('year', $year);

        if ($customersIds) {
            $customersBilling = $customersBilling->whereIn('customer_id', $customersIds);
        }

        $customersBilling = $customersBilling->get();

        $bindings = [
            DB::raw($month . ' as month'),
            DB::raw($year . ' as year'),
            'customers.id',
            'customers.code as code',
            'customers.agency_id as agency_id',
            'customers.name as name',
            'customers.vat',
            'customers.default_invoice_type as default_invoice_type',
            'customers.ignore_mass_billing',
            'shipments.customer_id',
        ];

        $shipmentsIds = [];
        $covenantsIds = [];
        $productsIds = [];
        foreach ($customersBilling as $item) {
            if ($item->shipments) {
                $shipmentsIds = array_merge($shipmentsIds, $item->shipments);
            }

            if ($item->covenants) {
                $covenantsIds = array_merge($covenantsIds, $item->covenants);
            }

            if ($item->products) {
                $productsIds = array_merge($productsIds, $item->products);
            }
        }

        $subSqlQueries = [];

        if ($shipmentsIds) { //quando já há faturados
            $subSqlQueries[] = 'COALESCE((select COALESCE(sum(total_price)) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0)
                         from shipments
                         where deleted_at is null and ignore_billing = 0 and
                         status_id not in (' . ShippingStatus::CANCELED_ID . ') and
                         payment_at_recipient = 0 and is_collection = 0 and
                         (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                         shipments.id not in (' . implode(',', $shipmentsIds) . ') and
                         customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select COALESCE(sum(total_price)) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0)
                         from shipments
                         where deleted_at is null and ignore_billing = 0 and
                         status_id not in (' . ShippingStatus::CANCELED_ID . ') and
                         payment_at_recipient = 0 and
                         (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                         customer_id = customers.id), 0)';
        }

        if ($covenantsIds) {
            $subSqlQueries[] = 'COALESCE((select sum(amount)
                        from customers_covenants
                        where deleted_at is null and
                        start_date <= "' . $periodFirstDay . '" and end_date >= "' . $periodLastDay . '" and
                        customers_covenants.id not in (' . implode(',', $covenantsIds) . ') and
                        customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select sum(amount)
                        from customers_covenants
                        where deleted_at is null and
                        start_date <= "' . $periodFirstDay . '" and end_date >= "' . $periodLastDay . '" and
                        customer_id = customers.id), 0)';
        }

        if ($productsIds) {
            $subSqlQueries[] = 'COALESCE((select sum(subtotal)
                        from products_sales
                        where deleted_at is null and
                        (date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                        products_sales.id not in (' . implode(',', $productsIds) . ') and
                        customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select sum(subtotal)
                        from products_sales
                        where deleted_at is null and
                        (date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                        customer_id = customers.id), 0)';
        }


        $subSql = implode(' + ', $subSqlQueries);

        if ($subSql) {
            $subSql = '(select COALESCE(' . $subSql . ', 0))';
        } else {
            $subSql = '0';
        }


        $data = Customer::leftJoin('shipments', function ($q) use ($periodFirstDay, $periodLastDay) {
            $q->on('customers.id', '=', 'shipments.customer_id');
            $q->whereBetween('shipments.billing_date', [$periodFirstDay, $periodLastDay]);
            $q->whereNull('shipments.deleted_at');
            $q->where('shipments.status_id', '<>', ShippingStatus::CANCELED_ID);
        })
            ->with(['shipments' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                $q->where('status_id', '<>', ShippingStatus::CANCELED_ID);
                $q->where('is_collection', 0);
            }])
            ->with(['productsBought' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ProductSale::CACHE_TAG);
                $q->whereBetween('date', [$periodFirstDay, $periodLastDay]);
            }])
            ->with(['covenants' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(CustomerCovenant::CACHE_TAG);
                $q->filterBetweenDates($periodFirstDay, $periodLastDay);
            }])
            ->with(['billing' => function ($q) use ($year, $month, $period) {
                $q->where('year', $year);
                $q->where('month', $month);
                $q->where('period', $period);
            }]);

        if ($customersIds) {
            $data = $data->whereIn('customers.id', $customersIds);
        }

        $data = $data->where(function ($q) use ($periodFirstDay, $periodLastDay, $covenantsCustomers) {
            $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
            $q->orWhereIn('customers.id', $covenantsCustomers);
        })
            ->groupBy('customers.name');


        if ($myAgencies) {
            $data = $data->whereIn('customers.agency_id', $myAgencies);
        }

        $monthCustomers = $data->whereRaw($subSql . ' > 0')
            ->get($bindings);


        return $monthCustomers;
    }

    /**
     * Create PDF file with shipments of a given month and year
     *
     * @param $customerId
     * @param null $month
     * @param null $year
     * @param string $outputFormat [I = Output to screen, F = Save on server, S = send by email]
     * @return mixed
     */
    public static function printShipments($customerId, $month = null, $year = null, $returnMode = 'pdf', $dataIds = null, $period = '30d', $apiKey = null, $invoice = null)
    {

        ini_set("pcre.backtrack_limit", "500000000");
        ini_set("memory_limit", "-1");

        $customer = Customer::find($customerId);

        $ids = $dataIds;
        $covenantsIds = $productsIds = [];
        if (!is_null($dataIds)) {
            if (isset($dataIds['shipments'])) {
                $ids = $dataIds['shipments'];
            }

            if (isset($dataIds['covenants'])) {
                $covenantsIds = $dataIds['covenants'];
            }

            if (isset($dataIds['products'])) {
                $productsIds = $dataIds['products'];
            }
        }

        $year   = empty($year)  ? date('Y') : $year;
        $month  = empty($month) ? date('n') : $month;
        $period = empty($period) ? '30d' : $period;
        $month  = str_pad($month, 2, "0", STR_PAD_LEFT);
        $locale = $customer->locale;

        $filename       = Billing::getPeriodName($year, $month, $period);
        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $documentTitle  = translation('admin/global.billing.pdf.title', $locale);
        if (@$periodDates['allMonth']) {
            $documentTitle  = translation('admin/global.billing.pdf.title', $locale) . ' - ' . $filename;
        }

        $shipments = Shipment::with('expenses')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with(['status' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
            }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
            }])
            ->filterAgencies()
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where(function ($q) {
                $q->where('is_collection', 0);
                $q->orWhere(function ($q) {
                    $q->where('is_collection', 1);
                    $q->where('status_id', ShippingStatus::PICKUP_FAILED_ID);
                });
            })
            ->where('customer_id', $customerId);

        if (!empty($ids)) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
        }

        if ($customerId != '999999999') {
            $shipments = $shipments->whereCustomerId($customerId);
        } else {
            $shipments = $shipments->whereNull('customer_id');
        }

        if (Setting::get('billing_ignored_services')) {
            $shipments = $shipments->whereNotIn('service_id', Setting::get('billing_ignored_services'));
        }

        $shipments = $shipments->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();


        $customer = Customer::filterAgencies();

        $customer = $customer->with(['covenants' => function ($q) use ($periodFirstDay, $periodLastDay, $covenantsIds) {
            if (!empty($covenantsIds)) {
                $q->whereIn('id', $covenantsIds);
            }
            $q->filterBetweenDates($periodFirstDay, $periodLastDay);
        }])
            ->with(['productsBought' => function ($q) use ($periodFirstDay, $periodLastDay, $productsIds, $dataIds) {
                $q->with('product');
                if (!empty($productsIds)) {
                    $q->whereIn('id', $productsIds);
                } else {
                    if (empty($dataIds)) { //quando se obtem a listagem global
                        $q->where('date', '>=', $periodFirstDay);
                        $q->where('date', '<=', $periodLastDay);
                    } else {
                        $q->where('date', '0000'); //quando a listagem ja está gravada e não tem produtos
                    }
                }
            }]);

        $customer = $customer->findOrFail($customerId);

        $billingData = CustomerBilling::getBilling($customerId, $month, $year, $period, $dataIds);

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 26,
            'margin_bottom' => 16,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->debug = true;

        $groupedResults = $shipments->groupBy('customer.name');

        $data = [
            'groupedResults'   => $groupedResults,
            'groupByCustomer'  => false,
            'documentTitle'    => $documentTitle,
            'documentSubtitle' => '<span style="color: #222">Periodo de ' . $periodFirstDay . ' a ' . $periodLastDay . '</span><br/>' . $customer->code . ' - ' . $customer->name,
            'shipments'        => $shipments,
            'customer'         => $customer,
            'month'            => $month,
            'year'             => $year,
            'period'           => $period,
            'billingData'      => $billingData,
            'invoice_type'     => @$invoice->doc_type,
            'view'             => 'admin.printer.shipments.docs.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if ($returnMode == 'string') {
            return $mpdf->Output($documentTitle . '.pdf', 'S'); //output base64 string
        }

        if ($returnMode == 'array') {
            return [
                'mime'      => 'application/pdf',
                'title'     => $documentTitle,
                'filename'  => $documentTitle . '.pdf',
                'content'   => $mpdf->Output($documentTitle . '.pdf', 'S'),
                'shipments' => $shipments
            ];
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        return $mpdf->Output($documentTitle . '.pdf', 'I'); //output to screen
        exit;
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
    public function setFuelTaxAttribute($value)
    {
        $this->attributes['fuel_tax'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setInsuranceTaxAttribute($value)
    {
        $this->attributes['insurance_tax'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setIrsTaxAttribute($value)
    {
        $this->attributes['irs_tax'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setTotalMonthCostAttribute($value)
    {
        $this->attributes['total_month_cost'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setTotalDiscountAttribute($value)
    {
        $this->attributes['total_discount'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setShipmentsAttribute($value)
    {
        $value = is_array($value) ? array_filter($value) : $value;
        $this->attributes['shipments'] = empty($value) ? null : json_encode($value);
    }

    public function setCovenantsAttribute($value)
    {
        $value = is_array($value) ? array_filter($value) : $value;
        $this->attributes['covenants'] = empty($value) ? null : json_encode($value);
    }

    public function setProductsAttribute($value)
    {
        $value = is_array($value) ? array_filter($value) : $value;
        $this->attributes['products'] = empty($value) ? null : json_encode($value);
    }

    public function getFuelTaxTotalAttribute()
    {
        $tax = $this->fuel_tax / 100;
        return number($this->total_month * $tax);
    }

    public function getFuelTaxTotalVatAttribute()
    {
        $tax = $this->fuel_tax / 100;
        return number($this->total_month_vat * $tax);
    }

    public function getFuelTaxTotalNoVatAttribute()
    {
        $tax = $this->insurance_tax / 100;
        return number(($this->total_month - $this->total_month_vat) * $tax);
    }

    public function getInsuranceTaxTotalAttribute()
    {
        $tax = $this->insurance_tax / 100;
        return number($this->total_month * $tax);
    }

    public function getInsuranceTaxTotalVatAttribute()
    {
        $tax = $this->insurance_tax / 100;
        return number($this->total_month_vat * $tax);
    }

    public function getInsuranceTaxTotalNoVatAttribute()
    {
        $tax = $this->insurance_tax / 100;
        return number(($this->total_month - $this->total_month_vat) * $tax);
    }

    public function getShipmentsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCovenantsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getProductsAttribute($value)
    {
        return json_decode($value, true);
    }
}
