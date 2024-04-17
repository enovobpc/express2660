<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\BillingZone;
use App\Models\BroadcastPusher;
use App\Models\IncidenceType;
use App\Models\OperatorTask;
use App\Models\PackType;
use App\Models\Route;
use App\Models\ShipmentExpense;
use App\Models\ShipmentSchedule;
use App\Models\ShippingExpense;
use App\Models\UserWorkgroup;
use App\Models\Vehicle;
use App\Models\ShippingStatus;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Customer;
use App\Models\CustomerRecipient;
use App\Models\CustomerType;
use App\Models\User;
use App\Models\ShipmentHistory;
use App\Models\Webservice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Auth, Response, Cache, Setting, Date, Mail;

class PickupsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'collections';

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
            ->filterSources()
            ->isVisible()
            ->isCollection()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            //->isCollection()
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

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id', 'is_operator']);

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators($allOperators->filter(function ($item) {
            return $item->is_operator;
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
            ->toArray();

        $vehicles = Vehicle::listVehicles();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            //->isSeller(true)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $recipientCounties = [];
        $recipientDistrict = $request->get('fltr_recipient_district');
        if ($request->has('fltr_recipient_district')) {
            $recipientCounties = trans('districts_codes.counties.pt.' . $recipientDistrict);
        }

        $hours = listHours(10);

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
            'hours',
            'recipientCounties',
            'workgroups',
            'routes',
            'expenses',
            'sellers',
            'cargoMode',
            'customerTypes',
            'sellers'
        );

        return $this->setContent('admin.shipments.pickups.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $shipmentsController = new ShipmentsController;
        $shipment = new Shipment;
        $shipment->is_collection = 1;

        //obtem taxa combustivel
        if (Setting::get('fuel_tax')) {
            $shipment->fuel_tax = $shipment->getFuelTaxRate();
        }

        $schedule = null;
        if ($request->get('schedule', false)) {
            $schedule = new ShipmentSchedule();
            $schedule->repeat_every = 1;
            $schedule->frequency = 'week';
        }

        $services = $shipmentsController->listServices(Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->pickupAssigned()
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
            ->ordered()
            ->get();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->get();

        $packTypes = new ShipmentsController();
        $packTypes = $packTypes->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });

        $userAgencies = $agencies;
        if (!empty(Auth::user()->agencies)) {
            $userAgencies = array_intersect_key($agencies, array_flip(Auth::user()->agencies));
        }

        $providers = $shipmentsController->listProviders($allProviders);

        $provider  = $allProviders->find($shipment->provider_id);

        if ($provider) {
            $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterAgencies()
                ->whereIn('id', $provider->agencies)
                ->orderBy('code', 'asc')
                ->get());

            $providerAgencies = $agencies;
        } else {
            $providerAgencies = $userAgencies;
        }

        $hours = listHours(10);

        $senderStates    = $shipmentsController->listStates($shipment->sender_country);
        $recipientStates = $shipmentsController->listStates($shipment->recipient_country);

        $action = 'Novo Pedido de Recolha';
        $formOptions = ['route' => ['admin.pickups.store'], 'class' => 'form-horizontal form-shipment', 'method' => 'POST'];

        $data = compact(
            'shipment',
            'action',
            'formOptions',
            'providers',
            'agencies',
            'userAgencies',
            'services',
            'complementarServices',
            'providerAgencies',
            'hours',
            'packTypes',
            'senderStates',
            'recipientStates'
        );

        return view('admin.shipments.pickups.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $shipmentsController = new ShipmentsController;

        if (is_int($id)) {
            $shipment = Shipment::with('customer')
                ->filterMyAgencies()
                ->findOrfail($id);
        } else {
            $shipment = $id;
        }


        try {
            $shipment->is_collection = 1;
        } catch (\Exception $e) {
        }

        $services = $shipmentsController->listServices(Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->pickupAssigned()
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
            ->ordered()
            ->get();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->get();

        $packTypes = new ShipmentsController();
        $packTypes = $packTypes->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });

        $userAgencies = $agencies;
        if (!empty(Auth::user()->agencies)) {
            $userAgencies = array_intersect_key($agencies, array_flip(Auth::user()->agencies));
        }

        $providers = $shipmentsController->listProviders($allProviders);

        $provider  = $allProviders->find(@$shipment->provider_id);

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

        $hours = listHours(10);

        $senderStates    = $shipmentsController->listStates($shipment->sender_country);
        $recipientStates = $shipmentsController->listStates($shipment->recipient_country);

        $action = 'Editar Pedido de Recolha #' . $shipment->tracking_code;

        $formOptions = [
            'route' => ['admin.pickups.update', $shipment->id],
            'class' => 'form-horizontal form-shipment',
            'method' => 'PUT'
        ];

        $data = compact(
            'shipment',
            'action',
            'formOptions',
            'providers',
            'agencies',
            'userAgencies',
            'services',
            'complementarServices',
            'providerAgencies',
            'hours',
            'departments',
            'packTypes',
            'senderStates',
            'recipientStates'
        );


        return view('admin.shipments.pickups.edit', $data)->render();
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

        $shipmentCollection = new ShipmentsController();

        $shipment = Shipment::filterMyAgencies()
            ->isPickup()
            ->findOrNew($id);

        $input           = $request->all();
        $shipmentExists  = $shipment->exists;
        $oldOperatorId   = $shipment->operator_id;

        $service  = Service::findOrFail(@$input['service_id']);
        $customer = Customer::find(@$input['customer_id']);
        $shipment->service  = $service;
        $shipment->customer = $customer;

        if(!$service || !$customer) {
            return response()->json([
                'result'    => false,
                'syncError' => false,
                'feedback'  => 'Recolha não gravada. Serviço ou Cliente em falta.'
            ]);
        }

        $input['recipient_agency_id'] = empty($input['recipient_agency_id']) ? $input['agency_id'] : $input['recipient_agency_id'];
        $input['billing_date']        = empty($input['billing_date']) ? $input['date'] : $input['billing_date'];
        $input['provider_id']         = $request->get('provider_id', Setting::get('shipment_default_provider'));
        $input['ignore_billing']      = $request->get('ignore_billing', false);
        $input['price_fixed']         = $request->get('price_fixed', false);
        $input['without_pickup']      = $request->get('without_pickup', false);
        $input['has_return']          = $request->get('has_return', []);
        $input['optional_fields']     = $request->get('optional_fields', []);
        $input['tags']                = explode(',', $request->get('tags'));
        $input['cod']                 = $request->get('cod');
        $input['volumes']             = @$input['volumes'] ? $input['volumes'] : 1;
        $input['weight']              = forceDecimal(@$input['weight'] ? $input['weight'] : 1);
        $input['provider_weight']     = forceDecimal(@$input['provider_weight']);
        $input['has_assembly']        = $request->get('has_assembly', false);

        if(in_array('rpack',$input['has_return'])) {
            if(!in_array('rpack', $input['tags'])) {
                $input['tags'][] = 'rpack';
            }
        } else {
            array_remove_val($input['tags'], 'rpack');
        }

        if (Setting::get('shipments_round_up_weight')) {
            $input['weight']            = roundUp($input['weight']);
            $input['volumetric_weight'] = roundUp($input['volumetric_weight']);
        }

        //prepara portes
        $input['ignore_billing'] = false;
        if($input['cod'] == 'P') {
            $input['ignore_billing'] = true;
        } elseif($input['cod'] == 'D') { //portes no destino. Atualiza campos anteriores a out/2022
            $input['payment_at_recipient']      = true;
            $input['total_price_for_recipient'] = $input['billing_total'];
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
        if(@$input['pickup_date']) {
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
        if($route) {
            $input['pickup_route_id'] = @$route->id;

            $schedule = $route->getSchedule(@$input['start_hour'], @$input['end_hour']);
            if (empty($input['pickup_operator_id'])) {
                $input['pickup_operator_id'] = @$schedule['operator']['id'];
            }
        }

        //deteta as rota entrega
        $route = Route::getRouteFromZipCode($input['recipient_zip_code'], @$input['service_id'], null, 'delivery');
        if($route) {
            $input['route_id'] = $route->id;

            $schedule = $route->getSchedule(@$input['start_hour'], @$input['end_hour']);
            if (empty($input['operator_id'])) {
                $input['operator_id'] = @$schedule['operator']['id'];
            }
        }

        //gravar dados remetente
        if ($request->has('save_sender')) {
            $input['sender_id'] = $shipmentCollection->insertOrUpdateSender($input);
        }

        //gravar dados destinatário
        if ($request->has('save_recipient')) {
            $input['recipient_id'] = $shipmentCollection->insertOrUpdateRecipient($input);
        }

        //grava shipment
        if ($shipment->validate($input)) {
            $shipment->fill($input);
            $shipment->is_collection = 1;

            if ($shipment->exists) {

                $saveHistory = false;
                if ($shipment->status_id == ShippingStatus::PICKUP_REQUESTED_ID) {
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
            $shipmentCollection->storeDimensions($shipment, $input);

            //grava taxas adicionais
            $shipmentCollection->storeExpenses($shipment, $input);

            //submete via webservice
            $debug = $request->get('debug', false);
            $shipment->submitWebservice($debug);


            //atualiza taxa de recolha no envio caso tenha envio gerado
            if (!empty($shipment->children_tracking_code) && $request) {
                $geratedShipment = Shipment::where('tracking_code', $shipment->children_tracking_code)->first();
                $geratedShipment->insertOrUpdadePickupExpense($shipment);
            }

            //desconta valor de pagamento da conta
            /*$paymentResult  = $shipment->walletPayment($customer);
            $paymentSuccess = $paymentResult['success'];
            $walletPayment  = $paymentResult['walletPayment'];
            if(!$paymentSuccess) {
                $shipment->status_id = ShippingStatus::PAYMENT_PENDING_ID;
                $submitWebservice    = false;
            }*/

            //detect ignore billing
            //CustomerBilling::detectCovenant($shipment);


            //envia e-mail de notificação
            if (!$shipmentExists && $request->get('send_email') && !empty($input['recipient_email'])) {
                $shipment->sendEmail();
            }

            //envia SMS de notificação
            if ($request->get('sms')) {
                try {
                    $shipment->sendSms(true);
                } catch (\Exception $e) {}
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
                foreach ($input['addr'] as $hash => $adicionalAddress) {
                    unset($shipment->provider_weight);
                    $newShipment = $shipment->replicate();
                    $newShipment->fill($adicionalAddress);
                    $newShipment->is_collection  = 1;
                    $newShipment->tracking_code  = null;
                    $newShipment->total_price    = 0;
                    $newShipment->total_expenses = 0;
                    $newShipment->charge_price   = null;
                    $newShipment->recipient_id   = null;
                    $newShipment->parent_tracking_code = $shipment->tracking_code;
                    $newShipment->type           = Shipment::TYPE_MASTER;
                    $newShipment->children_tracking_code = null;
                    $newShipment->children_type  = null;
                    $newShipment->setTrackingCode();
                }

                $shipment->update([
                    'children_type' => Shipment::TYPE_MASTER
                ]);
            }

            //PRINT DOCUMENTS
            $printGuide = $html = false;
            if ($request->has('print_guide')) {
                $html  = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printGuide = route('admin.printer.shipments.transport-guide', $shipment->id);
            }

            if ($debug) {
                $debug = asset('/dumper/request.txt');
            }

            //PREPARE RETURN AND FEEDBACK
            if (!isset($feedback) && empty($feedback)) {
                $feedback = 'Envio gravado com sucesso.';
            }

            if ($errorMsg = $shipment->hasSyncError()) {
                $result = [
                    'result'     => false,
                    'syncError'  => true,
                    'feedback'   => $errorMsg,
                    'debug'      => $debug,
                    'html'       => $html,
                    'trkid'      => $shipment->id
                ];
            } else {
                $result = [
                    'result'     => true,
                    'syncError'  => false,
                    'feedback'   => $feedback,
                    'printGuide' => $printGuide,
                    'printLabel' => false,
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
     * Create shipment from collection
     *
     * @param type $originalShipment
     * @param type $data
     */
    public function createShipment(Request $request, $id)
    {
        $shipmentsController = new ShipmentsController();

        $originalShipment = Shipment::with('service')
            ->filterAgencies()
            ->findOrfail($id);

        $shipment = $originalShipment->replicate();
        $shipment->reset2replicate();
        $shipment->tracking_code   = null;
        $shipment->parent_tracking_code = $originalShipment->tracking_code;
        $shipment->type            = Shipment::TYPE_PICKUP;
        $shipment->obs             = $shipment->obs_delivery;
        $shipment->obs_internal    = '';
        $shipment->date            = date('Y-m-d');
        $shipment->pack_dimensions = $originalShipment->pack_dimensions;
        $shipment->is_collection   = 0;
        $shipment->price_fixed     = 0;
        $shipment->ignore_billing  = 0;
        $shipment->convert_from_collection = 1;

        if(@$shipment->customer->vat == 'A85508299') { //sending. repoe os valores originais
            $shipment->provider_cargo_agency     = $originalShipment->provider_cargo_agency;
            $shipment->provider_sender_agency    = $originalShipment->provider_sender_agency;
            $shipment->provider_recipient_agency = $originalShipment->provider_recipient_agency;
        }

        $prices = Shipment::calcPrices($shipment);
        if(@$prices['fillable']) {
            $shipment->fill($prices['fillable']);

            //adiciona taxas
            foreach ($prices['expenses'] as $expense) {
                $shippingExpense = new ShippingExpense;
                $shippingExpense->pivot = (new ShipmentExpense())->fill($expense);
                $shipment->expenses->push($shippingExpense);
            }
        }

        if($originalShipment->total_price_after_pickup > 0.00) {
            $shipment->shipping_price = $originalShipment->total_price_after_pickup;
            $shipment->price_fixed    = !empty($originalShipment->total_price_after_pickup) && $originalShipment->total_price_after_pickup ? true : false;
        }

        $shipment->exists = true;
        return $shipmentsController->create($request, $shipment);
    }


    /**
     * Create shipment from collection
     *
     * @param type $originalShipment
     * @param type $data
     */
    public function convertToShipment(Request $request, $id)
    {
        $pickup = Shipment::findOrFail($id);
        $pickup->is_collection = 0;
        $pickup->tracking_code = null;
        $pickup->setTrackingCode();

        return Redirect::back()->with('success', 'Recolha convertida em envio com sucesso. TRK: ' . $pickup->tracking_code);
    }
}
