<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Admin\Shipments\PickupsController;
use App\Models\CustomerEcommerceGateway;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\BroadcastPusher;
use App\Models\CttDeliveryManifest;
use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerBilling;
use App\Models\CustomerMessage;
use App\Models\FileRepository;
use App\Models\GatewayPayment\Base;
use App\Models\IncidenceType;
use App\Models\Logistic\CartProduct;
use App\Models\Logistic\Product;
use App\Models\OperatorTask;
use App\Models\PackType;
use App\Models\PickupPoint;
use App\Models\Route;
use App\Models\ShipmentExpense;
use App\Models\ShipmentPackDimension;
use App\Models\ShipmentPallet;
use App\Models\ShippingExpense;
use App\Models\Sms\Sms;
use App\Models\User;
use App\Models\ZipCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\ShippingStatus;
use App\Models\Shipment;
use App\Models\Provider;
use App\Models\Service;
use App\Models\CustomerRecipient;
use App\Models\Webservice;
use App\Models\ShipmentHistory;
use Response, DB, View, Cache, Setting, Excel, Date, Log, File;

class ShipmentsController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $customer = Auth::guard('customer')->user();
        if (empty($customer->customer_id)) {
            $enabledServices = $customer->enabled_services;
        } else {
            $enabledServices = $customer->parent_customer->enabled_services;
        }

        $enabledServices = empty($enabledServices) ? [] : $enabledServices;

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->whereIn('id', $enabledServices)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $providers = null;
        if (!empty($customer->enabled_providers) || Setting::get('customer_show_provider_trk')) {

            $providersArr = $customer->enabled_providers;
            if (!$providersArr) {
                $providersArr = Shipment::where('customer_id', $customer->id)
                    ->groupBy('provider_id')
                    ->pluck('provider_id')
                    ->toArray();
            }

            $providers = Provider::whereIn('id', $providersArr)
                ->pluck('name', 'id')
                ->toArray();
        }

        $customerCtt = false;
        if (config('app.source') == 'entregaki') {
            $customerCtt = true;
        }

        $isShippingBlocked = $customer->is_shipping_blocked;

        $messagesPopup = CustomerMessage::getUnread($customer->id);

        $hasEcommerceGateways = (bool) CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->first();

        $data = compact(
            'services',
            'status',
            'customerCtt',
            'oldestUnpaidInvoice',
            'messagesPopup',
            'providers',
            'hasEcommerceGateways',
            'isShippingBlocked'
        );

        return $this->setContent('account.shipments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        /* $licenseStatus = File::exists(storage_path() . '/license.json') ? '0' : '1';
        if(!$licenseStatus) {
            return view('account.partials.modals.license_blocked')->render();
        } */

        if ($request->cart && $request->shipment) {
            $customer = Customer::find(@$request->shipment->customer_id);
        } else {
            $customer = Auth::guard('customer')->user();
        }

        //check wallet account
        if (hasModule('account_wallet') && !$customer->is_mensal && $customer->wallet_balance < Setting::get('wallet_min_amount')) {
            return view('account.partials.modals.without_wallet', compact('customer'))->render();
        }

        $isShippingBlocked = $customer->is_shipping_blocked;
        if($isShippingBlocked) {
            return view('account.partials.modals.blocked', compact('isShippingBlocked'))->render();
        }

        $parentCustomer = null;
        if (!empty($customer->customer_id)) {
            $parentCustomer = Customer::where('id', $customer->customer_id)->first();
            $customer->enabled_services  = $parentCustomer->enabled_services;
            $customer->enabled_providers = $parentCustomer->enabled_providers;
        }

        $serviceId = null;
        if (!empty($customer->default_service) || !empty(Setting::get('customers_default_service'))) {
            $serviceId = $customer->default_service ? $customer->default_service : Setting::get('customers_default_service');
        }

        $shipment = new Shipment;
        $fillDefaultValues = false;

        if($request->has('source')) {
            if($request->get('source') == 'budgeter') { //envio a ser criado a partir do orçamentador
                $packDimensions = $request->get('dimensions', []);

                $shipment = new Shipment;
                $shipment->exists = true;
                $shipment->fill($request->all());
                $shipment->pack_dimensions      = collect($packDimensions);
                $shipment->service_id           = $request->service;
                $shipment->sender_name          = $customer->name;
                $shipment->sender_address       = $customer->address;
                $shipment->sender_zip_code      = $request->sender_zip_code;
                $shipment->sender_city          = $request->sender_city;
                $shipment->sender_country       = $request->sender_country;
                $shipment->sender_phone         = $customer->mobile ? $customer->mobile : $customer->phone;
                $shipment->recipient_zip_code   = $request->recipient_zip_code;
                $shipment->recipient_city       = $request->recipient_city;
                $shipment->recipient_country    = $request->recipient_country;
                $shipment->obs                  = $customer->obs_shipments;
                if (!in_array($shipment->service_id, [$customer->enabled_services])) {
                    $enabledServices = $customer->enabled_services;
                    $enabledServices[] = $shipment->service_id;
                    $customer->enabled_services = $enabledServices;
                }

            } elseif ($request->get('source') == 'cart') { //envio criado a partir do carrinho de nova encomenda.
                $shipment = $request->shipment; //se entra uma collection, tem de vir do request conforme aqui está.

                $packDimensions = [];
                $qtyTotal = 0;
                $totalWeight = 0;
                foreach ($shipment->box_type as $key => $boxType) {

                    $packDim = new ShipmentPackDimension();
                    $packDim->qty = @$shipment->qty[$key];
                    $qtyTotal += @$shipment->qty[$key];
                    $packDim->type = $boxType;
                    $packDim->length = @$shipment->length[$key];
                    $packDim->width = @$shipment->width[$key];
                    $packDim->height = @$shipment->height[$key];
                    $packDim->weight = @$shipment->box_weight[$key];
                    if (!empty($shipment->box_weight[$key])) {
                        $totalWeight     += @$shipment->box_weight[$key] * @$shipment->qty[$key];
                    }
                    $packDim->sku = @$shipment->sku[$key]  ?? '';
                    $packDim->lote = @$shipment->lote[$key]  ?? '';
                    $packDim->stock = @$shipment->stock[$key]  ?? '';
                    $packDim->serial_no = @$shipment->serial_no[$key] ?? '';
                    $packDim->description = @$shipment->box_description[$key] ?? '';

                    $packDim->product_id = @$shipment->product[$key]  ?? '';

                    $packDim->fator_m3 = @$shipment->fator_m3[$key]  ?? '';

                    $packDimensions[] = $packDim;
                }

                if ($totalWeight != 0) {
                    $shipment->weight  = $totalWeight;
                } else {
                    $shipment->weight = 1;
                }

                $shipment->volumes = $qtyTotal;
                $shipment->pack_dimensions = collect($packDimensions);
            } elseif ($request->get('source') == 'ecommerce_gateway') {
                $fillDefaultValues = true;

                $packDimensions = [];
                foreach ($request->get('pack_dimensions', []) as $packDimension) {
                    $packDim = new ShipmentPackDimension();
                    $packDim->fill($packDimension);
                    $packDimensions[] = $packDim;
                }

                $shipment->pack_dimensions = collect($packDimensions);
            }
        }

        if (!$request->has('source') || $fillDefaultValues) {
            //criação normal de novo envio
            $shipment->is_collection    = false;
            $shipment->service_id       = $serviceId;
            $shipment->sender_name      = $customer->name;
            $shipment->sender_address   = $customer->address;
            $shipment->sender_zip_code  = $customer->zip_code;
            $shipment->sender_state     = $customer->state;
            $shipment->sender_city      = $customer->city;
            $shipment->sender_country   = empty($customer->country) ? Setting::get('app_country') : $customer->country;
            $shipment->sender_phone     = $customer->mobile ? $customer->mobile : $customer->phone;
            $shipment->obs              = $customer->obs_shipments;
            $shipment->sender_country    = Setting::get('app_country'); //default
            $shipment->recipient_country = Setting::get('app_country');
        }

        if (empty($customer->enabled_services)) {
            $services = null;
            $servicesCollection = null;
        } else {
            $allServices = Service::whereIn('id', $customer->enabled_services)
                ->where('is_collection', false)
                ->filterHorary()
                ->ordered()
                ->get();

            $services = $this->listServices($allServices, true);
        }

        if (empty($customer->enabled_providers)) {
            $providers = null;
        } else {
            $providers = Provider::whereIn('id', $customer->enabled_providers)
                ->pluck('name', 'id')
                ->toArray();
        }

        $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where('account_complementar_service', 1)
            ->ordered()
            ->get();

        $complementarServicesInputTypes = array_unique($complementarServices->pluck('form_type_account')->toArray());
        $allInputAreCheckboxes = count($complementarServicesInputTypes) == 1 && $complementarServicesInputTypes[0] == 'checkbox';

        $packTypes = $this->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        //check if exceeded
        $exceeded = false;
        if (Setting::get('shipments_daily_limit_hour') || $customer->shipments_daily_limit_hour) {
            $limitHour = $customer->shipments_daily_limit_hour ? $customer->shipments_daily_limit_hour : Setting::get('shipments_daily_limit_hour');
            $now = Date::now();
            $limit = new Date(date('Y-m-d') . ' ' . $limitHour . ':00');
            $exceeded = $now->gt($limit);
        }

        $shipmentDate = Date::today()->format('Y-m-d');
        if ($exceeded) {
            $shipmentDate = getNextUsefullDate($now->addDays(1));
        }

        $hours = listHours(5);

        $defaultPrint = Setting::get('shipment_print_default_customers');
        $defaultPrint = empty($customer->default_print) ? $defaultPrint : $customer->default_print;

        $senderStates    = $this->listStates($shipment->sender_country);
        $recipientStates = $this->listStates($shipment->recipient_country);

        $action = trans('account/shipments.modal-shipment.create-shipment');
        $formOptions = array('route' => array('account.shipments.store'), 'class' => 'form-horizontal form-shipment', 'method' => 'POST');

        $compact = compact(
            'shipment',
            'action',
            'formOptions',
            'customer',
            'services',
            'servicesCollection',
            'providers',
            'complementarServices',
            'shipmentDate',
            'exceeded',
            'hours',
            'defaultPrint',
            'packTypes',
            'senderStates',
            'recipientStates',
            'allInputAreCheckboxes'
        );

        if (($customer->id == '1443' || $customer->customer_id == '1443') && config('app.source') == 'corridadotempo') { //PT TELECOM
            return view('account.shipments.edit_ptelecom', $compact)->render();
        }

        if(0) {
            return view('account.shipments.edit_industries', $compact)->render();
        }

        return view('account.shipments.edit', $compact)->render();
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
        return $this->update($request, $id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $customer = Auth::guard('customer')->user();
        $locale = $customer->locale;

        $shipment = Shipment::with('expenses', 'status', 'last_history', 'service')
            ->whereHas('status', function ($q) {
                $q->isPublic();
            })
            ->with(['history' => function ($q) {
                $q->with('operator', 'status');
                $q->whereHas('status', function ($q) {
                    $q->isPublic();
                });
                $q->orderBy('created_at', 'desc');
                $q->orderBy('id', 'desc');
            }])
            ->filterCustomer();

        if (strlen($id) == '12') {
            $shipment = $shipment->where('tracking_code', $id)->first();
        } else {
            $shipment = $shipment->findOrFail($id);
        }

        $shipmentHistory = $shipment->history;

        $complementarServices = ShippingExpense::filterSource()
            ->isComplementarService()
            ->get(['id', 'name']);

        $shipmentAttachments = FileRepository::where('customer_visible', 1)
            ->where('source_id', $shipment->id)
            ->where('source_class', 'Shipment')
            ->orderBy('name', 'asc')
            ->get();

        $packTypes = $this->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $data = compact(
            'shipment',
            'shipmentHistory',
            'complementarServices',
            'locale',
            'packTypes',
            'shipmentAttachments',
            'customer'
        );

        return view('account.shipments.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $customer = Auth::guard('customer')->user();

        $parentCustomer = null;
        if (!empty($customer->customer_id)) {
            $parentCustomer = Customer::where('id', $customer->customer_id)->first();
            $customer->enabled_services = $parentCustomer->enabled_services;
        }

        $shipment = Shipment::filterCustomer()->findOrfail($id);

        if (empty($customer->enabled_services)) {
            $services = null;
            $servicesCollection = null;
        } else {
            $allServices = Service::whereIn('id', $customer->enabled_services)
                ->where('is_collection', false)
                ->filterHorary()
                ->ordered()
                ->get();

            $services = $this->listServices($allServices, $allServices->count() > 1 ? true : false);
        }

        if (empty($customer->enabled_providers)) {
            $providers = null;
        } else {
            $providers = Provider::whereIn('id', $customer->enabled_providers)
                ->pluck('name', 'id')
                ->toArray();
        }

        $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where('account_complementar_service', 1)
            ->ordered()
            ->get();

        $complementarServicesInputTypes = array_unique($complementarServices->pluck('form_type_account')->toArray());
        $allInputAreCheckboxes = count($complementarServicesInputTypes) == 1 && $complementarServicesInputTypes[0] == 'checkbox';

        $packTypes = $this->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        if ($shipment->recipient_pudo_id) {
            $pickupPoints = $this->listPudoPoints(PickupPoint::remember(config('cache.query_ttl'))
                ->cacheTags(PickupPoint::CACHE_TAG)
                ->filterSource()
                ->isActive()
                ->whereIn('provider_id', $customer->enabled_pudo_providers)
                ->get());
        }

        $exceeded     = false;
        $shipmentDate = $shipment->date;
        $hours        = listHours(5);

        $senderStates    = $this->listStates($shipment->sender_country);
        $recipientStates = $this->listStates($shipment->recipient_country);

        $defaultPrint = Setting::get('shipment_print_default_customers');
        $defaultPrint = empty($customer->default_print) ? $defaultPrint : $customer->default_print;

        $action       = trans('account/shipments.modal-shipment.edit-shipment');
        $formOptions  = array('route' => array('account.shipments.update', $id), 'class' => 'form-horizontal form-shipment', 'method' => 'PUT');

        $compact = compact(
            'shipment',
            'action',
            'services',
            'servicesCollection',
            'formOptions',
            'customer',
            'providers',
            'complementarServices',
            'shipmentDate',
            'exceeded',
            'hours',
            'defaultPrint',
            'packTypes',
            'senderStates',
            'recipientStates',
            'pickupPoints',
            'allInputAreCheckboxes'
        );

        if (($customer->id == '1443' || $customer->customer_id == '1443') && config('app.source') == 'corridadotempo') { //PT TELECOM
            return view('account.shipments.edit_ptelecom', $compact)->render();
        }

        if(0) {
            return view('account.shipments.edit_industries', $compact)->render();
        }

        return view('account.shipments.edit', $compact)->render();
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

        //OBRIGAR DIMENSÕES

        $input    = $request->all();
        $customer = Auth::guard('customer')->user();

        $mainCustomer = $customer;
        if ($customer->customer_id) { //subconta
            $mainCustomer = Customer::find($customer->customer_id);
        }

        if (empty($input['service_id'])) {
            $input['service_id'] = $customer->default_service ?? Setting::get('customers_default_service');
            $input['service_id'] = empty($input['service_id']) ? $input['service_id'] : null;
        }

        $service  = Service::find($input['service_id']);

        $shipment = Shipment::filterCustomer()->findOrNew($id);
        $shipmentExists = $shipment->exists;

        if ($shipmentExists && empty($service)) {
            $service = $shipment->service;
        }

        //verifica se o envio pode ser editado
        if (($shipmentExists && !in_array($shipment->status_id, Setting::get('services_can_delete'))) || $shipment->submited_at) {
            $result = [
                'result'   => false,
                'feedback' => 'O estado do serviço não permite a sua edição.'
            ];
            return Response::json($result);
        }

        if (empty($service)) {
            $result = [
                'result'   => false,
                'feedback' => 'Não possui serviços contratados. Contacte o seu comercial.'
            ];
            return Response::json($result);
        }

        $serviceProvider    = @$service->provider_id;
        $autoWalletPayment  = false; //pagamento automático pela wallet (nao perguntas outros métodos)
        $isPaid             = $mainCustomer->payment_method == 'wallet' && !$shipment->ignore_billing && !$shipment->invoice_id && empty($request->get('cod')) ? false : true;
        $submitWebservice   = true;
        $shipment->service  = $service;
        $shipment->customer = $mainCustomer;

        $input['customer_id']               = $mainCustomer->id;
        $input['department_id']             = $customer->customer_id ? $customer->id : @$input['department_id'];
        $input['requested_by']              = $customer->customer_id ? $customer->customer_id : $customer->id;
        $input['agency_id']                 = $customer->agency_id;
        $input['sender_agency_id']          = empty($input['sender_agency_id']) ? $input['agency_id'] : $input['sender_agency_id'];
        $input['recipient_agency_id']       = empty($input['recipient_agency_id']) ? $input['agency_id'] : $input['recipient_agency_id'];
        $input['provider_id']               = $request->get('provider_id', Setting::get('shipment_default_provider'));
        $input['without_pickup']            = $request->get('without_pickup', false);
        $input['has_return']                = $request->get('has_return', []);
        $input['tags']                      = explode(',', $request->get('tags'));
        $input['cod']                       = $request->get('cod');
        $input['volumes']                   = @$input['volumes'] ? $input['volumes'] : 1;
        $input['weight']                    = forceDecimal(@$input['weight'] ? $input['weight'] : 1);
        $input['has_assembly']              = $request->get('has_assembly', false);
        $input['start_hour']                = $request->get('start_hour_pickup');
        $input['customer_weight']           = $request->get('weight');

        if (config('app.source') == 'corridadotempo' && $mainCustomer->id == '1443') {
            $input['custom_fields'] = array_filter($input['custom_fields']);
            $lines = implode(',<br/>', $input['custom_fields']);
            $input['reference'] = br2nl($lines);
        }

        //retorno
        if (in_array('rpack', $input['has_return']) && @$service->allow_return) {
            if (!in_array('rpack', $input['tags'])) {
                $input['tags'][] = 'rpack';
            }
        } else {
            array_remove_val($input['tags'], 'rpack');
        }

        if (Setting::get('shipments_round_up_weight')) {
            $input['weight']            = roundUp($input['weight']);
            $input['volumetric_weight'] = roundUp($input['volumetric_weight']);
        }

        //determina informação do codigo postal de destino
        $fullZipCode  = @$input['recipient_zip_code'];
        $zipCodeParts = explode('-', $fullZipCode);
        $zipCode4     = $zipCodeParts[0];
        $zipCode = AgencyZipCode::where(function ($q) use ($fullZipCode, $zipCode4) {
            $q->where('zip_code', $zipCode4);
            $q->orWhere('zip_code', $fullZipCode);
        })
            ->where('country', @$input['recipient_country'])
            ->orderBy('zip_code', 'desc')
            ->first();
        $zipCodeProvider = @$zipCode->provider_id;


        //determina qual o fornecedor a usar
        if (!$shipment->hasSync()) {
            if (!empty(@$customer->enabled_providers) && count(@$customer->enabled_providers) == 1) {
                //regra 1: definido na ficha de cliente que só envia por 1 fornecedor
                $input['provider_id'] = @$customer->enabled_providers[0];
                $rule = 1;
            } elseif (!empty(@$customer->enabled_providers) && $request->has('provider_id') && $request->get('provider_id')) {
                //regra 2: o cliente escolhe o fornecedor na janela de envio
                $input['provider_id'] = $request->get('provider_id');
                $rule = 2;
            } else if ($serviceProvider) {
                //regra 3: serviço obriga a usar um serviço
                $input['provider_id'] = $serviceProvider;
                $rule = 3;
            } else if ($zipCodeProvider) {
                //regra 4: codigo postal destino obriga a usar serviço
                $input['provider_id'] = $zipCodeProvider;
                $rule = 4;
            } else {
                //default: fornecedor por defeito
                $input['provider_id'] = Setting::get('shipment_default_provider');
                $rule = 5;
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
            $shipment->created_by_customer = 1;
            $shipment->is_collection       = 0;

            if ($shipment->exists) {
                $saveHistory = false;
                if ($shipment->hasSyncError()) {
                    $shipment->resetWebserviceError();
                }
            } else {
                $saveHistory = true;
                $shipment->status_id = $shipment->getDefaultStatus(true);
            }

            $manualExpenses = [];
            $expensesVals = array_filter($request->get('optional_fields', []));
            $expensesIds  = array_keys($expensesVals);
            $expensesIds  = array_filter($expensesIds);
            if (!empty($expensesIds)) {
                //para cada despesa ativa, vai calcular individualmente o seu preço e adiciona o preço a variavel manualExpenses
                $expenses = ShippingExpense::whereIn('id', $expensesIds)->get();

                foreach ($expenses as $expense) {
                    $expense->qty   = $expensesVals[$expense->id];
                    $expenseDetails = $shipment->calcExpensePrice($expense);

                    if (@$expenseDetails['fillable']) {
                        $manualExpenses[] = $expenseDetails['fillable'];
                    }
                }
            }

            //calcula e obtem preços do envio
            $prices = Shipment::calcPrices($shipment, true, $manualExpenses);
            if (@$prices['fillable']) {
                $shipment->fill(@$prices['fillable']);

                //prepara portes
                if ($input['cod'] == 'D' || $input['cod'] == 'S') {
                    $shipment->payment_at_recipient      = true;
                    $shipment->total_price_for_recipient = @$prices['fillable']['billing_subtotal'];
                } else {
                    $shipment->payment_at_recipient      = false;
                    $shipment->total_price_for_recipient = null;
                }
            }

            //preenche informação de recolha
            $shipment->shipping_date      = @$prices['pickup']['shipping_date'];
            $shipment->pickup_operator_id = @$prices['pickup']['operator_id'];
            $shipment->pickup_route_id    = @$prices['pickup']['route_id'];

            //preenche informação de entrega
            $shipment->delivery_date = @$prices['delivery']['delivery_date'];
            $shipment->operator_id   = @$prices['delivery']['operator_id'];
            $shipment->route_id      = @$prices['delivery']['route_id'];


            //força operador se na ficha de cliente estiver definido um motorista associado
            if ($customer->operator_id) {
                $input['pickup_operator_id'] = @$customer->operator_id;
                $input['operator_id']        = @$customer->operator_id;
                $input['status_id']          = ShippingStatus::PENDING_OPERATOR;
            }

            //define data faturação
            if (empty($shipment->invoice_doc_id)) {
                $input['billing_date'] = $input['date'];
            }

            //grava e atribui código de envio
            unset($shipment->service, $shipment->customer, $shipment->original_provider_id, $shipment->coefficient_m3, $shipment->pack_dimensions);
            $shipment->setTrackingCode();

            //grava dimensões e mercadoria
            $shipmentCollection = new ShipmentsController();
            $shipmentCollection->storeDimensions($shipment, $input);
            $shipment->load('pack_dimensions');


            //grava taxas adicionais
            if (@$prices['expenses']) {
                $shipment->storeExpenses($prices);
            }

            //desconta valor de pagamento da conta
            $paymentSuccess = true;
            if ($autoWalletPayment) {
                $paymentResult  = $shipment->walletPayment($customer);
                $paymentSuccess = $paymentResult['success'];
                $walletPayment  = $paymentResult['walletPayment'];
                if ($paymentSuccess) {
                    $isPaid = true;
                }
            }

            //submete via webservice
            if ($submitWebservice && $isPaid) {
                $shipment->submitWebservice();
            }

            //grava historico envio
            if ($saveHistory && $isPaid) {
                $history = new ShipmentHistory();
                $history->status_id   = $shipment->status_id;
                $history->shipment_id = $shipment->id;
                $history->agency_id   = $shipment->agency_id;
                $history->operator_id = $shipment->operator_id;
                $history->vehicle     = $shipment->vehicle;
                $history->trailer     = $shipment->trailer;
                $history->save();
            }
            //grava histórico caso não tenha pagamento
            elseif ($saveHistory && !$isPaid) {
                $history = new ShipmentHistory();
                $history->status_id   = ShippingStatus::PENDING_ID;
                $history->shipment_id = $shipment->id;
                $history->save();

                $history = new ShipmentHistory();
                $history->status_id   = ShippingStatus::PAYMENT_PENDING_ID;
                $history->shipment_id = $shipment->id;
                $history->save();

                $shipment->update(['status_id' => $history->status_id]);
            }


            //ACTIVOS 24 CART PRODUCTS
            if (config('app.source') == 'activos24') {
                if (!empty($input['reference'])) {
                    $customerAuth = Auth::guard('customer')->user();

                    $cartProducts = CartProduct::where('reference', $input['reference'])->get();
                    if (!$cartProducts->isEmpty()) {
                        foreach ($cartProducts as $cartProduct) {
                            $cartProduct->shipment_id = $shipment->id;
                            $cartProduct->status      = 'accept';
                            $cartProduct->closed      = 1;
                            $cartProduct->accepted_by = $customerAuth->id;
                            $cartProduct->save();
                        }
                    }
                }
            }

            //envia e-mail de notificação
            if (!$shipmentExists && $isPaid && $request->get('send_email') && !empty($input['recipient_email'])) {
                $shipment->sendEmail();
            }

            //envia SMS de notificação
            if ($request->get('send_sms') && $isPaid) {
                try {
                    $shipment->sendSms(true);
                } catch (\Exception $e) {
                }
            }


            //envia notificação para funcionários escritorio
            if (!$shipmentExists && !Setting::get('notification_force_disable')) {
                $shipment->setNotification(BroadcastPusher::getGlobalChannel());
            }

            if ($isPaid) {
                $shipment->notifyOperators();
            }

            // Handle ecommerce shipment created
            if ($shipment->ecommerce_gateway_id && $shipment->ecommerce_gateway_order_code) {
                $customerEcommerceGateway = CustomerEcommerceGateway::find($shipment->ecommerce_gateway_id);

                if ($customerEcommerceGateway) {
                    $gateway = new \App\Models\EcommerceGateway\Base($customerEcommerceGateway);
                    $gateway->handleShipmentCreated($shipment);
                }
            }

            //cria shipping order (logistica)
            $logisticError = false;
            if (hasModule('logistic') && (isset($input['length']) || isset($input['box_description']))) {
                $shipment->customer = $customer;
                $result = $shipment->storeShippingOrder();
                if (!$result['result']) {
                    $logisticError = $result['feedback'];
                }
            }

            //PRINT DOCUMENTS
            $printGuide = $printLabel = $printCmr = $html = false;
            if ($request->has('print_guide') && $isPaid) {
                $html       = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printGuide = route('account.shipments.get.guide', $shipment->id);
            }

            //PRINT CMR
            if ($request->has('print_cmr') && $isPaid) {
                $html     = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printCmr = route('account.shipments.get.cmr', $shipment->id);
            }

            //PRINT LABEL
            if ($request->has('print_label') && $isPaid) {
                $html       = view('admin.shipments.shipments.modals.popup_denied')->render();
                $printLabel = route('account.shipments.get.labels', $shipment->id);
            }


            //PREPARE RETURN AND FEEDBACKS
            if (!isset($feedback) && empty($feedback)) {
                $feedback =  $feedback = trans('account/shipments.feedback.update.success');
            }

            $errorMsg = $shipment->hasSyncError();
            if (($errorMsg && Setting::get('customers_show_webservice_errors')) || $logisticError) {
                $result = [
                    'result'     => false,
                    'syncError'  => true,
                    'trkid'      => $shipment->id,
                    'feedback'   => $errorMsg ? $errorMsg : $logisticError,
                    'html'       => $html,
                    'trkid'      => $shipment->id,
                    'payment'    => $paymentSuccess,
                    'subtotal'   => number($shipment->billing_subtotal),
                    'vat'        => number($shipment->billing_vat),
                    'total'      => number($shipment->billing_total),
                    'wallet'     => money(@$walletPayment['wallet']),
                    'isPaid'     => $isPaid,
                ];
            } else {
                $result = [
                    'result'     => true,
                    'syncError'  => false,
                    'trkid'      => $shipment->id,
                    'feedback'   => $feedback,
                    'printGuide' => $printGuide,
                    'printCmr'   => $printCmr,
                    'printLabel' => $printLabel,
                    'html'       => $html,
                    'payment'    => $paymentSuccess,
                    'subtotal'   => number($shipment->billing_subtotal),
                    'vat'        => number($shipment->billing_vat),
                    'total'      => number($shipment->billing_total),
                    'wallet'     => money(@$walletPayment['wallet']),
                    'isPaid'     => $isPaid
                ];
            }
        } else {
            $result = [
                'result'    => false,
                'feedback'  => $shipment->errors()->first()
            ];
        }

        return response()->json($result);
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
                        'responsable' => @$input['sender_attn'],
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $editableStatus = Setting::get('services_can_delete');
        $editableStatus[] = ShippingStatus::PAYMENT_PENDING_ID;

        $shipment = Shipment::filterCustomer()
            ->whereIn('status_id', $editableStatus)
            ->whereId($id)
            ->first();

        if (!$shipment) {
            return Redirect::back()->with('error', trans('account/shipments.feedback.destroy.error'));
        }

        // Remove shipment from Operator Task
        $task = OperatorTask::filterSource()
            ->where('customer_id', $shipment->customer_id)
            ->where('shipments', 'LIKE', "%{$shipment->id}%")
            ->first();

        if ($task) {
            $task->removeShipment($shipment->id);
        }
        //--

        //delete from provider
        if ($shipment->hasSync()) {
            try {
                $webservice = new Webservice\Base();
                $result = $webservice->deleteShipment($shipment, null);
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }

        //repoe saldo em conta de cliente
        if ($shipment->ignore_billing && @$shipment->customer->payment_method == 'wallet') {
            $shipment->walletRefund($shipment->customer);
        }

        $result = $shipment->delete();

        if (!$result) {
            return Redirect::back()->with('error', trans('account/shipments.feedback.destroy.error'));
        }

        return Redirect::route('account.shipments.index')->with('success', trans('account/shipments.feedback.destroy.success'));
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $locale = $customer->locale;
        $nameTrans = 'name';
        if (in_array($locale, ['en', 'fr', 'es'])) {
            $nameTrans = 'name_' . $locale;
        }

        $isPickup = $request->get('pickup', 0);

        $data = Shipment::with(['service' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(Service::CACHE_TAG);
        }])
            ->with(['status' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
            }])
            ->where('is_collection', $isPickup);

        if (config('app.source') == 'ontimeservices') {
            $data = $data->whereNull('type');
        }

        $data = $data->filterCustomer()
            ->select();

        if ($customer->hide_old_shipments && @$customer->login_created_at) {
            $data = $data->where('date', '>=', $customer->login_created_at->format('Y-m-d'));
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter closed date
        $value = $request->get('closed');
        if ($request->has('closed')) {
            if ($value) {
                $data = $data->whereNotNull('closed_at');
            } else {
                $data = $data->whereNull('closed_at');
            }
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        /*//filter is closed
        $value = $request->get('closed');
        if ($request->has('closed')) {
            $data = $data->where('is_closed', $value);
        }*/

        //filter service
        $value = $request->get('service');
        if ($request->has('service')) {
            $data = $data->where('service_id', $value);
        }

        //filter printed label
        $value = $request->label;
        if ($request->has('printed')) {
            $data = $data->where('is_printed', $value);
        }

        //filter sender country
        $value = $request->sender_country;
        if ($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->recipient_country;
        if ($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
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

        //filter label
        $value = $request->label;
        if ($request->has('label')) {
            $data = $data->where('is_printed', $value);
        }

        return Datatables::of($data)
            ->edit_column('id', function ($row) use ($nameTrans) {
                return view('account.shipments.datatables.tracking', compact('row', 'nameTrans'))->render();
            })
            ->add_column('reference_show', function ($row) {
                return view('account.shipments.datatables.reference', compact('row'))->render();
            })
            ->edit_column('children_tracking_code', function ($row) {
                return view('account.shipments.datatables.parent_tracking', compact('row'))->render();
            })
            ->edit_column('service_id', function ($row) {
                return view('account.shipments.datatables.service', compact('row'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('account.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('account.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('shipping_date', function ($row) {
                return view('account.shipments.datatables.info', compact('row'))->render();
            })
            ->edit_column('volumes', function ($row) use ($packTypes) {
                return view('account.shipments.datatables.volumes', compact('row', 'packTypes'))->render();
            })
            ->edit_column('total_price', function ($row) use ($customer) {
                return view('account.shipments.datatables.price', compact('row', 'customer'))->render();
            })
            ->add_column('select', function ($row) use ($customer) {
                return view('account.shipments.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) use ($customer) {
                if ($row->is_collection) {
                    return view('account.shipments.datatables.actions_pickup', compact('row', 'customer'))->render();
                }
                return view('account.shipments.datatables.actions', compact('row', 'customer'))->render();
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @param $shipmentId
     * @return string
     * @throws \Throwable
     */
    public function editEmail(request $request, $shipmentId)
    {

        $shipment = Shipment::filterCustomer()
            ->where('id', $shipmentId)
            ->firstOrFail();

        return view('account.shipments.modals.send_email', compact('shipment'))->render();
    }

    /**
     * @param Request $request
     * @param $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNewEmail(Request $request, $shipmentId)
    {

        $email = $request->email;

        $shipment = Shipment::filterCustomer()
            ->find($shipmentId);

        $shipment->recipient_email = $email;
        $result = $shipment->sendEmail();

        if ($result) {
            if ($request->ajax()) {
                return response()->json([
                    'result'   => true,
                    'feedback' => 'E-mail enviado com sucesso.'
                ]);
            }
            return Redirect::back()->with('success', 'E-mail enviado com sucesso.');
        }

        if ($request->ajax()) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Falha ao enviar e-mail.'
            ]);
        }
        return Redirect::back()->with('error', 'Falha ao enviar e-mail.');
    }


    /**
     * @param Request $request
     * @param $shipmentId
     * @return string
     * @throws \Throwable
     */
    public function editCloseShipments(request $request)
    {
        $today = date('Y-m-d') . ' 00:00:00';

        $shipments = Shipment::filterCustomer()
            ->where('is_collection', 0)
            ->whereIn('status_id', [ShippingStatus::PENDING_ID, ShippingStatus::IN_PICKUP_ID, ShippingStatus::ACCEPTED_ID, ShippingStatus::PICKUP_DONE_ID])
            ->where('created_at', '>=', $today)
            ->whereNull('closed_at');

        if ($request->id) {
            $shipments = $shipments->whereIn('id', $request->id);
        }

        $shipments = $shipments->get();

        return view('account.shipments.modals.close_shipments', compact('shipments'))->render();
    }

    /**
     * @param Request $request
     * @param $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCloseShipments(Request $request)
    {

        $ids = $request->ids;
        $now = date('Y-m-d H:i:s');

        $shipments = Shipment::filterCustomer()
            ->where('is_collection', 0)
            ->whereNull('closed_at')
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {
            $shipment->closed_at = $now;
            $shipment->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'result'        => true,
                'printManifest' => route('account.shipments.selected.print', ['closed', 'date' => $now]),
                'popupDenied'   => view('admin.shipments.shipments.modals.popup_denied')->render(),
                'feedback'      => 'Serviços fechados com sucesso.'
            ]);
        }

        return Redirect::back()->with('success', 'Serviços fechados com sucesso.');
    }

    /**
     * @param Request $request
     * @param $shipmentId
     * @return string
     * @throws \Throwable
     */
    public function showCloseShipments(request $request)
    {

        $files = Shipment::filterCustomer()
            ->whereNotNull('closed_at')
            ->orderBy('closed_at', 'desc')
            ->groupBy('closed_at')
            ->take(50)
            ->get([
                'closed_at',
                DB::raw('count(id) as total'),
                DB::raw('sum(volumes) as volumes'),
                DB::raw('sum(weight) as weight')
            ]);


        $data = compact('files');

        return view('account.shipments.modals.print_closed_shipments', $data)->render();
    }

    /**
     * Search recipient
     *
     * @return type
     */
    public function searchRecipient(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'customers_recipients.id',
            'customers_recipients.code',
            'customers_recipients.name',
            'customers_recipients.address',
            'customers_recipients.zip_code',
            'customers_recipients.city',
            'customers_recipients.country',
            'customers_recipients.phone',
            'customers_recipients.email',
            'customers_recipients.obs',
            'customers_recipients.vat',
            'customers_recipients.responsable',
        ];

        try {

            $customers = CustomerRecipient::where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
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
                    $results[] = [
                        'data'     => $customer->id,
                        'value'    => strtoupper(trim($customer->name)),
                        'code'     => strtoupper(trim($customer->code)),
                        'name'     => strtoupper(trim($customer->name)),
                        'address'  => strtoupper(trim($customer->address)),
                        'zip_code' => trim($customer->zip_code),
                        'city'     => strtoupper(trim($customer->city)),
                        'country'  => $customer->country,
                        'phone'    => $customer->mobile ? trim($customer->mobile) : trim($customer->phone),
                        'email'    => strtolower(trim($customer->email)),
                        'responsable' => strtoupper(trim($customer->responsable)),
                        'vat'      => trim($customer->vat),
                        'obs'      => $customer->obs
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
     * Return customer department details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDepartment(Request $request)
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

        $customer = Auth::guard('customer')->user();

        if ($request->id) {
            $department = Customer::select($bindings)
                ->where('customer_id', $customer->id)
                ->findOrFail($request->id);
        } else {
            $department = $customer;
        }

        $department->name = trim($department->name);
        $department->city = trim($department->city);
        $department->phone = str_replace(' ', '', trim($department->mobile ? $department->mobile : $department->phone));

        return Response::json($department);
    }

    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAgency(Request $request)
    {

        $provider = null;

        if (Setting::get('customers_force_default_provider')) {
            $provider = Setting::get('shipment_default_provider');
        }

        $result = Shipment::getAgencyByZipCode($request->zipCode, $provider, $request->country, $request->service);

        return Response::json($result);
    }

    /**
     * Create Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function createTransportationGuide($shipmentId)
    {
        return Shipment::printTransportGuide([$shipmentId]);
    }

    /**
     * Create Value Statement
     *
     * @param type $shipmentId
     * @return type
     */
    public function createValueStatement($shipmentId)
    {
        return Shipment::printValueStatement([$shipmentId]);
    }

    /**
     * Create pickup manifest
     *
     * @param $pickupId
     * @return string
     */
    public function createPickupManifest($pickupId)
    {
        return Shipment::printPickupManifest([$pickupId]);
    }

    /**
     * Print CMR
     *
     * @param type $shipmentId
     * @return type
     */
    public function createCmr($shipmentId)
    {
        return Shipment::printCmr([$shipmentId]);
    }

    /**
     * Print adhesive labels of a given shipment
     *
     * @param type $shipmentId
     * @return type
     */
    public function createAdhesiveLabels(Request $request, $shipmentId)
    {
        if ($request->get('label_format') == 'A4') {
            return $this->labelsA4($request, $shipmentId);
        }
        return Shipment::printAdhesiveLabels([$shipmentId], false, 'customer');
    }

    /**
     * Create adhesive labels A4 format
     *
     * @param type $shipmentId
     * @return type
     */
    public function labelsA4(Request $request, $shipmentId = null)
    {

        try {
            if (empty($shipmentId) && !empty($request->id)) {
                $shipmentsIds = $request->id;
            } else {
                $shipmentsIds = [$shipmentId];
            }
            return Shipment::printAdhesiveLabelsA4($shipmentsIds, $request->get('label_start'));
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mass print adhesive labels
     *
     * @param type $shipmentId
     * @return type
     */
    public function massAdhesiveLabels(Request $request)
    {
        return Shipment::printAdhesiveLabels($request->id, false, 'customer');
    }

    /**
     * Mass Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function massTransportationGuide(Request $request)
    {
        $data = $request->toArray();
        return Shipment::printTransportGuide($request->id, null, $data, @$data['grouped']);
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
        return view('account.shipments.modals.printA4', compact('ids'))->render();
    }

    /**
     * Mass print adhesive labels
     *
     * @param type $shipmentId
     * @return type
     */
    public function massPickupManifest(Request $request)
    {
        return Shipment::printPickupManifest($request->id, false, 'customer');
    }

    /**
     * Mass print cold manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function massColdManifest(Request $request)
    {
        return Shipment::printShipmentsColdManifest($request->id);
    }

    /**
     * Print CTT reimbursement guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function createReimbursementGuide($shipmentId)
    {
        return Shipment::printReimbursementGuide([$shipmentId], false, 'customer');
    }

    /**
     * Print summary of shipments by given ids or by given a collection of shipments
     *
     * @param Request $request
     * @param null $shipments
     * @return string
     */
    public function massPrint(Request $request, $printType = null, $ids = [])
    {


        $idsRequest = $request->get('id');

        if (is_null($printType)) {
            $printType = $request->get('print-type', 'confirmation');
        }

        if ($printType == 'closed') {
            $printType = 'confirmation';
            $date = $request->get('date');
            $ids = Shipment::filterCustomer()
                ->where('closed_at', $date)
                ->pluck('id')
                ->toArray();
        }

        if (empty($ids) && empty($shipments) && empty($idsRequest)) {
            return Redirect::back()->with('error', 'Não selecionou nenhum envio.');
        }

        if (!empty($idsRequest) && !empty($ids)) {
            $ids = array_merge($idsRequest, $ids);
        } else if (!empty($idsRequest)) {
            $ids = $idsRequest;
        }

        if ($printType == 'confirmation') {
            return Shipment::printShipmentsCargoManifest($ids);
        } else {
            return Shipment::printShipments($ids);
        }
    }

    /**
     * Print shipments by a given date
     *
     * @param Request $request
     * @return mixed
     */
    public function printByDate(Request $request)
    {

        $minDate = $request->has('min-date') ? $request->get('min-date') : date('Y-m-d');
        $maxDate = $request->has('max-date') ? $request->get('max-date') : date('Y-m-d');

        $printType = $request->get('print-type', 'confirmation');

        $customer = Auth::guard('customer')->user();


        $shipments = Shipment::with('customer', 'expenses', 'service')
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);

                if ($customer->customer_id && $customer->view_parent_shipments) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }

                if ($customer->customer_id) {
                    $q->orWhere(function ($q) use ($customer) {
                        $q->where('customer_id', $customer->customer_id);
                        $q->where('department_id', $customer->id);
                    });
                }
            })
            ->whereBetween('date', [$minDate, $maxDate])
            ->get();

        $ids = $shipments->pluck('id')->toArray();
        if ($printType == 'confirmation') {
            return Shipment::printShipmentsCargoManifest($ids);
        } else {
            return self::massPrint($request, $shipments, $ids);
        }
    }

    /**
     * Return shipment prices
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPrice(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $shipment = new Shipment();
        $shipment->fill($request->all());

        //junta aos totais as taxas manuais
        $expensesVals = array_filter($request->get('optional_fields', []));
        $expensesIds  = array_keys($expensesVals);
        $expensesIds  = array_filter($expensesIds);

        $subtotal = $vat = $total = 0;
        $costSubtotal = $costVat = $costTotal = 0;
        $manualExpenses = [];
        if (!empty($expensesIds)) {

            //para cada despesa ativa, vai calcular individualmente o seu preço e adiciona o preço a variavel manualExpenses
            $expenses = ShippingExpense::whereIn('id', $expensesIds)->get();

            foreach ($expenses as $expense) {

                $expense->qty   = $expensesVals[$expense->id];
                $expenseDetails = $shipment->calcExpensePrice($expense);

                if ($expenseDetails) {

                    $manualExpenses[] = [
                        'expense_id'      => $expense->id,
                        'billing_item_id' => $expense->billingItem ? $expense->billing_item_id : null,
                        'qty'             => $expenseDetails['billing']['qty'],
                        'price'           => $expenseDetails['billing']['price'],
                        'subtotal'        => $expenseDetails['billing']['subtotal'],
                        'vat'             => $expenseDetails['billing']['vat'],
                        'total'           => $expenseDetails['billing']['total'],
                        'vat_rate'        => $expenseDetails['billing']['vat_rate'],
                        'vat_rate_id'     => $expenseDetails['billing']['vat_rate_id'],
                        'unity'           => $expenseDetails['expense']['unity'],

                        'cost_price'      => $expenseDetails['cost']['price'],
                        'cost_subtotal'   => $expenseDetails['cost']['subtotal'],
                        'cost_vat'        => $expenseDetails['cost']['vat'],
                        'cost_total'      => $expenseDetails['cost']['total']
                    ];

                    $subtotal     += (float) @$expenseDetails['billing']['subtotal'];
                    $vat          += (float) @$expenseDetails['billing']['vat'];
                    $total        += (float) @$expenseDetails['billing']['total'];
                    $costSubtotal += (float) @$expenseDetails['cost']['subtotal'];
                    $costVat      += (float) @$expenseDetails['cost']['vat'];
                    $costTotal    += (float) @$expenseDetails['cost']['total'];
                }
            }
        }

        $packDimensions = [];
        foreach ($request->box_type as $key => $boxType) {
            $packDimensions[] = [
                'type'     => $boxType,
                'qty'      => @$request->qty[$key],
                'length'   => @$request->length[$key],
                'width'    => @$request->width[$key],
                'height'   => @$request->height[$key],
                'weight'   => @$request->box_weight[$key],
                'fator_m3' => @$request->fator_m3_row[$key],
            ];
        }

        $shipment->pack_dimensions = $packDimensions;
        $shipment->customer_id     = $customer->id;
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

        unset($prices['costs']);

        $prices['modal_html'] = view('account.shipments.modals.price_details_table', compact('prices'))->render();

        return Response::json($prices);
    }

    /**
     * Store shipment dimensions of packs
     *
     * @param $shipment
     * @param $input
     */
    public function storeDimensions($shipment, $input)
    {

        //destroy all dimensions saved for shipment
        ShipmentPackDimension::where('shipment_id', $shipment->id)->delete();

        $totalfatorM3 = 0;
        $totalVolumes = 0;
        $typesSummary = [];

        if (isset($input['length'])) {

            foreach ($input['length'] as $key => $dimensions) {

                if ((!empty($input['height'][$key]) && !empty($input['length'][$key]) && !empty($input['width'][$key])) || !empty(!empty($input['box_description'][$key]))) {

                    try {
                        $volume = (@$input['height'][$key] * @$input['length'][$key] * @$input['width'][$key]) / 1000000;
                    } catch (\Exception $e) {
                        $volume = 0;
                    }

                    $totalVolumes++;
                    $totalfatorM3 += $volume;

                    // Prevent a very weird bug
                    if (!hasModule('logistic')) {
                        @$input['product'][$key]   = null;
                        @$input['sku'][$key]       = null;
                        @$input['serial_no'][$key] = null;
                        @$input['lote'][$key]      = null;
                        @$input['validity'][$key]  = null;
                        @$input['product'][$key]   = null;
                    }

                    $data = [
                        'shipment_id'       => $shipment->id,
                        'length'            => (float) @$input['length'][$key],
                        'width'             => (float) @$input['width'][$key],
                        'height'            => (float) @$input['height'][$key],
                        'weight'            => (float) @$input['box_weight'][$key],
                        'adr_letter'        => @$input['box_adr_letter'][$key],
                        'adr_class'         => @$input['box_adr_class'][$key],
                        'adr_number'        => @$input['box_adr_number'][$key],
                        'qty'               => @$input['qty'][$key],
                        'volume'            => $volume,
                        'description'       => @$input['box_description'][$key],
                        'type'              => @$input['box_type'][$key],
                        'price'             => @$input['box_price'][$key],
                        'optional_fields'   => @$input['box_optional_fields'][$key],

                        'product_id'        => @$input['product'][$key],
                        'sku'               => @$input['sku'][$key],
                        'serial_no'         => @$input['serial_no'][$key],
                        'lote'              => @$input['lote'][$key],
                        'validity'          => @$input['validity'][$key],
                        'product'           => @$input['product'][$key],
                    ];

                    $typesSummary[@$data['type']] = @$typesSummary[@$data['type']] + $data['qty'];

                    $linearMeter = $data['length'] + $data['width'] + $data['height'];
                    if (config('app.source') == "entregaki") {
                        $linearMeter = (2 * $data['length']) + $data['width'] + (2 * $data['height']); //formula dos CTT
                    }

                    $dimension = new ShipmentPackDimension;
                    $dimension->fill($data);
                    $dimension->save();
                }
            }

            if ($totalfatorM3 == 0.00 && $shipment->fator_m3 > 0) {
                //preserva o fator m3 caso exista o campo preenchido mas caso não existam dimensões inseridas.
                //caso contrário, o fator m3 seria subscrito
                $totalfatorM3 = $shipment->fator_m3;
            }

            $shipment->fator_m3 = $totalfatorM3;
            $shipment->packaging_type = $typesSummary;
            $shipment->save();

            return true;
        }
    }

    /**
     * Return list of services with data attributes
     *
     * @param array $allServices
     * @param bool $emptyValue
     * @param bool $isPickup
     * @return array
     */
    public function listServices($allServices, $emptyValue = false, $isPickup = false)
    {
        $authedCustomer = Auth::guard('customer')->user();
        
        $services = [];
        if ($emptyValue) {
            $services[] = ['value' => '', 'display' => ''];
        }

        foreach ($allServices as $service) {
            if ($service->allow_kms) {
                $service->unity = 'km';
            }

            $allowedPackTypes   = array_column($service->getAllowedPackTypes()->toArray(), 'code');
            $defaultPickupHours = $service->getDefaultPickupHours($isPickup, $authedCustomer->id);

            $services[] = [
                'value'                       => $service->id,
                'display'                     => $service->name,
                'data-unity'                  => $service->unity,
                'data-internacional'          => $service->is_internacional,
                'data-collection'             => $service->is_collection,
                'data-return'                 => $service->is_return,
                'data-max'                    => $service->max_volumes ? $service->max_volumes : 999999,
                'data-max-weight'             => $service->max_weight ? $service->max_weight : 999999,
                'data-zip-codes'              => $service->zip_codes ? $service->zip_codes : '',
                'data-dim-required'           => $service->dimensions_required,

                'data-pack-types'             => json_encode($allowedPackTypes),

                'data-min-hour'               => $defaultPickupHours['min_hour'],
                'data-max-hour'               => $defaultPickupHours['max_hour'],
                'data-default-min-hour'       => $defaultPickupHours['default_min_hour'],
                'data-default-max-hour'       => $defaultPickupHours['default_max_hour'],
                'data-default-hour-from-task' => (int)(config('app.source') == 'baltrans' ? $defaultPickupHours['hour_from_task'] : false),

                'data-allow-cod'              => $service->allow_cod,
                'data-allow-return'           => $service->allow_return,
                'data-email-required'         => (int)(Setting::get('tracking_email_active') && !Setting::get('customer_account_email_required') && @$service->settings['email_required']),
                'data-without-pickup'         => (int)(Setting::get('customer_shipment_without_pickup', false) && @$service->settings['without_pickup'])
            ];
        }

        return $services;
    }

    /**
     * Show modal to replicate shipment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicateEdit($id)
    {

        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $shipment = Shipment::filterCustomer()->findOrFail($id);

        return view('account.shipments.modals.replicate', compact('shipment', 'incidences'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicateStore(Request $request, $id)
    {

        $editAfterReplicate = 1;

        $originalShipment  = Shipment::filterCustomer()->findOrfail($id);

        $defaultStatusId   = ShippingStatus::PENDING_ID;

        $shipment = $originalShipment->replicate();
        $shipment->reset2Replicate();
        $shipment->status_id = $defaultStatusId;
        $shipment->setTrackingCode();

        if ($shipment->tracking_code) {

            //copia dimensões
            if ($request->get('replicate_packs')) {
                $packs = $originalShipment->pack_dimensions;

                foreach ($packs as $pack) {
                    $newPack = $pack->replicate();
                    $newPack->shipment_id = $shipment->id;
                    $newPack->save();
                }
            }

            //copia taxas
            $expenses = ShipmentExpense::where('shipment_id', $shipment->id)->get();
            foreach ($expenses as $expense) {
                $newExpense = $expense->replicate();
                $newExpense->shipment_id = $shipment->id;
                $newExpense->save();
            }

            //grava historico
            $history = new ShipmentHistory();
            $history->shipment_id   = $shipment->id;
            $history->status_id     = $shipment->status_id;
            $history->agency_id     = $shipment->agency_id;
            $history->save();
        }

        if (config('app.source') == 'corridadotempo' && $originalShipment->customer_id == 1443) {
            $incidenceId = $request->get('incidence_id');

            $originalShipment->status_id = ShippingStatus::INCIDENCE_ID; //incidence
            $originalShipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id  = $originalShipment->id;
            $history->status_id    = ShippingStatus::INCIDENCE_ID;
            $history->agency_id    = $originalShipment->sender_agency_id;
            $history->incidence_id = $incidenceId;
            $history->save();
        }

        if ($editAfterReplicate) {
            $request = new Request();
            if ($shipment->is_collection) {
                $pickupController = new PickupsController();
                $html = $pickupController->edit($request, $shipment->id);
            } else {
                $html = $this->edit($request, $shipment->id);
            }

            return Response::json([
                'result'   => true,
                'feedback' => 'Envio duplicado com sucesso.',
                'html'     => $html
            ]);
        } else {

            return Response::json([
                'result'   => true,
                'feedback' => 'Envio duplicado com sucesso.',
                'html'     => null
            ]);
        }

        return Redirect::back()->with('success', 'Serviço duplicado com sucesso.');
    }

    /**
     * Store list of adicional expenses
     *
     * @param $input
     * @return array
     */
    public function storeAdicionalExpenses($shipmentId, $input)
    {

        $data = [];
        foreach ($input['expense_id'] as $key => $expenseId) {
            if (!empty($expenseId)) {
                $data = [
                    'id'         => @$input['assigned_expense_id'][$key],
                    'expense_id' => $expenseId,
                    'qty'        => @$input['expense_qty'][$key] ? @$input['expense_qty'][$key] : 1,
                    'price'      => @$input['expense_price'][$key],
                    'subtotal'   => @$input['expense_subtotal'][$key],
                    'cost_price' => @$input['expense_cost_price'][$key] ? $input['expense_cost_price'][$key] : 0,
                ];

                Shipment::storeExpenseByShipmentId($shipmentId, $data);
            }
        }

        return $data;
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
                    return Redirect::route('admin.shipments.index')->with('error', 'O comprovativo POD do envio não está disponível.');
                }

                if ($shipment->webservice_method == 'envialia') {
                    header("Location: $url");
                } else {
                    header('Content-type: image/png');
                    echo file_get_contents($url);
                    exit;
                }
            } catch (\Exception $e) {
                return Redirect::route('account.shipments.index')->with('error', 'O comprovativo POD do envio não está disponível.');
            }
        } else {
            //Imprimir o nosso próprio POD
            $shipmentIds = [$id];
            return Shipment::printPod($shipmentIds);
        }
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

        if ($shipments) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Existem envios selecionados que já se encontram fechados. Reveja os envios selecionados e tente de novo.'
            ]);
        }

        $webservice = new Webservice\Base();
        $result = $webservice->closeShipments('ctt', $ids);

        return response()->json([
            'result'   => @$result['result'],
            'filepath' => @$result['filepath'],
            'feedback' => @$result['feedback'],
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payShipment($id)
    {
        // dd("test");
        // $shipment = Shipment::filterCustomer()
        //             ->where('ignore_billing', 0)
        //             ->whereId($id)
        //             ->first();

        // $customer = Auth::guard('customer')->user();


        // $data = compact(
        //     'shipment',
        //     'customer'
        // );

        // return view('account.shipments.modals.pay_shipment', $data)->render();

        // if (hasModule('account_wallet') && !$customer->is_mensal) {

        //     $shipment = Shipment::filterCustomer()
        //         ->whereNull('invoice_id')
        //         ->where('ignore_billing', 0)
        //         ->whereId($id)
        //         ->first();

        //     if (!$shipment) {
        //         return Redirect::back()->with('error', 'O envio já se encontra pago.');
        //     }

        //     $walletPayment = Base::storeShipmentPayment($shipment, $customer);
        //     if ($walletPayment['result']) {
        //         $shipment->status_id = ShippingStatus::PENDING_ID;
        //         if ($customer->operator_id) {
        //             $shipment->status_id   = ShippingStatus::PENDING_OPERATOR;
        //             $shipment->operator_id = @$customer->operator_id;
        //         }
        //         $shipment->ignore_billing = 1;
        //         $shipment->vat_rate       = @$walletPayment['vat_rate'];
        //         $shipment->save();

        //         //UPDATE HISTORY
        //         $history = new ShipmentHistory();
        //         $history->shipment_id = $shipment->id;
        //         $history->agency_id   = $shipment->agency_id;
        //         $history->status_id   = $shipment->status_id;
        //         $history->operator_id = $shipment->operator_id;
        //         $history->save();


        //         //SUBMIT BY WEBSERVICE
        //         $submitWebservice = false;
        //         if (!empty(Setting::get('webservices_auto_submit')) && (empty($shipment->webservice_method) || (!empty($shipment->webservice_method) && empty($shipment->submited_at)) || in_array($shipment->webservice_method, ['envialia', 'tipsa', 'nacex']))) {
        //             $submitWebservice = true;
        //         }

        //         if ($submitWebservice) {
        //             try {
        //                 $webservice = new Webservice\Base();
        //                 $webservice->submitShipment($shipment);
        //                 unset($shipment->provider_weight);
        //             } catch (\Exception $e) {
        //             }
        //         }

        //         $shipment->notifyOperators();

        //         //SEND NOTIFICATION BY EMAIL
        //         if (!empty($input['recipient_email'])) {
        //             $emails = validateNotificationEmails($input['recipient_email']);
        //             if (!empty($emails['valid'])) {
        //                 try {
        //                     Mail::send(transEmail('emails.shipments.tracking', $shipment->recipient_country), compact('input', 'shipment'), function ($message) use ($input, $emails, $shipment) {
        //                         $message->to($emails['valid'])
        //                             ->subject(transLocale('admin/email.subjects.shipments.create', $shipment->getRecipientLocale(), ['trk' => $shipment->tracking_code]));
        //                     });
        //                 } catch (\Exception $e) {
        //                 }
        //             }
        //         }

        //         return Redirect::back()->with('success', 'Envio pago com sucesso.');
        //     } else {
        //         return Redirect::route('account.shipments.index')->with('error', 'O saldo da conta não permite o pagamento do envio.');
        //     }
        // }
    }

    /**
     * Store pallet informations of a shipment
     *
     * @param $shipment
     * @param $input
     */
    public function storePallet($shipment, $input)
    {

        ShipmentPallet::where('shipment_id', $shipment->id)
            ->delete();

        if (isset($input['pallet_weight'])) {
            foreach ($input['pallet_weight'] as $key => $weight) {


                if (!empty($input['pallet_weight'][$key])) {

                    $input['pallet_qty'][$key] = empty(@$input['pallet_qty'][$key]) ? 1 : $input['pallet_qty'][$key];
                    $data = [
                        'shipment_id' => $shipment->id,
                        'weight' => @$input['pallet_weight'][$key],
                        'qty'    => @$input['pallet_qty'][$key],
                        'cost'   => @$input['pallet_cost'][$key],
                        'price'  => @$input['pallet_price'][$key],
                    ];

                    $pallet = new ShipmentPallet();
                    $pallet->fill($data);
                    $pallet->save();
                }
            }
        }

        $shipment->save();
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function searchSku(Request $request)
    {

        $customer   = Auth::guard('customer')->user();
        $customerId = $customer->customer_id ? $customer->customer_id : $customer->id;
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
                ->filterSource()
                ->where('customer_id', $customerId);

            if (@$customer->settings['logistic_stock_only_available'] || @$customer->parent_customer->settings['logistic_stock_only_available']) {
                $products = $products->where('stock_total', '>', '0');
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAcceptanceCertificates()
    {

        $customer = Auth::guard('customer')->user();

        $certificates = CttDeliveryManifest::where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->take(50)
            ->get();

        return view('account.shipments.modals.ctt_certificates', compact('certificates'))->render();
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

        $recipient->phone = str_replace(' ', '', $recipient->phone);

        $agency = Shipment::getAgencyByZipCode($recipient->zip_code);
        $recipient->agency_id = $agency->agency_id;
        $recipient->country   = $agency->zone;

        return Response::json($recipient);
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
     * Return list of pickup and dropover points
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPudos(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $availableProviders = $customer->enabled_pudo_providers;

        $pudos = PickupPoint::filterSource()
            ->whereIn('provider_id', $availableProviders)
            ->isActive()
            ->get();

        if (!$pudos->isEmpty()) {
            $html = '<option value=""></option>';
            foreach ($pudos as $pudo) {
                $html .= '<option value="' . $pudo->id . '" 
                            data-address="' . $pudo->address . '"
                            data-zip-code="' . $pudo->zip_code . '"
                            data-city="' . $pudo->city . '">
                            ' . $pudo->name . ' (' . $pudo->zip_code . ', ' . $pudo->city . ')
                            </option>';
            }
        } else {
            $html = [];
        }

        return Response::json($html);
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
     * Creates payments
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPayment(Request $request)
    {
        $shipmentId      = $request->get('shipment_id');
        $paymentMethod   = $request->get('payment_method');
        $walletPayment   = [];
        $invoiceRequired = false;
        $result          = false;

        $shipment        = Shipment::find($shipmentId);
        $customer        = $shipment->customer;

        $amount          = $shipment->billing_total;
        $reference       = 'TRK' . $shipment->tracking_code;
        $description     = 'Expedição ' . $shipment->tracking_code;

        if ($shipment->ignore_billing || $shipment->invoice_id) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Este envio já está liquidado'
            ]);
        }
        
        $data = [
            'sense'       => 'debit',
            'target'      => 'Shipment',
            'target_id'   => $shipment->id,
            'customer_id' => $customer->id,
            'reference'   => $reference,
            'value'       => $amount,
            'description' => $description,
        ];

        try {
            $gateway = new Base();
            if ($paymentMethod == 'wallet') {
                $paymentResult  = $shipment->walletPayment($customer);

                $response             = $paymentResult['walletPayment'];
                $response['feedback'] = 'Envio pago com sucesso!';
                $response['html']     = view('account.shipments.partials.payment.wallet', compact('response'))->render();

                $history = new ShipmentHistory();
                $history->shipment_id = $shipment->id;
                $history->status_id   = Setting::get('shipment_status_after_create', ShippingStatus::ACCEPTED_ID);
                $history->save();

                $shipment->ignore_billing = true;
                $shipment->status_id      = $history->status_id;
                $shipment->save();
            } else if ($paymentMethod == 'mbway') {
                $data['phone'] = $request['mbw_phone'];

                $response = $gateway->createPayment('mbway', $data);
                $response = [
                    'result'   => $response['result'],
                    'feedback' => $response['feedback'],
                    'id'       => @$response['payment']['id'],
                    'phone'    => @$response['payment']['mbway_phone'],
                    'amount'   => money($amount),
                    'wallet'   => $customer->wallet_balance,
                    'html'     => view('account.shipments.partials.payment.mbway', compact('response'))->render()
                ];
            } else if ($paymentMethod == 'visa') {
                $data['first_name'] = $request->card_first_name;
                $data['last_name']  = $request->card_last_name;
                $data['card']       = $request->card_no;
                $data['cvc']        = $request->card_cvc;
                $data['month']      = $request->card_month;
                $data['year']       = $request->card_year;

                $response = $gateway->createPayment('cc', $data);
                $response = [
                    'result'       => $response['result'],
                    'feedback'     => $response['feedback'],
                    'id'           => @$response['payment']['id'],
                    'entity'       => @$response['payment']['mb_entity'],
                    'reference'    => @$response['payment']['mb_reference'],
                    'amount'       => money($amount),
                    'wallet'       => $customer->wallet_balance,
                    'conclude_url' => @$response['conclude_url']
                ];
            } else {
                $response = $gateway->createPayment('mb', $data);
                $response = [
                    'result'    => $response['result'],
                    'feedback'  => $response['feedback'],
                    'id'        => @$response['payment']['id'],
                    'entity'    => @$response['payment']['mb_entity'],
                    'reference' => chunk_split(@$response['payment']['mb_reference'], 3, ' '),
                    'amount'    => money($amount),
                    'wallet'    => $customer->wallet_balance,
                    'html'      => view('account.shipments.partials.payment.mb', compact('response'))->render()
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'result'    => false,
                'feedback'  => $e->getMessage(),
                'wallet'    => $customer->wallet_balance
            ];
        }

        return Response::json($response);
    }

    /**
     * Shows the last payment relative to a shipment
     *
     * @return type
     */

    public function showPayment(Request $request, $shipmentId) 
    {
        if (!$shipmentId)
            return Redirect::back()->with('error', 'Não foi possivel obter a referência de pagamento.');

        $shipment = Shipment::where('id', $shipmentId)->first();
        $gateway  = Base::where('target_id', $shipmentId)->latest('created_at')->first();

        $data = compact(
            'shipment',
            'gateway'
        );

        return view('account.shipments.modals.show_payment', $data)->render();
    }

    public function searchZipCodes(Request $request) {
        $country = $request->get('country');
        if (!$country) {
            return Response::json([
                'suggestions' => []
            ]);
        }

        $zipCode = trim($request->get('query'));
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
            }
        } else {
            $zp = new ZipCode();
            if ($zp->isValid('GB', $zipCode)) {
                $zipCode = explode(' ', $zipCode);
                $zipCode = @$zipCode[0];
            }
        }

        $zipCodes = ZipCode::remember(config('cache.query_ttl'))
            ->cacheTags(ZipCode::CACHE_TAG)
            // ->groupBy('zip_code', 'zip_code_extension')
            ->where('country', $country)
            ->where('zip_code', 'LIKE', '%' . $zipCode . '%');

        if ($zipCodeExtension) {
            $zipCodes = $zipCodes->where('zip_code_extension', 'LIKE', '%' . $zipCodeExtension . '%');
        }

        $zipCodes = $zipCodes->limit(100)->get();

        $results = [];
        foreach ($zipCodes as $zipCode) {
            $completeZipCode = $zipCode->zip_code;
            if ($zipCode->zip_code_extension) {
                $completeZipCode .= '-' . $zipCode->zip_code_extension;
            }

            $results[] = [
                'data'  => $completeZipCode,
                'value' => $completeZipCode . ' ['. $zipCode->postal_designation .']',
                'city'  => $zipCode->postal_designation
            ];
        }

        return Response::json([
            'suggestions' => $results
        ]);
    }
}
