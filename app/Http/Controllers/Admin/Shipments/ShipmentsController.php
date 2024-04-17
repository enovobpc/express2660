<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\Billing\VatRate;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\BroadcastPusher;
use App\Models\CacheSetting;
use App\Models\CttDeliveryManifest;
use App\Models\CustomerBilling;
use App\Models\Trip\Trip;
use App\Models\Invoice;
use App\Models\Logistic\Product;
use App\Models\Map;
use App\Models\OperatorTask;
use App\Models\PackType;
use App\Models\FileRepository;
use App\Models\PaymentCondition;
use App\Models\PickupPoint;
use App\Models\ShipmentSchedule;
use App\Models\Sms\Pack;
use App\Models\UserWorkgroup;
use App\Models\ZipCode;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\BillingZone;
use App\Models\ShipmentExpense;
use App\Models\Vehicle;
use App\Models\IncidenceType;
use App\Models\Route;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShipmentPallet;
use App\Models\Traceability\ShipmentTraceability;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Customer;
use App\Models\CustomerRecipient;
use App\Models\CustomerType;
use App\Models\Email\Email;
use App\Models\Email\MailingList;
use App\Models\TransportType;
use App\Models\User;
use App\Models\ShipmentHistory;
use App\Models\ShipmentHistoryAttachament;
use App\Models\ShipmentPackDimension;
use App\Models\Webservice;

