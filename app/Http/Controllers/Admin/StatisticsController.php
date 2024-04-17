<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\CustomerCovenant;
use App\Models\FleetGest\Cost;
use App\Models\IncidenceType;
use App\Models\Invoice;
use App\Models\ProductSale;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePaymentNote;
use App\Models\Route;
use App\Models\Service;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\Statistic;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\CustomerSupport\Ticket;
use Illuminate\Http\Request;
use Html, Croppa, Auth, File, Date, DB;
use Illuminate\Support\Facades\Redirect;

class StatisticsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'statistics';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',statistics']);
        validateModule('statistics');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $tab = $request->get('tab', false);

        if (!empty($tab) && !in_array($tab, ['summary', 'services', 'sellers', 'gains', 'quality', 'users'])) {
            return Redirect::route('admin.statistics.index');
        }

        $sellerId   = $request->get('seller', false);

        if (Auth::user()->isSeller()) {
            $sellerId = Auth::user()->id;
        }

        $agencyId   = $request->get('agency', false);
        $metrics    = $request->get('metrics', 'daily');
        $period     = $request->get('period', '30d');
        $operatorId = $request->get('operator');
        $userId     = $request->get('user');
        $vehicle    = $request->get('vehicle');

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->orderBy('name', 'asc')
            ->get();

        $myOperators   = $allOperators->pluck('id')->toArray();
        $operatorsList = $allOperators->pluck('name', 'id')->toArray();

        $agencies = Auth::user()->listsAgencies();

        $sellers = User::with('meetings')
            ->remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->isSeller()
            ->filterSource()
            ->where('active', 1);
        if ($sellerId) {
            $sellers = $sellers->where('id', $sellerId);
        }
        $sellers = $sellers->get();

        $vehiclesList = Vehicle::listVehicles();

        $allStatus = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->isFinal()
            ->orWhere('id', ShippingStatus::INCIDENCE_ID)
            ->ordered()
            ->get();

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get();

        $data = [
            'date_min'  => $request->date_min,
            'date_max'  => $request->date_max,
            'year'      => $request->year,
            'month'     => $request->month,
            'period'    => $period
        ];

        $period = Statistic::getPeriodDates($metrics, $data);
        $startDate  = $period['start_date'];
        $endDate    = $period['end_date'];
        $months     = $period['months'];
        $years      = $period['years'];
        $periodList = $period['period_list'];
        $metric     = $period['metric'];

        //get all shipments for period
        $statusIds = $allStatus->pluck('id')->toArray();
        $allShipments = Statistic::getShipmentsForPeriod($startDate, $endDate, $sellerId, $agencyId, $statusIds, $operatorId, $vehicle, $userId);

        //get period covenants
        $allCovenants = CustomerCovenant::with('customer.seller')
            ->whereHas('customer', function ($q) use ($sellerId, $agencyId) {
                $q->filterSource();
                if ($sellerId) {
                    $q->where('seller_id', $sellerId);
                }

                if ($agencyId) {
                    $q->where('agency_id', $agencyId);
                }
            })
            ->where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $endDate)
            ->get();

        $allPurchaseInvoices = PurchaseInvoice::filterSource()
            ->filterAgencies($agencyId)
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('is_draft', '0')
            ->where('is_deleted', '0')
            ->where('ignore_stats', 0)
            ->whereNotIn('type_id', [1]) //Subcontratação transportes
            ->where(function ($q) {
                $q->whereNull('target_id');
                $q->orWhere('target_id', '');
            })
            ->whereHas('provider', function ($q) use ($agencyId) {
                if ($agencyId) {
                    $q->where('agencies', 'like', '%"' . $agencyId . '%"');
                }
            });

        if ($vehicle) {
            //se tem viatura no filtro, força a que não existam resultados para faturas "soltas",
            //para não dar estatisticas incorretas de coisas que não têm a haver com a viatura
            $allPurchaseInvoices = $allPurchaseInvoices->where('id', '999999999999');
        }

        $allPurchaseInvoices = $allPurchaseInvoices->get(['doc_date', 'subtotal', 'vat', 'total']);


        $allPurchaseReceipts = PurchasePaymentNote::filterSource()
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->whereHas('provider', function ($q) use ($agencyId) {
                if ($agencyId) {
                    $q->where('agencies', 'like', '%"' . $agencyId . '%"');
                }
            })
            ->get(['doc_date', 'subtotal', 'vat_total', 'total']);

        //para contadores de faturas
        $allBillingInvoices = Invoice::filterSource()
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('is_draft', '0')
            ->where('is_deleted', '0')
            ->whereIn('doc_type', ['invoice', 'invoice-receipt', 'simplified-invoice', 'internal-doc', 'nodoc', 'credit-note'])
            ->where('target', 'Invoice');

        if ($agencyId) {
            $allBillingInvoices = $allBillingInvoices->whereHas('customer', function ($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        $allBillingInvoices = $allBillingInvoices->get(['doc_date', 'doc_subtotal', 'doc_vat', 'doc_total']);
        $allBillingInvoices->map(function ($item) { //coloca as notas de crédito com valor negativo
            return $item->doc_subtotal = $item->doc_type == 'credit-note' && $item->doc_subtotal > 0.00 ? $item->doc_subtotal * -1 : $item->doc_subtotal;
        });


        //variavel que so considera faturas do tipo Invoice
        $allInvoices = Invoice::filterSource()
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('is_draft', '0')
            ->where('is_deleted', '0')
            ->whereIn('doc_type', ['invoice', 'invoice-receipt', 'simplified-invoice', 'internal-doc', 'nodoc', 'credit-note'])
            ->where('target', 'Invoice');

        if ($agencyId) {
            $allInvoices = $allInvoices->whereHas('customer', function ($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        if ($vehicle) { //se tem viatura no filtro, força a que não existam resultados para faturas "soltas", para não dar estatisticas incorretas de coisas que não têm a haver com a viatura
            $allInvoices = $allInvoices->where('id', '999999999999');
        }

        $allInvoices = $allInvoices->get(['doc_date', 'doc_subtotal', 'doc_vat', 'doc_total', 'doc_type']);
        $allInvoices->map(function ($item) { //coloca as notas de crédito com valor negativo
            return $item->doc_subtotal = $item->doc_type == 'credit-note' && $item->doc_subtotal > 0.00 ? $item->doc_subtotal * -1 : $item->doc_subtotal;
        });

        $allReceipts = Invoice::filterSource()
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where('is_draft', '0')
            ->where('is_deleted', '0')
            ->whereIn('doc_type', ['receipt']);

        if ($agencyId) {
            $allReceipts = $allReceipts->whereHas('customer', function ($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }
        $allReceipts = $allReceipts->get(['doc_date', 'doc_subtotal', 'doc_vat', 'doc_total']);

        $allCustomers = Customer::filterSource()
            ->isProspect(false)
            ->isActive()
            ->get();

        $fleetCosts = null;
        if (hasModule('fleet')) {
            $fleetCosts = Cost::filterSource()
                ->filterAgencies($agencyId)
                ->whereBetween('date', [$startDate, $endDate]);
            if ($vehicle) {
                $fleetCosts = $fleetCosts->whereHas('vehicle', function ($q) use ($vehicle) {
                    $q->where('license_plate', $vehicle);
                });
            }
            $fleetCosts = $fleetCosts->get(['date', 'total', 'source_type']);
        }

        $allProducts = null;
        if (hasModule('products')) {
            $allProducts = ProductSale::filterSource()
                ->whereBetween('date', [$startDate, $endDate])
                ->get(['date', 'subtotal']);
        }

        /**função criar no model a função para os serviços por fornecedor*/
        $allServices = null;
        $allServices = Service::filterSource()
                ->orderBy('code', 'asc')
                ->get();

        $allProviders = null;
        $allProviders = Provider::filterSource()
                    ->orderBy('code', 'asc')
                    ->pluck('name', 'id')
                    ->toArray();  
        
        $allProvidersColor = null;
        $allProvidersColor = Provider::filterSource()
                            ->orderBy('code', 'asc')
                            ->pluck('color', 'id')
                            ->toArray();  

        $serviceProviders = $allShipments->groupBy('provider.id')->transform(function($item){
            return $item->groupBy('service.id')->transform(function($item){
                return ['count' => $item->count('shipment.id'), 'volumes' => $item->sum('volumes')];
            });
        });

        /***/

        if ($tab == 'quality') {
            $shipmentsByDay   = Statistic::getShipmentsStatsByDay($allShipments);
            $incidencesTypes  = Statistic::getIncidencesTotals($startDate, $endDate, $allShipments);
            $billingTotals    = Statistic::getTotalCounters($allShipments, $allCovenants, $allCustomers, null, null, $allInvoices, $allPurchaseInvoices);
            $customersTotals  = Statistic::getCustomersTotals($allShipments, $startDate, $endDate);
            $customersChart   = Statistic::getCustomersChartData($customersTotals);
            $operatorAvgChart = Statistic::getOperatorsAvgChartData($allShipments);

            $incidencesByProviderChart = Statistic::getSimpleChartData($incidencesTypes['providers']);
            $incidencesByCustomerChart = Statistic::getSimpleChartData($incidencesTypes['customers']);
            $incidencesByServiceChart  = Statistic::getSimpleChartData($incidencesTypes['services']);

            $totalClaims = Ticket::whereBetween('date', [$startDate, $endDate])
                ->where('category', 'complaint')
                ->count();

            $values = [
                $billingTotals['shipments']['deliveries']['count'],
                $billingTotals['shipments']['incidences']['count'],
                $billingTotals['shipments']['devolutions']['count']
            ];
            $statusChart = [
                'labels' => '"Entregas", "Incidências","Devoluções"',
                'colors' => '"#17A72D", "#E83D28","#FFC107"',
                'values' => implode(',', $values)
            ];
        }

        if ($tab == 'services') {
            $typeOfServices    = Statistic::byTypeOfService($allShipments);
            $nacionalShipments = Statistic::topNacional($allShipments);
            $regionalShipments = Statistic::topRegional($allShipments);
            $exportShipments   = Statistic::topExportCountries($allShipments);
            $importShipments   = Statistic::topImportCountries($allShipments);
            $routesPickups     = Statistic::topPickupsByRoute($allShipments);
            $billingTotals     = Statistic::getTotalCounters($allShipments, $allCovenants, $allCustomers, $allProducts, $fleetCosts, $allInvoices, $allPurchaseInvoices);

            $operatorShipments = Statistic::topOperatorDeliveries($allShipments, $myOperators);
            $routesShipments   = Statistic::topShipmentsByRoute($allShipments, $routes);
        }

        if ($tab == 'gains') {
            $customerShipments  = Statistic::topCustomers($allShipments);
            $billingProviders   = Statistic::topProviders($allShipments);
            $billingTotals      = Statistic::getTotalCounters($allShipments, $allCovenants, $allCustomers, $allProducts, $fleetCosts, $allInvoices, $allPurchaseInvoices);

            $balanceChart       = Statistic::getBalanceChartData($billingTotals);
            $balanceDetails     = Statistic::getBallanceDetails($billingTotals);
        }

        if ($tab == 'sellers') {
            $operators = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterSource()
                ->isOperator()
                ->isActive()
                ->whereNotNull('comission_percent');
            if ($request->has('operator'))
                $operators = $operators->whereId($request->get('operator'));
            $operators = $operators->get();

            $salesCommercial    = Statistic::getSalesBySeller($startDate, $endDate, $sellers, $allShipments, $allCovenants);
            $salesOperators     = Statistic::getSalesByOperator($startDate, $endDate, $operators, $allShipments);
            $prospectHistory    = Statistic::getProspectHistory($startDate, $endDate, $sellers);
        }

        if ($tab == 'users') {
            $users = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterSource()
                ->isActive()
                ->get();

            $salesUsers         = Statistic::getSalesByUser($startDate, $endDate, $allShipments, $users);
            $prospectHistory    = Statistic::getProspectHistory($startDate, $endDate, $sellers);
        }

        if (!$tab || $tab == 'summary') {
            $billingTotals      = Statistic::getTotalCounters($allShipments, $allCovenants, $allCustomers, $allProducts, $fleetCosts, $allInvoices, $allPurchaseInvoices);
            $customersTotals    = Statistic::getCustomersTotals($allShipments, $startDate, $endDate);
            $statusTotals       = Statistic::getStatusTotals($allShipments, $statusIds);

            //CHART DATA
            $balanceChart       = Statistic::getBalanceChartData($billingTotals);
            $monthBillingChart  = Statistic::getBillingChart($allShipments, $metric, $startDate, $endDate);
            $providersChart     = Statistic::getProvidersChartData($allShipments);
            $statusChart        = Statistic::getStatusChartData($statusTotals, $allStatus);
            $recipientsChart    = Statistic::getRecipientsChartData($allShipments);

            $salesCommercial = Statistic::getSalesBySeller($startDate, $endDate, $sellers, $allShipments, $allCovenants);
            $sellersChart = Statistic::getSellerChartData($salesCommercial);
        }

        $sellers = $sellers->pluck('name', 'id')->toArray();

        $compact = compact(
            'metrics',
            'providerStats',
            'periodList',
            'vehiclesList',
            'operatorsList',
            'months',
            'years',
            'startDate',
            'endDate',

            'services',
            'typeOfServices',


            'billingTotals',
            'statusTotals',

            'importShipments',
            'exportShipments',
            'nacionalShipments',
            'regionalShipments',

            'customerShipments',
            'operatorShipments',
            'topCustomers',
            'operatorsShipments',

            'routesShipments',
            'routesPickups',
            'salesCommercial',
            'salesOperators',
            'salesUsers',
            'allCovenants',
            'allBusinessHistory',
            'billingProviders',
            'totalCovenants',
            'customersTotals',
            'shipmentsByDay',

            'sellers',
            'agencies',

            'monthBillingChart',
            'balanceChart',
            'providersChart',
            'statusChart',
            'recipientsChart',
            'customersChart',
            'sellersChart',
            'operatorAvgChart',
            'incidencesByProviderChart',
            'incidencesByCustomerChart',
            'incidencesByServiceChart',

            'prospectHistory',
            'balanceDetails',

            'incidencesTypes',
            'totalClaims',
            'allInvoices',
            'allReceipts',
            'allBillingInvoices',
            'allPurchaseInvoices',
            'allPurchaseReceipts',

            'allServices',
            'allProviders',
            'serviceProviders',
            'allProvidersColor'
        );

        return $this->setContent('admin.statistics.index', $compact);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function incidencesDetails(Request $request)
    {

        $source     = $request->get('source');
        $startDate  = $request->get('datemin');
        $endDate    = $request->get('datemax');
        $providerId = $request->get('provider');
        $customerId = $request->get('customer');
        $serviceId  = $request->get('service');

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        if ($source == 'providers' && empty($providerId)) {
            $key = array_keys($providers);
            $providerId = @$key[0];
        }

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        if ($source == 'services' && empty($serviceId)) {
            $key = array_keys($services);
            $serviceId = @$key[0];
        }

        if ($source == 'customers' && !empty($customerId)) {
            $customer = Customer::filterSource()->select(['name', 'id'])->find($customerId);
        }

        $types = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $bindings = [
            'id',
            'shipment_id',
            'status_id',
            'incidence_id',
            'obs',
            DB::raw('DATE(created_at) as date'),
            DB::raw('DAY(created_at) as day'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('HOUR(created_at) as hour')
        ];

        $allIncidences = ShipmentHistory::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status_id', ShippingStatus::INCIDENCE_ID);

        //filter provider
        if ($source == 'providers') {
            $allIncidences = $allIncidences->whereHas('shipment', function ($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            });
        }

        //filter customers
        if ($source == 'customers') {
            $allIncidences = $allIncidences->whereHas('shipment', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        //filter services
        if ($source == 'services') {
            $allIncidences = $allIncidences->whereHas('shipment', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            });
        }

        $allIncidences = $allIncidences->get($bindings);

        $totalIncidences  = $allIncidences->count();
        $incidencesByType = $allIncidences->groupBy('incidence_id');

        $incidences = [];
        foreach ($incidencesByType as $typeId => $items) {
            $incidences[@$types[$typeId]] = $items->count();
        }
        arsort($incidences);

        $data = compact(
            'source',
            'providers',
            'services',
            'totalIncidences',
            'incidences',
            'incidencesTypes',
            'startDate',
            'endDate',
            'customer'
        );

        return view('admin.statistics.incidence_details', $data)->render();
    }
}
