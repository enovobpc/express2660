<?php

namespace App\Http\Controllers\Account;

use App\Models\ZipCode\AgencyZipCode;
use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerBilling;
use App\Models\OperatorTask;
use App\Models\PackType;
use App\Models\Route;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\ShippingStatus;
use App\Models\Shipment;
use App\Models\Provider;
use App\Models\Service;
use App\Models\CustomerRecipient;
use App\Models\Webservice;
use App\Models\ShipmentHistory;
use Response, DB, View, Cache, Setting, Excel, Date;

class PickupsController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'pickups';

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
    public function index()
    {

        $customer = Auth::guard('customer')->user();

        if (@$customer->settings['hide_menu_pickups']) {
            return Redirect::route('account.shipments.index');
        }

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
            ->isCollection(true)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->whereIn('id', $enabledServices)
            ->whereNotNull('assigned_service_id')
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $isShippingBlocked = $customer->is_shipping_blocked;

        $data = compact(
            'services',
            'status',
            'isShippingBlocked'
        );

        return $this->setContent('account.pickups.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $customer = Auth::guard('customer')->user();

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

        $shipment = new Shipment;
        $shipment->is_collection = true;

        if ($request->get('source') == 'budgeter' && $request->get('intercity', false)) {
            $shipment->recipient_zip_code = $request->get('recipient_zip_code');
            $shipment->recipient_country = $request->get('recipient_country');
        } else {
            $shipment->recipient_name      = $customer->name;
            $shipment->recipient_address   = $customer->address;
            $shipment->recipient_zip_code  = $customer->zip_code;
            $shipment->recipient_city      = $customer->city;
            $shipment->recipient_country   = empty($customer->country) ? Setting::get('app_country') : $customer->country;
            $shipment->recipient_phone     = $customer->mobile ? $customer->mobile : $customer->phone;
        }

        //coloca os dados do cliente no campo de remetente em vez de destino
        if (config('app.source') == 'kmestafetas' && $customer->customer_id == '465') {
            $shipment->recipient_name      = null;
            $shipment->recipient_address   = null;
            $shipment->recipient_zip_code  = null;
            $shipment->recipient_city      = null;
            $shipment->recipient_country   = 'pt';
            $shipment->recipient_phone     = null;

            $shipment->sender_name      = $customer->name;
            $shipment->sender_address   = $customer->address;
            $shipment->sender_zip_code  = $customer->zip_code;
            $shipment->sender_city      = $customer->city;
            $shipment->sender_country   = empty($customer->country) ? Setting::get('app_country') : $customer->country;
            $shipment->sender_phone     = $customer->mobile ? $customer->mobile : $customer->phone;
        }

        $shipment->service_id = null;
        if (!empty($customer->default_service) || !empty(Setting::get('customers_default_service'))) {
            $shipment->service_id = $customer->default_service ? $customer->default_service : Setting::get('customers_default_service');
        }

        if (empty($customer->enabled_services)) {
            $services = null;
            $servicesCollection = null;
        } else {
            $allServices = Service::whereIn('id', $customer->enabled_services)
                ->whereNotNull('assigned_service_id')
                ->filterHorary()
                ->ordered()
                ->get();

            $shipmentController = new ShipmentsController();
            $services = $shipmentController->listServices($allServices, $allServices->count() > 1 ? true : false, true);
        }

        if (empty($customer->enabled_providers)) {
            $providers = null;
        } else {
            $providers = Provider::whereIn('id', $customer->enabled_providers)
                ->isCarrier()
                ->pluck('name', 'id')
                ->toArray();
        }

        $allExpenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->where('account_complementar_service', 1)
            ->filterSource()
            ->ordered()
            ->get();

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });

        $shipmentController = new ShipmentsController();
        $packTypes = $shipmentController->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        if (config('app.source') == 'baltrans') {
            $shipmentDate = Date::today()->addDay()->format('Y-m-d');
        } else {
            $shipmentDate = Date::today()->format('Y-m-d');
        }
        $hours = listHours(5);

        $shipmentController = new ShipmentsController();
        $senderStates    = $shipmentController->listStates($shipment->sender_country);
        $recipientStates = $shipmentController->listStates($shipment->recipient_country);

        $action = trans('account/shipments.modal-shipment.create-pickup');
        $formOptions = array('route' => array('account.pickups.store'), 'class' => 'form-horizontal form-shipment', 'method' => 'POST');

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
            'hours',
            'packTypes',
            'senderStates',
            'recipientStates'
        );


        return view('account.pickups.edit', $compact)->render();
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
    public function edit($id)
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
            $shipmentController = new ShipmentsController();
            $services = $shipmentController->listServices(Service::whereIn('id', $customer->enabled_services)
                ->whereNotNull('assigned_service_id')
                ->filterHorary()
                ->ordered()
                ->get(), false, true);
        }

        if (empty($customer->enabled_providers)) {
            $providers = null;
        } else {
            $providers = Provider::whereIn('id', $customer->enabled_providers)
                ->isCarrier()
                ->pluck('name', 'id')
                ->toArray();
        }

        $allExpenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->where('account_complementar_service', 1)
            ->filterSource()
            ->ordered()
            ->get();

        $complementarServices = $allExpenses->filter(function ($item) {
            return $item->complementar_service == 1;
        });

        $shipmentController = new ShipmentsController();
        $packTypes = $shipmentController->listPackTypes(PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get());

        $shipmentDate = $shipment->date;
        $hours        = listHours(5);

        $shipmentController = new ShipmentsController();
        $senderStates    = $shipmentController->listStates($shipment->sender_country);
        $recipientStates = $shipmentController->listStates($shipment->recipient_country);

        $action       = trans('account/shipments.modal-shipment.edit-pickup');
        $formOptions  = array('route' => array('account.pickups.update', $id), 'class' => 'form-horizontal form-shipment', 'method' => 'PUT');

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
            'hours',
            'packTypes',
            'senderStates',
            'recipientStates'
        );

        return view('account.pickups.edit', $compact)->render();
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

        $input    = $request->all();
        $customer = Auth::guard('customer')->user();

        $mainCustomer = $customer;
        if ($customer->customer_id) { //subconta
            $mainCustomer = Customer::find($customer->customer_id);
        }

        if(empty($input['service_id'])) {
            $input['service_id'] = $customer->default_service ?? Setting::get('customers_default_service');
            $input['service_id'] = empty($input['service_id']) ? $input['service_id'] : null;
        }

        $service  = Service::find($input['service_id']);

        $shipment = Shipment::filterCustomer()->findOrNew($id);
        $shipmentExists = $shipment->exists;

        if($shipmentExists && empty($service)) {
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

        if(empty($service)) {
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

        //retorno
        if (in_array('rpack', $input['has_return'])) {
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

        //determina informação do codigo postal de origem
        $fullZipCode  = $shipment->sender_zip_code;
        $zipCodeParts = explode('-', $fullZipCode);
        $zipCode4     = $zipCodeParts[0];
        $zipCode = AgencyZipCode::where(function ($q) use ($fullZipCode, $zipCode4) {
                $q->where('zip_code', $zipCode4);
                $q->orWhere('zip_code', $fullZipCode);
            })
            ->where('country', $shipment->recipient_country)
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
            $shipment->is_collection       = 1;

            if ($shipment->exists) {
                $saveHistory = false;
                if ($shipment->hasSyncError()) {
                    $shipment->resetWebserviceError();
                }
            } else {
                $saveHistory = true;
                $shipment->status_id = $shipment->getDefaultStatus(true);
            }

            //calcula e obtem preços do envio
            $prices = Shipment::calcPrices($shipment);
            $shipment->fill(@$prices['fillable']);

            //prepara portes
            /*if ($input['cod'] == 'D' || $input['cod'] == 'S') {
                $shipment->payment_at_recipient      = true;
                $shipment->total_price_for_recipient = @$prices['fillable']['billing_subtotal'];
            } else {
                $shipment->payment_at_recipient      = false;
                $shipment->total_price_for_recipient = null;
            }*/

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
            unset($shipment->service, $shipment->customer, $shipment->original_provider_id, $shipment->coefficient_m3);
            $shipment->setTrackingCode();

            //grava dimensões e mercadoria
            $shipmentCollection = new ShipmentsController();
            $shipmentCollection->storeDimensions($shipment, $input);

            //grava taxas adicionais
            if ($prices['expenses']) {
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

            /* if ($isPaid) {
                $shipment->notifyOperators(); //nas recolhas não há notificacao de operadores
            } */

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
}
