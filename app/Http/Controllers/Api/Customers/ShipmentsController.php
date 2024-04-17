<?php

namespace App\Http\Controllers\Api\Customers;

use App\Models\Agency;
use App\Models\PackType;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\Api\OauthClient;
use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\CustomerBilling;
use App\Models\FileRepository;
use App\Models\IncidenceResolutionType;
use App\Models\IncidenceType;
use App\Models\Logistic\Product;
use App\Models\LogViewer;
use App\Models\Map;
use App\Models\OperatorTask;
use App\Models\PickupPoint;
use App\Models\Provider;
use App\Models\RefundControl;
use App\Models\Route;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShipmentPackDimension;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\Webservice\Base;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Auth, Validator, Setting, Mail, Log, DB;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{

    protected $delnextCustomers = [
        '16791', //asfalto
        '16850', //fozpost
        '16851', //nmx
        '16852', //tortuga
        '16925', //vasco santos
        '17007', //aveirofast
        '17010', //sentido suposto
        '17719', //gigantexpress
        '17791', //FVP transportes
        '17818', //TCM
        '67',    //MRC
        '17955', //FPRM
        '45',    //Utiltrans
        '17818', //TCM
        '2',     //TRP express
        '8',    //Transportes Nunes
    ];

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'id',
        'tracking_code',
        'type',
        'parent_tracking_code',
        'reference',
        'reference2',
        'sender_attn',
        'sender_vat',
        'sender_name',
        'sender_address',
        'sender_zip_code',
        'sender_city',
        'sender_country',
        'sender_phone',
        'recipient_attn',
        'recipient_vat',
        'recipient_name',
        'recipient_address',
        'recipient_zip_code',
        'recipient_city',
        'recipient_country',
        'recipient_phone',
        'recipient_attn',
        'recipient_email',
        'volumes',
        'weight',
        'volumetric_weight',
        'fator_m3',
        'charge_price',
        'total_price',
        'payment_at_recipient',
        'complementar_services',
        'is_collection',
        'cod',
        'date',
        'shipping_date',
        'delivery_date',
        'status_date',
        'obs',
        'obs_delivery',
        'service_id',
        'status_id',
    ];

    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        try {
            $this->usage_exceed = false;
            $this->log_usage = Setting::get('api_debug_mode') ? true : false;

            $customer = Auth::guard('api')->user();
            if ($customer) {
                $oauth = OauthClient::where('user_id', $customer->id)->first();

                $lastCallDate = new Date($oauth->last_call);
                $lastCallDate = $lastCallDate->format('Y-m-d');

                if ($lastCallDate == date('Y-m-d')) {
                    $oauth->daily_counter += 1;
                    $oauth->last_call = date('Y-m-d H:i:s');

                    if ($oauth->daily_counter > $oauth->daily_limit) {
                        $this->usage_exceed = true;
                    }
                } else {
                    $oauth->daily_counter = 1;
                    $oauth->last_call = date('Y-m-d H:i:s');
                }
                $oauth->save();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request, $apiLevel)
    {

        $source = config('app.source');
        $partnersApi = $request->has('_partner_api_');
        if ($partnersApi) {
            $this->bindings = $request->bindings;
        } else {
            $customer = Auth::guard('api')->user();

            $this->logUsage($customer, 'lists');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }

            $customer->id;
        }

        //get input fields
        $input = $request->all();
        if (empty($input)) {
            return $this->responseError('lists', '-002');
        }

        $shipments = Shipment::with(['service' => function ($q) {
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->with(['last_incidence' => function($q){
                $q->with(['incidence' => function($q){
                  $q->select(['id', 'name']);
                }]);
            }]);


        if ($partnersApi) {
            $shipments = $shipments->with(['provider' => function ($q) {
                $q->select(['id', 'code', 'name']);
            }]);

            //filter customer
            if ($request->has('customer')) {
                $shipments = $shipments->whereRaw('customer_id = (select id from customers where code="' . $request->get('customer') . '" and source="' . $source . '")');
            }

            //filter provider
            if ($request->has('provider')) {
                $shipments = $shipments->whereRaw('provider_id = (select id from providers where code="' . $request->get('provider') . '" and source="' . $source . '")');
            }

            //filter operator
            if ($request->has('operator')) {
                $shipments = $shipments->whereRaw('operator_id = (select id from users where code="' . $request->get('operator') . '" and source="' . $source . '")');
            }
        } else {
            $shipments = $shipments->where('customer_id', $customer->id);
        }


        //filter date
        if ($request->has('date')) {
            $shipments = $shipments->where('date', $request->get('date'));
        }

        //filter tracking
        if ($request->has('tracking')) {
            $shipments = $shipments->whereIn('tracking_code', explode(',', $request->get('tracking')));
        }

        //filter is collection
        if ($request->has('pickup')) {
            $shipments = $shipments->where('is_collection', $request->get('pickup'));
        }

        //filter concluded status
        if ($request->has('concluded')) {
            $finalStatus = ShippingStatus::where('is_final', 1)->pluck('id')->toArray();

            if ($request->get('concluded')) {
                $shipments = $shipments->whereIn('status_id', $finalStatus);
            } else {
                $shipments = $shipments->whereNotIn('status_id', $finalStatus);
            }
        }

        if ($request->has('page')) {
            $page = explode(',', $request->get('page'));
            $min = @$page[0];
            $max = @$page[1];

            $shipments = $shipments->skip($min)->take($max ? $max : 1000);
        } else {
            $shipments = $shipments->take(1000);
        }

        $shipments = $shipments->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get($this->bindings);

        $dataArr = [];
        foreach($shipments as &$shipment){
            $shipmentArr = $shipment->toArray();
            
            $shipmentArr['last_incidence'] = @$shipment->last_incidence->incidence->name;
            
            $dataArr[] = $shipmentArr;
        }
        $shipments = $dataArr;

        if (!$shipments) {
            return $this->responseError('lists', '-001');
        }

        return response($shipments, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request, $apiLevel)
    {
        return $this->update($request, $apiLevel, null);
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $apiLevel, $tracking = null)
    {

        try {

            $partnersApi = $request->has('_partner_api_');
            if (!$partnersApi) {
                $customer = Auth::guard('api')->user();
                $this->logUsage($customer, 'insert/update');

                if ($this->checkUsageLimit()) {
                    return $this->responseUsageExceed();
                }
            }

            $inputs = $request->except(['_partner_api_']);
            $shipmentsIds = [];
            $responseBag  = [];
            $multipleShipments = true;

            if (!isset($inputs[0])) { //verifica se existem vários envios
                $multipleShipments = false;
                $inputs = [$inputs];
            }

            //descrimar os erros pelo trk do cliente
            if ($request['errors'] == 1) {
                unset($inputs['errors']);
            }

            $errorDescription = true;
            foreach ($inputs as $inputKey => $input) { //processa todos os envios para inserir ou atualizar
                $errorBag = [];
                $input = array_map('trim_data', $input);

                if ($errorDescription) {
                    $inputKey = @$input['reference'] ? @$input['reference'] : $inputKey;
                    //vat - via direta
                    if ($customer->vat == 502975326 || config('app.source') == "viadireta") {
                        $inputKey = @$input['provider_tracking_code'];
                    }
                }

                if (empty($input)) {
                    $responseBag[$inputKey] = $this->responseError('store', '-998', 'JSON format error. Check your JSON sintax', true);
                } else {

                    //ligação entre parceiros. Força a que o Código de envio seja igual para ambos
                    $partnerTracking = null;
                    if (@$input['source_partner'] && @$input['source_tracking']) {
                        $partnerTracking = @$input['source_tracking'];
                    }

                    //se é api Partners, permite configurar qual o cliente do envio
                    if ($partnersApi) {
                        $customer = Customer::filterSource()->where('code', @$input['customer'])->first();
                    }

                    //se o fornecedor vem definido na api, força este fornecedor
                    $provider = true;
                    if (@$input['provider']) {
                        $provider = Provider::filterSource()->where('code', @$input['provider'])->first();
                        if ($provider) {
                            $input['provider'] = $provider->id;
                        } else {
                            $provider = false;
                        }
                    }

                    //valida serviço
                    $serviceCode = $input['service'];
                    $service = Service::filterSource()->where('display_code', $serviceCode)->first();
                    $input['service_id'] = @$service->id;
                    if (empty($service)) {
                        $errorBag[] = $this->responseError('store', '-002', null, true);
                    }


                    if (@$customer && $provider && @$service) {
                        $tracking = @$input['tracking_code'];
                        $input['created_by_customer'] = 1;
                        $input['date']              = !isset($input['date']) ? date('Y-m-d') : $input['date'];
                        $input['customer_id']       = @$customer->id;
                        $input['agency_id']         = @$customer->agency_id;
                        $input['sender_agency_id']  = @$customer->agency_id;
                        $input['recipient_email']   = @$input['email'];
                        $input['has_return']        = isset($input['return_pack']) && !empty($input['return_pack']) ? ['rpack'] : null;
                        $input['is_collection']     = isset($input['is_collection']) ? $input['is_collection'] : 0;
                        $input['has_assembly']      = @$input['has_assembly'] ? 1 : 0;
                        $submitWebservice           = true;
                        $autoWalletPayment          = false; //pagamento automático pela wallet (nao perguntas outros métodos)
                        $isPaid                     = 1; //$customer->payment_method == 'wallet' && !$customer->ignore_billing && !$shipment->invoice_id && empty($request->get('cod')) ? false : true;

                        if(config('app.source') == 'pesamatrans' && $customer->id == '97') {
                            $input['cod'] = 'D'; //força portes no destino.
                            //$input['payment_at_recipient'] = true; //força portes no destino.
                        } elseif (config('app.source') == 'trpexpress' && $customer->id == '2') {
                            $input['service'] = '48H'; //delnext.
                        }

                        if (config('app.source') == 'activos24' && $customer->id == 1476 && !in_array(strtolower($input['recipient_country'] ?? ''), ['pt', 'es'])) {
                            $errorBag[] = $this->responseError('store', '-998', 'Apenas envios para Portugal e Espanha são permitidos.', true);
                        }


                        //Valida e prepara dimensões antes de iniciar a gravação
                        $dimensions = [];
                        if (!empty(@$input['dimensions'])) {
                            $dimensions = $this->validateDimensions($input, $errorBag);
                        }


                        //Valida anexos a carregar antes de iniciar a gravação
                        $inputAttachments = @$input['attachments'];
                        if (!empty($inputAttachments)) {
                            foreach (@$inputAttachments as $attachment) {
                                if (empty(@$attachment['fileurl']) || empty(@$attachment['title'])) {
                                    $errorBag[] = $this->responseError('store', '-018', 'Attachments: File URL and Title are required.', true);
                                }
                            }
                        }

                        //Valida se departamento existe antes de iniciar a gravação
                        if (@$input['department']) {
                            $department = Customer::filterSource()
                                ->where('customer_id', @$customer->id)
                                ->where('code', @$input['department'])
                                ->first();

                            if (empty($department)) {
                                $errorBag[] = $this->responseError('store', '-009', null, true);
                            }

                            $input['department_id'] = @$department->id;
                        }

                        //obtem o envio ou cria nova instancia
                        if (!empty($tracking)) {
                            $shipment = Shipment::where(function ($q) use ($tracking) {
                                $q->where('tracking_code', $tracking);
                                $q->orWhere('provider_tracking_code', $tracking);
                            })
                                ->where('customer_id', @$customer->id)
                                ->first();
                        } else {
                            if ($partnerTracking) { //verifica se existe algum envio eliminado e restaura ou cria novo
                                $shipment = Shipment::where('tracking_code', $partnerTracking)->withTrashed()->first() ?: new Shipment();
                                $shipment->deleted_at = null;
                            } else {
                                $shipment = new Shipment();
                            }
                        }

                        //envio não encontrado
                        $shipmentExists = @$shipment->exists;
                        if (!empty($tracking) && empty($shipment)) {
                            $responseBag[$inputKey] = $this->responseError('store', '-001', 'Serviço ' . $tracking . ' não encontrado.', true);
                        } else {

                            // Check if customer is shipping blocked (plafound, monthly limits, ...)
                            $isShippingBlocked = @$customer->is_shipping_blocked;
                            if (empty($tracking) && $isShippingBlocked) {
                                $errorBag[] = $this->responseError('store', '-019', null, true);
                            }

                            //envio não pode ser editado
                            if(config('app.source') != 'viadireta'){
                                if (!empty($shipment->status_id) && !in_array($shipment->status_id, Setting::get('services_can_delete'))) {
                                    $errorBag[] = $this->responseError('store', '-008', null, true);
                                }
                            }

                            //valida email
                            if (@$input['recipient_email']) {
                                $emails = validateNotificationEmails($input['email']);
                                if (empty($emails['valid'])) {
                                    $invalidMail = implode(',', $emails['error']);
                                    $errorBag[]  = $this->responseError('store', '-001', 'Endereços email inválidos: ' . $invalidMail, true);
                                }
                            }

                            //valida data
                            if (config('app.source') != 'viadireta') {
                                $date  = new Date($input['date']);
                                $today = new Date(date('Y-m-d'));
                                if ($date->lt($today)) {
                                    $errorBag[] = $this->responseError('store', '-005', null, true);
                                }
                            }

                            //valida país origem / destino
                            $allCountries = trans('country');
                            $input['sender_country']    = strtolower($input['sender_country']);
                            $input['recipient_country'] = strtolower($input['recipient_country']);

                            if (!isset($allCountries[$input['sender_country']])) {
                                $errorBag[] = $this->responseError('store', '-003', null, true);
                            }

                            if (!isset($allCountries[$input['recipient_country']])) {
                                $errorBag[] = $this->responseError('store', '-004', null, true);
                            }

                            //identifica fornecedor a usar
                            $fullZipCode  = $shipment->recipient_zip_code;
                            $zipCodeParts = explode('-', $fullZipCode);
                            $zipCode4     = $zipCodeParts[0];
                            $zipCode = AgencyZipCode::where(function ($q) use ($fullZipCode, $zipCode4) {
                                $q->where('zip_code', $zipCode4);
                                $q->orWhere('zip_code', $fullZipCode);
                            })
                                ->where('country', @$shipment->recipient_country)
                                ->orderBy('zip_code', 'desc')
                                ->first();
                            $input['recipient_agency_id'] = @$zipCode->agency_id ?? @$customer->agency_id;
                            $zipCodeProvider  = @$zipCode->provider_id;

                            if (!empty(@$input['provider'])) {
                                $input['provider_id'] = $input['provider'];
                            } else {
                                //determina qual o fornecedor a usar
                                if ($shipment && !$shipment->hasSync()) {
                                    if (!empty(@$customer->enabled_providers) && count(@$customer->enabled_providers) == 1) {
                                        //regra 1: definido na ficha de cliente que só envia por 1 fornecedor
                                        $input['provider_id'] = @$customer->enabled_providers[0];
                                    } elseif (!empty(@$customer->enabled_providers) && $request->has('provider_id') && $request->get('provider_id')) {
                                        //regra 2: o cliente escolhe o fornecedor na janela de envio
                                        $input['provider_id'] = $request->get('provider_id');
                                    } else if (@$service->provider_id) {
                                        //regra 3: serviço obriga a usar um serviço
                                        $input['provider_id'] = $service->provider_id;
                                    } else if ($zipCodeProvider) {
                                        //regra 4: codigo postal destino obriga a usar serviço
                                        $input['provider_id'] = $zipCodeProvider;
                                    } else {
                                        //default: fornecedor por defeito
                                        $input['provider_id'] = Setting::get('shipment_default_provider');
                                    }
                                }
                            }

                            /** Check if charge price is bigger than the allowed value */
                            if (Setting::get('shipment_max_charge_price')) {
                                if (!empty($input['charge_price']) && $input['charge_price'] > Setting::get('shipment_max_charge_price')) {
                                    $errorBag[] = $this->responseError('store', '-018', null, true);
                                }
                            }
                            

                            $validator = Validator::make($input, $this->storeRules(), [], $this->customAttributes());
                            if ($validator->passes()) {
                                $shipment->fill($input);
                                $shipment->service  = $service;
                                $shipment->customer = $customer;

                                if ($shipment->exists) {
                                    $saveHistory = false;
                                    if ($shipment->hasSyncError()) {
                                        $shipment->resetWebserviceError();
                                    }
                                } else {
                                    $saveHistory = true;
                                    $shipment->status_id = $shipment->getDefaultStatus(true);
                                }

                                //DELNEXT
                                if ($shipment->customer_id == 17007) { //aveirofast
                                    $shipment->provider_id = 62;
                                } else if ($shipment->customer_id == 17719) { //gigantexpress
                                    $shipment->provider_id = 13;
                                } else if ($shipment->customer_id == 17818) { //tcmtransportes
                                    $shipment->provider_id = 314;
                                }

                                //calcula e obtem preços do envio
                                $prices = Shipment::calcPrices($shipment);
                                if (@$prices['fillable']) {
                                    $shipment->fill(@$prices['fillable']);
                                }

                                if (!empty(@$prices['errors'])) {
                                    $errorBag[] = $this->responseError('store', '-002', @$prices['errors'][0], true);
                                }

                                //prepara portes
                                if (@$input['cod'] == 'D' || @$input['cod'] == 'S') {
                                    $shipment->payment_at_recipient      = true;
                                    $shipment->total_price_for_recipient = @$prices['fillable']['billing_subtotal'];
                                } else {
                                    $shipment->payment_at_recipient      = false;
                                    $shipment->total_price_for_recipient = null;
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

                                //calcula os KM atuais e corretos
                                if (@$shipment->service->allow_kms) {
                                    $distance = Map::getDistance($shipment->sender_zip_code, $shipment->recipient_zip_code, $shipment->sender_country, $shipment->recipient_country);
                                    $shipment->kms = @$distance['distance_value'];
                                }

                                if (empty($errorBag)) { //se não houve erros de validação

                                    //Grava o envio
                                    unset($shipment->customer, $shipment->service);
                                    if ($shipment->exists) {
                                        $shipment->save();
                                    } else {
                                        if ($partnerTracking) {
                                            $shipment->tracking_code = $partnerTracking; //coloca o mesmo Código de envio que o código do parceiro.
                                            $shipment->save();
                                        } else {
                                            $shipment->setTrackingCode();
                                            $shipmentsIds[] = $shipment->id;
                                        }
                                    }

                                    //grava dimensões e mercadoria
                                    $this->storeDimensions($shipment, $dimensions);
                                    if (hasModule('logistic') && !empty($dimensions)) {
                                        $shipment->customer = $customer;
                                        $shipment->storeShippingOrder();
                                    }

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

                                    //adiciona anexos
                                    if (!empty(@$input['attachments'])) {
                                        foreach (@$input['attachments'] as $attachment) {
                                            if (@$attachment['fileurl']) {
                                                FileRepository::insert([
                                                    'source'            => config('app.source'),
                                                    'parent_id'         => 4, //pasta envios
                                                    'filepath'          => $attachment['fileurl'],
                                                    'name'              => @$attachment['title'],
                                                    'source_class'      => 'Shipment',
                                                    'source_id'         => $shipment->id,
                                                    'customer_id'       => $shipment->customer_id,
                                                    'customer_visible'  => true,
                                                    'extension'         => 'web',
                                                    'created_at'        => date('Y-m-d H:i:s')
                                                ]);
                                            }
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
                                    if (!$shipmentExists && $isPaid && Setting::get('shipments_disable_email') && !empty($input['recipient_email'])) {
                                        try {
                                            $shipment->sendEmail();
                                        } catch (\Exception $e) {
                                        }
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

                                    $responseBag[$inputKey] = [
                                        'error'         => '',
                                        'message'       => 'Shipment saved successfully.',
                                        'tracking_code' => $shipment->tracking_code,
                                        'reference'     => $shipment->reference,
                                        'reference2'    => $shipment->reference2,
                                        'reference_prv' => $shipment->provider_tracking_code,
                                        'delivery_route' => [
                                            'code' => @$shipment->route->code,
                                            'name' => @$shipment->route->name
                                        ],
                                        'tracking_url'  => route('trk.index', $shipment->tracking_code)
                                    ];
                                } else { //fim do if(empty($errorBag)
                                    if ($multipleShipments) {
                                        $responseBag[$inputKey] = $errorBag;
                                    } else {
                                        $responseBag[$inputKey] = $errorBag[0];
                                    }
                                }
                            } else {

                                if ($multipleShipments) {
                                    $responseBag[$inputKey] = [$this->responseError('store', '-998', $validator->errors()->first(), true)];
                                } else {
                                    $responseBag[$inputKey][] = $this->responseError('store', '-998', $validator->errors()->first(), true);
                                }
                            }
                        }
                    } else {

                        if (!@$service) {
                            $responseBag[$inputKey][] = $this->responseError('store', '-998', 'Service required or not found', true);
                        } else if (!@$customer) {
                            $responseBag[$inputKey][] = $this->responseError('store', '-998', 'Customer required or not found', true);
                        } else {
                            $responseBag[$inputKey][] = $this->responseError('store', '-998', 'Provider required or not found', true);
                        }
                    }
                }
            } //endforeach

            if (!$multipleShipments) { //só 1 resposta individual
                $responseBag = @$responseBag[$inputKey];
            }

            return response($responseBag, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {

            $details = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            $trace = LogViewer::getTrace(null, $trace = LogViewer::getTrace(null, 'API ERROR - ' . $e->getMessage() . ' LINE ' . $e->getLine() . ' FILE ' . $e->getFile(), $details));
            Log::error(br2nl($trace));

            //APAGA TODOS OS SERVIÇOS CRIADOS
            Shipment::whereIn('id', $shipmentsIds)->delete();

            return $this->responseError('store', '-999', $e->getMessage() .' ' . $e->getLine());
        }
    }

    /**
     * Delete a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request, $apiLevel, $tracking)
    {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'destroy');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $tracking = explode(',', $tracking);

        $acceptedStatus = Setting::get('services_can_delete') ? Setting::get('services_can_delete') : [];

        $shipment = Shipment::where(function ($q) use ($tracking) {
            $q->whereIn('tracking_code', $tracking);
            $q->orWhereIn('provider_tracking_code', $tracking);
            $q->orWhereIn('reference', $tracking);
        });

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->first();

        if (!$shipment) {
            return $this->responseError('destroy', '-001');
        }

        if (!in_array($shipment->status_id, $acceptedStatus)) {
            return $this->responseError('destroy', '-002');
        }

        $shipment->delete();

        $response = [
            'error' => '',
            'message' => 'Shipment deleted successfully.'
        ];
        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a label of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getLabels(Request $request, $apiLevel, $tracking)
    {

        $tracking = explode(',', $tracking);

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getLabels');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where(function ($q) use ($tracking) {
                $q->whereIn('tracking_code', $tracking);
                $q->orWhereIn('provider_tracking_code', $tracking);
                $q->orWhereIn('reference', $tracking);
            });

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->select(['id'])
            ->first();

        if (!$shipment) {
            return $this->responseError('labels', '-001');
        }

        try {
            if ($shipment->hasSync()) {
                $labelStr = base64_encode(Shipment::printAdhesiveLabels([$shipment->id], true, $partnersApi ? 'partnersApi' : 'api', 'string'));
            } else {
                $labelStr = base64_encode(Shipment::printAdhesiveLabels([$shipment->id], true, $partnersApi ? 'partnersApi' : 'api', 'string'));
            }
        } catch (\Exception $e) {
            return $this->responseError('labels', '-999', $e->getMessage());
        }

        $response = [
            'content' => 'application/pdf',
            'label'   => $labelStr
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a label of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getCargoManifest(Request $request, $apiLevel)
    {

        $trks = explode(',', $request->get('tracking'));

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getLabels');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        if (empty($request->get('tracking'))) {
            return $this->responseError('manifest', '-001', 'Tracking field is required. Trackings must be separated by a comma');
        }

        $shipments = Shipment::whereIn('tracking_code', $trks);

        if (!$partnersApi) {
            $shipments = $shipments->where('customer_id', $customer->id);
        }

        $shipments = $shipments->get(['id']);

        if ($shipments->isEmpty()) {
            return $this->responseError('labels', '-001');
        }

        $ids = $shipments->pluck('id')->toArray();

        try {
            $fileStr = base64_encode(Shipment::printShipmentsCargoManifest($ids, 's'));
        } catch (\Exception $e) {
            return $this->responseError('labels', '-999');
        }

        $response = [
            'content'  => 'application/pdf',
            'manifest' => $fileStr
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a transportation guide of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getTransportationGuide(Request $request, $apiLevel, $tracking)
    {

        $tracking = explode(',', $tracking);

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getTransportationGuide');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where(function ($q) use ($tracking) {
                $q->whereIn('tracking_code', $tracking);
                $q->orWhereIn('provider_tracking_code', $tracking);
                $q->orWhereIn('reference', $tracking);
            });

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->select(['id'])
            ->first();

        if (!$shipment) {
            return $this->responseError('labels', '-001');
        }

        try {
            $labelStr = base64_encode(Shipment::printTransportGuide([$shipment->id], null, null, false, 'string'));
        } catch (\Exception $e) {
            return $this->responseError('labels', '-999');
        }

        $response = [
            'content' => 'application/pdf',
            'label'   => $labelStr
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a CMR of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getCMR(Request $request, $apiLevel, $tracking)
    {

        $tracking = explode(',', $tracking);

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getCMR');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where(function ($q) use ($tracking) {
                $q->whereIn('tracking_code', $tracking);
                $q->orWhereIn('provider_tracking_code', $tracking);
                $q->orWhereIn('reference', $tracking);
            });

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->select(['id'])
            ->first();

        if (!$shipment) {
            return $this->responseError('labels', '-001');
        }

        try {
            $labelStr = base64_encode(Shipment::printCmr([$shipment->id]));
        } catch (\Exception $e) {
            return $this->responseError('labels', '-999');
        }

        $response = [
            'content' => 'application/pdf',
            'label'   => $labelStr
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a POD of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getPOD(Request $request, $apiLevel, $tracking)
    {

        $tracking = explode(',', $tracking);

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getPOD');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where(function ($q) use ($tracking) {
                $q->whereIn('tracking_code', $tracking);
                $q->orWhereIn('provider_tracking_code', $tracking);
                $q->orWhereIn('reference', $tracking);
            });

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->select(['id', 'status_id'])
            ->first();

        if (!$shipment) {
            return $this->responseError('labels', '-001');
        }


        if (@$shipment->status_id != ShippingStatus::DELIVERED_ID) {
            return $this->responseError('labels', '-002', 'Prova de entrega nao disponível.');
        }

        try {
            $labelStr = base64_encode(Shipment::printPod([$shipment->id], 'S'));
        } catch (\Exception $e) {
            return $this->responseError('labels', '-999');
        }

        $response = [
            'content' => 'application/pdf',
            'label'   => $labelStr
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de um envio dado o seu código.
     *
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request, $apiLevel, $tracking)
    {

        $partnersApi = $request->has('_partner_api_');
        if ($partnersApi) {
            $this->bindings = $request->get('bindings');
        } else {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'show');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where(function ($q) use ($tracking) {
                $q->where('tracking_code', $tracking);
                $q->orWhere('provider_tracking_code', $tracking);
                $q->orWhere('reference', $tracking);
            });

        if ($partnersApi) {
            $shipment = $shipment->with(['provider' => function ($q) {
                $q->select(['id', 'code', 'name']);
            }])
                ->with(['operator' => function ($q) {
                    $q->select(['id', 'code', 'name']);
                }]);
        } else {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->select($this->bindings)
            ->first();

        if (!$shipment) {
            return $this->responseError('show', '-001');
        }

        return response($shipment, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de um envio dado o seu código.
     *
     * @param Request $request
     * @return mixed
     */
    public function history(Request $request, $apiLevel, $tracking, $massive = false)
    {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            if ($this->log_usage) {
                $trace = LogViewer::getTrace(null, 'API GET HISTORY [' . ($massive ? 'MASSIVO' : 'SINGLE') . '] - CUSTOMER ' . $customer->name);
                Log::info(br2nl($trace));
            }

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }


        $originalTracking = $tracking;
        $tracking = explode(',', $tracking);

        $searchField = 'tracking_code';
        /*if(in_array($customer->id, $this->delnextCustomers)) {
            $searchField = 'reference';
        }*/

        $bindings = [
            'created_at',
            'status_id',
            'incidence_id',
            'operator_id',
            'agency_id',
            'city',
            'obs',
            'receiver',
            'signature',
            'filepath',
            'latitude',
            'longitude',
            'vehicle'
        ];

        $sourceAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        $shipments = Shipment::with(['history' => function ($q) use ($bindings) {
            $q->with(['status' => function ($q) {
                $q->get(['id', 'name']);
            }]);
            $q->with(['incidence' => function ($q) {
                $q->get(['id', 'name']);
            }]);
            $q->with(['operator' => function ($q) {

                $fields = ['id', 'code'];

                if (Setting::get('tracking_show_operator_name')) {
                    $fields[] = 'fullname';
                }

                if (Setting::get('tracking_show_operator_phone')) {
                    $fields[] = 'professional_mobile';
                }

                $q->select($fields);
            }]);
            $q->orderBy('created_at');
            $q->get($bindings);
        }])
            //->where('customer_id', $customer->id)
            ->whereIn('agency_id', $sourceAgencies)
            ->whereIn($searchField, $tracking)
            ->get(['id', $searchField, 'operator_id']);

        if ($shipments->isEmpty()) {
            return $this->responseError('update', '-001', 'Envio não encontrado. Verifique o código do envio. #' . $originalTracking);
        }

        $histories = [];
        foreach ($shipments as $shipment) {
            $shipmentHistory = $shipment->history;
            /*if (!$shipmentHistory) {
                return $this->responseError('history', '-001');
            }*/

            $history = [];
            foreach ($shipmentHistory as $item) {
                $history[] = [
                    'date'          => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'status'        => @$item->status->name,
                    'status_id'     => $item->status_id,
                    'incidence'     => @$item->incidence->name,
                    'incidence_id'  => $item->incidence_id,
                    //'agency_id'     => $item->agency_id,
                    'receiver'      => $item->receiver,
                    'signature'     => $item->signature,
                    'attachment'    => $item->filepath ? asset($item->filepath) : null,
                    'obs'           => trim($item->obs),
                    'city'          => $item->city,
                    'latitude'      => $item->latitude,
                    'longitude'     => $item->longitude,
                    'tracking_code' => null,
                    'full_name_operator'   => @$item->operator->fullname ?? null,
                    'phone_operator'       => @$item->operator->professional_mobile ?? null,
                ];
            }

            $histories[$shipment->{$searchField}] = $history;
        }

        if (!$massive) {
            $histories = $histories[@$tracking[0]];
        }

        return response($histories, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar o histórico de multiplos envios
     *
     * @param Request $request
     * @return mixed
     */
    public function massHistory(Request $request, $apiLevel)
    {
        $tracking = $request->trackings;
        return $this->history($request, $apiLevel, $tracking, true);
    }

    /**
     * Permite inserir uma solução para uma incidência
     *
     * @param Request $request
     * @return mixed
     */
    public function resolveIncidence(Request $request, $apiLevel)
    {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'resolveIncidence');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $sourceAgencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        $shipment = Shipment::whereIn('agency_id', $sourceAgencies)
            ->where('tracking_code', $request->get('tracking'));

        if (!$partnersApi) {
            $shipment = $shipment->where('customer_id', $customer->id);
        }

        $shipment = $shipment->first(['id', 'tracking_code', 'status_id']);

        if (!$shipment) {
            return $this->responseError('incidences', '-001');
        }

        if ($shipment->status_id != ShippingStatus::INCIDENCE_ID) {
            return $this->responseError('incidences', '-004');
        }

        $action = IncidenceResolutionType::where('code', $request->get('action'))->first();
        if (!$action) {
            return $this->responseError('incidences', '-002');
        }


        if (!empty($request->get('solution_code'))) {
            $resolution = ShipmentIncidenceResolution::where('shipment_id', $shipment->id)
                ->where('solution_code', $request->get('solution_code'))
                ->first();

            $exists = true;
            if (!$resolution) {
                return $this->responseError('incidences', '-003');
            }
        } else {
            $resolution = new ShipmentIncidenceResolution();
            $exists = false;
        }

        $lastIncidence = $shipment->history
            ->sortByDesc('created_at')
            ->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID && $item->resolved == 0;
            })
            ->first();

        if (!$lastIncidence) {
            return $this->responseError('incidences', '-004', 'O envio não tem incidências por resolver.');
        }

        $resolution->resolution_type_id = $action->id;
        $resolution->shipment_id = $shipment->id;
        $resolution->shipment_history_id = $lastIncidence->id;
        $resolution->obs = $request->obs;
        $resolution->is_api = 1;

        if ($exists) {
            $resolution->save();
        } else {
            $resolution->setCode();
        }

        //Notify intervenients
        $resolution->setNotification(BroadcastPusher::getGlobalChannel(), 'Resposta à incidência do envio ' . $shipment->tracking_code, true);

        $response = [
            'code'          => '',
            'message'       => 'Solution saved successfully.',
            'solution_code' => $resolution->solution_code
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite devolver o histórico de reembolsos
     *
     * @param Request $request
     * @return mixed
     */
    public function getCOD(Request $request, $apiLevel)
    {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getCOD');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $tracking = explode(',', $request->tracking);
        if (empty($tracking)) {
            return $this->responseError('store', '-001');
        }

        $refunds = RefundControl::rightJoin('shipments', function ($join) {
            $join->on('refunds_control.shipment_id', '=', 'shipments.id');
        })
            ->whereIn('tracking_code', $tracking);

        if (!$partnersApi) {
            $refunds = $refunds->where('customer_id', $customer->id);
        }

        if ($request->has('customer')) {
            $refunds = $refunds->whereRaw('customer_id = (select id from customers where code="' . $request->customer . '" and source="' . config('app.source') . '")');
        }

        if ($request->has('devolution_date')) {
            $refunds = $refunds->where('payment_date', $request->devolution_date);
        }

        if ($request->has('devolution_method')) {
            $refunds = $refunds->where('payment_method', $request->devolution_method);
        }

        $refunds = $refunds->orderBy('shipments.date', 'desc')
            ->take(300)
            ->get([
                'shipments.tracking_code',
                'shipments.reference',
                DB::raw('shipments.charge_price as "amount"'),
                DB::raw('shipments.date as "shipment_date"'),
                'refunds_control.received_date',
                'refunds_control.received_method',
                DB::raw('refunds_control.payment_date as "devolution_date"'),
                DB::raw('refunds_control.payment_method as "devolution_method"'),
                DB::raw('refunds_control.customer_obs as "obs"'),
                'refunds_control.canceled',
                DB::raw('(CONCAT("' . env('APP_URL') . '", filepath)) as attachment'),
            ]);

        return response($refunds, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite calcular o preço de um envio
     *
     * @param Request $request
     * @return mixed
     */
    public function getPrice(Request $request, $apiLevel)
    {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'getPrice');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        } else {
            $customer = Customer::filterSource()
                ->where('code', $request->get('customer'))
                ->toArray();

            if (!$customer) {
                return $this->responseError('store', '-002', 'Customer not found.');
            }
        }

        /**
         * Validate Service
         */
        $serviceCode = $request->service;
        $service = Service::filterSource()->where('display_code', $serviceCode)->first();

        if (!$service) {
            return $this->responseError('store', '-002');
        }

        /**
         * Validate country
         */
        $allCountries = trans('country');
        $input['sender_country']    = strtolower($request->sender_country);
        $input['recipient_country'] = strtolower($request->recipient_country);

        if (!isset($allCountries[$request->sender_country])) {
            return $this->responseError('store', '-003');
        }

        if (!isset($allCountries[$request->recipient_country])) {
            return $this->responseError('store', '-004');
        }

        /**
         * Get provider
         */
        $providerId =  Setting::get('shipment_default_provider');

        if (!in_array($request->recipient_country, ['es', 'pt']) && config('app.source') == 'asfaltolargo') {
            $request->recipient_country = 'z1';
        }

        $tmpShipment = new Shipment();
        $tmpShipment->fill($request->all());
        $tmpShipment->customer_id = @$customer->id;
        $tmpShipment->agency_id   = @$customer->agency_id;
        $tmpShipment->service_id  = @$service->id;
        $tmpShipment->provider_id = $providerId;
        $prices = Shipment::calcPrices($tmpShipment);

        if(@$prices['fillable']) {

            $fillableData = $prices['fillable'];
            
            $response = [
                'error'             => '',
                'message'           => 'Price calculated successfully.',
                'subtotal'          => $fillableData['billing_subtotal'],
                'vat'               => $fillableData['billing_vat'],
                'total'             => $fillableData['billing_total'],
                'shipment_price'    => $fillableData['shipping_price'],
                'expenses_price'    => $fillableData['expenses_price'],
                'total_price'       => $fillableData['billing_subtotal'],
                'fuel_tax'          => $fillableData['fuel_tax'],
                'taxable_weight'    => @$prices['parcels']['taxable_weight'],
                'volumetric_weight' => @$prices['parcels']['volumetric_weight'],
                'exceeded_weight'   => $fillableData['extra_weight'],
                'currency'          => 'EUR'
            ];
        } else {
            $response = [
                'error'   => '-001',
                'message' => 'Unable to calculate prices for this service.',
            ];
        }
        

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get list of services
     * @param Request $request
     * @return array
     */
    public function listsServices(Request $request, $apiLevel)
    {

        if (!$request->has('__partner_api__')) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'listsServices');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $services = Service::filterSource()
            ->where('custom_prices', true);

        if (!empty($customer->enabled_services)) {
            $services->whereIn('id', $customer->enabled_services);
        }

        $services = $services
            ->ordered()
            ->get([
                DB::raw('display_code as "code"'),
                'name',
                'transit_time',
                'is_collection',
                'is_internacional'
            ]);

        return response($services, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get list of services
     * @param Request $request
     * @return array
     */
    public function listsProviders(Request $request, $apiLevel)
    {

        if (!$request->has('_partner_api_')) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'listsServices');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $providers = Provider::filterSource()
            ->where('type', 'carrier')
            ->where('is_active', true);

        if (!empty($customer->enabled_services)) {
            $providers->whereIn('id', $customer->enabled_services);
        }

        $providers = $providers
            ->ordered()
            ->get([
                'code',
                'name',
            ]);

        return response($providers, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get list of status
     * @param Request $request
     * @return array
     */
    public function listsStatus(Request $request, $apiLevel)
    {

        if (!$request->has('_partner_api_')) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'listsServices');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $refunds = ShippingStatus::filterSources()
            ->where('is_visible', true)
            ->ordered()
            ->get([
                'id',
                'name',
                'name_en',
                'name_fr',
                'name_es',
                'description',
                'description_en',
                'description_fr',
                'description_es',
            ]);

        return response($refunds, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get list of pick-up and drop over pointw
     * @param Request $request
     * @return array
     */
    public function listsPudo(Request $request, $apiLevel)
    {

        if (!$request->has('_partner_api_')) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'listsServices');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $data = PickupPoint::filterSource();

        if ($request->has('provider')) {
            $data = $data->where('provider_id', $request->get('provider'));
        }

        if ($request->has('zip_code')) {
            $zipCode = $request->get('zip_code');
            $zipCode = explode('-', $zipCode);
            $zipCode = $zipCode[0];

            $data = $data->where('zip_code', 'like', '%' . $zipCode . '%');
        }

        $data = $data->get([
            "id", "code", "provider_id", "type",
            "name", "address", "zip_code", "city", "country", "latitude", "longitude", "email",
            "phone", "mobile", "horary", "is_active", "delivery_saturday", "delivery_sunday"
        ]);

        return response($data, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get list of incidences status
     * 
     * @param Request $request
     * @return array
     */
    public function listsIncidencesStatus(Request $request)
    {
        if (!$request->has('_partner_api_')) {
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'listsServices');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $incidences = IncidenceType::filterSource()
            ->where('is_active', true)
            ->ordered()
            ->get([
                'id',
                'name'
            ]);

        return response($incidences, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Close CTT shipments
     * 
     * @param Request $request 
     * @param int $apiLevel 
     * @return Illuminate\Http\JsonResponse
     */
    public function closeCtt(Request $request, $apiLevel)
    {

        try {
            if (!$request->has('_partner_api_')) {
                $customer = Auth::guard('api')->user();
                $this->logUsage($customer, 'closeCtt');

                if ($this->checkUsageLimit()) {
                    return $this->responseUsageExceed();
                }
            }

            $trackings = $request->get('trackings');
            if (!$trackings) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Trackings can\'t be empty.',
                    'base64'    => null,
                ], 200);
            }

            $trackings = explode(',', $trackings);
            $shipments = Shipment::where('is_closed', 1)->whereIn('tracking_code', $trackings)->count();
            if ($shipments) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'There are selected shipments that are already closed. Review selected shipments and try again.',
                    'base64'    => null,
                ]);
            }

            $shipmentsIds = Shipment::whereIn('tracking_code', $trackings)
                ->pluck('id')->toArray();

            $webservice = new Base();
            $result = $webservice->closeShipments('ctt', $shipmentsIds);

            if (!$result['result']) {
                return response()->json([
                    'result'    => false,
                    'message'   => $result['feedback'],
                    'base64'    => null,
                ]);
            }

            return response()->json([
                'result'    => true,
                'message'   => 'Shipments closed successfully.',
                'base64'    => base64_encode(file_get_contents(public_path() . $result['filepath'])),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'result'    => false,
                'message'   => 'Internal server error',
            ]);
        }
    }

    /**
    * GET list of traceability 
    * @param Request $request, string apiLevel, string $tracking
    * @return array
    */
    public function traceabilityHistory(Request $request, $apiLevel, $tracking){
        
        if (!$request->has('_partner_api_')) {
            
            $customer = Auth::guard('api')->user();
            $this->logUsage($customer, 'traceabilityHistory');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }
        
        $source = config('app.source');
        
        $input  = $request->all();

        if(empty($tracking)){
            
            $data = [
                'error'   => '-001',
                'message' => 'It is necessary to indicate shipping tracking'
            ];
            
            return response($data, 404)->header('Content-Type', 'application/json');
        }
        
        $shipmentId = Shipment::where(function ($q) use($tracking){
            $q->where('tracking_code', $tracking);
            $q->orWhere('provider_tracking_code', $tracking);
        })->first();
        
        if(empty($shipmentId)){
            
            $data = [
                'error'   => '-002',
                'message' => 'Not found shipment'
            ];
            
            return response($data, 404)->header('Content-Type', 'application/json');

        }
        
        $shipmentTraceability = ShipmentTraceability::with(['agency' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Agency::CACHE_TAG);
            }])
            ->where('shipment_id', $shipmentId->id)
            ->orderBy('created_at', 'desc');
        
        if($request->has('read_point')){
            
            $shipmentTraceability = $shipmentTraceability->where('read_point', $input['read_point']);
            
        } else {
            
             $data = [
                'error'   => '-003',
                'message' => 'The read point parameter is a mandatory requirement'
            ];
            
            return response($data, 404)->header('Content-Type', 'application/json');
        }
        
        if($request->has('agency')){
            $shipmentTraceability = $shipmentTraceability->where('agency_id', $input['agency']);
        }
        
        if($request->has('date')){
            
            $firstDate = $input['date'].' '.'00:00:00';
            $endDate   = $input['date'].' '.'23:59:59';
            
            $shipmentTraceability = $shipmentTraceability->whereBetween('created_at', [$firstDate, $endDate]);

        }
        
        $shipmentTraceability = $shipmentTraceability->get();
        
        $array = [];
        $aux   = [];

        foreach($shipmentTraceability as $info){
            

            $array['read_point']     = $info['read_point'];
            $array['agency_code']    = $info['agency']['code'];
            $array['agency']         = $info['agency']['name'];
            $array['date']           = $info['created_at']->format('Y-m-d h:m:s');
            $array['tracking_code']  = ($source == "viadireta") ? $shipmentId->provider_tracking_code : $shipmentId->tracking_code;
            $array['volume']         = $info['volume'];
            $array['vehicle']        = $info['vehicle'];
            
            if($info['read_point'] == "out"){
                $array['description']   = "Saída - Delegação ".$info['agency']['name'];
            }else if($info['read_point'] == "in"){
                $array['description']   = "Entrada - Delegação ".$info['agency']['name'];
            }else if($info['read_point'] == "supervisor"){
                $array['description']   = "Controlo - Delegação ".$info['agency']['name'];
            }
            
            $aux[] = $array;
            
        }
        

        /*{
            "read_point" : "out",
            "agency_code": "LX-321",
            "agency" : "Lisboa Norte",
            "date": "2016-12-12 06:38:34",
            "tracking_code": "010019000003",
            "volume": "003",
            "volumes_total" : "4"
            "vehicle" : "34-FG-22",
            "description" : "Saída - Delegação Lisboa Norte"
        },*/
        
        return response($aux, 200)->header('Content-Type', 'application/json');
            
    }

    /**
     * Check usage limit
     * @return array
     */
    public function checkUsageLimit()
    {
        return $this->usage_exceed;
    }

    /**
     * @return array
     */
    public function responseUsageExceed()
    {
        return $this->responseError('', '-996', 'Daily maximum API calls exceeded.');
    }

    /**
     * @param $customer
     */
    public function logUsage($customer, $method)
    {
        if ($this->log_usage) {
            $trace = LogViewer::getTrace(null, 'API SHIPMENTS - ' . $method . ' - CUSTOMER ' . $customer->name);
            Log::info(br2nl($trace));
        }
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function storeRules()
    {
        return trans('api.shipments.rules');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function customAttributes()
    {
        return trans('api.shipments.attributes');
    }

    /**
     * Check usage limit
     * @return array
     */
    public function entregakiShipmentsList(Request $request, $token)
    {

        if ($token != 'NPhZZRoz9230qgFvHtVPN6OjSzInEW3MdFmxdSuDNHDa3RuuawmyPHAg4T05u0NsXe89bpgpS8KRm5Q0') {
            $response = [
                'result'    => false,
                'shipments' => null
            ];

            return response($response, 200)->header('Content-Type', 'application/json');
        }

        $date = date('Y-m-d');
        if ($request->has('date')) {
            $date = $request->get('date');
        }

        $shipments = Shipment::with(['customer' => function ($q) {
            $q->select(['id', 'code', 'vat', 'name', 'contact_email']);
        }])
            ->with(['service' => function ($q) {
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['provider' => function ($q) {
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where('agency_id', 29)
            ->where('date', $date)
            ->get([
                'tracking_code',
                'provider_tracking_code',
                'reference',
                'sender_name',
                'sender_address',
                'sender_zip_code',
                'sender_city',
                'sender_country',
                'sender_phone',
                'recipient_name',
                'recipient_address',
                'recipient_zip_code',
                'recipient_city',
                'recipient_country',
                'recipient_phone',
                'recipient_email',
                'total_price',
                'total_expenses',
                'volumes',
                'weight',
                'date',
                'billing_date',
                'status_id',
                'provider_id',
                'service_id',
                'customer_id'
            ]);

        $response = [
            'result'    => true,
            'shipments' => $shipments
        ];

        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Valida dimensões
     * @param $input
     * @param $inputDimensions
     * @return array|void
     */
    public function validateDimensions(&$input, &$errorBag)
    {

        $packTypes = PackType::filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray() ?? [];

        $inputDimensions = @$input['dimensions'];

        if (!is_array($inputDimensions)) {
            $inputDimensions = json_decode($inputDimensions, true);
            $inputDimensions = is_array($inputDimensions) ? array_filter($inputDimensions) : $inputDimensions;
        }

        unset($input['dimensions']);

        $dimensions = [];
        $createdAt = date('Y-m-d H:i:s');
        $sumM3 = $sumWeight = 0;
        if (!empty($inputDimensions)) {

            $totalStocks = [];
            foreach ($inputDimensions as $dimension) {

                $width  = forceDecimal(@$dimension['width']);
                $length = forceDecimal(@$dimension['length']);
                $height = forceDecimal(@$dimension['height']);
                $weight = forceDecimal(@$dimension['weight']);
                $qty      = @$dimension['qty'] ? @$dimension['qty'] : 1;
                $sku      = @$dimension['sku'];
                $lote     = @$dimension['lote'];
                $serialNo = @$dimension['serial_no'];
                $hasProduct = $sku || $lote || $serialNo;

                if (empty($width) || empty($length) || empty($height)) {
                    $errorBag[] = $this->responseError('store', '-010', null, true);
                }

                if (!empty(@$dimension['type']) && empty($packTypes[@$dimension['type']])) {
                    $errorBag[] = $this->responseError('store', '-011', null, true);
                }

                //VALIDATE SKU
                if (hasModule('logistic') && ($sku || $lote || $serialNo)) {
                    if (empty($sku) && ($lote || $serialNo)) {
                        $errorBag[] = $this->responseError('store', '-012', null, true);
                    }

                    $allProducts = Product::where('sku', $sku)
                        ->orderBy('stock_total', 'desc')
                        ->get([
                            'customer_id', 'sku', 'serial_no', 'lote', 'stock_status', 'expiration_date',
                            'stock_total', 'stock_allocated', 'width', 'height', 'length', 'weight', 'name', 'id'
                        ]);

                    $product = $allProducts->filter(function ($q) use ($sku, $serialNo, $lote) {
                        if ($serialNo) {
                            return $q->serial_no == strtoupper($serialNo);
                        } elseif ($lote) {
                            return $q->lote == strtoupper($lote);
                        } else {
                            return $q->sku == strtoupper($sku);
                        }
                    });

                    //escolhe só 1 produto caso existam vários
                    if ($product->count() == 1) {
                        $product = $product->first();
                    } elseif ($product->count() > 1) {
                        //escolhe o que tem validade menor
                        $productTmp = $product->filter(function ($q) {
                            return $q->stock_total > 0;
                        })->sortBy('expiration_date');

                        $stkTtl = $productTmp->sum('stock_total'); //soma o stock total de todos os lotes
                        $stkAlc = $productTmp->sum('stock_allocated');

                        //dd($stkAlc);
                        $productTmp = $productTmp->first(); //obtem o primeiro registo e faz ficticio o uso de stocks
                        if ($productTmp) {
                            @$productTmp->stock_total = $stkTtl;
                            @$productTmp->stock_allocated = $stkAlc;
                        }

                        //dd($dimension['qty']);
                        if (empty($product)) { //acontece quando todas as linhas com o mesmo SKU têm todas stock a 0
                            $productTmp = $product->first();
                        }

                        $product = $productTmp;
                    }


                    if ($hasProduct) {
                        $totalStocks[@$product->id] = @$totalStocks[@$product->id] + $qty;

                        if (empty($product) || !@$product->exists) {
                            $errorBag[] = $this->responseError('store', '-014', 'Item ' . $sku . ': Not found. Check SKU, Serial No or Lote', true);
                        } elseif (!empty($product->stock_status) && $product->stock_status == 'blocked') {
                            $errorBag[] = $this->responseError('store', '-015', 'Item ' . $sku . ': Item bloqued', true);
                        } elseif (!empty($product->serial_no) && $qty > 1) {
                            $errorBag[] = $this->responseError('store', '-016', 'Item ' . $sku . ': It is only possible to order 1 unit of the item.', true);
                        } elseif ($qty > ($product->stock_total - $product->stock_allocated)) {
                            if ($product->stock_total > 0) {
                                $errorBag[] = $this->responseError('store', '-017', 'Item ' . $sku . ': Only ' . ($product->stock_total - $product->stock_allocated) . 'un available.', true);
                            }
                            $errorBag[] = $this->responseError('store', '-017', 'Item ' . $sku . ': No stock available.', true);
                        } elseif ($qty > $product->stock_total) {
                            if ($product->stock_total > 0) {
                                $errorBag[] = $this->responseError('store', '-017', 'Item ' . $sku . ': Only ' . ($product->stock_total - $product->stock_allocated) . 'un available.', true);
                            }
                            $errorBag[] = $this->responseError('store', '-017', 'Item ' . $sku . ': No stock available.', true);
                        }

                        if (!empty(@$product) && @$totalStocks[@$product->id] > @$product->stock_total && @$product->stock_total > 0) {
                            $errorBag[] = 'Item ' . $dimension['sku'] . ':  Stock totaly allocated.';
                        }
                    }
                }

                $volumeM3 = ($width * $length * $height) / 1000000;
                $sumM3 += $volumeM3 * $qty;
                $sumWeight += $weight * $qty;

                $dimensions[] = [
                    'width'       => $width,
                    'length'      => $length,
                    'height'      => $height,
                    'weight'      => $weight,
                    'qty'         => @$dimension['qty'] ? $dimension['qty'] : 1,
                    'type'        => @$dimension['type'] ? $dimension['type'] : 'box',
                    'volume'      => $volumeM3,
                    'price'       => @$dimension['price'],
                    'sku'         => @$dimension['sku'] ? $dimension['sku'] : null,
                    'serial_no'   => @$dimension['serial_no'] ? $dimension['serial_no'] : null,
                    'lote'        => @$dimension['lote'] ? $dimension['lote'] : null,
                    'description' => (@$dimension['sku'] && @$product->name) ? @$product->name : @$dimension['description'],
                    'product_id'  => @$product->id,
                    'created_at'  => $createdAt,
                    'optional_fields' => @$dimension['optional_fields'] ?? null,
                    'barcode'         => @$dimension['barcode'] ?? null
                ];
            }

            $input['fator_m3'] = $sumM3;
            $input['weight']   = $sumWeight > 0.00 ? $sumWeight : $input['weight'];

            return $dimensions;
        }
    }

    /**
     * Store shipment dimensions of packs
     *
     * @param $shipment
     * @param $input
     */
    public function storeDimensions($shipment, $inputDimensions)
    {
        if (!empty($inputDimensions)) {
            ShipmentPackDimension::where('shipment_id', $shipment->id)->delete();
            foreach ($inputDimensions as $dimension) {
                $dimension['shipment_id'] = $shipment->id;
                ShipmentPackDimension::insert($dimension);
            }
        }
    }


    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null, $returnArr = false)
    {

        $errors = trans('api.shipments.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        if ($returnArr) {
            return $data;
        }

        return response($data, 404)->header('Content-Type', 'application/json');
    }


    /**
     * Store shipment from decathlon (TTMB)
     * @return response
     */
    public function createDecathlon(Request $request, $apiLevel = 'v1')
    {

        $json = $request->getContent();
        $shipments = json_decode($json);




        $lastReference = 0;
        $cont = 0;
        $responses = [];
        $products = [];
        $contResponse = 0;
        $actualDate = Carbon::now();

        foreach ($shipments as $inputs) {
            foreach ($inputs as $input) {


                // $serviceCode = $input->parcel->service;                        
                $serviceCode = 'ECM';

                $service = Service::filterSource()->where('display_code', $serviceCode)->first();
                $agency = Agency::filterSource()->first();


                if ($lastReference !== $input->parcel->saleOrder) {
                    $cont++;

                    //CALCULAR OS VOLUMES
                    if (isset($shipment) && $lastReference !== 0 && isset($products)) {
                        foreach ($products as $product) {
                            $assembly = $this->products_assembly_decathlon($product['productCode']);
                            $optional_fields = null;
                            if ($assembly != null)
                                $optional_fields = '{"Montagem":"1"}';

                            $dimensions[] = [
                                'qty'               => 1,
                                'type'              => "box",
                                'width'             => "0.01",
                                'height'            => "0.01",
                                'length'            => "0.01",
                                'description'       => $product['productDescription'],
                                'weight'            => $product['weight'],
                                'optional_fields'   => $optional_fields,
                                'barcode'           => $product['productCode'] ?? null
                            ];
                        }
                        $shipment->dimensions = $dimensions;
                        $requestData = new Request($shipment->toArray());
                        $response = $this->store($requestData, $apiLevel);
                        $response = json_decode($response->getContent(), true);
                        $responses[$contResponse] = $response;
                        $products = array();
                        $dimensions = array();
                        $contResponse++;
                    }

                    $date = Carbon::createFromFormat('Y-m-d', $input->parcel->date);
                    $formattedDate = $date->format('Y-m-d');


                    $shipment                       = new Shipment();
                    $shipment->reference            = $input->parcel->saleOrder;
                    // $shipment->reference2            = $input->parcel->parcelTrackingNumber;
                    $shipment->date                 = $actualDate->format('Y-m-d');
                    $shipment->shipping_date        = $formattedDate;
                    $shipment->service_id           = @$service->id;
                    $shipment->service              = @$serviceCode;
                    $shipment->agency_id            = @$agency->id;
                    $shipment->recipient_agency_id  = @$agency->id;
                    $shipment->sender_agency_id     = @$agency->id;
                    $shipment->customer_id          = '65';
                    $shipment->provider_id          = '1';
                    $shipment->sender_name          = $input->parcel->sender_name;
                    $shipment->sender_address       = $input->parcel->sender_address;
                    $shipment->sender_zip_code      = $input->parcel->sender_zip_code;
                    $shipment->sender_city          = $input->parcel->sender_city;
                    $shipment->sender_country       = $input->parcel->sender_country;
                    $shipment->sender_phone         = $input->parcel->sender_phone;
                    $shipment->recipient_name       = $input->parcel->recipient_name;
                    $shipment->recipient_address    = $input->parcel->recipient_address;
                    $shipment->recipient_zip_code   = $input->parcel->recipient_zip_code;
                    $shipment->recipient_city       = $input->parcel->recipient_city;
                    $shipment->recipient_country    = $input->parcel->recipient_country;
                    $shipment->recipient_phone      = $input->parcel->recipient_phone;
                    // $shipment->recipient_attn       = $input->parcel->recipientAttn;
                    $shipment->recipient_email      = $input->parcel->recipient_email;
                    $shipment->volumes              = $input->parcel->volumes;
                    $shipment->weight               = $input->parcel->weight;

                    $boxDiscription = '';
                    $boxWeigh = 0;
                    $productCode = '';
                    foreach ($input->parcel->products as $product) {
                        $productCode = $product->productCode;
                        if ($boxDiscription == '')
                            $boxDiscription = $product->quantity  . ' ' . $product->productDescription;
                        else
                            $boxDiscription = $boxDiscription . ' + ' . $product->quantity . ' ' . $product->productDescription;
                        $boxWeigh       = $boxWeigh + $product->weight;
                    }

                    $newProduct = array(
                        'productDescription'    => $boxDiscription,
                        'weight'                => $boxWeigh,
                        'productCode'           => $productCode,
                    );

                    $products[] = $newProduct;

                    $lastReference = $shipment->reference;
                } else {

                    $boxDiscription = '';
                    $boxWeigh = 0;
                    foreach ($input->parcel->products as $product) {
                        $productCode = $product->productCode;
                        if ($boxDiscription == '')
                            $boxDiscription = $product->quantity  . ' ' . $product->productDescription;
                        else
                            $boxDiscription = $boxDiscription . ' + ' . $product->quantity . ' ' . $product->productDescription;
                        $boxWeigh       = $boxWeigh + $product->weight;
                    }

                    $newProduct = array(
                        'productDescription'        => $boxDiscription,
                        'weight'                    => $boxWeigh,
                        'productCode'               => $productCode,
                    );

                    $products[] = $newProduct;
                }
            }

            //calcular os ultimos produtos e submeter o ultimo envio
            foreach ($products as $product) {
                $assembly = $this->products_assembly_decathlon($product['productCode']);
                $optional_fields = null;
                if ($assembly != null)
                    $optional_fields = '{"Montagem":"1"}';

                $dimensions[] = [
                    'qty'           => 1,
                    'type'          => "box",
                    'width'         => "0.01",
                    'height'        => "0.01",
                    'length'        => "0.01",
                    'description'   => $product['productDescription'],
                    'weight'        => $product['weight'],
                    'optional_fields'   => $optional_fields,
                    'barcode'           => $product['productCode'] ?? null
                ];
            }

            $shipment->dimensions = $dimensions;

            $requestData = new Request($shipment->toArray());
            $response = $this->store($requestData, $apiLevel);
            $response = json_decode($response->getContent(), true);
            $responses[$contResponse] = $response;


            return $responses;
        }
    }


    /**
     * 
     * @return string
     */
    function replace_last_occurrence($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    /**
     * VERIFY LIST CODES OF DECATHLON
     * @return string
     */
    function products_assembly_decathlon($productCode)
    {

        $listProducts = array(
            "2159573"       => "Montagem Piscina",
            "2144227"       => "Montagem Tabela de Basquetebol",
            "2144228"       => "Montagem Mesa de Ping-Pong",
            "2144225"       => "Montagem Máquina de Musculação",
            "2144226"       => "Montagem Trampolim",
            "2144223"       => "Montagem Passadeira Fitness",
            "2144222"       => "Montagem Bicicleta Elíptica",
            "2144224"       => "Montagem Banco de Musculação",
            "2144220"       => "Montagem Bicicleta Estática"
        );

        return $listProducts[$productCode] ?? null;
    }
}