use Auth, Date, DB, Setting, Response, Log, DateTime;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $trips = Trip::remember(config('cache.query_ttl'))
            ->cacheTags(Trip::CACHE_TAG)
            ->pluck('code', 'id')
            ->toArray();

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id', 'is_operator', 'login_app']);

        $operators = User::listOperators($allOperators->filter(function ($item) {
            return $item->is_operator || $item->login_app;
        }));
        $users     = User::listOperators($allOperators->filter(function ($item) {
            return !$item->is_operator;
        }));

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $workgroups = UserWorkgroup::remember(config('cache.query_ttl'))
            ->cacheTags(UserWorkgroup::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $expenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            //->isSeller(true)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $transportTypes = TransportType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $recipientCounties = ['not-assigned' => 'Todos'];

        $hours = listHours(1);

        $cargoMode = app_mode_cargo();

        $data = compact(
            'services',
            'status',
            'providers',
            'agencies',
            'operators',
            'users',
            'incidences',
            'billingZones',
            'vehicles',
            'trailers',
            'hours',
            'recipientCounties',
            'workgroups',
            'routes',
            'expenses',
            'sellers',
            'cargoMode',
            'customerTypes',
            'trips',
            'transportTypes'
        );

        return $this->setContent('admin.shipments.shipments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipment = null)
    {

        if (is_null($shipment)) {
            $shipment = new Shipment;
            $shipment->sender_country    = Setting::get('app_country'); //default
            $shipment->recipient_country = Setting::get('app_country');
        }

        if ($shipment->convert_from_collection && @$shipment->customer->vat == 'A85508299') { //sending
            $senderAgency                        = $shipment->provider_sender_agency;
            $recipientAgency                     = $shipment->provider_recipient_agency;
            $shipment->provider_sender_agency    = $recipientAgency; //troca origem com destino
            $shipment->provider_recipient_agency = $senderAgency;
            $shipment->reference3 = $shipment->reference3 . '#PICKTRK' . $shipment->reference;
        }

        if ($request->has('transhipment')) {
            $shipment = Shipment::find($request->get('transhipment'));
            $shipment->type = Shipment::TYPE_TRANSHIPMENT;
            $shipment->parent_tracking_code = $shipment->tracking_code;
            $shipment->total_expenses = 0;
            $shipment->total_expenses_cost = 0;
            $shipment->total_price = 0;
            $shipment->volumes = null;
            $shipment->weight  = null;
            $shipment->ldm     = null;
        }

        $schedule = null;
        if ($request->get('schedule', false)) {
            $schedule = new ShipmentSchedule();
            $schedule->repeat_every = 1;
            $schedule->frequency = 'week';
        }

        //pré-preenche a partir das shiiping orders
        if ($request->has('logistic-shipping-order')) {
            $shippingOrder = \App\Models\Logistic\ShippingOrder::filterSource()->find($request->get('logistic-shipping-order'));

            if (@$shippingOrder->shipment->id) {
                return $this->edit($request, $shippingOrder->shipment_id);
            } else {
                $shipment->exists           = true;
                $shipment->shipping_order_id = @$shippingOrder->id;
                $shipment->customer         = @$shippingOrder->customer;
                $shipment->customer_id      = @$shippingOrder->customer_id;
                $shipment->agency_id        = @$shippingOrder->customer->agency_id;
                $shipment->sender_agency_id = @$shippingOrder->customer->agency_id;
                $shipment->sender_name      = @$shippingOrder->customer->name;
                $shipment->sender_address   = @$shippingOrder->customer->address;
                $shipment->sender_zip_code  = @$shippingOrder->customer->zip_code;
                $shipment->sender_city      = @$shippingOrder->customer->city;
                $shipment->sender_state     = @$shippingOrder->customer->state;
                $shipment->sender_country   = @$shippingOrder->customer->country;
                $shipment->sender_phone     = @$shippingOrder->customer->mobile ? @$shippingOrder->customer->mobile : @$shippingOrder->customer->phone;
                $shipment->sender_vat       = @$shippingOrder->customer->vat;
                $shipment->sender_attn      = @$shippingOrder->customer->responsable;
                $shipment->volumes          = @$shippingOrder->total_volumes;
                $shipment->weight           = @$shippingOrder->total_weight;
                $shipment->fator_m3         = @$shippingOrder->total_volume;
                $shipment->reference        = @$shippingOrder->document;
                $shipment->status_id        = ShippingStatus::PENDING_ID;
                $shipment->pack_dimensions  = $this->getDimensionsFromShippingOrder($shippingOrder);
            }
        }

        //obtem taxa combustivel
        if (Setting::get('fuel_tax')) {
            $shipment->fuel_tax = $shipment->getFuelTaxRate();
        }

        if (Setting::get('shipment_prefill_hour')) {
            $shipment->start_hour = Shipment::chooseSelectboxCurrentHour();
        }

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get();

        $operators = $this->listOperators($allOperators);
        $users     = $this->listOperators($allOperators, false);

        $services = $this->listServices(Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isShipment()
            ->ordered()
            ->get());

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('source')
            ->pluck('name', 'id')
            ->toArray();

        $allExpenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where('type', '<>', 'fuel')
            ->ordered()
            ->get();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->get();

        $packTypes = $this->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $transportTypes = TransportType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vatTaxes = Invoice::getVatTaxes(true, true);
        $vatRatesValues = VatRate::filterSource()
            ->pluck('value', 'id')
            ->toArray() ?? [];

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });

        $allExpenses = $this->listExpenses($allExpenses);

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $userAgencies = $agencies;
        if (!empty(Auth::user()->agencies)) {
            $userAgencies = array_intersect_key($agencies, array_flip(Auth::user()->agencies));
        }

        $providers = $this->listProviders($allProviders);

        $provider  = $allProviders->find($shipment->provider_id);

        if ($provider) {
            $providerAgencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->whereIn('id', $provider->agencies)
                ->orderBy('code', 'asc')
                ->get());
        } else {
            $providerAgencies = $userAgencies;
        }

        if (in_array($shipment->type, [Shipment::TYPE_DEVOLUTION, Shipment::TYPE_RETURN, Shipment::TYPE_PICKUP, Shipment::TYPE_RECANALIZED])) {
            $agencies = Agency::filterAgencies()
                ->cacheTags(Agency::CACHE_TAG)
                ->pluck('name', 'id')
                ->toArray();

            $userAgencies = $agencies;
        }


        $myAgencies = Auth::user()->agencies;
        if (empty($myAgencies)) {
            $myAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->pluck('id')
                ->toArray();
        }

        $departments = null;
        if ($shipment->customer_id) {
            $departments = Customer::whereCustomerId($shipment->customer_id)
                ->isDepartment()
                ->pluck('name', 'id')
                ->toArray();
        }

        if (hasModule('sms')) {
            $smsPacks = Pack::remember(config('cache.query_ttl'))
                ->cacheTags(Pack::CACHE_TAG)
                ->countRemaining()
                ->sum('remaining_sms');
        }

        $hours = listHours(10);

        $senderStates    = $this->listStates($shipment->sender_country);
        $recipientStates = $this->listStates($shipment->recipient_country);


        $action = 'Criar Envio';
        if ($schedule) {
            $action = 'Agendar envio ou recolha';
        }

        $formOptions = [
            'route' => ['admin.shipments.store'],
            'class' => 'form-horizontal form-shipment',
            'method' => 'POST'
        ];

        $compact = compact(
            'shipment',
            'action',
            'formOptions',
            'providers',
            'agencies',
            'userAgencies',
            'services',
            'complementarServices',
            'myAgencies',
            'providerAgencies',
            'operators',
            'vehicles',
            'trailers',
            'allExpenses',
            'hours',
            'schedule',
            'smsPacks',
            'packTypes',
            'users',
            'departments',
            'senderStates',
            'recipientStates',
            'paymentConditions',
            'vatTaxes',
            'customerCategories',
            'transportTypes',
            'vatRatesValues'
        );


        return view($this->getEditView(), $compact)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $id = $request->get('trkid', null);

        if (!empty(env('SHIPMENTS_LIMIT'))) { //number limit creation of shipments
            $start = date('Y-m') . '-01';
            $end   = date('Y-m') . '-31';

            $total = Shipment::whereNotNull('created_by')
                ->whereBetween('date', [$start, $end])
                ->count();

            if ($total > env('SHIPMENTS_LIMIT')) {
                $result = [
                    'result'    => false,
                    'feedback'  => 'Ultrapassou o limite de ' . env('SHIPMENTS_LIMIT') . ' envios mês possíveis criar diretamente pela plataforma.'
                ];
                return Response::json($result);
            }
        }

        return $this->update($request, $id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        //expande a row da datatable
        $action = null;
        if($id == 'expand-row') {
            $action = 'expand-row';
            $id = $request->id;
        }

        $shipment = Shipment::with('pack_dimensions')
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->with(['senderAgency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->with(['recipientAgency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
            }])
            ->with(['expenses' => function ($q) {
                $q->orderBy('auto', 'desc');
                $q->orderBy('date', 'asc');
            }])
            ->withTrashed()
            ->filterAgencies();

        if (strlen($id) >= 12) {
            $shipment = $shipment->where('tracking_code', $id)
                ->firstOrfail();
        } else {
            $shipment = $shipment->findOrfail($id);
        }

        if($action == 'expand-row') {
            return $this->datatableExpandRow($shipment); //expande a row da datatable
        }

        $shipmentHistory = ShipmentHistory::with(['status' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(ShippingStatus::CACHE_TAG);
        }])
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
            }])
            ->with(['operator' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
            }])
            ->with(['user' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
            }])
            ->with(['incidence' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(IncidenceType::CACHE_TAG);
            }]);

        if (in_array($shipment->webservice_method, ['envialia', 'tipsa', 'gls_zeta'])) {
            $shipmentHistory = $shipmentHistory->with('provider_agency');
        }

        $shipmentHistory = $shipmentHistory->where('shipment_id', $shipment->id)
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $attachamentHistory = ShipmentHistoryAttachament::where('shipment_id', $shipment->id);

        $shipmentTraceability = ShipmentTraceability::with(['operator' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(User::CACHE_TAG);
        }])
            ->with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->where('shipment_id', $shipment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $shipmentIncidencesResolutions = ShipmentIncidenceResolution::with(['operator' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(User::CACHE_TAG);
        }])
            ->with('operator', 'resolution')
            ->where('shipment_id', $shipment->id)
            ->orderBy('id', 'desc')
            ->get();

        $complementarServices = ShippingExpense::filterSource()
            ->isComplementarService()
            ->get(['id', 'name']);

        $arr = [];
        foreach ($complementarServices as $service) {
            if ($shipment->complementar_services && in_array($service->id, $shipment->complementar_services)) {
                $arr[] = $service->name;
            }
        }
        $complementarServices = $arr;

        $shipmentAttachments = FileRepository::with('created_user')
            ->where('source_class', 'Shipment')
            ->where('source_id', $shipment->id)
            ->orderBy('name', 'asc')
            ->get();

        $userAgencies = Auth::user()->agencies;

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $groupedShipments = Shipment::where(function ($q) use ($shipment) {
            $q->where('id', $shipment->id)
                ->orWhere(function ($q) use ($shipment) {
                    $q->where('type', 'M');
                    $q->where('parent_tracking_code', $shipment->tracking_code);
                });
        });

        if (Setting::get('shipment_list_detail_master')) {
            $groupedShipments = $groupedShipments->where('id', '0'); //para nao devolver resultados
        }

        $groupedShipments = $groupedShipments->orderBy('id', 'asc')
            ->get();


        $transhipments = Shipment::where(function ($q) use ($shipment) {
            $q->where('type', 'T');
            $q->where('parent_tracking_code', $shipment->tracking_code);
        })
            ->orderBy('id', 'asc')
            ->get();

        $shipmentTotals = $this->getShipmentTotals($shipment, $transhipments, $groupedShipments);

        $tab = $request->tab;

        $data = compact(
            'shipment',
            'shipmentHistory',
            'shipmentTraceability',
            'attachamentHistory',
            'transhipments',
            'groupedShipments',
            'complementarServices',
            'userAgencies',
            'shipmentIncidencesResolutions',
            'shipmentAttachments',
            'packTypes',
            'shipmentTotals',
            'tab'
        );

        return view('admin.shipments.shipments.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $shipment = null)
    {

        if (is_null($shipment) && !empty($id)) {
            if (strlen($id) >= 12) {
                $shipment = Shipment::with('customer')
                    ->filterMyAgencies()
                    ->where('tracking_code', $id)
                    ->firstOrfail();
            } else {
                $shipment = Shipment::with('customer')
                    ->filterMyAgencies()
                    ->findOrfail($id);
            }
        }

        if ($shipment->is_collection) {
            $pickupController = new PickupsController();
            return $pickupController->edit($request, $shipment);
        }

        if (
            in_array(Setting::get('shipment_adicional_addr_mode'), ['pro', 'pro_fixed']) &&
            ($shipment->type == Shipment::TYPE_MASTER
                || $shipment->parent_type == Shipment::TYPE_MASTER
                || $shipment->children_type == Shipment::TYPE_MASTER)
        ) {

            if ($shipment->parent_tracking_code) { //abriu um envio filho, por isso obtem o envio master.
                $shipment = Shipment::where('tracking_code', $shipment->parent_tracking_code)->firstOrFail();
                $id = $shipment->id;
            }

            $shipment->multiple_addresses = $shipment->listAllAddresses();
        }

        $schedule = null;
        if ($request->get('schedule', false)) {
            $schedule = ShipmentSchedule::firstOrNew([
                'shipment_id' => $shipment->id
            ]);

            if (!$schedule->exists) {
                $schedule->repeat_every = 1;
                $schedule->frequency = 'week';
            }
        }

        $allServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->isShipment()
            ->ordered()
            ->get();

        $services = $this->listServices($allServices);
        $shipment->service = $allServices->filter(function ($item) use ($shipment) {
            return $item->id == $shipment->service_id;
        })->first();

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get();

        $operators = $this->listOperators($allOperators);
        $users     = $this->listOperators($allOperators, false);

        $shipment->operator = $allOperators->filter(function ($item) use ($shipment) {
            return $item->id == $shipment->operator_id;
        });


        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->get();
        $providers = $this->listProviders($allProviders);
        $shipment->provider = $allProviders->filter(function ($item) use ($shipment) {
            return $item->id == $shipment->provider_id;
        })->first();

        $agencies = Agency::filterAgencies()
            ->cacheTags(Agency::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray();

        $userAgencies = $agencies;

        $provider = $allProviders->find($shipment->provider_id);
        if ($provider) {
            //obtem as agencias para o fornecedor selecionado
            /* $providerAgencies = Agency::listsGrouped(
                Agency::remember(config('cache.query_ttl'))
                    ->cacheTags(Agency::CACHE_TAG)
                    ->filterAgencies()
                    ->whereIn('id', $provider->agencies)
                    ->orderBy('code', 'asc')
                    ->get()
            );*/

            $providerAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->whereIn('id', $provider->agencies)
                ->pluck('name', 'id')
                ->toArray();
        } else {
            $providerAgencies = $userAgencies;
        }

        $packTypes = $this->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $transportTypes = TransportType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vatTaxes = Invoice::getVatTaxes(true, true);
        $vatRatesValues = VatRate::filterSource()
            ->pluck('value', 'id')
            ->toArray() ?? [];

        if ($shipment->recipient_pudo_id) {
            $pickupPoints = $this->listPudoPoints(PickupPoint::remember(config('cache.query_ttl'))
                ->cacheTags(PickupPoint::CACHE_TAG)
                ->filterSource()
                ->where('provider_id', $shipment->provider_id)
                ->get());
        }

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $allExpenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where('type', '<>', 'fuel')
            ->ordered()
            ->get();

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });
        $allExpenses = $this->listExpenses($allExpenses);

        $departments = null;
        if ($shipment->customer_id) {
            $departments = Customer::whereCustomerId($shipment->customer_id)
                ->isDepartment()
                ->pluck('name', 'id')
                ->toArray();
        }

        $myAgencies = Auth::user()->agencies;
        if (empty($myAgencies)) {
            $myAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->pluck('id')
                ->toArray();
        }

        if (hasModule('sms')) {
            $smsPacks = Pack::remember(config('cache.query_ttl'))
                ->cacheTags(Pack::CACHE_TAG)
                ->countRemaining()
                ->sum('remaining_sms');
        }

        $hours  = listHours(10);

        $senderStates    = $this->listStates($shipment->sender_country);
        $recipientStates = $this->listStates($shipment->recipient_country);

        $action = 'Editar TRK#' . $shipment->tracking_code;
        if ($schedule) {
            $action = 'Editar agendamento';
        }

        $formOptions = [
            'route' => ['admin.shipments.update', $id],
            'class' => 'form-horizontal form-shipment',
            'method' => 'PUT'
        ];


        $ptTelecom = false;
        if ((@$shipment->customer->id == '1443' || @$shipment->customer->customer_id == '1443') && config('app.source') == 'corridadotempo') { //PT TELECOM
            $ptTelecom = true;
        }


        $compact = compact(
            'shipment',
            'action',
            'formOptions',
            'providers',
            'agencies',
            'userAgencies',
            'services',
            'departments',
            'complementarServices',
            'myAgencies',
            'providerAgencies',
            'operators',
            'vehicles',
            'trailers',
            'allExpenses',
            'schedule',
            'hours',
            'smsPacks',
            'ptTelecom',
            'packTypes',
            'users',
            'pickupPoints',
            'senderStates',
            'recipientStates',
            'paymentConditions',
            'vatTaxes',
            'customerCategories',
            'transportTypes',
            'vatRatesValues'
        );

        return view($this->getEditView(), $compact)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReturn(Request $request, $shipmentId)
    {

        $originalShipment = Shipment::filterAgencies()
            ->findOrfail($shipmentId);

        $shipment = $originalShipment->createDirectReturn(null, null, false);

        return $this->create($request, $shipment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createDevolution($shipmentId, $redirectBack = true)
    {
        $originalShipment = Shipment::filterAgencies()
            ->findOrfail($shipmentId);

        $shipment = $originalShipment->createDirectDevolution();

        if ($redirectBack) {
            return Redirect::back()->with('success', 'Devolução criada com sucesso.');
        } else {
            return $shipment;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReplicate($shipmentId)
    {
        $shipment = Shipment::filterAgencies()->findOrFail($shipmentId);

        return view('admin.shipments.shipments.modals.replicate', compact('shipment'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicate(Request $request, $shipmentId)
    {

        $linked = $request->get('linked', false);
        $edit   = $request->get('edit', false);
        $keepExpenses = $request->get('expenses', false);


        $myAgencies = Auth::user()->agencies;

        $defaultStatusId = empty(Setting::get('shipment_status_after_create')) ? ShippingStatus::ACCEPTED_ID : Setting::get('shipment_status_after_create');

        $originalShipment = Shipment::filterAgencies()->findOrFail($shipmentId);

        $shipment = $originalShipment->replicate();

        if (!empty($myAgencies) && !in_array($shipment->agency_id, $myAgencies) && in_array($shipment->recipient_agency_id, $myAgencies)) {
            $shipment->agency_id                 = $shipment->recipient_agency_id;
            $shipment->sender_agency_id          = $shipment->recipient_agency_id;
        }

        $shipment->reset2Replicate();
        $shipment->status_id = $defaultStatusId;

        if ($linked) {
            $shipment->type = 'M';
            $shipment->parent_tracking_code = $originalShipment->tracking_code;
        }

        if (!$keepExpenses) {
            $shipment->total_expenses = 0;
            $shipment->total_expenses_cost = 0;
        }

        $shipment->setTrackingCode();

        if ($linked) {
            $originalShipment->children_type = 'M';
            $originalShipment->children_tracking_code = $shipment->tracking_code;
            $originalShipment->save();
        }

        if ($keepExpenses) {
            $originalExpenses = ShipmentExpense::where('shipment_id', $originalShipment->id)->get();

            foreach ($originalExpenses as $originalExpense) {

                $fill = $originalExpense->toArray();
                unset($fill['created_at'], $fill['updated_at']);

                $expense = new ShipmentExpense();
                $expense->fill($fill);
                $expense->shipment_id = $shipment->id;
                $expense->save();
            }
        }

        $history = new ShipmentHistory();

        $history->status_id   = $defaultStatusId;
        $history->shipment_id = $originalShipment->id;
        $history->agency_id   = $shipment->agency_id;
        $history->save();

        if ($edit) {
            if ($shipment->is_collection) {
                $pickupController = new PickupsController();
                $html = $pickupController->edit($request, $shipment->id);
            } else {
                $html = $this->edit($request, $shipment->id);
            }

            return Response::json([
                'result'   => true,
                'feedback' => 'Envio duplicado com sucesso.',
                'button'   => '<a href="'. route('admin.shipments.edit', [$shipment->id]) .'" data-toggle="modal" data-target="#modal-remote-xl" style="display: none"></a>'
            ]);
        } else {

            return Response::json([
                'result'   => true,
                'feedback' => 'Envio duplicado com sucesso.',
                'html'     => null,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createDirectReturn($shipmentId)
    {

        $originalShipment = Shipment::filterAgencies()
            ->findOrFail($shipmentId);

        $originalShipment->createDirectReturn();

        return Redirect::back()->with('success', 'Retorno direto criado com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if ($request->get('source') == 'timeline') {
            return $this->updateFields($request, $id);
        }

        $shipment = Shipment::filterMyAgencies()->findOrNew($id);

        $input           = $request->all();
        $saveOther       = (int) $request->save_other;
        $shipmentExists  = $shipment->exists;
        $oldOperatorId   = $shipment->operator_id;

        $service  = Service::findOrFail(@$input['service_id']);
        $customer = Customer::find(@$input['customer_id']);
        $shipment->service  = $service;
        $shipment->customer = $customer;

        $input['recipient_agency_id']       = empty($input['recipient_agency_id']) ? $input['agency_id'] : $input['recipient_agency_id'];
        $input['billing_date']              = empty($input['billing_date']) ? $input['date'] : $input['billing_date'];
        $input['provider_id']               = $request->get('provider_id', Setting::get('shipment_default_provider'));
        $input['ignore_billing']            = $request->get('ignore_billing', false);
        $input['price_fixed']               = $request->get('price_fixed', false);
        $input['without_pickup']            = $request->get('without_pickup', false);
        $input['has_return']                = $request->get('has_return', []);
        $input['tags']                      = explode(',', $request->get('tags'));
        $input['cod']                       = $request->get('cod');
        $input['volumes']                   = @$input['volumes'] ? $input['volumes'] : 1;
        $input['weight']                    = forceDecimal(@$input['weight'] ? $input['weight'] : 1);
        $input['provider_weight']           = forceDecimal(@$input['provider_weight']);
        $input['has_assembly']              = $request->get('has_assembly', false);
        $input['has_sku']                   = $request->get('has_sku', false);

        if (in_array('rpack', $input['has_return'])) {
            if (!in_array('rpack', $input['tags'])) {
                $input['tags'][] = 'rpack';
            }
        } else {
            array_remove_val($input['tags'], 'rpack');
        }

        if (Setting::get('shipments_round_up_weight')) {
            $input['weight']            = roundUp(@$input['weight']);
            $input['volumetric_weight'] = roundUp(@$input['volumetric_weight']);
        }

        //prepara portes
        $input['ignore_billing'] = false;
        if ($input['cod'] == 'P') {
            $input['ignore_billing'] = true;
        } elseif ($input['cod'] == 'D') { //portes no destino. Atualiza campos anteriores a out/2022
            $input['payment_at_recipient']      = true;
            $input['total_price_for_recipient'] = $input['billing_total'];
        }

        //apaga envios adicionais
        if ($request->deleted_addrs) {
            $deletedIds = explode(',', $request->deleted_addrs);
            $deletedIds = array_filter($deletedIds);

            Shipment::whereIn('id', $deletedIds)
                ->delete();
        }

        //grava os dados do envio que originou este envio.
        if (@$input['shp_type'] && @$input['shp_parent']) {
            $input['parent_tracking_code'] = $input['shp_parent'];
            $input['type'] = $input['shp_type'];
        }

        //anula estado de impressão porque mudou o fornecedor
        if ($shipment->exists && $shipment->is_printed && $shipment->provider_id != $input['provider_id']) {
            $input['is_printed'] = false;
        }

        //se nao foi criado pelo cliente, altera sempre o requested by para ficar igual ao cliente
        if ($shipment->exists && !$shipment->created_by_customer) {
            $input['requested_by'] = $input['customer_id'];
        }

        //data de recolha
        if (@$input['pickup_date']) {
            //data definida na modal
            $hour = @$input['pickup_end_hour'] ? $input['pickup_end_hour'] . ':00' : '00:00:00';
            $input['pickup_date'] = $input['pickup_date'] . ' ' . $hour;
        } else {
            //não existe data definida. Detecta automatico
            $pickupDate = $shipment->getPickupDate(true);
            $input['pickup_date'] = $pickupDate['shipping_date'];
        }

        //data entrega
        if (@$input['delivery_date']) {
            //data definida na modal
            $hour = @$input['end_hour'] ? $input['end_hour'] . ':00' : '00:00:00';
            $input['delivery_date'] = $input['delivery_date'] . ' ' . $hour;
        } else {
            //não existe data definida. Detecta automatico
            $deliveryDate = $shipment->getDeliveryDate(true, $input['pickup_date']);
            $input['delivery_date'] = $pickupDate['shipping_date'];
        }

        //deteta as rota recolha
        $route = Route::getRouteFromZipCode($input['sender_zip_code'], @$input['service_id'], null, 'pickup');
        if ($route) {
            $input['pickup_route_id'] = @$route->id;

            $schedule = $route->getSchedule(@$input['start_hour'], @$input['end_hour']);
            if (empty($input['pickup_operator_id'])) {
                $input['pickup_operator_id'] = @$schedule['operator']['id'];
            }
        }

        //deteta as rota entrega
        $route = Route::getRouteFromZipCode($input['recipient_zip_code'], @$input['service_id'], null, 'delivery');
        if ($route) {
            $input['route_id'] = $route->id;

            $schedule = $route->getSchedule(@$input['start_hour'], @$input['end_hour']);
            if (empty($input['operator_id'])) {
                $input['operator_id'] = @$schedule['operator']['id'];
            }
        }

        //gravar dados remetente
        if ($request->has('save_sender')) {
            $input['sender_id'] = $this->insertOrUpdateSender($input);
        }

        //gravar dados destinatário
        if ($request->has('save_recipient')) {
            $input['recipient_id'] = $this->insertOrUpdateRecipient($input);
        }


        //grava shipment
        if ($shipment->validate($input)) {
            $shipment->fill($input);
            $shipment->is_collection = 0;

            if ($shipment->exists) {

                $saveHistory = false;
                if ($shipment->status_id == ShippingStatus::PENDING_ID) {
                    $shipment->status_id = $shipment->getDefaultStatus();
                    $saveHistory = true;
                }

                if ($shipment->hasSyncError()) {
                    $shipment->resetWebserviceError();
                }
            } else {
                $saveHistory = true;
                $shipment->status_id = $shipment->getDefaultStatus();
            }

            //grava e atribui código de envio
            unset($shipment->service, $shipment->customer);
            $shipment->setTrackingCode();

            //grava dimensões e mercadoria
            $this->storeDimensions($shipment, $input);

            //grava taxas adicionais
            $this->storeExpenses($shipment, $input);

            //submete via webservice
            $debug = $request->get('debug', false);
            $shipment->submitWebservice($debug);


            //desconta valor de pagamento da conta
            /*$paymentResult  = $shipment->walletPayment($customer);
            $paymentSuccess = $paymentResult['success'];
            $walletPayment  = $paymentResult['walletPayment'];
            if(!$paymentSuccess) {
                $shipment->status_id = ShippingStatus::PAYMENT_PENDING_ID;
                $submitWebservice    = false;
            }*/

            //UPDATE WALLET SHIPMENT PAYMENT (only update -- don't store)
            \App\Models\GatewayPayment\Base::updateWalletShipmentPayment($shipment, $customer);

            //detect ignore billing
            //CustomerBilling::detectCovenant($shipment);

            //create shipment from pickup
            if (!empty($shipment->parent_tracking_code) && $shipment->type == Shipment::TYPE_PICKUP) {
                unset($shipment->provider_weight);
                $parentShipment = Shipment::where('tracking_code', $shipment->parent_tracking_code)->first();

                if ($parentShipment) {
                    $parentHistory = new ShipmentHistory();
                    $parentHistory->shipment_id = $parentShipment->id;
                    $parentHistory->status_id   = ShippingStatus::PICKUP_CONCLUDED_ID;
                    $parentHistory->obs         = 'Gerado TRK' . @$shipment->tracking_code;
                    $parentHistory->save();

                    $shipment->insertOrUpdadePickupExpense($parentShipment); //add expense

                    $parentShipment->update([
                        'children_tracking_code' => $shipment->tracking_code,
                        'children_type' => Shipment::TYPE_PICKUP,
                        'status_id'     => ShippingStatus::PICKUP_CONCLUDED_ID
                    ]);

                    //desconta da wallet
                    if (hasModule('account_wallet') && !@$shipment->customer->is_mensal) {
                        $price = $shipment->billing_total;

                        if ($price > 0.00) {
                            try {
                                $shipment->walletPayment();
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }


            //envia e-mail de notificação
            if (!$shipmentExists && $request->get('send_email') && !empty($input['recipient_email'])) {
                $shipment->sendEmail();
            }

            //envia SMS de notificação
            if ($request->get('sms')) {
                try {
                    $shipment->sendSms(true);
                } catch (\Exception $e) {
                }
            }

            //grava historico envio
            if ($saveHistory) {
                $history = new ShipmentHistory();
                $history->status_id   = $shipment->status_id;
                $history->shipment_id = $shipment->id;
                $history->agency_id   = $shipment->agency_id;
                $history->operator_id = $shipment->operator_id;
                $history->vehicle     = $shipment->vehicle;
                $history->save();

                //notifica motoristas via PUSH
                if (!$shipment->created_by_customer && Setting::get('shipment_notify_operator')) {
                    $shipment->setOperatorNotification($shipment->operator_id);
                }
            }

            //SCHEDULE SHIPMENT
            if ($request->get('schedule_frequency') && $request->get('schedule_repeat_every')) {
                unset($shipment->provider_weight);

                $schedule = ShipmentSchedule::firstOrNew(['shipment_id' => $shipment->id]);
                $schedule->source           = config('app.source');
                $schedule->shipment_id      = $shipment->id;
                $schedule->repeat_every     = $request->get('schedule_repeat_every');
                $schedule->frequency        = $request->get('schedule_frequency');
                $schedule->repeat           = $request->get('schedule_repeat');
                $schedule->month_days       = $request->get('schedule_month_days');
                $schedule->weekdays         = $request->get('schedule_weekdays');
                $schedule->end_date         = $request->get('schedule_end_date');
                $schedule->end_repetitions  = $request->get('schedule_end_repetitions');
                $schedule->save();

                $shipment->is_scheduled = 1;
                $shipment->save();
            }

            //CREATE GROUPED SERVICE
            if (isset($input['addr']) && !empty(@$input['addr'])) {

                $keywords = [];
                $countAddr = 0;
                foreach ($input['addr'] as $hash => $adicionalAddress) {

                    //if(!empty($adicionalAddress['sender_name']) && !empty($adicionalAddress['recipient_name'])) {
                    unset($shipment->provider_weight);

                    $adicionalAddress['service_id'] = $shipment->service_id;
                    $adicionalAddress['date'] = @$adicionalAddress['date'] ? $adicionalAddress['date'] : $shipment->date;

                    if (empty($adicionalAddress['id'])) {
                        $newShipment = $shipment->replicate();
                        $newShipment->reset2replicate();
                        $newShipment->resetPrices();
                        $newShipment->fill($adicionalAddress);
                        $newShipment->tracking_code  = null;
                        $newShipment->charge_price   = null;
                        $newShipment->recipient_id   = null;
                        $newShipment->parent_tracking_code = $shipment->tracking_code;
                        $newShipment->type           = Shipment::TYPE_MASTER;
                        $newShipment->setTrackingCode();
                    } else {
                        $newShipment = Shipment::find($adicionalAddress['id']);
                        $newShipment->fill($adicionalAddress);
                        $newShipment->resetPrices();
                        $newShipment->charge_price   = null;
                        $newShipment->save();
                    }

                    $this->storeDimensions($newShipment, $input, $hash);

                    $keywords['addr_' . $countAddr] = array_merge(array_values($adicionalAddress), [$newShipment->tracking_code]); //lista de palavras chave
                    $countAddr++;
                    //}
                }

                $shipment->addKeyword('addrs', $keywords);
                $shipment->children_type = Shipment::TYPE_MASTER;
                $shipment->save();
            }

            if (Setting::get('shipment_notify_operator') && ((!$shipmentExists && $shipment->operator_id) || ($shipmentExists && $shipment->operator_id != $oldOperatorId))) {
                $shipment->notifyOperators();
            }

            //UPDATE SHIPPING EXPENSE
            if (@$input['shipping_order_id']) {
                \App\Models\Logistic\ShippingOrder::where('id', $input['shipping_order_id'])
                    ->update([
                        'shipment_id'  => $shipment->id,
                        'shipment_trk' => $shipment->tracking_code
                    ]);
            }

            //CREATE SHIPPING ORDER
            $logisticError = false;
            if (hasModule('logistic') && (isset($input['length']) || isset($input['box_description']))) {
                $shipment->customer = $customer;
                $result = $shipment->storeShippingOrder();
                if (!$result['result']) {
                    $logisticError = $result['feedback'];
                }
            }

            //PRINT DOCUMENTS
            $printGuide = $printLabel = $printCMR = $html = false;
            if ($request->has('print_guide')) {
                $html  = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printGuide = route('admin.printer.shipments.transport-guide', $shipment->id);
            }

            if ($request->has('print_label')) {
                $html  = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printLabel = route('admin.printer.shipments.labels', $shipment->id);
            }

            if ($request->has('print_cmr')) {
                $html  = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printCMR = route('admin.printer.shipments.cmr', $shipment->id);
            }

            if ($debug) {
                $debug = asset('/dumper/request.txt');
            }

            //PREPARE RETURN AND FEEDBACKS
            if (!isset($feedback) && empty($feedback)) {
                $feedback = 'Envio gravado com sucesso.';
            }

            $errorSyncMsg = $shipment->hasSyncError();
            if ($errorSyncMsg || $logisticError) {
                $result = [
                    'result'     => false,
                    'syncError'  => true,
                    'feedback'   => $errorSyncMsg ? $errorSyncMsg : $logisticError,
                    'debug'      => $debug,
                    'html'       => $html,
                    'saveOther'  => 0,
                    'trkid'      => $shipment->id
                ];
            } else {
                $result = [
                    'result'     => true,
                    'syncError'  => false,
                    'feedback'   => $feedback,
                    'saveOther'  => $saveOther,
                    'printGuide' => $printGuide,
                    'printCmr'   => $printCMR,
                    'printLabel' => $printLabel,
                    'debug'      => $debug,
                    'html'       => $html
                ];
            }
        } else {
            $result = [
                'result'    => false,
                'syncError' => false,
                'feedback'  => $shipment->errors()->first()
            ];
        }

        //dd($result);
        return Response::json($result);
    }

    /**
     * Open modal to confirm row delete
     * @param $id
     * @return mixed|null|string
     */
    public function confirmDestroy($id)
    {
        $shipment = Shipment::filterMyAgencies()->findOrNew($id);
        return view('admin.shipments.shipments.modals.confirm.destroy', compact('shipment'))->render();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $shipment = Shipment::filterMyAgencies()
            ->whereId($id)
            ->first();

        $result = false;

        if ($shipment) {

            if ($request->get('delete_provider', false)) {
                try {
                    $webservice = new Webservice\Base();
                    $result = $webservice->deleteShipment($shipment, null);
                } catch (\Exception $e) {
                    return Redirect::back()->with('error', $e->getMessage());
                }
            }

            //reset parent shipment
            if ($shipment->parent_tracking_code) {
                Shipment::where('tracking_code', $shipment->parent_tracking_code)
                    ->update([
                        'children_tracking_code' => null,
                        'children_type' => null
                    ]);
            }

            //repoe saldo em conta de cliente
            if ($shipment->ignore_billing && @$shipment->customer->payment_method == 'wallet') {
                $shipment->walletRefund($shipment->customer);
            }

            $isCollection = $shipment->is_collection;

            // Remove shipment from Operator Task
            $task = OperatorTask::filterSource()
                ->where('customer_id', $shipment->customer_id)
                ->where('shipments', 'LIKE', "%{$shipment->id}%")
                ->first();

            if ($task) {
                $task->removeShipment($shipment->id);
            }
            //--

            $result = $shipment->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o envio ou pedido de recolha.');
        }

        if ($isCollection) {
            return Redirect::route('admin.pickups.index')->with('success', 'Pedido de recolha removido com sucesso.');
        }

        return Redirect::route('admin.shipments.index')->with('success', 'Envio removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $shipments = Shipment::whereIn('id', $ids)->get();

        $result = true;
        $parentsTrk = [];
        foreach ($shipments as $shipment) {

            $error = false;
            if ($request->get('delete_provider', false) && $shipment->hasSync()) {
                try {
                    $webservice = new Webservice\Base();
                    $result = $webservice->deleteShipment($shipment, null);
                } catch (\Exception $e) {
                    $error = true;
                }
            }

            if ($shipment->parent_tracking_code) {
                $parentsTrk[] = $shipment->parent_tracking_code;
            }

            //repoe saldo em conta de cliente
            if ($shipment->ignore_billing && @$shipment->customer->payment_method == 'wallet') {
                $shipment->walletRefund($shipment->customer);
            }

            if (!$error) {
                // Remove shipment from Operator Task
                $task = OperatorTask::filterSource()
                    ->where('customer_id', $shipment->customer_id)
                    ->where('shipments', 'LIKE', "%{$shipment->id}%")
                    ->first();

                if ($task) {
                    $task->removeShipment($shipment->id);
                }
                //--

                $deleteResult = $shipment->delete();
                if (!$deleteResult) {
                    $result = false;
                }
            }
        }


        //reset parent shipment
        if ($parentsTrk) {
            Shipment::whereIn('tracking_code', $parentsTrk)
                ->update([
                    'children_tracking_code' => null,
                    'children_type' => null
                ]);
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        if (!empty($error)) {
            return Redirect::back()->with('warning', 'Registos selecionados removidos com sucesso. Alguns registos não puderam ser anulados porque não foi possível eliminar o envio no fornecedor.');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {

        $result = Shipment::filterMyAgencies()
            ->withTrashed()
            ->whereId($id)
            ->restore();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar restaurar o serviço.');
        }

        return Redirect::route('admin.shipments.index')->with('success', 'Serviço restaurado com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $appMode  = Setting::get('app_mode');
        $ref2Name = Setting::get('shipments_reference2_name');
        $ref3Name = Setting::get('shipments_reference3_name');
        $showIconFlag = Setting::get('shipments_show_country_flag');

        $canEditShipments = hasPermission('edit_shipments') && Auth::user()->showPrices();
        $canBilling       = hasPermission('billing,billing_shipments,invoices') && hasModule('invoices');

        $isCollection = 0;
        if ($request->get('pickup')) {
            $isCollection = 1;
        }

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $finalStatus = $statusList->filter(function ($item) {
            return $item->is_final;
        })->pluck('id')->toArray();
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();

        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $forceDisableFinalStatus = false;
        if (($request->has('status') && !empty(array_intersect($request->status, $finalStatus)))
            || ($request->has('type') && in_array($request->get('type'), ['pod_signature', 'pod_file']))
        ) {
            $forceDisableFinalStatus = true;
        }

        $bindings = [
            'id', 'tracking_code', 'type', 'parent_tracking_code', 'children_type', 'children_tracking_code',
            'provider_tracking_code', 'webservice_method', 'submited_at',
            'agency_id', 'service_id', 'provider_id', 'status_id', 'operator_id', 'sender_agency_id', 'recipient_agency_id',
            'customer_id', 'requested_by', 'pickup_operator_id', 'route_id',
            'sender_name', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_phone', 'sender_country',
            'recipient_name', 'recipient_address', 'recipient_zip_code', 'recipient_city', 'recipient_phone', 'recipient_country',
            'volumes', 'weight', 'volumetric_weight', 'customer_weight', 'volume_m3', 'fator_m3', 'charge_price', 'has_return',
            'date', 'shipping_date', 'delivery_date', 'tags',
            'cod', 'ignore_billing', 'price_fixed', 'is_closed', 'is_blocked', 'is_printed',
            'deleted_at', 'invoice_id', 'invoice_doc_id', 'invoice_type', 'invoice_draft', 'invoice_key', 'reference', 'reference2', 'reference3',
            'kms', 'hours', 'zone', 'start_hour', 'end_hour', 'vehicle', 'trailer',
            'obs', 'obs_internal', 'recipient_email', 'packaging_type', 'recipient_pudo_id', 'customer_conferred', 'without_pickup',
            'end_hour_pickup', 'start_hour_pickup', 'conferred_weight', 'conferred_volumes', 'at_guide_doc_id', 'at_guide_serie', 'at_guide_key', 'at_guide_codeat',
            'shipping_price', 'expenses_price', 'fuel_price', 'billing_subtotal', 'created_at',
            'total_price_for_recipient', 'payment_at_recipient', 'trip_code', 'trip_id', 'keywords'
        ];

        $data = Shipment::filterAgencies()
            /*->with(['service' => function($q){
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Service::CACHE_TAG);
                }])
                ->with(['status' => function($q){
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(ShippingStatus::CACHE_TAG);
                }])
                ->with(['operator' => function($q){
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(User::CACHE_TAG);
                    $q->select(['id', 'code', 'name', 'code_abbrv']);
                }])
                ->with(['provider' => function($q){
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Provider::CACHE_TAG);
                    $q->select(['id', 'name', 'color']);
                }])*/
            ->with(['route' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'code_abbrv']);
            }])
            ->with('attachments')
            ->where('is_collection', $isCollection)
            ->select($bindings);

        if (Setting::get('shipment_list_pin_pending')) {
            $data->selectRaw('IF(status_id = ' . ShippingStatus::PENDING_ID . ', 1, 0) AS "is_pending"');
            $data->orderBy('is_pending', 'desc');
        }

        //limit to sellers customers
        $value = $request->customer_type;
        if (Auth::user()->isSeller()) {
            $userId = Auth::user()->id;
            $data = $data->whereHas('customer', function ($q) use ($userId) {
                $q->where('seller_id', $userId);
            });
        }

        $data = $data->where(function ($q) {
            $q->whereNull('type');
            if (Setting::get('shipment_list_detail_master')) { //mostra o detalhe dos serviços agrupados
                $q->orWhere('type', '<>', 'T'); //esconde sempre os transbordos
            } else {
                $q->orWhereNotIn('type', ['T', 'M']);
            }
        });


        //search TRK
        $value = $request->trk;
        if ($request->has('trk') && !empty($value)) {
            $data = $data->where('tracking_code', 'like', '%' . $value);

            //limit search
            $value = $request->limit_search;
            if ($request->has('limit_search') && !empty($value)) {
                $minId = (int) CacheSetting::get('shipments_limit_search');
                if ($minId) {
                    $data = $data->where('id', '>=', $minId);
                }
            }
        } else {

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
            if ($request->has('hide_final_status') && !empty($value) && !$forceDisableFinalStatus) {
                if (in_array(config('app.source'), ['corridadotempo'])) {
                    $finalStatus[] = 9;
                }

                /*if($isCollection) {
                    $pos = array_search(ShippingStatus::PICKUP_FAILED_ID, $finalStatus);
                    unset($finalStatus[$pos]); //não oculta recolha falhada
                }*/

                $data = $data->whereNotIn('status_id', $finalStatus);
            }

            //show hidden
            $value = $request->hide_scheduled;
            if ($request->has('hide_scheduled') && !empty($value)) {
                $data = $data->where('date', '<=', date('Y-m-d'));
            }

            //filter service
            $value = $request->get('source');
            if (!empty($value) && Auth::user()->isAdmin()) {
                $sourceAgencies = $allAgencies->filter(function ($item) use ($value) {
                    return $item->source == $value;
                })->pluck('id')->toArray();
                $data = $data->whereIn('agency_id', $sourceAgencies);
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

            //filter customer type   
            $value = $request->customer_type;
            if ($request->has('customer_type')) {
                $data = $data->whereHas('customer', function ($q) use ($value) {
                    $q->where('type_id', $value);
                });
            }

            // Filter seller
            $value = $request->seller;
            if ($request->has('seller')) {
                $data = $data->whereHas('customer', function($q) use($value) {
                    $q->where('seller_id', $value);
                });
            }

            //filter delivery manifest
            $value = $request->trips;
            if (!empty($value)) {
                if ($value == '-1') {
                    $data = $data->whereNull('trip_id');
                } else {
                    $data = $data->where('trip_id', $value);
                }
            }

            //filter status
            $value = $request->get('status');
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

            //filter route
            $value = $request->route;
            if ($request->has('route')) {
                if ($value == '-1') {
                    $data = $data->whereNull('route_id');
                } else {
                    $data = $data->where('route_id', $value);
                }
            }

            //filter pickup route
            $value = $request->pickup_route;
            if ($request->has('pickup_route')) {

                $route = Route::find($value);
                $data = $data->whereIn('sender_zip_code', @$route->zip_codes);
                /* if($value == '-1') {
                    $data = $data->whereNull('pickup_route_id');
                } else {
                    $data = $data->where('pickup_route_id', $value);
                }*/
            }

            //filter agency
            $value = $request->agency;
            if (!empty($value)) {
                $data = $data->whereIn('agency_id', $value);
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

            //filter conferred
            $value = $request->get('customer_conferred');
            if ($request->has('customer_conferred')) {
                if ($value == 0) {
                    $data = $data->where(function ($q) {
                        $q->whereNull('customer_conferred');
                        $q->orWhere('customer_conferred', 0);
                    });
                } else {
                    $data = $data->where('customer_conferred', 1);
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
            if ($request->has('volumes_min')) {

                $volMax = $volMin;

                if ($request->has('volumes_max')) {
                    $volMax = $request->get('volumes_max');
                }

                $data = $data->whereBetween('volumes', [$volMin, $volMax]);
            }

            //filter weight min
            $weightMin = $request->get('weight_min');
            if ($request->has('weight_min')) {

                $weightMax = $weightMin;

                if ($request->has('weight_max')) {
                    $weightMax = $request->get('weight_max');
                }

                $data = $data->whereBetween('weight', [$weightMin, $weightMax]);
            }

            //filter operator
            $value = $request->operator;
            if ($request->has('operator')) {
                if (in_array('not-assigned', $value)) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->whereNull('operator_id')
                            ->orWhereIn('operator_id', $value);
                    });
                } else {
                    $data = $data->whereIn('operator_id', $value);
                }
            }

            //filter operator pickup
            $value = $request->operator_pickup;
            if ($request->has('operator_pickup')) {
                if (in_array('not-assigned', $value)) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->whereNull('pickup_operator_id')
                            ->orWhereIn('pickup_operator_id', $value);
                    });
                } else {
                    $data = $data->whereIn('pickup_operator_id', $value);
                }
            }

            //filter responsable
            $value = $request->dispatcher;
            if ($request->has('dispatcher')) {
                if (in_array('not-assigned', $value)) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->whereNull('dispatcher_id')
                            ->orWhereIn('dispatcher_id', $value);
                    });
                } else {
                    $data = $data->whereIn('dispatcher_id', $value);
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

            //filter cod
            $value = $request->get('cod');
            if ($value) {
                if ($value == 'C') {
                    $data = $data->whereNull('cod');
                } else {
                    $data = $data->where('cod', $value);
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

            //filter ignore billing
            $value = $request->ignore_billing;
            if ($request->has('ignore_billing')) {
                $data = $data->where('ignore_billing', $value);
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

            //filter expense type
            $value = $request->expense_type;
            if ($request->has('expense_type')) {
                $data = $data->where(function ($q) use ($value) {

                    if (in_array('rpack', $value)) {
                        $q->where('has_return', 'like', '%rpack%');
                    }

                    $q->orWhereHas('expenses', function ($q) use ($value) {
                        $q->whereIn('expense_id', $value);
                    });
                });
            }

            //show hidden
            $value = $request->deleted;
            if ($request->has('deleted') && !empty($value)) {
                $data = $data->withTrashed();
            }

            //filter type
            $value = $request->get('type');
            if ($request->has('type')) {
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
                } else if ($value == 'readed') {
                    $data = $data->has('traceability');
                } else if ($value == 'unreaded') {
                    $data = $data->has('traceability', '=', '0');
                } else if ($value == 'pudo') {
                    $data = $data->whereNotNull('recipient_pudo_id');
                } else if ($value == 'closed') {
                    $data = $data->where('is_closed', 0);
                } else if ($value == 'api') {
                    $data = $data->whereHas('firstHistory', function ($q) {
                        $q->where('api', 1);
                    });
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
                /**
                 * If workgroups have a new filter just add it to the following array
                 * 
                 * key: workgroups table column
                 * value: shipments table column
                 */
                $workgroupBindings = [
                    'services'              => 'service_id',
                    'sender_countries'      => 'sender_country',
                    'recipient_countries'   => 'recipient_country',
                    'status'                => 'status_id',
                    'pickup_routes'         => 'pickup_route_id',
                    'delivery_routes'       => 'route_id',
                ];
                /** -- */

                $workgroup = UserWorkgroup::remember(config('cache.query_ttl'))
                    ->cacheTags(UserWorkgroup::CACHE_TAG)
                    ->filterSource()
                    ->whereIn('id', $value)
                    ->get(['values'])
                    ->toArray();

                /**
                 * Magic filter
                 */
                $workgroupWhereData = [];
                foreach ($workgroup as $group) {
                    foreach (array_keys($workgroupBindings) as $binding) {
                        $value = $group['values'][$binding] ?? null;
                        if (empty($value)) {
                            continue;
                        }

                        $workgroupWhereData[$binding] = array_merge($workgroupWhereData[$binding] ?? [], $value);
                    }
                }

                foreach ($workgroupWhereData as $key => $dataArr) {
                    $data = $data->whereIn($workgroupBindings[$key], $dataArr);
                }
                /** -- */
            }

            //filter recipient district
            $district = $request->get('recipient_district');
            $county   = $request->get('recipient_county');
            if ($request->has('recipient_district') || $request->has('recipient_county')) {

                $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                    ->cacheTags(ShippingStatus::CACHE_TAG)
                    ->whereIn('district_code', $district)
                    ->where('country', 'pt');

                if ($county) {
                    $zipCodes = $zipCodes->whereIn('county_code', $county);
                }

                $zipCodes = $zipCodes->groupBy('zip_code')
                    ->pluck('zip_code')
                    ->toArray();

                $data = $data->where(function ($q) use ($zipCodes) {
                    $q->where('recipient_country', 'pt');
                    $q->whereIn(DB::raw('SUBSTRING(`recipient_zip_code`, 1, 4)'), $zipCodes);
                });
            }
        }

        $isAdmin = Auth::user()->isAdmin();

        $datatable = Datatables::of($data)
            ->edit_column('service_id', function ($row) use ($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('reference', function ($row) use ($ref2Name, $ref3Name) {
                return view('admin.shipments.shipments.datatables.reference', compact('row', 'ref2Name', 'ref3Name'))->render();
            })
            ->edit_column('sender_name', function ($row) use ($showIconFlag) {
                return view('admin.shipments.shipments.datatables.sender', compact('row', 'showIconFlag'))->render();
            })
            ->edit_column('recipient_name', function ($row) use ($showIconFlag) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row', 'showIconFlag'))->render();
            })
            ->edit_column('status_id', function ($row) use ($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            ->edit_column('volumes', function ($row) use ($servicesList, $packTypes, $appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'servicesList', 'packTypes', 'appMode'))->render();
            })
            ->edit_column('vehicle', function ($row) {
                return view('admin.shipments.shipments.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('shipping_date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.date', compact('row', 'statusList'))->render();
            })
            ->edit_column('delivery_date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.delivery_date', compact('row', 'statusList'))->render();
            })
            ->edit_column('total_price', function ($row) {
                return view('admin.shipments.shipments.datatables.price', compact('row'))->render();
            })
            ->edit_column('children_tracking_code', function ($row) {
                return view('admin.shipments.pickups.datatables.children_tracking_code', compact('row'))->render();
            })
            ->edit_column('kms', function ($row) {
                return view('admin.shipments.shipments.datatables.kms', compact('row'))->render();
            })
            ->edit_column('customer_conferred', function ($row) {
                return view('admin.shipments.shipments.datatables.conferred', compact('row'))->render();
            })
            ->edit_column('customer_id', function ($row) use ($servicesList) {
                return str_limit(@$row->customer->name, 40);
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            });

        if ($isCollection) {
            $datatable = $datatable->add_column('actions', function ($row) use ($isAdmin, $statusList, $canEditShipments, $canBilling) {
                return view('admin.shipments.pickups.datatables.actions', compact('row', 'isAdmin', 'statusList', 'canEditShipments', 'canBilling'))->render();
            });
        } else {
            $datatable = $datatable->add_column('actions', function ($row) use ($isAdmin, $statusList, $canEditShipments, $canBilling) {
                return view('admin.shipments.shipments.datatables.actions', compact('row', 'isAdmin', 'statusList', 'canEditShipments', 'canBilling'))->render();
            });
        }

        $datatable = $datatable->make(true);

        return $datatable;
    }



    /**
     * Expand row of datatable
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function datatableExpandRow($rowShipment)
    {

        $shipments = Shipment::where('id', $rowShipment->id)
            ->orWhere(function($q) use($rowShipment) {
                $q->where('type', 'M')
                  ->where('parent_tracking_code', $rowShipment->tracking_code);
            })
            ->orderBy('id')
            ->get();

        $isCollection = 0;
        $isAdmin  = Auth::user()->isAdmin();
        $appMode  = Setting::get('app_mode');
        $ref2Name = Setting::get('shipments_reference2_name');
        $ref3Name = Setting::get('shipments_reference3_name');
        $showIconFlag = Setting::get('shipments_show_country_flag');

        $canEditShipments = hasPermission('edit_shipments') && Auth::user()->showPrices();
        $canBilling       = hasPermission('billing,billing_shipments,invoices') && hasModule('invoices');


        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $finalStatus = $statusList->filter(function ($item) {
            return $item->is_final;
        })->pluck('id')->toArray();
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();

        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $data = compact(
            'shipments',
            'appMode',
            'isAdmin',
            'ref2Name',
            'refeName',
            'showIconFlag',
            'canEditShipments',
            'canBilling',
            'isCollection',
            'allAgencies',
            'providersList',
            'operatorsList',
            'servicesList',
            'statusList',
            'agencies',
            'packTypes'
        );

        return view('admin.shipments.shipments.datatables.expanded_row', $data)->render();
    }



    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request)
    {

        $showPrices = Auth::user()->perm('invoices');

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'agency_id',
            'vat',
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'email',
            'responsable',
            'obs_shipments',
            'distance_km',
            'distance_from_agency',
            'balance_total_unpaid',
            'unpaid_invoices_limit',
            'unpaid_invoices_credit',
            'wallet_balance',
            'payment_method'
        ];

        try {

            $results  = [];

            $customers = Customer::filterSource()
                ->filterAgencies()
                ->isActive()
                ->filterSeller()
                ->with('last_invoice')
                ->with('agency')
                ->with('paymentCondition')
                ->with(['departments' => function ($q) {
                    $q->select([
                        'name',
                        'id',
                        'vat',
                        'code',
                        'address',
                        'zip_code',
                        'city',
                        'country',
                        'phone',
                        'email',
                        'agency_id',
                        'obs',
                        'distance_km',
                        'distance_from_agency',
                        'customer_id',
                        'wallet_balance',
                        'payment_method'
                    ]);
                }])
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search);
                    /*->orWhere('phone', 'LIKE', $search);*/
                });

            if (Setting::get('shipment_alert_unpaid_invoices')) {
                $customers = $customers->with('unpaid_invoices');
            }

            if (Auth::user()->is_developer) {
                $customers = $customers->whereIn('id', [205]);
            }

            $customers = $customers->isProspect(false)
                ->isDepartment(false)
                ->get($fields);

            if ($customers) {
                $results  = array();
                $currency = Setting::get('app_currency');
                $alertPaymentMethods   = Setting::get('shipment_alert_payment_condition') ? Setting::get('shipment_alert_payment_condition') : [];
                $alertPaymentMethods[] = 'wallet';

                foreach ($customers as $customer) {

                    $blocked = 0;
                    $today   = Date::today();

                    $blockedReason = null;
                    if (@$customer->balance_total_unpaid > @$customer->unpaid_invoices_credit) {
                        $blocked = $customer->unpaid_invoices_credit;
                        $blockedReason = 'credit';
                    } elseif (@$customer->last_invoice->due_date && $customer->last_invoice->due_date->lte($today)) {

                        $date  = new Date($customer->last_invoice->due_date);
                        $customer->days_late = $date->diffInDays($today);

                        if (!empty($customer->unpaid_invoices_limit) && @$customer->days_late > $customer->unpaid_invoices_limit) {
                            $blocked = $customer->unpaid_invoices_limit;
                        } elseif ((empty($customer->unpaid_invoices_limit) && !empty(Setting::get('customers_unpaid_invoices_limit'))) && @$customer->days_late > Setting::get('customers_unpaid_invoices_limit')) {
                            $blocked = Setting::get('customers_unpaid_invoices_limit');
                        }
                        $blockedReason = 'invoices';
                    }

                    $departments = null;

                    if (!$customer->departments->isEmpty()) {

                        $departments[] = [
                            'id'   => '',
                            'text' => ''
                        ];

                        foreach ($customer->departments as $department) {
                            $departments[] = [
                                'id'           => $department->id,
                                'text'         => str_limit($department->name, 40),
                                'code'         => $department->code,
                                'name'         => $department->name,
                                'address'      => $department->address,
                                'zip_code'     => $department->zip_code,
                                'city'         => $department->city,
                                'country'      => $department->country,
                                'phone'        => $department->mobile ? $department->mobile : $department->phone,
                                'email'        => $department->contact_email,
                                'agency'       => $department->agency_id,
                                'obs'          => $department->obs_shipments,
                                'kms'          => $department->distance_km,
                                'wallet'       => @$department->wallet_balance,
                                'payment'      => in_array($department->payment_method, $alertPaymentMethods) ? @$department->paymentCondition->name : null
                            ];
                        }
                    }

                    $unpaidInvoicesHtml = '';
                    if (Setting::get('shipment_alert_unpaid_invoices') && !$customer->unpaid_invoices->isEmpty()) {
                        $unpaidInvoicesHtml .= '<table class="table table-condensed m-0">';
                        $unpaidInvoicesHtml .= '<tr>';
                        $unpaidInvoicesHtml .= '<th>Documento</th>';
                        if ($showPrices) {
                            $unpaidInvoicesHtml .= '<th class="w-85px">Valor</th>';
                        }
                        $unpaidInvoicesHtml .= '<th class="w-90px">Limite</th>';
                        $unpaidInvoicesHtml .= '</tr>';

                        foreach ($customer->unpaid_invoices as $unpaidInvoice) {
                            $unpaidInvoicesHtml .= '<tr>';
                            $unpaidInvoicesHtml .= '<td class="text-left">' . $unpaidInvoice->doc_series . ' ' . $unpaidInvoice->doc_series_id . '/' . $unpaidInvoice->doc_id . '</td>';

                            if ($showPrices) {
                                $unpaidInvoicesHtml .= '<td>' . money($unpaidInvoice->doc_total, $currency) . '</td>';
                            }
                            $unpaidInvoicesHtml .= '<td>' . $unpaidInvoice->due_date . '</td>';
                            $unpaidInvoicesHtml .= '<tr>';
                        }

                        $unpaidInvoicesHtml .= '</table>';
                    }

                    $results[] = [
                        'id'             => $customer->id,
                        'text'           => $customer->code . ' - ' . str_limit($customer->name, 40),
                        'code'           => $customer->code,
                        'vat'            => $customer->vat,
                        'name'           => $customer->name,
                        'address'        => $customer->address,
                        'zip_code'       => $customer->zip_code,
                        'city'           => $customer->city,
                        'country'        => $customer->country,
                        'phone'          => $customer->mobile ? $customer->mobile : $customer->phone,
                        'email'          => $customer->contact_email,
                        'agency'         => $customer->agency_id,
                        'obs'            => $customer->obs_shipments,
                        'blocked'        => $blocked,
                        'blocked_reason' => $blockedReason,
                        'kms'            => $customer->distance_km,
                        'km_from_agency' => $customer->distance_from_agency,
                        'origin_zp'      => $customer->agency->zip_code,
                        'origin_city'    => $customer->agency->city,
                        'wallet'         => $customer->wallet_balance,
                        'payment'        => in_array($customer->payment_method, $alertPaymentMethods) ? @$customer->paymentCondition->name : null,
                        'unpaid_invoices' => $unpaidInvoicesHtml,
                        'departments'    => $departments
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum cliente encontrado.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage() . ' Line ' . $e->getLine()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search providers on DB
     *
     * @return type
     */
    public function searchProvider(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'name',
            'company',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'email',
        ];

        try {
            $results = [];

            $providers = Provider::filterSource()
                ->filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('company', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->get($fields);

            if ($providers) {
                $results = array();
                foreach ($providers as $provider) {
                    $results[] = [
                        'id'           => $provider->id,
                        'text'         => $provider->code . ' - ' . str_limit($provider->name, 40),
                        'code'         => $provider->code,
                        'name'         => $provider->company,
                        'address'      => $provider->address,
                        'zip_code'     => $provider->zip_code,
                        'city'         => $provider->city,
                        'country'      => $provider->country,
                        'phone'        => $provider->mobile ? $provider->mobile : $provider->phone,
                        'email'        => $provider->email,
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum fornecedor encontrado.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function searchSender(Request $request)
    {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'agency_id',
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'email',
            'responsable',
            'obs_shipments'
        ];

        try {

            $customers = Customer::filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->take(10)
                ->get($fields);

            if ($customers) {

                $results = array();
                foreach ($customers as $customer) {
                    $results[] = [
                        'data'          => $customer->id,
                        'value'         => strtoupper(trim($customer->name)),
                        'code'          => strtoupper(trim($customer->code)),
                        'name'          => strtoupper(trim($customer->name)),
                        'address'       => strtoupper(trim($customer->address)),
                        'zip_code'      => strtoupper(trim($customer->zip_code)),
                        'city'          => strtoupper(trim($customer->city)),
                        'country'       => $customer->country,
                        'phone'         => $customer->mobile ? $customer->mobile : $customer->phone,
                        'email'         => strtolower(trim($customer->contact_email)),
                        'agency'        => $customer->agency_id,
                        'obs'           => $customer->obs_shipments,
                        'responsable'   => strtoupper(trim($customer->responsable)),
                    ];
                }
            } else {
                $results = ['Nenhum cliente encontrado.'];
            }
        } catch (\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Search recipient
     *
     * @return type
     */
    public function searchRecipient(Request $request)
    {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $userAgencies = Auth::user()->agencies;

        $agenciesZipCode = AgencyZipCode::remember(AgencyZipCode::CACHE_TAG)
            ->cacheTags(AgencyZipCode::CACHE_TAG)
            ->filterSource()
            ->pluck('agency_id', 'zip_code')
            ->toArray();

        $fields = [
            'customers_recipients.id',
            'customers_recipients.vat',
            'customers_recipients.code',
            'customers_recipients.name',
            'customers_recipients.address',
            'customers_recipients.zip_code',
            'customers_recipients.city',
            'customers_recipients.country',
            'customers_recipients.phone',
            'customers_recipients.email',
            'customers_recipients.obs',
            //DB::raw('COUNT(*) as `count`')
        ];

        try {

            $customers = CustomerRecipient::leftjoin('customers', 'customers_recipients.customer_id', '=', 'customers.id')
                ->where('customers.source', config('app.source'))
                ->where(function ($q) use ($search) {
                    $q->where('customers_recipients.code', 'LIKE', $search)
                        ->orWhere('customers_recipients.name', 'LIKE', $search)
                        ->orWhere('customers_recipients.zip_code', 'LIKE', $search)
                        ->orWhere('customers_recipients.city', 'LIKE', $search);
                })
                ->take(30)
                ->select($fields)
                ->get();

            if ($customers) {

                $results = array();
                foreach ($customers as $customer) {
                    $agency = null;
                    $zipCode = trim($customer->zip_code);
                    if ($customer->country == 'pt') {
                        if (count($userAgencies) == 1) {
                            $agency = $userAgencies[0];
                        } else {
                            $zip4Code = substr($zipCode, 0, 4);
                            $agency = @$agenciesZipCode[$zip4Code];
                        }
                    }

                    $results[] = [
                        'data'     => $customer->id,
                        'value'    => strtoupper(trim($customer->name)),
                        'code'     => strtoupper(trim($customer->code)),
                        'vat'      => trim($customer->vat),
                        'name'     => strtoupper(trim($customer->name)),
                        'address'  => strtoupper(trim($customer->address)),
                        'zip_code' => $zipCode,
                        'city'     => strtoupper(trim($customer->city)),
                        'country'  => $customer->country,
                        'phone'    => trim($customer->phone),
                        'email'    => strtolower(trim($customer->email)),
                        'obs'      => $customer->obs,
                        'agency'   => $agency
                    ];
                }
            } else {
                $results = ['Nenhum remetente encontrado.'];
            }
        } catch (\Exception $e) {
            $results = ['Erro interno. ' . $e->getMessage()];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function searchSku(Request $request)
    {

        $customerId = $request->customer;
        $index      = $request->index;
        $search     = trim($request->get('query'));
        $search     = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'name',
            'sku',
            'serial_no',
            'lote',
            'stock_total',
            'stock_allocated',
            'stock_status',
            'width',
            'length',
            'height',
            'weight',
            'unity_type',
            'packs_by_box',
            'price',
            'warehouse_id'
        ];

        try {
            $products = Product::with('warehouse')
                ->filterSource();

            if ($customerId) {
                $products = $products->where('customer_id', $customerId);

                // $customer = Customer::find($customerId);
                // if (@$customer->settings['logistic_stock_only_available'] || @$customer->parent_customer->settings['logistic_stock_only_available']) {
                //     $products = $products->where('stock_total', '>', '0');
                // }
            }

            $products = $products->where(function ($q) use ($search) {
                $q->where('sku', 'LIKE', $search)
                    ->orWhere('name', 'LIKE', $search)
                    ->orWhere('serial_no', 'LIKE', $search)
                    ->orWhere('lote', 'LIKE', $search);
            })
                ->where('is_active', 1)
                ->take(10)
                ->get($fields);

            if ($products) {

                $results = array();
                foreach ($products as $row) {
                    $results[] = [
                        'index'         => $index,
                        'data'          => $row->id,
                        'value'         => strtoupper(trim($row->name)),
                        'product'       => $row->id,
                        'sku'           => trim($row->sku),
                        'serial_no'     => trim($row->serial_no),
                        'lote'          => trim($row->lote),
                        'stock_total'   => $row->stock_total - $row->stock_allocated,
                        'stock_status'  => $row->stock_status,
                        'width'         => $row->width,
                        'height'        => $row->height,
                        'length'        => $row->length,
                        'height'        => $row->height,
                        'weight'        => $row->weight,
                        'box_type'      => $row->unity_type ? $row->unity_type : 'box',
                        'packs_by_box'  => $row->packs_by_box,
                        'price'         => $row->price,
                        'warehouse'     => $row->warehouse_id ? '[' . @$row->warehouse->code . '] ' . @$row->warehouse->name : '',
                    ];
                }
            } else {
                $results = ['Nenhum produto encontrado.'];
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Get stock total by product id
     *
     * @return type
     */
    public function getSkuStocks(Request $request)
    {

        $productIds = explode(',', $request->ids);

        $products = Product::filterSource()
            ->whereIn('id', $productIds)
            ->select(['id', DB::raw('(stock_total-stock_allocated) as stock_total')])
            ->pluck('stock_total', 'id')
            ->toArray();

        return Response::json($products);
    }

    /**
     * Return customer details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCustomer(Request $request)
    {

        $bindings = [
            'id',
            'name',
            'agency_id',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'obs_shipments as obs',
            'unpaid_invoices_limit',
        ];

        $customer = Customer::filterAgencies()
            ->select($bindings)
            ->findOrFail($request->id);

        $customer->phone = empty($customer->mobile) ? $customer->phone : $customer->mobile;
        $customer->phone = str_replace(' ', '', $customer->phone);

        $departments = Customer::whereCustomerId($customer->id)
            ->isDepartment()
            ->orderBy('code', 'asc')
            ->get();

        if ($departments->isEmpty()) {
            $customer->departments = null;
        } else {
            $arr[] = ['id' => '', 'text' => ''];
            foreach ($departments as $department) {
                $arr[] = ['id' => $department->id, 'text' => $department->code . ' - ' . $department->name];
            }

            $customer->departments = $arr;
        }

        if (Setting::get('shipments_sender_fields_empty')) {
            $customer->name     = '';
            $customer->address  = '';
            $customer->zip_code = '';
            $customer->city     = '';
            $customer->phone    = '';
            $customer->vat      = '';
        }

        return Response::json($customer);
    }

    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRecipient(Request $request)
    {

        $bindings = [
            'id',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'email',
            'responsable',
            'obs'
        ];

        $recipient = CustomerRecipient::select($bindings)
            ->findOrFail($request->id);

        $recipient->phone = str_replace(' ', '', ($recipient->mobile ? $recipient->mobile : $recipient->phone));
        $agency = Shipment::getAgencyByZipCode($recipient->zip_code);
        $recipient->agency_id = $agency->agency_id;
        $recipient->country   = $agency->zone;

        return Response::json($recipient);
    }

    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCounties(Request $request)
    {
        $recipientDistricts = $request->get('district');

        if ($request->has('district')) {
            $countiesArr = [
                [
                    'id'   => '',
                    'text' => 'Todos'
                ]
            ];
            foreach ($recipientDistricts as $districtCode) {
                $districtCounties = trans('districts_codes.counties.pt.' . $districtCode);
                foreach ($districtCounties as $countyCode => $countyName) {
                    $countiesArr[] = [
                        'id'   => $countyCode,
                        'text' => $countyName
                    ];
                }
            }
        }

        return Response::json($countiesArr);
    }

    /**
     * Return agency details for country & zipcode
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAgency(Request $request)
    {
        $input = $request->all();

        $shipment = new Shipment();
        $shipment->fill($input);

        $agency = Shipment::getAgency(
            $request->zip_code,
            $request->country,
            $shipment
        );

        return Response::json($agency);
    }

    /**
     * Return list of pickup and dropover points
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPudos(Request $request)
    {
        $pudos = PickupPoint::filterSource()
            ->where('provider_id', $request->providerId)
            ->isActive()
            ->get();

        if (!$pudos->isEmpty()) {
            $html = '<option value=""></option>';
            foreach ($pudos as $pudo) {
                $html .= '<option value="' . $pudo->id . '" 
                            data-address="' . $pudo->address . '"
                            data-zip-code="' . $pudo->zip_code . '"
                            data-city="' . $pudo->city . '">
                            ' . trim($pudo->name) . ' <i>(' . $pudo->zip_code . ' ' . $pudo->city . ')</i>
                            </option>';
            }
        } else {
            $html = [];
        }

        return Response::json($html);
    }

    /**
     * Return shipment prices
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPrice(Request $request)
    {
        //junta aos totais as taxas manuais
        $expensesIds          = $request->get('expense_id') ? array_filter($request->get('expense_id')) : [];
        $expensesAuto         = $request->get('expense_auto');
        $expensesQty          = $request->get('expense_qty');
        $expensesPrice        = $request->get('expense_price');
        $expensesSubtotal     = $request->get('expense_subtotal');
        $expensesVat          = $request->get('expense_vat');
        $expensesUnity        = $request->get('expense_unity');
        $expensesTotal        = $request->get('expense_total');
        $expensesVatRate      = $request->get('expense_vat_rate');
        $expensesVatRateId    = $request->get('expense_vat_rate_id');
        $expensesCostPrice    = $request->get('expense_cost_price');
        $expensesCostSubtotal = $request->get('expense_cost_subtotal');
        $expensesCostVat      = $request->get('expense_cost_vat');
        $expensesCostTotal    = $request->get('expense_cost_total');

        $subtotal = $vat = $total = 0;
        $costSubtotal = $costVat = $costTotal = 0;
        $manualExpenses = [];
        if (!empty($expensesIds)) {
            foreach ($expensesIds as $key => $expenseId) {

                if (!@$expensesAuto[$key]) {

                    $manualExpenses[] = [
                        'expense_id'      => $expenseId,
                        'qty'             => $expensesQty[$key],
                        'price'           => $expensesPrice[$key],
                        'subtotal'        => $expensesSubtotal[$key],
                        'vat'             => $expensesVat[$key],
                        'total'           => $expensesTotal[$key],
                        'vat_rate'        => $expensesVatRate[$key],
                        'vat_rate_id'     => @$expensesVatRateId[$key],
                        'unity'           => $expensesUnity[$key],
                        'cost_price'      => $expensesCostPrice[$key],
                        'cost_subtotal'   => $expensesCostSubtotal[$key],
                        'cost_vat'        => $expensesCostVat[$key],
                        'cost_total'      => $expensesCostTotal[$key]
                    ];

                    $subtotal     += (float) @$expensesSubtotal[$key];
                    $vat          += (float) @$expensesVat[$key];
                    $total        += (float) @$expensesTotal[$key];
                    $costSubtotal += (float) @$expensesCostSubtotal[$key];
                    $costVat      += (float) @$expensesCostVat[$key];
                    $costTotal    += (float) @$expensesCostTotal[$key];
                }
            }
        }

        $packDimensions = [];

        foreach (($request->box_type ?? []) as $key => $boxType) {
            $packDimensions[] = [
                'type'     => $boxType,
                'qty'      => @$request->qty[$key],
                'length'   => @$request->length[$key],
                'width'    => @$request->width[$key],
                'height'   => @$request->height[$key],
                'weight'   => @$request->box_weight[$key],
                'fator_m3' => @$request->fator_m3_row[$key],
                'sku'      => @$request->sku[$key],
                'product'  => @$request->product[$key],
                'assembly' => @$request->box_optional_fields[$key]['Montagem']
            ];
        }

        $shipment = new Shipment();
        $shipment->fill($request->all());
        $shipment->pack_dimensions = $packDimensions;
        $prices = Shipment::calcPrices($shipment, true, $manualExpenses);


        if (in_array($request->get('trigger_field'), ['recipient_zip_code', 'recipient_country', 'service_id'])) {
            $prices['agency'] = Shipment::getAgency(
                $request->get('recipient_zip_code'),
                $request->get('recipient_country'),
                $shipment
            );
        } elseif (in_array($request->get('trigger_field'), ['sender_zip_code', 'sender_country'])) {
            $prices['agency'] = Shipment::getAgency(
                $request->get('sender_zip_code'),
                $request->get('sender_country'),
                $shipment
            );
        }

        return Response::json($prices);
    }

    /**
     * Return shipment prices
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPricesCompare(Request $request)
    {

        $providers = Provider::filterSource()
            ->isCarrier()
            ->ordered()
            ->get(['id', 'name']);

        $providersPrices = [];
        $emptyPrices = [];
        $bestProvider = null;
        foreach ($providers as $provider) {

            $request->zone = empty($request->zone) ? Setting::get('app_country') : $request->zone;

            $tmpShipment = new Shipment();
            $tmpShipment->fill($request->all());
            $tmpShipment->provider_id = $provider->id;

            $price = Shipment::calcPrices($tmpShipment, false);

            $arr = [
                'id'      => $provider->id,
                'name'    => $provider->name,
                'cost'    => @$price['costs']['subtotal'],
                'total'   => @$price['billing']['subtotal'],
                'balance' => @$price['balance']['value'],
            ];

            if (@$price['costs']['subtotal'] > 0.00) {
                $providersPrices[] = $arr;
            } else {
                $emptyPrices[] = $arr;
            }
        }

        aasort($providersPrices, 'cost');
        $providersPrices = array_merge($providersPrices, $emptyPrices);
        $providersPrices = array_slice($providersPrices, 0, 10);

        $data = [
            'result' => true,
            'html'   => view('admin.shipments.shipments.partials.compare_prices', compact('providersPrices'))->render()
        ];

        return Response::json($data);
    }

    /**
     * Create Global Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function createGlobalTransportGuide()
    {
        $vehicles   = Vehicle::listVehicles();
        $hours      = listHours(15, 1, 5, 0, 24);
        $lastHour   = lastHour();
        return view('admin.shipments.shipments.modals.generic_labels', compact('vehicles', 'hours', 'lastHour'))->render();
    }

    /**
     * Return list of services with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listServices($allServices)
    {

        if ($allServices->count() > 1) {
            $services[] = ['value' => '', 'display' => ''];
        } else {
            $services = [];
        }

        foreach ($allServices as $service) {

            if ($service->allow_kms) {
                $service->unity = 'km';
            }

            $services[] = [
                'value'                 => $service->id,
                'display'               => $service->name, //$service->display_code . ' - ' . $service->name,
                'data-unity'            => $service->unity,
                'data-collection'       => $service->is_collection,
                'data-import'           => $service->is_import,
                'data-return'           => $service->is_return,
                'data-courier'          => $service->is_courier ? 1 : 0,
                'data-max'              => $service->max_volumes ? $service->max_volumes : 9999999,
                'data-max-weight'       => $service->max_weight ? $service->max_weight : 9999999,
                'data-assigned-service' => $service->assigned_service_id,
                'data-dim-required'     => $service->dimensions_required,
                'data-transit'          => $service->transit_time ? $service->transit_time : '24.00',
                'data-delivery-hour'    => $service->delivery_hour ? $service->delivery_hour : '',
                'data-transport-type'   => $service->transport_type_id
            ];
        }
        return $services;
    }

    /**
     * Return list of providers with data attributes
     *
     * @param type $allProviders
     * @return type
     */
    public function listProviders($allProviders)
    {

        if ($allProviders->count() > 1) {
            $providers[] = ['value' => '', 'display' => ''];
        } else {
            $providers = [];
        }

        foreach ($allProviders as $provider) {

            $providers[] = [
                'value'             => $provider->id,
                'display'           => $provider->name,
                'data-autodetect'   => $provider->autodetect_agencies,
                'method'            => $provider->webservice_method,
            ];
        }
        return $providers;
    }

    /**
     * Return list of providers with data attributes
     *
     * @param type $allProviders
     * @return type
     */
    public function listOperators($allOperators, $isOperator = true)
    {

        if ($isOperator) {
            $allOperators = $allOperators->filter(function ($item) {
                return $item->source = config('app.source') && ($item->is_operator == 1 || $item->login_app == 1);
            });
        } else {
            $allOperators = $allOperators->filter(function ($item) {
                return $item->source = config('app.source') && ($item->is_operator == 0 || $item->login_admin == 1);
            });
        }

        $operators[] = ['value' => '', 'display' => ''];
        foreach ($allOperators as $operator) {

            $operators[] = [
                'value'          => $operator->id,
                'display'        => $operator->name,
                'data-vehicle'   => $operator->vehicle,
                'data-provider'  => $operator->provider_id,
            ];
        }

        return $operators;
    }

    /**
     * Return list of states by given country
     *
     * @param type $country
     * @return type
     */
    public function listStates($country)
    {
        return Shipment::listStates($country);
    }

    /**
     * Return list of services with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listPudoPoints($allPudoPoints)
    {

        if ($allPudoPoints->count() > 1) {
            $pudos[] = ['value' => '', 'display' => ''];
        } else {
            $pudos = [];
        }

        foreach ($allPudoPoints as $pudoPoint) {
            $pudos[] = [
                'value'         => $pudoPoint->id,
                'display'       => $pudoPoint->name,
                'data-address'  => $pudoPoint->address,
                'data-zip-code' => $pudoPoint->zip_code,
                'data-city'     => $pudoPoint->city,
                'data-country'  => $pudoPoint->country,
            ];
        }
        return $pudos;
    }

    /**
     * Return list of all packtypes with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listPackTypes($allPackTypes)
    {

        if ($allPackTypes->count() > 1) {
            $packTypes[] = ['value' => '', 'display' => ''];
        } else {
            $packTypes = [];
        }

        foreach ($allPackTypes as $packType) {
            $packTypes[] = [
                'value'        => $packType->code,
                'display'      => $packType->name,
                'data-id'      => $packType->id,
                'data-code'    => $packType->code,
                'data-service' => $packType->assigned_service_id,
                'data-width'   => $packType->width,
                'data-length'  => $packType->length,
                'data-height'  => $packType->height,
                'data-weight'  => $packType->weight,
                'data-description' => $packType->description,
            ];
        }

        return $packTypes;
    }

    /**
     * Return list of expenses with ata attributes
     *
     * @param type $allExpenses
     * @return type
     */
    public function listExpenses($allExpenses)
    {

        $expenses[] = ['value' => '', 'display' => ''];
        foreach ($allExpenses as $expense) {

            $arr = [
                'value'         => $expense->id,
                'display'       => $expense->name,
                'data-unity'    => $expense->unity,
                'data-trigger'  => $expense->trigger_qty,
                'data-tax-rate' => $expense->vat_rate,
                'data-type'     => $expense->type,
                'data-form-shipment'   => $expense->form_type_shipments,
                'data-form-pickups'    => $expense->form_type_pickups,
                'data-complementar-pickup' => $expense->collection_complementar_service,
                'data-complementar' => $expense->complementar_service
            ];

            foreach ($expense->price as $locale => $price) {
                $arr['data-' . $locale] = $price;
            }

            $expenses[] = $arr;
        }

        return $expenses;
    }

    /**
     * Assign status to all selected resources from storage.
     * GET /admin/shipments/selected/update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request, $targetField)
    {

        $ids = explode(',', $request->ids);
        $calcPrices = $request->get('calc_prices', false);

        try {
            $this->{'massUpdate' . ucwords($targetField)}($request);

            if ($calcPrices) {
                foreach ($ids as $id) {
                    $shipment = Shipment::find($id);
                    $shipment->updatePrices();
                }
            }

            return Redirect::back()->with('success', 'Estado dos envios alterado com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao alterar dados: ' . $e->getMessage());
        }
    }

    /**
     * Update service id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massUpdateVehicle(Request $request)
    {

        $ids              = explode(',', $request->ids);
        $autoKm           = $request->get('auto_km', false);
        $kms              = $request->assign_kms;
        $operatorId       = $request->assign_operator_id;
        $vehicle          = $request->assign_vehicle;
        $trailer          = $request->assign_trailer;
        $volumes          = $request->assign_volumes;
        $weight           = $request->assign_weight;
        $costPrice        = $request->assign_cost_price;
        $totalPrice       = $request->assign_total_price;
        $priceFixed       = $request->assign_price_fixed;
        $ignoreBilling    = $request->assign_ignore_billing;
        $date             = $request->assign_date;
        $deliveryDate     = $request->assign_delivery_date;
        $startHour        = $request->assign_start_hour;
        $endHour          = $request->assign_end_hour;
        $trailer          = $request->assign_trailer;
        $senderCountry    = $request->assign_sender_country;
        $recipientCountry = $request->assign_recipient_country;


        $data = [];

        if (!empty($vehicle)) {
            $data['vehicle'] = $vehicle;
            if ($vehicle == '-1') {
                $data['vehicle'] = null;
            }
        }

        if (!empty($trailer)) {
            $data['trailer'] = $trailer;
            if ($trailer == '-1') {
                $data['trailer'] = null;
            }
        }

        if (!empty($senderCountry)) {
            $data['sender_country'] = $senderCountry;
        }

        if (!empty($recipientCountry)) {
            $data['recipient_country'] = $recipientCountry;
        }

        if (!empty($recipientCountry) || !empty($senderCountry)) {
            $data['zone'] = Shipment::getBillingZone($senderCountry, $recipientCountry);
        }

        if ($priceFixed != "") {
            $data['price_fixed'] = $priceFixed;
        }

        if ($ignoreBilling != "") {
            $data['ignore_billing'] = $ignoreBilling;
        }

        if (!empty($operatorId)) {
            $data['operator_id'] = $operatorId;
        }

        if (!empty($kms)) {
            $data['kms'] = $kms;
        }

        if ($autoKm) {

            $shipments = Shipment::whereIn('id', $ids)->get([
                'id', 'sender_zip_code', 'sender_city', 'sender_country',
                'recipient_zip_code', 'recipient_city', 'recipient_country',
                'agency_id'
            ]);

            foreach ($shipments as $shipment) {

                $matrixData = [
                    'origin'                => $shipment->sender_zip_code . ' ' . $shipment->sender_city . $shipment->sender_country,
                    'destination'           => $shipment->recipient_zip_code . ' ' . $shipment->recipient_city . $shipment->recipient_country,
                    'agency'                => $shipment->agency_id,
                    'origin_zp'             => $shipment->sender_zip_code,
                    'destination_zp'        => $shipment->recipient_zip_code,
                    'agency_zp'             => $shipment->sender_zip_code,
                    'origin_city'           => $shipment->sender_city,
                    'destination_city'      => $shipment->recipient_city,
                    'agency_city'           => $shipment->recipient_city,
                    'origin_country'        => $shipment->sender_country,
                    'destination_country'   => $shipment->recipient_country,
                    'agency_country'        => 'pt',
                    'triangulation'         => 0,
                ];

                $queryString = http_build_query($matrixData);
                $url = env('APP_CORE') . '/helper/maps/distance?' . $queryString;
                $result = json_decode(file_get_contents($url), true);

                $shipment->update(['kms' => @$result['distance_value']]);
            }
        }

        if (!empty($volumes)) {
            $data['volumes'] = $volumes;
        }

        if (!empty($weight)) {
            $data['weight'] = $weight;
        }

        if (!empty($costPrice)) {
            $data['cost_price'] = $costPrice;
        }

        if (!empty($totalPrice)) {
            $data['total_price'] = $totalPrice;
        }

        if (!empty($date)) {
            $data['date'] = $date;
            $data['billing_date'] = $date;

            $shippingDate = new Date($date);

            if (empty($startHour)) {
                $startHour = $shippingDate->format('H:i');
            }

            $data['shipping_date'] = $date . ' ' . $startHour . ':00';
        }

        if (!empty($deliveryDate)) {

            $date = new Date($deliveryDate);

            if (empty($endHour)) {
                $endHour = $date->format('H:i');
            }


            $data['delivery_date'] = $deliveryDate . ' ' . $endHour . ':00';
        }

        return Shipment::whereIn('id', $ids)->update($data);
    }

    /**
     * Update service id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massUpdateService(Request $request)
    {

        $ids = explode(',', $request->ids);
        $serviceId = $request->assign_service_id;

        $data = [
            'service_id' => $serviceId
        ];

        return Shipment::whereIn('id', $ids)->update($data);
    }

    /**
     * Update provider id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massUpdateProvider(Request $request)
    {

        $autoSubmit = $request->get('auto_submit', false);
        $ids = explode(',', $request->ids);
        $providerId = $request->assign_provider_id;

        $data = [
            'provider_id' => $providerId,
            'is_printed'  => 0
        ];

        if ($autoSubmit) {

            $shipments = Shipment::whereIn('id', $ids)->get();

            //1. Remove antigo webservice
            foreach ($shipments as $shipment) {
                if ($shipment->provider_id != $providerId && $shipment->webservice_method && $shipment->submited_at) {
                    try {
                        $webservice = new Webservice\Base();
                        $webservice->deleteShipment($shipment);
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
            }

            //2. Update provider
            $data['submited_at']               = null;
            $data['webservice_method']         = null;
            $data['provider_tracking_code']    = null;
            $data['provider_cargo_agency']     = null;
            $data['provider_recipient_agency'] = null;
            $data['provider_sender_agency']    = null;
            Shipment::whereIn('id', $ids)->update($data);

            //3. mass submit
            foreach ($shipments as $shipment) {
                try {
                    $shipment->provider_id = $providerId;
                    $shipment->provider_tracking_code = null;
                    $shipment->provider_cargo_agency = null;
                    $shipment->provider_recipient_agency = null;
                    $shipment->provider_sender_agency = null;
                    $webservice = new Webservice\Base();
                    $providerTrk = $webservice->submitShipment($shipment);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    throw new \Exception($e->getMessage());
                }


                //4. submit/update logistic shipping order
                if (hasModule('logistic') || config('app.source') == 'activos24') {
                    if ($providerTrk) {
                        $shipment->provider_tracking_code = $providerTrk;
                    }
                    $result = $shipment->storeShippingOrder();
                }
            }

            $result = true;
        } else {
            return Shipment::whereIn('id', $ids)->update($data);
        }


        return $result;
    }

    /**
     * Update customer id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massUpdateCustomer(Request $request)
    {

        $ids = explode(',', $request->ids);
        $customerId = $request->assign_customer_id;
        $departmentId = $request->assign_department_id;
        $departmentId = empty($departmentId) ? null : $departmentId;
        $updateSender = $request->get('update_name', false);

        $data = [
            'customer_id'   => $customerId,
            'department_id' => $departmentId
        ];

        if ($updateSender == 'sender') {
            $customer = Customer::find($customerId);
            $data['sender_name']    = $customer->name;
            $data['sender_address'] = $customer->address;
            $data['sender_zip_code'] = $customer->zip_code;
            $data['sender_city']    = $customer->city;
            $data['sender_country'] = $customer->country;
            $data['sender_phone']   = $customer->phone ? $customer->phone : $customer->mobile;
            $data['sender_vat']     = $customer->vat;
        } elseif ($updateSender == 'recipient') {
            $customer = Customer::find($customerId);
            $data['recipient_name']    = $customer->name;
            $data['recipient_address'] = $customer->address;
            $data['recipient_zip_code'] = $customer->zip_code;
            $data['recipient_city']    = $customer->city;
            $data['recipient_country'] = $customer->country;
            $data['recipient_phone']   = $customer->phone ? $customer->phone : $customer->mobile;
            $data['recipient_vat']     = $customer->vat;
        }

        return Shipment::whereIn('id', $ids)->update($data);
    }

    /**
     * Update provider id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massEditGrouped(Request $request)
    {

        $ids = $request->id;

        $shipments = Shipment::whereIn('id', $ids)
            ->get([
                'id',
                'type',
                'parent_tracking_code',
                'children_type',
                'children_tracking_code',
                'tracking_code',
                'shipping_price',
                'cost_shipping_price'
            ]);

        //find if in collection has more than 1 master trk
        $shipmentsMaster = $shipments->filter(function ($item) {
            return $item->children_type == 'M';
        })->pluck('tracking_code')->toArray();



        if (empty($shipmentsMaster)) {
            $shipmentsMaster = $shipments->filter(function ($item) {
                return $item->type == 'M';
            })->pluck('parent_tracking_code')->toArray();
        }

        $shipmentsMaster = array_unique($shipmentsMaster);

        $shipmentsTrk = $shipments->filter(function ($item) use ($shipmentsMaster) {
            return !in_array($item->tracking_code, $shipmentsMaster) && !in_array($item->parent_tracking_code, $shipmentsMaster);
        })
            ->pluck('tracking_code', 'tracking_code')
            ->toArray();

        $ids = implode(',', $ids);

        $price = $cost = '';
        if (count($shipmentsMaster) == 1) {
            $masterShipmentData = $shipments->filter(function ($item) use ($shipmentsMaster) {
                return $item->tracking_code == $shipmentsMaster[0];
            })->first();

            $price = @$masterShipmentData->shipping_price;
            $cost  = @$masterShipmentData->cost_shipping_price;
        }

        $data = compact(
            'shipmentsTrk',
            'shipmentsMaster',
            'ids',
            'price',
            'cost'
        );

        return view('admin.shipments.shipments.modals.assign.grouped', $data)->render();
    }

    /**
     * Update provider id for all selected shipments
     *
     * @param Request $request
     * @return mixed
     */
    public function massStoreGrouped(Request $request)
    {
        $ids        = explode(',', $request->ids);
        $master     = $request->get('assign_master_trk');
        $costPrice  = $request->get('assign_master_cost');
        $price      = $request->get('assign_master_price');
        $ungroup    = $request->get('assign_master_ungroup', false);

        $masterShipment = Shipment::where('tracking_code', $master)
            ->whereNull('invoice_id')
            ->first();

        if (!$masterShipment) {
            return Redirect::back()->with('error', 'Serviços master não encontrado.');
        }

        if ($ungroup) {

            $allShipments = Shipment::whereIn('id', $ids)->get();


            //verifica se algum dos envios é o master.
            //se for o master, desagrupa todos os que pertençam ao master
            $existsMaster = $allShipments->filter(function ($item) use ($master) {
                return $item->tracking_code == $master;
            })->first();


            if (@$existsMaster->exists) {
                $allShipments = Shipment::where('parent_tracking_code', $master)
                    ->orWhere('tracking_code', $master)
                    ->get();
            } else {
                //se nao tem master é na lista de ID's é porque estamos a apagar um (ou mais) envios filhos do master.
                //verificar quantos envios ficam alem do master
                $countAllShipments = Shipment::where('parent_tracking_code', $master)
                    ->orWhere('tracking_code', $master)
                    ->count();

                if (($countAllShipments - count($ids)) == 1) { //significa que só sobra o master. obriga a apagar tudo
                    $allShipments = Shipment::where('parent_tracking_code', $master)
                        ->orWhere('tracking_code', $master)
                        ->get();
                }
            }

            foreach ($allShipments as $shipment) {
                // Delete shipment expenses
                ShipmentExpense::where('shipment_id', $shipment->id)
                    ->delete();

                $shipment->type                   = null;
                $shipment->children_type          = null;
                $shipment->children_tracking_code = null;
                $shipment->parent_tracking_code   = null;
                $shipment->resetPrices();
                $shipment->save();
            }


            return Redirect::back()->with('success', 'Serviços desagrupados com sucesso.');
        } else {

            // Delete child shipments expenses
            ShipmentExpense::whereIn('shipment_id', $ids)
                ->where('id', '<>', $masterShipment->id)
                ->delete();

            //atualiza todos os "sub-envios" e coloca os valores a zero.
            Shipment::whereIn('id', $ids)
                ->where('tracking_code', '<>', $master)
                ->update([
                    'type'                      => 'M',
                    'parent_tracking_code'      => $master,
                    'price_kg'                  => null,
                    'shipping_price'            => 0,
                    'expenses_price'            => 0,
                    'fuel_price'                => 0,
                    'fuel_tax'                  => 0,
                    'vat_rate'                  => null,
                    'vat_rate_id'               => null,
                    'billing_subtotal'          => 0,
                    'billing_vat'               => 0,
                    'billing_total'             => 0,
                    'cost_shipping_base_price'  => 0,
                    'cost_shipping_price'       => 0,
                    'cost_expenses_price'       => 0,
                    'cost_billing_subtotal'     => 0,
                    'cost_billing_vat'          => 0,
                    'cost_billing_total'        => 0,
                    'total_price_for_recipient' => null,
                    'payment_at_recipient'      => 0,
                    'cod'                       => null,
                    'price_fixed'               => false
                ]);

            //para o envio master, coloca o preço do cliente bloqueado e chama o orçamentador do sistema para recalcular o preço.
            $masterShipment->children_type           = 'M';
            $masterShipment->children_tracking_code  = $master;
            $masterShipment->shipping_price          = $price;
            $masterShipment->cost_shipping_price     = $costPrice;
            $masterShipment->payment_at_recipient    = 0;
            $masterShipment->cod                     = null;
            $masterShipment->price_fixed             = true;


            $prices = Shipment::calcPrices($masterShipment);

            if (@$prices['fillable']) {
                $masterShipment->fill($prices['fillable']);
                $masterShipment->save();

                //adiciona taxas
                $masterShipment->storeExpenses($prices);
            }

            return Redirect::back()->with('success', 'Serviços agrupados com sucesso.');
        }
    }

    /**
     * Submit by webservice all shipments selected
     * GET /admin/shipments/selected/webservice
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massBlockShipments(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->update(['is_blocked' => $request->selected_block_status]);

        if ($result) {
            return Redirect::back()->with('success', 'Envios bloqueados com sucesso.');
        }

        return Redirect::back()->with('success', 'Não foi possível bloquear os envios selecionados.');
    }

    /**
     * Force sync shipment by webservice
     *
     * @param type $shipmentId
     * @return type
     */
    public function forceSync($shipmentId)
    {

        $shipment = Shipment::with('customer')
            ->with(['service'  => function ($q) {
                $q->remember(config('cache.query_ttl'));
            }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
            }])
            ->filterAgencies()
            ->findOrFail($shipmentId);

        $webservice = new Webservice\Base();

        try {
            $trackingCode = $webservice->submitShipment($shipment);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('success', 'Envio sincronizado com sucesso. TRK#' . $trackingCode);
    }

    /**
     * Submit by webservice all shipments selected
     * GET /admin/shipments/selected/webservice
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massForceSync(Request $request)
    {

        $ids = explode(',', $request->ids);

        $shipments = Shipment::with('customer', 'provider', 'service')
            ->filterAgencies()
            ->whereIn('id', $ids)
            ->whereNull('submited_at')
            ->get();


        foreach ($shipments as $shipment) {

            $webservice = new Webservice\Base();

            if (config('app.source') == 'volumedourado') {
                $result = $webservice->submitShipment($shipment, null, false, true);
            } else {
                $result = $webservice->submitShipment($shipment);
            }
        }

        return Redirect::back()->with('success', 'Envios submetidos com sucesso.');
    }

    /**
     * Open modal to confirm webservice reset
     *
     * @param type $shipmentId
     * @return type
     */
    public function editResetSync(Request $request, $shipmentId)
    {
        $shipment = Shipment::filterAgencies()->findOrFail($shipmentId);
        return view('admin.shipments.shipments.modals.sync.reset', compact('shipment'))->render();
    }

    /**
     * Rollback sync shipment webservice
     *
     * @param type $shipmentId
     * @return type
     */
    public function storeResetSync(Request $request, $shipmentId)
    {

        $destroyWebserviceConnection = $request->get('delete_provider', false);

        $shipment = Shipment::filterAgencies()->findOrFail($shipmentId);

        if ($destroyWebserviceConnection) {
            try {
                $shipment->destroyWebservice();
            } catch (\Exception $e) {

                if ($request->ajax()) {
                    return Response::json([
                        'result'   => false,
                        'feedback' => $e->getMessage()
                    ]);
                }

                return Redirect::back()->with('error', $e->getMessage());
            }
        }

        $shipment->resetWebserviceError(true);

        if ($request->ajax()) {
            return Response::json([
                'result'   => true,
                'feedback' => 'Sincronização do envio anulada com sucesso.'
            ]);
        }

        return Redirect::back()->with('success', 'Sincronização do envio anulada com sucesso.');
    }

    /**
     * Massive Confirmation of Shipments
     *
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function confirmShipment(Request $request)
    {

        $ids = $request->ids;
        $ids = explode(',', $ids);


        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {
            if ($request->has('confirm_status')) {
                $shipment->customer_conferred = $request->get('confirm_status', false);
            } else {
                $shipment->customer_conferred = !$shipment->customer_conferred;
            }
            //dd($shipment->conferred);
            $shipment->save();
        }

        $feedback = 'Envio confirmado com sucesso.';
        if (count($ids) > 1) {
            $feedback = 'Envios selecionados confirmados com sucesso.';
        }

        $row = $shipment;

        if ($request->ajax()) {
            return Response::json([
                'result'   => true,
                'feedback' => $feedback,
                'html'     => view('admin.shipments.shipments.datatables.conferred', compact('row'))->render()
            ]);
        }

        return Redirect::back()->with('success', $feedback);
    }

    /**
     * Open modal to store or edit webservice details
     *
     * @param type $shipmentId
     * @return type
     */
    public function editManualSync(Request $request, $shipmentId)
    {
        $shipment = Shipment::filterAgencies()->findOrFail($shipmentId);

        $webserviceMethods = \App\Models\WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(\App\Models\WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        return view('admin.shipments.shipments.modals.sync.manual', compact('shipment', 'webserviceMethods'))->render();
    }

    /**
     * Rollback sync shipment webservice
     *
     * @param type $shipmentId
     * @return type
     */
    public function storeManualSync(Request $request, $shipmentId)
    {

        $input = $request->all();

        $shipment = Shipment::filterAgencies()->findOrFail($shipmentId);

        $shipment->fill($input);
        $shipment->submited_at = Date::now();
        $shipment->webservice_error = null;

        if (empty($request->webservice_method)) {
            $shipment->submited_at       = null;
            $shipment->webservice_method = null;
            $shipment->webservice_error  = null;
        }

        $shipment->save();

        return Response::json([
            'result'   => true,
            'feedback' => 'Gravado com sucesso'
        ]);
    }


    /**
     * Get volumetric weight for budgets
     *
     * @param Request $request
     * @return mixed
     */
    /*    public function getVolumetricWeight(Request $request)
    {
        $result = Shipment::getVolumetricWeight($request->fatorM3, $request->serviceId);
        return Response::json($result);
    }*/

    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createBudget(Request $request)
    {
        $services = $this->listServices(Service::filterAgencies()
            ->ordered()
            ->get());

        $providers = Provider::filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.shipments.shipments.modals.budget', compact('services', 'providers'));
    }


    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calculateBudget(Request $request)
    {
        throw new \Exception('DESCONTINUADO');
    }

    /**
     * Close all selected shipments
     * GET /admin/users/selected/close-shipment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massCloseShipments(Request $request)
    {
        $ids = explode(',', $request->ids);

        $shipments = Shipment::where('is_closed', 1)->whereIn('id', $ids)->count();
        $providers = Shipment::with('provider')->whereIn('id', $ids)->first();

        if ($shipments) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Existem envios selecionados que já se encontram fechados. Reveja os envios selecionados e tente de novo.'
            ]);
        }

        $webservice = new Webservice\Base();
        $result = $webservice->closeShipments($providers->provider->webservice_method, $ids);

        return response()->json([
            'result'   => @$result['result'],
            'filepath' => @$result['filepath'],
            'feedback' => @$result['feedback'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAcceptanceCertificates()
    {

        $action = 'Certificados de Aceitação';

        /*$dir = public_path().'/uploads/labels/certificates/';

        if(!File::exists($dir)) {
            File::makeDirectory($dir);
        }

        $files = collect(File::allFiles($dir))
            ->sortByDesc(function ($file) {
                return $file->getCTime();
            });

        return view('admin.shipments.shipments.modals.ctt_certificates', compact('files'))->render();*/

        $certificates = CttDeliveryManifest::orderBy('id', 'desc')->take(50)->get();

        return view('admin.shipments.shipments.modals.ctt_certificates', compact('certificates'))->render();
    }

    /**
     * Store shipment dimensions of packs
     *
     * @param $shipment
     * @param $input
     */
    public function storeDimensions(&$shipment, $input, $sourceHash = '')
    {

        //destroy all dimensions saved for shipment
        ShipmentPackDimension::where('shipment_id', $shipment->id)->delete();

        $totalfatorM3 = 0;
        $totalVolumes = 0;

        if (isset($input['box_type']) || isset($input['length']) || isset($input['box_description'])) {

            $rows = $input['length'];
            if (empty($input['length']) && !empty($input['box_description'])) {
                $rows = $input['box_description'];
            } else if (!empty($input['box_type'])) {
                $rows = $input['box_type'];
            }

            $products = [];
            $skus = [];
            $typesSummary = [];
            $biggerSide = $biggerSize = $biggerWeight = 0;
            foreach ($rows as $key => $dimensions) {

                if ($input['dim_src'][$key] == $sourceHash) { //só grava os dados referentes ao dim_src
                    if ((!empty($input['height'][$key]) && !empty($input['length'][$key]) && !empty($input['width'][$key])) || !empty(!empty($input['box_description'][$key]))) {

                        try {
                            if (empty(Setting::get('shipments_volumes_mesure_unity')) || Setting::get('shipments_volumes_mesure_unity') == 'cm') {
                                $volume = (@$input['height'][$key] * @$input['length'][$key] * @$input['width'][$key]) / 1000000;
                            } else {
                                $volume = (@$input['height'][$key] * @$input['length'][$key] * @$input['width'][$key]);
                            }
                        } catch (\Exception $e) {
                            $volume = 0;
                        }

                        $totalVolumes++;
                        $totalfatorM3 += $volume;

                        $data = [
                            'shipment_id'       => $shipment->id,
                            'length'            => (float)@$input['length'][$key],
                            'width'             => (float)@$input['width'][$key],
                            'height'            => (float)@$input['height'][$key],
                            'weight'            => (float)@$input['box_weight'][$key],
                            'volume'            => $volume,
                            'description'       => @$input['box_description'][$key],
                            'type'              => @$input['box_type'][$key],
                            'adr_letter'        => @$input['box_adr_letter'][$key],
                            'adr_class'         => @$input['box_adr_class'][$key],
                            'adr_number'        => @$input['box_adr_number'][$key],
                            'qty'               => @$input['qty'][$key],
                            'price'             => @$input['box_price'][$key],
                            'total_cost'        => @$input['box_cost'][$key],
                            'total_price'       => @$input['box_total_price'][$key],
                            'optional_fields'   => @$input['box_optional_fields'][$key],

                            'product_id'    => @$input['product'][$key],
                            'sku'           => @$input['sku'][$key],
                            'serial_no'     => @$input['serial_no'][$key],
                            'lote'          => @$input['lote'][$key],
                            'validity'      => @$input['validity'][$key],
                            'product'       => @$input['product'][$key],
                        ];

                        $typesSummary[@$data['type']] = @$typesSummary[@$data['type']] + @$data['qty'];


                        $sideSum      = $data['length'] + $data['width'] + $data['height'];
                        $biggerSide   = $biggerSide > $data['length'] ? $biggerSide : $data['length'];
                        $biggerSide   = $biggerSide > $data['width'] ? $biggerSide : $data['width'];
                        $biggerSide   = $biggerSide > $data['height'] ? $biggerSide : $data['height'];
                        $biggerSize   = $biggerSize > $sideSum ? $biggerSize : $sideSum;
                        $biggerWeight = $biggerWeight > $data['weight'] ? $biggerWeight : $data['weight'];

                        $dimension = new ShipmentPackDimension;
                        $dimension->fill($data);
                        $dimension->save();
                    }
                }
            }

            if ($totalfatorM3 == 0.00 && $shipment->fator_m3 > 0) {
                //preserva o fator m3 caso exista o campo preenchido mas caso não existam dimensões inseridas.
                //caso contrário, o fator m3 seria subscrito
                $totalfatorM3 = $shipment->fator_m3;
            }

            $shipment->fator_m3           = $totalfatorM3;
            $shipment->packaging_type     = $typesSummary;
            $shipment->dims_bigger_side   = $biggerSide;
            $shipment->dims_bigger_size   = $biggerSize;
            $shipment->dims_bigger_weight = $biggerWeight;
            $shipment->save();

            return true;
        }
    }

    /**
     * Store shipment dimensions of packs
     *
     * @param $shipment
     * @param $input
     */
    public function storeExpenses(&$shipment, $input)
    {

        try {

            $now = date('Y-m-d H:i:s');

            $insertArr = [];

            $subtotal = $vat = $total = $costSubtotal = $costVat = $costTotal = 0;

            foreach ($input['expense_id'] as $rowId => $expenseId) {
                if ($expenseId) {

                    $isAuto        = @$input['expense_auto'][$rowId] ? 1 : 0;
                    $qty           = !is_null(@$input['expense_qty'][$rowId]) ? @$input['expense_qty'][$rowId] : 1;
                    $providerId    = @$input['expense_provider_id'][$rowId] ? @$input['expense_provider_id'][$rowId] : $shipment->provider_id;
                    $billingItemId = !empty(@$input['expense_billing_item_id'][$rowId]) ? @$input['expense_billing_item_id'][$rowId] : null;

                    $arr = [
                        'shipment_id'       => $shipment->id,
                        'expense_id'        => $expenseId,
                        'billing_item_id'   => $billingItemId,
                        'auto'              => $isAuto,
                        'qty'               => $qty,
                        'unity'             => @$input['expense_unity'][$rowId],
                        'price'             => forceDecimal(@$input['expense_price'][$rowId]),
                        'subtotal'          => forceDecimal(@$input['expense_subtotal'][$rowId]),
                        'vat'               => forceDecimal(@$input['expense_vat'][$rowId]),
                        'total'             => forceDecimal(@$input['expense_total'][$rowId]),
                        'vat_rate'          => forceDecimal(@$input['expense_vat_rate'][$rowId]),
                        'vat_rate_id'       => @$input['expense_vat_rate_id'][$rowId],

                        'cost_price'        => forceDecimal(@$input['expense_cost_price'][$rowId]),
                        'cost_subtotal'     => forceDecimal(@$input['expense_cost_subtotal'][$rowId]),
                        'cost_vat'          => forceDecimal(@$input['expense_cost_vat'][$rowId]),
                        'cost_total'        => forceDecimal(@$input['expense_cost_total'][$rowId]),
                        'cost_vat_rate'     => forceDecimal(@$input['expense_cost_vat_rate'][$rowId]),
                        'cost_vat_rate_id'  => @$input['expense_cost_vat_rate_id'][$rowId],
                        'provider_id'       => $providerId,
                        'date'              => $shipment->billing_date,
                        'created_by'        => Auth::user()->id,
                        'created_at'        => $now,
                        'updated_at'        => $now
                    ];


                    $insertArr[] = $arr;

                    $subtotal += $arr['subtotal'];
                    $vat +=      $arr['vat'];
                    $total +=    $arr['total'];
                    $costSubtotal += $arr['cost_subtotal'];
                    $costVat +=   $arr['cost_vat'];
                    $costTotal += $arr['cost_total'];
                }
            }

            //remove todas as taxas anteriores
            ShipmentExpense::where('shipment_id', $shipment->id)->forceDelete();

            //adiciona as novas taxas
            ShipmentExpense::insert($insertArr);
        } catch (\Exception $e) {
            $shipment->expenses_price      = 0;
            $shipment->cost_expenses_price = 0;
            $shipment->save();

            return false;
        }

        return [
            'result'        => true,
            'subtotal'      => $subtotal,
            'vat'           => $vat,
            'total'         => $total,
            'cost_subtotal' => $costSubtotal,
            'cost_vat'      => $costVat,
            'cost_total'    => $costTotal
        ];
    }

    /**
     * Get POD
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPod($id)
    {

        $shipment = Shipment::filterMyAgencies()->findOrfail($id);

        if ($shipment->hasSync() && !$shipment->hasSyncError() && $shipment->webservice_method != 'enovo_tms') {

            try {
                $webservice = new Webservice\Base();
                $url = $webservice->getPodUrl($shipment);

                if (!$url) {
                    //Imprimir o nosso próprio POD
                    $shipmentIds = [$id];
                    return Shipment::printPod($shipmentIds);

                    //return Redirect::route('admin.shipments.index')->with('error', 'O comprovativo POD do envio não está disponível.');
                }

                if ($shipment->webservice_method == 'envialia') {
                    header("Location: $url");
                } else {
                    header('Content-type: image/png');
                    echo file_get_contents($url);
                    exit;
                }
            } catch (\Exception $e) {
                return Redirect::route('admin.shipments.index')->with('error', 'O comprovativo POD do envio não está disponível.');
            }
        } else {
            //Imprimir o nosso próprio POD
            $shipmentIds = [$id];
            return Shipment::printPod($shipmentIds);
        }
    }

    /**
     * Edit Property Declaration
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editPropertyDeclaration($id)
    {

        $shipment = Shipment::with('customer')
            ->filterMyAgencies()
            ->findOrfail($id);

        return view('admin.shipments.shipments.edit_property_declaration', compact('shipment'))->render();
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipmentsScheduled(Request $request)
    {

        $appMode = Setting::get('app_mode');

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        $bindings = [
            'shipments.*',
            'shipments_scheduled.frequency',
            'shipments_scheduled.repeat_every',
            'shipments_scheduled.repeat',
            'shipments_scheduled.weekdays',
            'shipments_scheduled.month_days',
            'shipments_scheduled.end_repetitions',
            'shipments_scheduled.end_date',
            'shipments_scheduled.count_repetitions',
            'shipments_scheduled.last_schedule',
            'shipments_scheduled.finished',
        ];

        $shipmentIds = ShipmentSchedule::filterSource()
            ->where('finished', 0)
            ->pluck('shipment_id')
            ->toArray();

        $data = Shipment::join('shipments_scheduled', 'shipments.id', '=', 'shipments_scheduled.shipment_id')
            ->filterAgencies()
            ->with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name']);
            }])
            ->whereIn('shipments.id', $shipmentIds)
            ->select($bindings);

        $agencies = Agency::filterAgencies()
            ->remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['name', 'code', 'id', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();

        return Datatables::of($data)
            ->edit_column('finished', function ($row) {
                return view('admin.shipments.shipments.datatables.schedule.finished', compact('row'))->render();
            })
            ->edit_column('service_id', function ($row) use ($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('volumes', function ($row) use ($appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'appMode'))->render();
            })
            ->edit_column('date', function ($row) {
                return view('admin.shipments.shipments.datatables.date', compact('row'))->render();
            })
            ->add_column('schedule', function ($row) {
                return view('admin.shipments.shipments.datatables.schedule.schedule_time', compact('row'))->render();
            })
            ->add_column('schedule_end', function ($row) {
                return view('admin.shipments.shipments.datatables.schedule.schedule_end', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.shipments.shipments.datatables.schedule.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Automatic generate shipments from pickups
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function autoGenerateShipmentsFromPickups(Request $request)
    {

        try {

            $customer = $request->customer;
            $year  = $request->year ?? date('Y');
            $month = $request->month ?? date('m');
            $date  = $year . '-' . $month . '-01';

            Shipment::generateShipmentsFromPickups($date, $customer);
        } catch (\Exception $e) {
            if (Auth::user()->isAdmin()) {
                return Redirect::back()->with('error', $e->getMessage());
            } else {
                return Redirect::back()->with('error', 'Ocorreu um erro ao tentar gerar os envios a partir das recolhas.');
            }
        }

        return Redirect::back()->with('success', 'Envios gerados com sucesso');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editPrintA4(Request $request)
    {
        $ids = $request->get('id');
        return view('admin.shipments.shipments.modals.printA4', compact('ids'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massCreateManifest(Request $request)
    {
        $ids = $request->get('id');
        $ids = implode(',', $ids);

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $operators = $this->listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator()
            ->orderBy('name', 'asc')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $finalStatus = ShippingStatus::where('is_final', 1)->pluck('id')->toArray();
        $finalStatus[] = 9;

        $trips = Trip::whereHas('shipments', function ($q) use ($finalStatus) {
                $q->whereNotIn('status_id', $finalStatus);
            })
            ->pluck('code', 'id')
            ->toArray();

        $data = compact(
            'ids',
            'status',
            'providers',
            'operators',
            'routes',
            'vehicles',
            'trailers',
            'agencies',
            'trips'
        );

        return view('admin.shipments.shipments.modals.mass.create_manifest', $data)->render();
    }

    /**
     * Return shipment totals
     *
     * @param $shipment
     * @param $transhipments
     * @param $groupedShipments
     * @return array
     */
    public function getShipmentTotals($shipment, $transhipments, $groupedShipments)
    {

        $totalPrice = $transhipments->sum('shipping_price')
            + $transhipments->sum('expenses_price')
            + $transhipments->sum('fuel_price')
            + @$groupedShipments->sum('shipping_price')
            + @$groupedShipments->sum('expenses_price')
            + @$groupedShipments->sum('fuel_price');

        $costTotal = $transhipments->sum('cost_shipping_price')
            + $transhipments->sum('cost_expenses_price')
            + $transhipments->sum('cost_fuel_price')
            + @$groupedShipments->sum('cost_shipping_price')
            + @$groupedShipments->sum('cost_expenses_price')
            + @$groupedShipments->sum('cost_fuel_price');

        if (@$groupedShipments->isEmpty()) { //se nao tem envios agrupados, soma o valor do envio atual, caso contrário vai somar de novo porque o envio atual já está na lista de agrupados
            $totalPrice += $shipment->shipping_price + $shipment->expenses_price + $shipment->fuel_price;
            $costTotal += $shipment->cost_shipping_price + $shipment->cost_expenses_price + $shipment->cost_fuel_price;
        }

        $gains = $totalPrice - $costTotal;
        $balance = 0.00;
        if ($totalPrice > 0.00) {
            $balance = ($gains / $totalPrice) * 100;
        }


        return [
            'cost'    => number($costTotal),
            'price'   => number($totalPrice),
            'gain'    => number($gains),
            'gain_percent' => number($balance)
        ];
    }

    public function getDimensionsFromShippingOrder($shippingOrder)
    {
        $arr = [];

        foreach ($shippingOrder->lines as $line) {

            $volume = number((@$line->product->weight * @$line->product->height * @$line->product->length) / 1000000, 3);

            $packDim = new ShipmentPackDimension();
            $packDim->qty         = $line->qty;
            $packDim->description = @$line->product->name;
            $packDim->weight      = @$line->product->weight;
            $packDim->height      = @$line->product->height;
            $packDim->width       = @$line->product->width;
            $packDim->length      = @$line->product->length;
            $packDim->volume      = $volume;
            $packDim->product_id  = @$line->product_id;
            $packDim->sku         = @$line->product->sku;
            $packDim->lote        = @$line->product->lote;
            $packDim->serial_no   = @$line->product->serial_no;
            $packDim->type        = 'box';
            $arr[] = $packDim;
        }

        return collect($arr);
    }

    /**
     * Otimiza rota de entrega do envio
     *
     * @param Request $request
     */
    public function getDeliveryRoute(Request $request)
    {

        $optimize = $request->get('optimize', false);
        $optimize = $optimize == 'false' ? false : true;

        $returnBack = $request->get('return_back', false);
        $returnBack = $returnBack == 'false' ? false : true;

        $waypointAgency = $request->get('waypoint_agency', false);
        $waypointAgency = $waypointAgency == 'false' ? false : true;

        $addresses = [];

        $senderAddress = $request->sender_address . ', ' .
            $request->sender_zip_code . ' ' .
            $request->sender_city . ', ' .
            strtoupper($request->sender_country);

        $recipientAddress = $request->recipient_address . ', ' .
            $request->recipient_zip_code . ' ' .
            $request->recipient_city . ', ' .
            strtoupper($request->recipient_country);


        //MORADAS DE RECOLHA
        $addresses[] = $senderAddress;

        if ($waypointAgency && !empty($request->get('sender_agency_id'))) {
            $agency = Agency::find($request->get('sender_agency_id'));
            $addresses[] = $agency->address . ', ' . $agency->zip_code . ' ' . $agency->city . ', ' . $agency->country;
        }


        //MORADAS DE ENTREGA
        if ($waypointAgency && !empty($request->get('recipient_agency_id')) && $request->get('sender_agency_id') != $request->get('recipient_agency_id')) {
            $agency = Agency::find($request->get('recipient_agency_id'));
            $addresses[] = $agency->address . ', ' . $agency->zip_code . ' ' . $agency->city . ', ' . $agency->country;
        }
        $addresses[] = $recipientAddress;

        if ($request->addr) {
            foreach ($request->addr as $addr) {

                if (@$addr['recipient_address']) {
                    $address = @$addr['recipient_address'] . ', ' .
                        @$addr['recipient_zip_code'] . ' ' .
                        @$addr['recipient_city'] . ', ' .
                        strtoupper(@$addr['recipient_country']);

                    $addresses[] = $address;
                }
            }
        }

        if ($returnBack) {
            $addresses[] = $senderAddress;
        }

        $params = [
            'optimize'    => $optimize,
            'return_back' => $returnBack
        ];

        try {
            $orderedPos = Map::optimizeDeliveryFromAddresses($addresses, $params, false);
        } catch (\Exception $e) {
            return response()->json([
                'result'   => false,
                'feedback' => $e->getMessage(),
                'html'     => null,
                'distance' => '-.--km',
                'duration' => '--h--'
            ]);
        }


        $getDistPos = 0;
        if ($returnBack) {
            $getDistPos = 1;
        }

        $orderedAddresses = [];
        $html = '<ul>';
        $distanceKms = $distance = $duration = 0;


        foreach ($orderedPos as $pos => $address) {
            $orderedAddresses[] = $address['address'];

            $alphabet = range('A', 'Z');

            $letter = $alphabet[$pos];
            $html .= '<li data-duration="' . $address['duration'] . '" data-distance="' . $address['distance'] . '" data-lat="' . $address['latitude'] . '" data-lng="' . $address['longitude'] . '" data-id="' . $pos . '">';
            $html .= '<span class="step">' . $letter . '</span><input type="text" value="' . $address['address'] . '" class="form-control input-sm"/>';
            $html .= '<small>' . $address['duration'] . ' | ' . $address['distance'] . '</small>';
            $html .= '<div class="clearfix"></div>';
            $html .= '</li>';


            if ($pos == $getDistPos) {
                $distanceKms = $address['distance_val'];
                $distance    = $address['distance'];
                $duration    = $address['duration'];
            }
        }
        $html .= '</ul>';

        if ($returnBack) {
            $distanceKms = $distanceKms * 2;
        }

        $vehicle         = \App\Models\FleetGest\Vehicle::where('license_plate', $request->vehicle)->first();

        $distanceKms     = number($distanceKms / 1000);
        $fuelPriceLiter  = forceDecimal(Setting::get('guides_fuel_price'));
        $fuelConsumption = @$vehicle->average_consumption;
        $fuelLiters      = number(($distanceKms * $fuelConsumption) / 100);
        $fuelPrice       = number($fuelLiters * $fuelPriceLiter);

        return response()->json([
            'result'            => true,
            'feedback'          => 'Rota traçada com sucesso.',
            'html'              => $html,
            'distance'          => $distance,
            'duration'          => $duration,
            'fuel_price'        => $fuelPrice,
            'fuel_liters'       => $fuelLiters,
            'fuel_consumption'  => $fuelConsumption,
            'fuel_price_liter'  => $fuelPriceLiter,
            //'coordinates'       => $coordinates    
        ]);
    }

    /**
     * Insert or update sender / recipient address
     * @param $input
     */
    public function insertOrUpdateSender($input)
    {

        try {
            if (!empty($input['sender_id'])) { //atualiza informação
                $recipient = CustomerRecipient::where('id', $input['sender_id'])
                    ->update([
                        'vat'         => @$input['sender_vat'],
                        'name'        => @$input['sender_name'],
                        'address'     => @$input['sender_address'],
                        'zip_code'    => @$input['sender_zip_code'],
                        'city'        => @$input['sender_city'],
                        'state'       => @$input['sender_state'],
                        'country'     => @$input['sender_country'],
                        'phone'       => @$input['sender_phone'],
                        'email'       => @$input['sender_email'],
                        'responsable' => @$input['sender_attn']
                    ]);
            }

            // Save new address
            elseif (empty($input['sender_id']) && !empty($input['customer_id'])) {

                $recipient = CustomerRecipient::firstOrNew([
                    'customer_id' => $input['customer_id'],
                    'name'        => $input['sender_name'],
                    'address'     => $input['sender_address'],
                    'zip_code'    => $input['sender_zip_code'],
                    'city'        => $input['sender_city']
                ]);

                if ($recipient->validate($input)) {
                    $recipient->customer_id = @$input['customer_id'];
                    $recipient->vat         = @$input['sender_vat'];
                    $recipient->name        = @$input['sender_name'];
                    $recipient->address     = @$input['sender_address'];
                    $recipient->zip_code    = @$input['sender_zip_code'];
                    $recipient->city        = @$input['sender_city'];
                    $recipient->state       = @$input['sender_state'];
                    $recipient->country     = @$input['sender_country'];
                    $recipient->phone       = @$input['sender_phone'];
                    $recipient->email       = @$input['sender_email'];
                    $recipient->responsable = @$input['sender_attn'];
                    $recipient->save();
                }
            }
        } catch (\Exception $e) {
        }

        return @$recipient->id;
    }

    /**
     * Insert or update sender / recipient address
     * @param $input
     */
    public function insertOrUpdateRecipient($input)
    {

        try {
            //update existing address
            if (!empty($input['recipient_id'])) {
                $recipient = CustomerRecipient::where('id', $input['recipient_id'])
                    ->update([
                        'vat'           => @$input['recipient_vat'],
                        'name'          => @$input['recipient_name'],
                        'address'       => @$input['recipient_address'],
                        'zip_code'      => @$input['recipient_zip_code'],
                        'city'          => @$input['recipient_city'],
                        'state'         => @$input['recipient_state'],
                        'country'       => @$input['recipient_country'],
                        'phone'         => @$input['recipient_phone'],
                        'email'         => @$input['recipient_email'],
                        'responsable'   => @$input['recipient_attn']
                    ]);
            }

            //save new address
            elseif (empty($input['recipient_id']) && !empty($input['customer_id'])) {
                $recipient = CustomerRecipient::firstOrNew([
                    'customer_id'   => $input['customer_id'],
                    'name'          => $input['recipient_name'],
                    'address'       => $input['recipient_address'],
                    'zip_code'      => $input['recipient_zip_code'],
                    'city'          => $input['recipient_city']
                ]);

                if ($recipient->validate($input)) {
                    $recipient->customer_id = @$input['customer_id'];
                    $recipient->vat         = @$input['recipient_vat'];
                    $recipient->name        = @$input['recipient_name'];
                    $recipient->address     = @$input['recipient_address'];
                    $recipient->zip_code    = @$input['recipient_zip_code'];
                    $recipient->city        = @$input['recipient_city'];
                    $recipient->state       = @$input['recipient_state'];
                    $recipient->country     = @$input['recipient_country'];
                    $recipient->phone       = @$input['recipient_phone'];
                    $recipient->email       = @$input['recipient_email'];
                    $recipient->responsable = @$input['recipient_attn'];
                    $recipient->save();
                }
            }
        } catch (\Exception $e) {
        }

        return @$recipient->id;
    }

    /**
     * Return view name
     *
     * @return string
     */
    public function getEditView()
    {

        $view = 'edit';

        /*if (app_mode_transfers()) {
            $view = 'edit_transfers';
        }*/

        return 'admin.shipments.shipments.' . $view;
    }

    /**
     * Edit Delivery Date
     *
     * @return string
     */
    public function editDeliveryDate(Request $request, $id)
    {
        $action = "Alterar data serviço";

        $shipment = Shipment::findOrFail($id);

        $formOptions = array('route' => array('admin.shipments.delivery-date.store', $shipment), 'method' => 'POST', 'class' => 'ajax-form');

        $hours = listHours(1);

        $data = compact(
            'shipment',
            'formOptions',
            'action',
            'hours'
        );

        return view('admin.shipments.shipments.edit.delivery_date', $data)->render();
    }

    /**
     * Store change dates
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function storeDeliveryDate(Request $request, $id)
    {

        try {
            $shippingDate = $request->get('shipping_date');
            $shippingHour = $request->get('shipping_hour', '00:00');
            //$shippingDate = trim($shippingDate . ' ' . ($shippingHour ? $shippingHour.':00' : '00:00:00'));

            $deliveryDate = $request->get('delivery_date');
            $deliveryHour = $request->get('delivery_hour');
            $deliveryHour = $deliveryHour ? $deliveryHour . ':00' : '00:00:00';

            $shipment = Shipment::findOrFail($id);
            $shipment->date          = $shippingDate;
            $shipment->start_hour    = $shippingHour;
            $shipment->end_hour      = $deliveryHour;
            $shipment->shipping_date = $shippingDate . ' ' . $deliveryHour;
            $shipment->delivery_date = $deliveryDate . ' ' . $deliveryHour;
            $shipment->status_id     = ShippingStatus::ACCEPTED_ID;
            $shipment->save();

            //add history
            $history = new ShipmentHistory();
            $history->shipment_id = $shipment->id;
            $history->status_id   = ShippingStatus::ACCEPTED_ID;
            $history->agency_id   = $shipment->sender_agency_id;
            $history->obs         = 'Agendado para ' . $deliveryDate;
            $history->save();


            //prepare response
            $row = $shipment;
            $statusList = ShippingStatus::remember(config('cache.query_ttl'))
                ->cacheTags(Service::CACHE_TAG)
                ->get(['id', 'name', 'color', 'is_final']);
            $statusList  = $statusList->groupBy('id')->toArray();

            $response = [
                'result'   => true,
                'feedback' => 'Data alterada com sucesso.',
                'html'     => view('admin.shipments.shipments.datatables.delivery_date', compact('row', 'statusList'))->render()
            ];
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => $e->getMessage(),
                'html'     => null
            ];
        }



        return Response::json($response);
    }

    /**
     * Main function to edit email
     *
     * @param Request $request
     * @param [type] $id
     * @param [type] $target
     * @return void
     */
    public function editEmailDispacher(Request $request, $shipmentId, $target)
    {
        if ($target == 'provider') {
            return $this->editEmailProvider($request, $shipmentId);
        } elseif ($target == 'customer') {
            return $this->editEmailCustomer($request, $shipmentId);
        } elseif ($target == 'auction') {
            return $this->editEmailAuction($request, $shipmentId);
        } elseif($target == 'provider_request_info') {
            return $this->editEmailProviderRequestInfo($request, $shipmentId);
        }else {
            return $this->editEmailDocs($request, $shipmentId);
        }
    }

    /**
     * Main function to submit email
     *
     * @param Request $request
     * @param [type] $id
     * @param [type] $target
     * @return Redirect
     */
    public function sendEmailDispacher(Request $request, $shipmentId, $target)
    {
        if ($target == 'auction') {
            return $this->sendEmailAuction($request, $shipmentId);
        } else {
            return $this->sendEmailDocs($request, $shipmentId);
        }
    }

    /**
     * Open modal to edit email to provider
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEmailProvider(Request $request, $shipmentId)
    {
        $shipment = Shipment::with('provider')
            ->filterMyAgencies()
            ->findOrfail($shipmentId);

        //verifica se o envio
        if ($masterTrk = $shipment->isGrouped()) {
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
        } else {
            $shipments = [$shipment];
        }

        $locale = $shipment->provider->locale;
        $locale = empty($locale) ? Setting::get('app_country') : $locale;

        $subject = $shipment->tracking_code . ' - ' . transLocale('admin/email.cargo-instructions.subject-expedition', $locale);
        if ($shipment->is_collection) {
            $subject = $shipment->tracking_code . ' - ' . transLocale('admin/email.cargo-instructions.subject-pickup', $locale);
        }

        $message = view('admin.shipments.shipments.partials.provider_email', compact('locale', 'shipments', 'shipment'))->render();

        //cria instancia de e-mail
        $email = new Email([
            'modal_title'   => 'Enviar confirmação/instruções de carga',
            'subject'       => $subject,
            'to'            => @$shipment->provider->email,
            'cc'            => null,
            'bcc'           => null,
            'message'       => $message,
            'attached_docs' => [
                [
                    'doc'   => 'cargo_instructions',
                    'title' => 'Instruções de Carga',
                    'url'   => Email::createTempAttachment(Shipment::printShippingInstructions([$shipmentId], null, null, 'S'))
                ]
            ]
        ]);

        return view('admin.emails.emails.edit', compact('email'))->render();
    }

    /**
     * Open modal to edit email to customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEmailCustomer(Request $request, $shipmentId)
    {
        $shipment = Shipment::filterMyAgencies()
            ->findOrfail($shipmentId);

        //verifica se o envio
        if ($masterTrk = $shipment->isGrouped()) {
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
        } else {
            $shipments = [$shipment];
        }

        $locale = $shipment->customer->country;
        $locale = empty($locale) ? Setting::get('app_country') : $locale;

        $subject = transLocale('admin/email.shipment-summary.subject', $locale, ['trk' => $shipment->tracking_code]);
        $message = transLocale('admin/email.shipment-summary.message', $locale);
        $message .= '<br/><br/>' . Email::getSignature();

        $email = new Email([
            'modal_title'   => 'Envio de cotação/resumo serviço',
            'subject'       => $subject,
            'to'            => @$shipment->customer->contact_email,
            'cc'            => null,
            'bcc'           => null,
            'message'       => $message,
            'attached_docs' => [
                [
                    'doc'   => 'summary',
                    'title' => 'Resumo de serviço',
                    'url'   => Email::createTempAttachment(Shipment::printShipmentProof([$shipmentId], null, null, 'S'))
                ]
            ]
        ]);

        return view('admin.emails.emails.edit', compact('email'))->render();
    }

    /**
     * Open modal to edit notification to recipient
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editNotification(Request $request)
    {
        $ids = $request->get('id');
        $ids = implode(',', $ids);

        return view('admin.shipments.shipments.modals.email_notification', compact('ids'))->render();
    }

    /**
     * Send notification to shipment recipient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendNotification(Request $request)
    {

        $ids = $request->get('ids');
        $ids = explode(',', $ids);

        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {
            try {
                $shipment->sendEmail();
            } catch (\Exception $e) {
            }

            try {
                $shipment->sendSms();
            } catch (\Exception $e) {
            }
        }

        return  Redirect::back()->with('success', 'Notificações enviadas com sucesso');
    }


    /**
     * Show modal to edit email to send docs
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function editEmailDocs(Request $request, $id)
    {
        $shipment = Shipment::with('customer')->findOrFail($id);

        $mailingList = MailingList::orderBy('sort')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'shipment',
            'mailingList'
        );

        return view('admin.shipments.shipments.modals.email_docs', $data)->render();
    }

    /**
     * Send shipment documents (cmr, guide, labels)
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function sendEmailDocs(Request $request, $id)
    {
        $shipment = Shipment::with('customer')
            ->findOrFail($id);

        if (empty($request->get('email'))) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail. Não inseriu nenhum email.'
            ]);
        } else if (empty($request->get('attachments', []))) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail. Não selecionou nenhum documento para enviar em anexo.'
            ]);
        }

        $data = [
            'email'       => $request->get('email'),
            'attachments' => $request->get('attachments', []),
            'emailsList'  => $request->get('mailingList', []),
        ];

        $result = $shipment->sendEmailWithDocs($data);

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'E-mail enviado com sucesso.'
        ]);
    }


    /**
     * Open modal to edit auction email
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEmailAuction(Request $request, $shipmentId = null)
    {

        if ($shipmentId && !$request->has('id')) {
            $ids = $shipmentId;
        } else {
            $ids = $request->get('id');
            $ids = implode(',', $ids);
        }

        $mailingLists = MailingList::orderBy('sort')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'ids',
            'mailingLists'
        );

        return view('admin.shipments.shipments.modals.email_auction', $data)->render();
    }

    /**
     * Send notification to all providers
     *
     * @param Request $request
     * @return redirect
     * @throws \Throwable
     */
    public function sendEmailAuction(Request $request, $shipmentId = null)
    {
        $locale       = $request->get('locale', 'pt');
        $shipmentsIds = explode(',', $request['ids']);
        $shipments    = Shipment::whereIn('id', $shipmentsIds)->get();
        $mailingList  = MailingList::where('id', $request->mailing_list)->first();
        $emails       = explode(',', $mailingList->emails);

        $totalToSend = count($emails) * $shipments->count();
        $count = 0;

        $emailView = 'emails.shipments.auction_' . $locale;
        if (!view()->exists($emailView)) { //valida se existe o view para a linguagem definida.
            $emailView = 'emails.shipments.auction_en';
        }

        foreach ($shipments as $shipment) {

            $date     = new DateTime($shipment->shipping_date);
            $limitDay = $date->modify('+1 day');
            $limitDay = $limitDay->format('Y-m-d');

            Mail::send($emailView, compact('shipment', 'limitDay'), function ($message) use ($emails, $limitDay) {
                $message->bcc($emails);
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->subject('Nova carga disponível');
            });
        }

        if (count(Mail::failures()) > 0) {
            Redirect::back()->with('success', 'Alguns serviços não puderam ser notificados.');
        }


        return Redirect::back()->with('success', 'Leilão comunicado com sucesso.');
    }

    /**
     * Open modal to edit provider request info email
     *
     * @param Request $request
     * @param  int  $shipmentId
     * @return \Illuminate\Http\Response
     */
    public function editEmailProviderRequestInfo(Request $request, $shipmentId = null) {
        $shipment = Shipment::with('provider')
            ->filterMyAgencies()
            ->findOrfail($shipmentId);

        $locale = $shipment->provider->locale;
        $locale = empty($locale) ? Setting::get('app_country') : $locale;

        $shipments = [$shipment];

        $emailView = 'admin.shipments.shipments.partials.provider_request_info';

        // Campos e-mail
        $subject = transLocale('admin/email.provider-request-info.subject', $locale, ['trk' => $shipment->tracking_code]);
        $message = view($emailView, compact('locale', 'shipments', 'shipment'))->render(); 
        $message.= Email::getSignature();

        // Cria instancia de e-mail
        $email = new Email([
            'modal_title'   => 'Enviar confirmação/instruções de carga',
            'subject'       => $subject,
            'to'            => @$shipment->provider->email,
            'cc'            => null,
            'bcc'           => null,
            'message'       => $message,
        ]);

        return view('admin.emails.emails.edit', compact('email'))->render();
    }

    /**
     * Update one or more fiels for one shipment
     *
     * @param Request $request
     * @return void
     */
    public function updateFields(Request $request, $id = null)
    {

        $id = $request->id ? $request->id : $id;
        $input = $request->all();

        $allowedFields = [
            'sender_latitude',
            'sender_longitude',
            'recipient_latitude',
            'recipient_longitude',
            'vehicle',
            'date',
            'start_hour',
            'delivery_date',
            'end_hour'
        ];

        if ($request->get('source') == 'timeline') {

            $date = new Date($input['start_date']);
            $input['date']       = $date->format('Y-m-d');
            $input['start_hour'] = $date->format('H:i');

            $date = new Date($input['end_date']);
            $input['delivery_date'] = $date->format('Y-m-d');
            $input['end_hour']      = $date->format('H:i');

            $input['vehicle'] = $input['resource'] == '000' ? null : strtoupper($input['resource']);
        }

        $input = array_only($input, $allowedFields);

        $shipment = Shipment::findOrFail($id);
        $shipment->fill($input);
        $shipment->save();

        $response = [
            'result' => true,
        ];

        return response()->json($response);
    }

    public function routeDetails(Request $request){
        $timeAux = $request->get('time')/(60);
        $time = intval($request->get('time')/(60));

        if($timeAux > $time){
            $time++;
        }

        $daysWithOutInterrupt = 0;
        
        $date = Carbon::parse($request->get('start_date'))->format('Y-m-d');
        $hour = convertHoursToMinutes($request->get('start_hour'));

        $routeDetails = Map::processRoute($time, $daysWithOutInterrupt, $date, $hour);

        //dd($routeDetails);

        $data = compact(
            'routeDetails'
        );

        if($request->has('return_type')){
            $returnType = $request->get('return_type');

            if($returnType == 'modal'){
                return view('admin.trips.modals.route_details', $data)->render();
            }else if($returnType == 'routeDetails'){
                return $routeDetails;
            }else if($returnType == 'totalTime'){
                return $routeDetails['totalTime'];
            }
        }
        
        return null;
    }

}
