<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Agency;
use App\Models\ShippingExpense;
use App\Models\ShipmentExpense;
use App\Models\Map;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\LogViewer;
use App\Models\Sms\Sms;
use App\Models\User;
use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date, Log;


class ShipmentsController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Set authorized fields to store
     * @var array
     */
    protected $authorizedFields = [
        'reference',
        'weight',
        'volumes',
        'kms',
        'scannedBarCodes',
        'reference',
        'reference2',
        'reference3',
        'obs',
        'obs_delivery',
        'delivery_hour',
        'refund_method',
        'cod_method',
        'peage_price'
    ];


    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'id',
        'tracking_code',
        'parent_tracking_code',
        'children_tracking_code',
        'children_type',
        'type',
        'reference',
        'reference2',
        'reference3',
        'sender_attn',
        'sender_name',
        'sender_address',
        'sender_zip_code',
        'sender_city',
        'sender_country',
        'sender_phone',
        'recipient_attn',
        'recipient_name',
        'recipient_address',
        'recipient_zip_code',
        'recipient_city',
        'recipient_country',
        'recipient_phone',
        'recipient_attn',
        'vehicle',
        'trailer',
        'volumes',
        'weight',
        'kms',
        'hours',
        'volumetric_weight',
        'fator_m3',
        'total_price',
        'total_expenses',
        'charge_price',
        'cost_price',
        'total_price_for_recipient',
        'payment_at_recipient',
        'complementar_services',
        'has_return',
        'is_collection',
        'date',
        'start_hour',
        'end_hour',
        'obs',
        'obs_delivery',
        'obs_internal',
        'service_id',
        'status_id',
        'customer_id',
        'invoice_doc_id',
        'invoice_type',
        'price_fixed',
        'is_blocked',
        'sort',
        'cod',
        'shipping_price',
        'vehicle',
        'trailer'
    ];

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request)
    {

        $input = $request->all();

        if (empty($input)) {
            return $this->responseError('lists', '-002');
        }

        $user = $this->getUser(@$input['user']);
        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $operatorId = @$user->id;

        $statusIds = [36, 38, 37, 31, 20, 21, 22, 20, 16, 3, 4];
        if (Setting::get('mobile_app_status_delivery')) {
            $statusIds = Setting::get('mobile_app_status_delivery');
        }

        $shipments = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name', 'priority_color', 'priority_level', 'delivery_hour', 'transit_time']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name', 'color']);
            }])
            ->with(['customer' => function ($q) {
                $q->select(['id', 'code', 'code_abbrv', 'name']);
            }])
            ->with(['pack_dimensions' => function ($q) {
                $q->select(['id', 'shipment_id', 'description', 'qty', 'weight', 'width', 'height', 'length', 'type']);
            }]);

        if (hasModule('shipment_attachments')) {
            $shipments = $shipments->with(['attachments' => function ($q) {
                $q->where('operator_visible', 1);
                $q->select(['id', 'source_id', \DB::raw('source_id as shipment_id'), 'name', 'filepath']);
            }]);
        }

        $shipments = $shipments->with(['last_history' => function ($q) {
            $q->with(['incidence' => function ($q) {
                $q->select(['id', 'name']);
            }]);
            $q->select(['id', 'shipment_id', 'incidence_id', 'receiver', 'signature', 'filepath', 'obs', 'latitude', 'longitude', 'created_at']);
        }])
            ->where('is_collection', 0)
            ->where(function ($q) use ($operatorId) {
                $q->where('operator_id', $operatorId);

                if (!Setting::get('mobile_app_show_scheduled')) {
                    $q->where('date', '<=', date('Y-m-d')); //oculta envios agendados
                }

                if (config('app.source') == 'asfaltolargo') {
                    $q->orWhere(function ($q) {
                        $q->where('operator_id', 375);
                        $q->where(function ($q) {
                            $q->where('agency_id', 1);
                            $q->orWhere('recipient_agency_id', 1);
                        });
                    });
                }
            });

        //filter status load api
        if ($request->has('sync') && !empty($request->get('sync'))) {

            $transportStatus = $statusIds;
            $incidenceStatus = [
                ShippingStatus::INCIDENCE_ID
            ];
            $finalStatus = [
                ShippingStatus::DELIVERED_ID,
                ShippingStatus::DELIVERED_PARTIAL_ID,
                ShippingStatus::DEVOLVED_ID,
                ShippingStatus::CANCELED_ID,
                ShippingStatus::PICKUP_DONE_ID,
                ShippingStatus::PICKUP_FAILED_ID
            ];

            $transportShipments = clone $shipments;
            $incidenceShipments = clone $shipments;
            $deliveredShipments = clone $shipments;

            $transportShipments = $transportShipments->whereIn('status_id', $transportStatus)->get($this->bindings);

            $incidenceShipments = $incidenceShipments->whereHas('last_history', function ($q) { //mostra so serviços hoje
                $q->whereRaw('DATE(created_at) = "' . date('Y-m-d') . '"');
            })->whereIn('status_id', $incidenceStatus)->get($this->bindings);

            $deliveredShipments = $deliveredShipments->whereHas('last_history', function ($q) { //mostra so serviços hoje
                $q->whereRaw('DATE(created_at) = "' . date('Y-m-d') . '"');
            })->whereIn('status_id', $finalStatus)->orderBy('date', 'desc')->take(100)->get($this->bindings);

            $shipmentsIdsTranport  = $transportShipments->pluck('id')->toArray();
            $shipmentsIdsIncidence = $incidenceShipments->pluck('id')->toArray();
            $shipmentsIdsDelivery  = $deliveredShipments->pluck('id')->toArray();
            
            $deliverMangement = TripShipment::with('manifest')
                                            ->where(function($q) use($shipmentsIdsTranport, $shipmentsIdsIncidence, $shipmentsIdsDelivery) {
                                                $q->whereIn('shipment_id', $shipmentsIdsTranport);
                                                $q->orWhereIn('shipment_id', $shipmentsIdsIncidence);
                                                $q->orWhereIn('shipment_id', $shipmentsIdsDelivery);
                                            })
                                            ->get()
                                            ->pluck('manifest.code', 'shipment_id')
                                            ->toArray();


            foreach($transportShipments as $item){
                $item['manifest'] = @$deliverMangement[$item->id];
            }
            
            foreach($incidenceShipments as $item){
                $item['manifest'] = @$deliverMangement[$item->id];
            }
            
            foreach($deliveredShipments as $item){
                $item['manifest'] = @$deliverMangement[$item->id];
            }
            
            $settingsController = new SettingsController();

            $settings = new SettingsController();
            $settings = $settings->getSettings($request, Auth::user(), true);

            $shipments = [
                'transport' => $transportShipments,
                'incidence' => $incidenceShipments,
                'delivered' => $deliveredShipments,
                'version'   => $settingsController->getApkVersion(),
                'logout'    => false,
                'settings'  => $settings
            ];

            /*
            $trace = LogViewer::getTrace(null, 'Auto SYNC for user: ' . $user->name);
            Log::debug(br2nl($trace));
            */

            return response($shipments, 200)->header('Content-Type', 'application/json');
        }

        //filter trk
        if ($request->has('trk')) {
            $shipments = $shipments->where('tracking_code', $request->get('trk'));
        }

        //filter date
        if ($request->has('date')) {
            $shipments = $shipments->where('date', $request->get('date'));
        }

        //filter pickup
        if ($request->has('pickup')) {
            $shipments = $shipments->where('is_collection', $request->get('pickup'));
        }

        //filter status (delivered, incidence, transport)
        $status = $request->get('status');
        if ($request->has('status') && in_array($status, ['delivered', 'incidence'])) {
            if ($status == 'incidence') {
                $shipments = $shipments->where('status_id', ShippingStatus::INCIDENCE_ID);
            } elseif ($status == 'delivered') {
                $shipments = $shipments->whereHas('status', function ($q) {
                    $q->where('is_final', 1);
                });
            }
        } else {
            $shipments = $shipments->whereIn('status_id', $statusIds);
        }

        $shipments = $shipments->orderBy('date', 'desc')
            /*->orderBy('recipient_name')
            ->orderBy('recipient_zip_code', 'asc')*/
            ->take(100)
            ->get($this->bindings);


        $shipmentIds = $shipments->pluck('id')->toArray();    

        $deliverMangement = TripShipment::with('manifest')
            ->whereIn('shipment_id', $shipmentIds)
            ->get()
            ->pluck('manifest.code', 'shipment_id')
            ->toArray();

        //acrescentar a listagem caso esteja associado a um manifesto
        foreach($shipments as $item){
            $item['manifest'] = @$deliverMangement[$item->id];
        }

        if(!$shipments) {
            return $this->responseError('lists', '-001') ;
        }

        return response($shipments, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de um envio dado o seu código.
     *
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request, $tracking)
    {

        $user = $this->getUser($request->get('user'));

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $filterById = false;
        if ($request->has('qrcode') && $request->get('qrcode')) {
            if (strlen($tracking) == 7) {
                //$filterById = true; //comentado em 27/07 para evitar que ao picar códigos da delnext o sistema abra envios da asfalto
                $filterById = false;
            } else if (strlen($tracking) == 15 || strlen($tracking) == 18) {
                $tracking = substr($tracking, 0, 12);
            }
        }

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->pluck('id')
            ->toArray();

        $shipment = Shipment::with(['service' => function ($q) {
            $q->select(['id', 'code', 'name', 'priority_color', 'priority_level', 'delivery_hour', 'transit_time']);
        }])
            ->with(['status' => function ($q) {
                $q->select(['id', 'name', 'color']);
            }])
            ->with(['customer' => function ($q) {
                $q->select(['id', 'code', 'code_abbrv', 'name']);
            }])
            ->with(['last_history' => function ($q) {
                $q->with(['incidence' => function ($q) {
                    $q->select(['id', 'name']);
                }]);
                $q->select(['id', 'shipment_id', 'incidence_id', 'receiver', 'signature', 'filepath', 'latitude', 'longitude', 'created_at']);
            }])
            ->with(['attachments' => function ($q) {
                $q->where('operator_visible', 1);
                $q->select(['name', 'filepath']);
            }])
            ->with(['pack_dimensions' => function ($q) {
                $q->select(['id', 'shipment_id', 'description', 'qty', 'weight', 'width', 'height', 'length', 'type']);
            }])
            ->where(function ($q) use ($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            });

        if ($request->has('force_operator')) {
            $shipment = $shipment->where('operator_id', $user->id);
        }


        if ($filterById) {
            $shipment = $shipment->whereId($tracking)
                ->select($this->bindings)
                ->first();
        } else {
            $shipment = $shipment->where(function ($q) use ($tracking) {
                    $q->where('tracking_code', $tracking);
                    $q->orWhere('provider_tracking_code', $tracking);
                    $q->orWhere('reference', $tracking);
                })
                ->select($this->bindings)
                ->first();
        }

        //Associa de imediato o envio ao operador que fez leu o código QR
        /*if($shipment->operator_id != $user->id) {
            $shipment->update(['operator_id' => $user->id]);
        }*/

        if(!$shipment) {
            return $this->responseError('show', '-001');
        }

        $deliveryMangement = TripShipment::with('manifest')
            ->where('shipment_id', $shipment->id)
            ->get()
            ->pluck('manifest.code')
            ->toArray();

        $shipment->{'manifest'} = @$deliveryMangement[0];

        $attachments = $shipment->attachments;

        $attachments[] = [
            'name'     => 'eCMR - CMR Digital',
            'filepath' => route('api.mobile.shipments.cmr.download')
        ];

        $shipment->attachments = $attachments;

        if(!$shipment) {
            return $this->responseError('show', '-001');
        }

        return response($shipment, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de um envio dado o seu c贸digo.
     *
     * @param Request $request
     * @return mixed
     */
    public function history(Request $request, $tracking)
    {

        $user = $this->getUser($request->get('user'));

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $bindings = [
            'created_at',
            'status_id',
            'incidence_id',
            'obs',
            'receiver',
            'signature',
            'filepath',
            'latitude',
            'longitude'
        ];

        $allHistories = ShipmentHistory::with('status', 'incidence')
            ->whereHas('shipment', function ($q) use ($tracking, $user, $request) {
                $q->where(function ($q) use ($tracking) {
                    $q->where('tracking_code', $tracking);
                    /*$q->orWhere('provider_tracking_code', $tracking);
                    $q->orWhere('reference', $tracking);*/
                });

                if ($request->has('force_operator')) {
                    $q->where('operator_id', $user->id);
                }
            })
            ->orderBy('created_at')
            ->get($bindings);

        if (!$allHistories) {
            return $this->responseError('history', '-001');
        }

        $history = [];
        foreach ($allHistories as $item) {

            $history[] = [
                'date'           => $item->created_at->format('Y-m-d H:i:s'),
                'status_name'    => @$item->status->name,
                'status_color'   => @$item->status->color,
                'status_id'      => $item->status_id,
                'incidence_name' => @$item->incidence->name,
                'incidence_id'   => $item->incidence_id,
                'receiver'       => $item->receiver,
                'signature'      => $item->signature,
                'filepath'       => $item->filepath,
                'obs'            => $item->obs,
                'latitude'       => $item->latitude,
                'longitude'      => $item->longitude
            ];
        }

        return response($history, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update shipment data
     * @param Request $request
     * @param $tracking
     * @return mixed
     */
    public function update(Request $request, $tracking)
    {
        $user = $this->getUser($request->get('user'));

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->pluck('id')
            ->toArray();

        //dd($expenses);

        try {
            $shipment = Shipment::where('tracking_code', $tracking)
                ->where(function ($q) use ($sourceAgencies) {
                    $q->whereIn('agency_id', $sourceAgencies);
                    $q->orWhereIn('recipient_agency_id', $sourceAgencies);
                })
                ->first($this->bindings);




            if (!$shipment) {
                $this->responseError('update', '-001');
            }


            /*if($request->get('peage_price')) {
                $expense = ShippingExpense::where('type', 'peages')->first();
                    
                if($expense) {
                    $shipmentExpense = ShipmentExpense::where('shipment_id', $shipment->id)
                                ->where('expense_id', $expense->id)
                                ->first();

                    if(!$shipmentExpense) {
                        $shipmentExpense = new ShipmentExpense();
                    }

                    $shipmentExpense->expense_id  = $expense->id;
                    $shipmentExpense->shipment_id = $shipment->id;
                    $shipmentExpense->qty = 1;
                    $shipmentExpense->subtotal = $request->get('peage_price');
                    $shipmentExpense->price = $request->get('peage_price');
                    $shipmentExpense->date = date('Y-m-d');
                    $shipmentExpense->save();
                }
    
            }*/

            $input = array_filter($request->only($this->authorizedFields));

            if (empty($input)) {
                return $this->responseError('update', '-002');
            }
            //dd($input);
            $shipment->fill($input);

            if ($request->get('peage_price')) {

                $expense = ShippingExpense::where('type', 'peages')->first();

                if ($expense) {
                    $shipmentExpense = ShipmentExpense::where('shipment_id', $shipment->id)
                        ->where('expense_id', $expense->id)
                        ->first();

                    if (!$shipmentExpense) {
                        $shipmentExpense = new ShipmentExpense();
                    }

                    $shipmentExpense->expense_id  = $expense->id;
                    $shipmentExpense->shipment_id = $shipment->id;
                    $shipmentExpense->qty = 1;
                    $shipmentExpense->subtotal = $request->get('peage_price');
                    $shipmentExpense->price = $request->get('peage_price');
                    $shipmentExpense->date = date('Y-m-d');
                    $shipmentExpense->save();
                }
            }

            //dd($shipment);

            //Scanned codes from app
            if ($request->has('scannedBarCodes')) {
                $references = $request->get('scannedBarCodes');
                $references = explode(',', $references);
                $references = array_filter($references);

                if (!empty($references)) {
                    $references = implode(',', $references);
                    $shipment->obs = $shipment->obs . ' #COD: ' . $references;
                }
            }

            if ($request->has('delivery_hour')) {

                $timeWindow = $request->get('delivery_hour');
                $timeWindow = explode('-', $timeWindow);

                $timeWindowMin = trim(@$timeWindow[0]);
                $timeWindowMax = trim(@$timeWindow[1]);

                $date    = date('Y-m-d');
                $minHour = $date . ' ' . $timeWindowMin . ':00';
                $maxHour = $date . ' ' . $timeWindowMax . ':00';

                $shipment->estimated_delivery_time_min = $minHour;
                $shipment->estimated_delivery_time_max = $maxHour;

                //send SMS
                if ($shipment->recipient_phone) {

                    $url = request()->getHttpHost() . '/trk/' . $shipment->tracking_code;

                    $msg = "A sua encomenda de " . str_limit($shipment->sender_name, 50) . " sera entregue hoje entre as " . $timeWindowMin . " e as " . $timeWindowMax . ".\n";
                    $msg .= "Informe-nos caso nao seja possivel.\n";
                    $msg .= "Siga entrega: " . $url;

                    try {
                        $sms = new Sms();
                        $sms->send([
                            'to' => $shipment->recipient_phone,
                            'message' => $msg
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $shipment->save();

            $response = [
                'error'     => '',
                'feedback'  => 'Envio atualizado com sucesso.',
            ];

            return response($response, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response($e->getMessage(), 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Store timer log
     *
     * @param Request $request
     * @return mixed
     */
    public function timer(Request $request)
    {

        $input = $request->all();

        $user = $this->getUser($request->get('user'));

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        if (empty($request->tracking)) {
            return $this->responseError('shipments', '-002');
        }

        //get time
        $time = $request->get('time');

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if (!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        $result = [
            'code' => '',
            'feedback' => 'Saved sucessfully'
        ];

        return response($result, 200)->header('Content-Type', 'application/json');

        //verify if customer has active logs
        $log = UsageLog::firstOrNew([
            'vehicle_id'  => $request->vehicle,
            'operator_id' => $user->id
        ]);

        if ($log->exists) {

            if ($request->kms <= $log->start_km) {
                return $this->responseError('fleet', '-004');
            }

            $log->end_date = date('Y-m-d H:i:s');
            $log->end_km   = $request->kms;
            $log->save();
        } else {
            $log->start_date = date('Y-m-d H:i:s');
            $log->start_km   = $request->kms;
            $log->save();
        }

        $result = [
            'code' => '',
            'feedback' => 'Log saved sucessfully'
        ];

        return response($result, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Print transport guide
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function printTransportGuide(Request $request, $trackingCode)
    {

        $userHash = $request->get('user');

        if (str_contains($userHash, config('app.source'))) {
            $userHash = explode('-', $userHash);
            $user = @$userHash[1];
            $user = User::where('source', config('app.source'))->where('id', $user)->first();
        } else {
            $user = $this->getUser($userHash);
        }

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $shipment = Shipment::where('tracking_code', $trackingCode)->first(['id']);
        if ($shipment) {
            return Shipment::printTransportGuide([$shipment->id]);
        }
        return false;
    }

   /**
     * Print CMR
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function printCMR(Request $request, $trackingCode)
    {

        $userHash = $request->get('user');

        if (str_contains($userHash, config('app.source'))) {
            $userHash = explode('-', $userHash);
            $user = @$userHash[1];
            $user = User::where('source', config('app.source'))->where('id', $user)->first();
        } else {
            $user = $this->getUser($userHash);
        }

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $shipment = Shipment::where('tracking_code', $trackingCode)->first(['id']);
        if ($shipment) {
            return Shipment::printCMR([$shipment->id]);
        }
        return false;
    }

    /**
     * Print Proof Of Delivery POD
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function printPOD(Request $request, $trackingCode)
    {

        $userHash = $request->get('user');

        if (str_contains($userHash, config('app.source'))) {
            $userHash = explode('-', $userHash);
            $user = @$userHash[1];
            $user = User::where('source', config('app.source'))->where('id', $user)->first();
        } else {
            $user = $this->getUser($userHash);
        }

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $shipment = Shipment::where('tracking_code', $trackingCode)->first(['id']);
        if ($shipment) {
            return Shipment::printPOD([$shipment->id]);
        }
        return false;
    }

    /**
     * Print labels
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function printLabels(Request $request, $trackingCode)
    {

        $user = $this->getUser($request->get('user'));

        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $shipment = Shipment::where('tracking_code', $trackingCode)->first(['id']);
        if ($shipment) {
            return Shipment::printAdhesiveLabels([$shipment->id]);
        }
        return false;
    }

    /**
     * Get traceability Check
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function traceabilityCheck(Request $request, $trackingCode)
    {
        return Shipment::where('tracking_code', $trackingCode)->first(['recipient_name']);
    }

    /**
     * Optimize shipment Delivery
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function optimizeDelivery(Request $request)
    {

        if (!hasModule('maps')) {
            return $this->responseError('login', '-999', 'Funcionalidade não contratada.');
        }

        $user       = $this->getUser($request->get('user'));
        $operatorId = @$user->id;
        if (!$user) {
            return $this->responseError('login', '-002');
        }

        $curLat = $request->get('latitude');
        $curLng = $request->get('longitude');
        if (empty($curLat) || empty($curLng)) {
            return $this->responseError('optimize', '-001', 'Não foi possível determinar a sua localização.');
        }

        $statusIds = [36, 38, 37, 31, 20, 21, 22, 20, 16, 3, 4];
        if (Setting::get('mobile_app_status_delivery')) {
            $statusIds = Setting::get('mobile_app_status_delivery');
        }

        //Obtem envios em transporte
        $shipmentsIds = Shipment::where('is_collection', 0)
            ->where(function ($q) use ($operatorId) {
                $q->where('operator_id', $operatorId);
                $q->where('date', '<=', date('Y-m-d')); //oculta envios agendados
            })
            ->whereIn('status_id', $statusIds)
            ->pluck('id')
            ->toArray();

        try {
            $deliveryOrder = Map::optimizeDelivery($shipmentsIds, ['origin_lat' => $curLat, 'origin_lng' => $curLng, 'return_key' => 'trk']);
            $deliveryOrder = array_flip($deliveryOrder);
        } catch (\Exception $e) {
            return $this->responseError('optimize', '-099', $e->getMessage());
        }

        try {

            $response = [
                'error'     => '',
                'feedback'  => 'Entregas otimizadas com sucesso.',
                'order'     => $deliveryOrder
            ];

            return response($response, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response($e->getMessage(), 500)->header('Content-Type', 'application/json');
        }
    }

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
}
