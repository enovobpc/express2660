<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use App\Models\Traceability\ShipmentTraceability;
use App\Models\LogViewer;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date, Log, DB;

class TraceabilityController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Get traceability Check
     * @param $trackingCode
     * @return \App\Models\type
     */
    public function check(Request $request, $trackingCode) {

        $input = $request->all();

     /*   Mail::raw(print_r($input, true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        $finalStatus = ShippingStatus::where('is_final')
            ->pluck('id')
            ->toArray();

        $bindings = [
            'sender_name',
            'sender_zip_code',
            'sender_city',
            'recipient_name',
            'recipient_zip_code',
            'recipient_city',
            'volumes',
            'weight',
            'date',
            'service_id',
            'status_id',
        ];

        if ($request->accept_others) {
            if (strlen($trackingCode) == 26) {
                //envios sending
                $providerName = 'sending';
                $input['current_volume'] = substr($trackingCode, -3);
                $trackingCode = substr($trackingCode, 0, 12);
            }

            $shipment = Shipment::where(function ($q) use ($trackingCode) {
                $q->where('tracking_code', $trackingCode);
                $q->orWhere('reference', $trackingCode);
                $q->orWhere('provider_tracking_code', $trackingCode);
            })
                ->whereNotIn('status_id', $finalStatus)
                ->first($bindings);
        } else {
            $shipment = Shipment::where('tracking_code', $trackingCode)
                ->whereNotIn('status_id', $finalStatus)
                ->first($bindings);
        }

        if ($shipment) {
            if (@$input['current_volume']) {
                $shipment->current_volume = @$input['current_volume'];
            } else {
                $shipment->current_volume = 'all';
            }

            $shipment->tracking_code = $trackingCode;
            $shipment->valid = true;
        } else {
            $shipment = new Shipment();
            $shipment->tracking_code = $trackingCode;
            $shipment->valid = false;
        }

        return $shipment;
    }

    /**
     * Update user information
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {

        $content = $request->get('content');

        /*Mail::raw(print_r($content, true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        if(empty($content)) {
            return $this->responseError('status', '-002');
        }

        /*
        chamada exemplo postman
        {
            "user": "asfaltolargo_003",
            "content": [
                {
                    "is_trk": 0,
                    "tracking": "99994862203428061033501001",
                    "read_point": 1,
                    "agency_id": 1,
                    "status_id": 4,
                    "volume": "001",
                    "created_at": "2022-06-18 15:15:55"
                },
                {
                    "is_trk": 0,
                    "tracking": "99994862203428061033501002",
                    "read_point": 1,
                    "agency_id": 1,
                    "status_id": 4,
                    "volume": "002",
                    "created_at": "2022-06-18 15:15:55"
                }
            ]
        }
         */
        /*$content = [
            [
                'is_trk' => true,
                'tracking' => '001001265065',
                'read_point' => 'in',
                'agency_id' => 1,
                'status_id' => 1,
                'vehicle' => '43-34-RH',
                'volume' => '003',
                'created_at' => '2020-04-29 18:31:07',
            ],
            [
                'is_trk' => true,
                'tracking' => '001001161791',
                'read_point' => 'in',
                'agency_id' => 1,
                'status_id' => 1,
                'vehicle' => '43-34-RH',
                'volume' => '003',
                'created_at' => '2020-04-29 18:31:07',
            ],
            [
                'is_trk' => false,
                'tracking' => '908538',
                'read_point' => 'in',
                'agency_id' => 1,
                'status_id' => 1,
                'vehicle' => '43-34-RH',
                'volume' => '003',
                'created_at' => '2020-04-29 18:31:07',
            ],
        ];*/

        $finalStatus = ShippingStatus::where('is_final')->pluck('id')->toArray();

        if(!is_array($content)) {
            $content = json_decode($content, true);
            if (empty($content)) {
                return $this->responseError('status', '-002');
            }
        }

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        /*$totalRows = count(@$content);
        $trace = LogViewer::getTrace(null, 'Traceability for user: ' . $user->name. '. Total '. $totalRows. ' rows.');
        Log::debug(br2nl($trace));*/

        $shipmentsTrackings = [];
        $otherTrackings = [];
        foreach ($content as $key => $data) {
            if($data['is_trk']) {
                $shipmentsTrackings[] = @$data['tracking'];
            } else {

                $otherTrk = @$data['tracking'];

                if(strlen($otherTrk) == '26') {

                    @$content[$key]['barcode'] = $otherTrk;

                    $otherTrk = substr($otherTrk, 0, 12);
                    @$content[$key]['tracking'] = $otherTrk; //atualiza o codigo lido para o codigo de envio em vez do codigo de barras completo
                }

                $otherTrackings[] = $otherTrk;
            }
        }

        $shipmentsTrk   = Shipment::whereIn('tracking_code', $shipmentsTrackings)
            ->whereNotIn('status_id', $finalStatus)
            ->pluck('id', 'tracking_code')
            ->toArray();

        $otherShipments = Shipment::whereIn('reference', $otherTrackings)
            ->whereNotIn('status_id', $finalStatus)
            ->pluck('id', 'reference')
            ->toArray();

        //via direta    
        if(config('app.source') == 'viadireta'){
            
            $otherShipments = Shipment::whereIn('provider_tracking_code', $otherTrackings)
            ->whereNotIn('status_id', $finalStatus)
            ->pluck('id', 'provider_tracking_code')
            ->toArray();
        }

        $shipments = $shipmentsTrk + $otherShipments;

        $errors = [];
        $insertArr = [];
        $insertHistoryArr = [];
        $shipmentsIds = [];
        $readPoint = null;
        foreach ($content as $data) {

            try {

                $readPoint = @$data['read_point'];

                if(@$shipments[@$data['tracking']]) {

                    $shipmentsIds[] = $shipments[@$data['tracking']];

                    $insertArr[] = [
                        'shipment_id' => $shipments[@$data['tracking']],
                        'operator_id' => $user->id,
                        'agency_id'   => @$data['agency_id'],
                        'volume'      => @$data['volume'],
                        'vehicle'     => @$data['vehicle'],
                        'read_point'  => @$data['read_point'],
                        'barcode'     => @$data['barcode'],
                        'created_at'  => @$data['created_at'] ? @$data['created_at'] : date('Y-m-d H:i:s')
                    ];

                    $arrkey = $shipments[@$data['tracking']];
                    $insertHistoryArr[$arrkey] = [
                        'shipment_id' => $shipments[@$data['tracking']],
                        'operator_id' => $user->id,
                        'user_id'     => $user->id,
                        'status_id'   => $data['status_id'],
                        'agency_id'   => $data['agency_id'],
                        'created_at'  => date('Y-m-d H:i:s'),
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = $data['tracking'];
            }
        }

        ShipmentTraceability::insert($insertArr);

        //original
        //ShipmentHistory::insert($insertHistoryArr);

        $shipmentsCurStatus = Shipment::whereIn('id', array_values($shipments))
            ->pluck('status_id', 'id')
            ->toArray();

        //alterado para enviar sms
        foreach ($insertHistoryArr as $insertHistory) {
            $curStatus = @$shipmentsCurStatus[$insertHistory['shipment_id']];

            if(!in_array($curStatus, [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])) {
                $history = new ShipmentHistory();
                $history->fill($insertHistory);
                $history->save();

                $history->sendEmail(false, false, true);
            } else {
                $removeId = $insertHistory['shipment_id']; //Remove da lista de ID's a atualizar os ID já entregues
                if (($key = array_search($removeId, $shipments)) !== false) {
                    unset($shipments[$key]);
                }
            }
        }


        //comentado em 14/11/2022 e substituido pelo metodo abaixo
        /*Shipment::whereIn('id', array_values($shipments))->update([
            'status_id'   => $data['status_id'],
            'operator_id' => $user->id,
        ]);*/


        //cria manifesto ao picar chegadas e saidas
        //so nao cria se for controlo
        if($readPoint == 'out' || $readPoint == 'in') {

            //ordena envios pela ordem em que foram picados
            $shipments = Shipment::whereIn('id', $shipmentsIds)->get(['id']);
            foreach ($shipments as $shipment) {

                $position = array_search($shipment->id, $shipmentsIds);

                $shipment->update([
                    'status_id' => $data['status_id'],
                    'operator_id' => $user->id,
                    'sort' => $position
                ]);
            }

            //store delivery manifest
            $route = Route::filterOperator($user->id)->first();
            $trip = new Trip();
            $trip->operator_id = $user->id;
            $trip->delivery_route_id = @$route->id;
            $trip->vehicle = @$data['vehicle'];
            $trip->pickup_date = date('Y-m-d H:i:s');
            $trip->source = config('app.source');
            $trip->created_by = $user->id;
            $trip->setCode();

            foreach ($shipmentsIds as $shipmentId) {
                $manifestShipment = new TripShipment();
                $manifestShipment->shipment_id = $shipmentId;
                $manifestShipment->trip_id     = $trip->id;
                $manifestShipment->save();
            }
        }

        if($errors) {
            $result = [
                'code'     => '-001',
                'feedback' => 'Alguns registos não foram gravados.',
                'failures' => $errors
            ];
        } else {
            $result = [
                'code'     => '',
                'feedback' => 'Data saved successfully.',
                'failures' => null
            ];
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Update user information
     *
     * @param Request $request
     * @return mixed
     */
    public function storeWeightControl(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $content = $request->get('content');

         /*Mail::raw(print_r($content, true), function($message) {
             $message->to('paulo.costa@enovo.pt')
                 ->subject('RESPONSE');
         });*/

        if(empty($content)) {
            return $this->responseError('status', '-002');
        }

        $dateNow = date('Y-m-d H:i:s');
        $errors  = [];
        foreach ($content as $rowData) {

            $trk     = trim(@$rowData['tracking']);
            $weight  = forceDecimal(trim(@$rowData['weight']));
            $volumes = forceDecimal(trim(@$rowData['volumes']));

            $shipment = Shipment::where('tracking_code', $trk)->first();

            $updateArr = [
                'conferred_volumes'     => !empty($volumes) ? $volumes : null,
                'conferred_weight'      => !empty($weight) ? $weight : null,
                'conferred_operator_at' => $dateNow,
                //'provider_weight'       => Shipment::getProviderWeight($weight ? $weight : $shipment->weight)
            ];

            if(!empty($volumes)){
                $updateArr['volumes'] = $volumes;
            }

            if(!empty($weight) && $weight > $shipment->weight){
                $updateArr['weight'] = $weight;
            }

            if(!empty($updateArr)) {
                $shipment->weight  = $weight;
                $shipment->volumes = $volumes;
                $prices = Shipment::calcPrices($shipment);
                $shipment->fill($prices['fillable']);
                $shipment->fill($updateArr);
                $shipment->save();
            }
        }


        $result = [
            'code' => '',
            'feedback' => 'Picking finalizado.',
            'failures' => $errors,
        ];


        return response($result, 200)->header('Content-Type', 'application/json');
    }


      /**
     * Get list - pending shipments 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listShipment(Request $request){
         

        $bindings = [
            'tracking_code',
            'provider_tracking_code',
            'sender_name',
            'sender_zip_code',
            'sender_city',
            'recipient_name',
            'recipient_zip_code',
            'recipient_city',
            'volumes',
            'weight',
            'date',
            'service_id',
            'status_id'
        ];
         
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }
        
        $agencyId   = $request->get('agency_id');
        $readPoint  = $request->get('read_point');
        
        $date       = new Date();
        $dateToday  = $date->format('Y-m-d');
        

        //configuração de estados nas definições   
        $statusId = [Setting::get('shipment_status_after_create')];
        if(Setting::get('mobile_app_traceability_state')){
            $statusId = Setting::get('mobile_app_traceability_state');
        }
        
    
        $shipments = Shipment::
            with(['service' => function($q){
                $q->select(['id','code', 'display_code', 'name']);
            }])
            ->whereIn('status_id', $statusId)
            ->where('is_collection', 0)
            ->where('date', $dateToday)
            ->whereDoesntHave('traceability', function ($q) use ($readPoint, $agencyId) { //onde não está presente
                $q->where('read_point', $readPoint);
                $q->where('agency_id', $agencyId);
            })
            ->where('operator_id', $user->id);
            

        if ($readPoint == 'in') {
            $shipments = $shipments->where('recipient_agency_id', $agencyId);
        } elseif ($readPoint == 'out') {
            $shipments = $shipments->where('sender_agency_id', $agencyId);
        }
        
        $numTotalVol    = $shipments->sum('volumes');
        $numTotalWeight = $shipments->sum('weight');
        

        $shipments = $shipments->get($bindings);
        
        $list = [ 
                  'shipments'    => $shipments,
                  'totalVolumes' => $numTotalVol,
                  'totalWeights'  => $numTotalWeight
                ];
    
        
        return response($list, 200)->header('Content-Type', 'application/json');
     }
}
