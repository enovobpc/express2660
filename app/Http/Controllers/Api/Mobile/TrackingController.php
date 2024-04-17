<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Agency;
use App\Models\IncidenceType;
use App\Models\LogViewer;
use App\Models\OperatorTask;
use App\Models\Shipment;
use App\Models\ShipmentExpense;
use App\Models\ShipmentHistory;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\PickupPoint;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShipmentHistoryAttachament;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date, File, Log;

class TrackingController extends \App\Http\Controllers\Api\Mobile\BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Lists all shipping status
     *
     * @param Request $request
     * @return mixed
     */
    public function listsStatus(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $bindings = [
            'id',
            'name',
            'slug',
            'description',
            'color',
            'map_icon',
            'is_collection',
            'is_final',
        ];

        $status = ShippingStatus::select($bindings);

        if($request->has('pickup')) {
            $status = $status->where('is_collection', $request->get('pickup'));
        }

        if($request->has('concluded')) {
            $status = $status->where('is_final', $request->get('concluded'));
        }

        $status = $status->orderBy('sort', 'asc')->get();

        if(!$status) {
            return $this->responseError('lists', '-001') ;
        }

        return response($status, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Lists all shipping status
     *
     * @param Request $request
     * @return mixed
     */
    public function listsIncidences(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $bindings = [
            'id',
            'name'
        ];

        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->where('operator_visible', 1)
            ->ordered()
            ->get($bindings);

        if(!$incidences) {
            return $this->responseError('lists', '-001') ;
        }

        return response($incidences, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment status
     *
     * @param Request $request
     * @param $inputData - Optionaly send data from array
     * @return array
     */
    public function store(Request $request, $inputData = null) {

        if($inputData) {
            $input = $inputData;
            $user  = $inputData['user'];
            unset($inputData['user']);
        } else {
            $input = $request->all();
        }

       /* Mail::raw(print_r($input, true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        //validate user
        if(empty($user)) {
            $user = $this->getUser($request->get('user'));
            if (!$user) {
                return $this->responseError('login', '-002');
            }
        }

        //schedule new date/hour
        $newDate    = @$input['new_date'];
        $newHour    = @$input['new_hour'];

        //pickup point
        $pickupPointId          = @$input['pudo_id'];
        $descriptionPickupPoint = null;
        if(!empty($pickupPointId)){
            $descriptionPickupPoint = PickupPoint::where('id', $pickupPointId)->select('name', 'address', 'zip_code', 'city')->get()->toArray();
        }

        //validate tracking code
        $trackingCodes = $input['tracking'];
        $trackingCodes = array_filter(explode(',', $trackingCodes));
        if(empty($trackingCodes)) {
            return $this->responseError('status', '-001') ;
        }

        //find shipment
        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        $shipments = Shipment::where('tracking_code', $trackingCodes)
            ->where(function($q) use($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('sender_agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            //->where('operator_id', $user->id) //força a só dar entrega os envios do proprio motorista
            ->get();
  
        //viadireta 
        if(config('app.source') == 'viadireta'){
            
            $shipments = Shipment::where('provider_tracking_code', $trackingCodes)
            ->where(function($q) use($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('sender_agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            //->where('operator_id', $user->id) //força a só dar entrega os envios do proprio motorista
            ->get();
            
        }

        $smsPin = @$input['sms_code'];
        if(!empty($smsPin)){
            
            if($smsPin != $shipments[0]['sms_code']){
                return  $this->responseError('smsCode', '-001');
            }
        }

        if($shipments->isEmpty()) {
            return $this->responseError('status', '-002') ;
        }

        //validate status
        $status = ShippingStatus::whereId($input['status_id'])->first();
        if(!$status) {
            return $this->responseError('status', '-003') ;
        }

        //validate incidence
        $incidenceId = null;
        if($status->id == ShippingStatus::INCIDENCE_ID) {

            if(!$input['incidence_id']) {
                return $this->responseError('status', '-004');
            }

            $incidence = IncidenceType::whereId($input['incidence_id'])->first();
            if(!$incidence) {
                return $this->responseError('status', '-003') ;
            }

            $incidenceId = $incidence->id;

            //verificar o número de agendamentos possíveis após várias tentativas de entregas (implementado para via direta para já)
            if(!empty($newDate) && !empty($newHour)){
                if($this->scheduleAttempts($trackingCodes)){
                    return  $this->responseError('schedule', '-001');
                
                   
                }
            }
        }

        //get file if exists
        $filepath = $filename = '';
        if(!empty($input['uploaded_file'])) {

            $fileContent = $input['uploaded_file'];
            $folder = ShipmentHistory::DIRECTORY;

            if(!File::exists(public_path($folder))) {
                File::makeDirectory(public_path($folder));
            }

            $filename = strtolower(str_random(8).'.png');
            $filepath = $folder.'/'.$filename;
            File::put(public_path($filepath), base64_decode($fileContent));
        }

         //multiple images
        $photos[] = '';
        if(!empty($input['listPhotos'])){
             
            foreach($input['listPhotos'] as $photo){
                 
                $fileContent = $photo['uploaded_file'];
 
                $folder = ShipmentHistoryAttachament::DIRECTORY;
 
                if(!File::exists(public_path($folder))) {
                    File::makeDirectory(public_path($folder));
                }
                
                $filename = strtolower(str_random(8).'.png');
                $filepath = $folder.'/'.$filename;
                File::put(public_path($filepath), base64_decode($fileContent));
                
                $photos[$filename] = $filepath;
                     
            }
        }

        $result = [];
        try {

            foreach ($shipments as $shipment) {

                if(config('app.source') == 'transnos' && in_array($shipment->status_id, [ShippingStatus::SHIPMENT_PICKUPED, 20]) && $status->id == ShippingStatus::DELIVERED_ID) {
                    $result[] = [
                        'error'    => true,
                        'shipment' => $shipment->tracking_code,
                        'feedback' => 'Tem de dar entrada em armazém.',
                    ];

                    return response($result, 200)->header('Content-Type', 'application/json');
                } else {

                    //get operator vehicle
                    $operatorVehicleId = null;
                    if(!$shipment->vehicle) {
                        //1. verifica a viatura da rota do envio
                        $operatorVehicleId = @$shipment->route->vehicle;

                        //2. se a rota não tem viatura, verifica se o motorista tem viatura
                        if(!$operatorVehicleId) {
                            $operatorVehicleId = $user->vehicle;
                        }
                    }

                    $statusId = $status->id;
                    if($shipment->is_collection && $statusId == ShippingStatus::DELIVERED_ID) {
                        $statusId = ShippingStatus::PICKUP_DONE_ID; //nas recolhas marca como recolha realizada
                    }

                    if($statusId == 9 && config('app.source') == 'asfaltolargo') {
                        if($incidenceId == 13) {
                            $statusId = 50; //recusado
                        }
                    }

                    $history = new ShipmentHistory();
                    $history->agency_id     = $user->agency_id;
                    $history->shipment_id   = $shipment->id;
                    $history->operator_id   = $user->id;
                    $history->user_id       = $user->id;
                    $history->status_id     = $statusId;
                    $history->incidence_id  = $incidenceId;
                    $history->agency_id     = @$input['agency'];
                    $history->obs           = @$input['obs'];
                    $history->receiver      = @$input['receiver'];
                    $history->signature     = @$input['signature'];
                    $history->vat           = @$input['vat'];

                    if($filepath) {
                        $history->filehost = config('app.url');
                        $history->filepath = @$filepath;
                        $history->filename = @$filename;
                    }

                    $history->latitude      = @$input['latitude'];
                    $history->longitude     = @$input['longitude'];
                    $saved = $history->save();

                    //se estava em incidencia, marca todas as incidencias como resolvidas.
                    ShipmentHistory::where('shipment_id', $history->shipment_id)
                        ->where('status_id', ShippingStatus::INCIDENCE_ID)
                        ->where('id', '<>', $history->id)
                        ->update(['resolved' => 1]);

                    //multiple images
                    if(@$input['listPhotos']){
                        
                        foreach($photos as $key => $info){
                            
                            if($key == 0 && empty($info))
                                continue;
                            
                            $attachament = new ShipmentHistoryAttachament();
                            
                            $attachament->shipment_id         = $history->shipment_id;
                            $attachament->shipment_history_id = $history->id;
                            
                            $attachament->name     = $key;
                            $attachament->filename = $key;
                            $attachament->filepath = $info;
                            
                            $attachament->save();
                            
                        }
                
                    }


                    if ($saved) {
                        $shipment->status_id     = $history->status_id;
                        $shipment->status_date   = $history->created_at->format('Y-m-d H:i:s');
                        $shipment->operator_id   = $history->operator_id;
                        $shipment->vehicle       = empty($shipment->vehicle) ? $operatorVehicleId : $shipment->vehicle;

                        if(@$input['refund_method']) {
                            $shipment->refund_method = @$input['refund_method'];
                        }

                        if(@$input['cod_method']) {
                            $shipment->cod_method = @$input['cod_method'];
                        }

                        if(@$input['weight']) {
                            $shipment->weight = @$input['weight'];
                        }

                        if(@$input['volumes']) {
                            $shipment->volumes = @$input['volumes'];
                        }

                        if(@$input['reference2']) {
                            $shipment->reference2 = @$input['reference2'];
                        }

                        $shipment->save();

                          //pudo
                        if(!empty($pickupPointId)){
                            $resolution = new ShipmentIncidenceResolution();

                            $resolution->shipment_id          = $shipment->id;
                            $resolution->shipment_history_id  = $history->id;
                            $resolution->operator_id          = $user->id;
                            $resolution->resolution_type_id   = 1;
                            $resolution->obs                  = 'ENTREGA NO PICKUP POINT: '.$descriptionPickupPoint[0]['name']."\r\n".$descriptionPickupPoint[0]['address']."\r\n".$descriptionPickupPoint[0]['zip_code'].' '.$descriptionPickupPoint[0]['city'];
                            $resolution->save();

                            ShipmentHistory::where('shipment_id', $history->shipment_id)
                                            ->where('status_id', ShippingStatus::INCIDENCE_ID)
                                            ->where('id', $history->id)
                                            ->update(['resolved' => 1]);

                        }

                         //schedule
                        if(!empty($newDate) && !empty($newHour)){
                            $resolution = new ShipmentIncidenceResolution();

                            $resolution->shipment_id          = $shipment->id;
                            $resolution->shipment_history_id  = $history->id;
                            $resolution->operator_id          = $user->id;
                            $resolution->resolution_type_id   = 1;
                            $resolution->obs                  = 'Nova hora e data de entrega: '.$newDate.' | '. $newHour;
                            $resolution->save();

                            ShipmentHistory::where('shipment_id', $history->shipment_id)
                                            ->where('status_id', ShippingStatus::INCIDENCE_ID)
                                            ->where('id', $history->id)
                                            ->update(['resolved' => 1]);

                            //nova data de expedição
                            $history = new ShipmentHistory();
                            $history->agency_id     = $user->agency_id;
                            $history->shipment_id   = $shipment->id;
                            $history->operator_id   = $user->id;
                            $history->user_id       = $user->id;
                            $history->status_id     = ShippingStatus::SHIPMENT_WAINT_EXPEDITION;
                            $history->obs           = 'Nova hora e data de entrega: '.$newDate.' | '. $newHour."r\n".@$input['obs'];
                            $history->save();


                            $shipment->status_id     = $history->status_id;
                            $shipment->status_date   = $history->created_at;
                            $shipment->operator_id   = $history->operator_id;
                            $shipment->vehicle       = empty($shipment->vehicle) ? $operatorVehicleId : $shipment->vehicle;
                            // $shipment->obs_internal  = 'Nova hora e data de entrega: '.$newDate.' | '. $newHour;
                            $shipment->save();
                        }

                        //store wainting time
                        if(!empty(@$input['wainting_time'])) {

                            $waintingTime = $input['wainting_time'];
                            $customerComplementarServices = $shipment->customer->complementar_services;

                            $waintingTimeExpense = ShippingExpense::filterSource()->whereType('wainting_time')->first();
                            $zonesArr = $waintingTimeExpense["zones_arr"];
                            $zones = array_flip($zonesArr);
                            $zone  = $shipment->zone;

                            $customerValue = null;
                            $key = $waintingTimeExpense->id;

                            if(!empty($customerComplementarServices[$key][$zone]) || !empty($customerComplementarServices[$key]["qqz"])) {
                                $priceQqz = (float) @$customerComplementarServices[$key]["qqz"];
                                $customerValue = (float) @$customerComplementarServices[$key][$zone];

                                if(empty($customerValue) && !empty($priceQqz)) {
                                    $customerValue = $priceQqz;
                                }
                            }

                            $key = 0;
                            if(isset($zones[$zone])) {
                                $key = $zones[$zone];
                            } elseif(isset($zones['qqz'])) {
                                $key = $zones['qqz'];
                            }

                            $value   = (float) @$waintingTimeExpense["values_arr"][$key];
                            $value   = $customerValue ? $customerValue : $value;
                            $unity   = @$waintingTimeExpense["unity_arr"][$key];

                            if($unity == 'percent') {
                                $value = $value / 100; //converte % em numérico
                            }

                            //STORE EXPENSE
                            $expense = ShipmentExpense::firstOrNew([
                                'shipment_id' => $shipment->id,
                                'expense_id'  => $waintingTimeExpense->id
                            ]);

                            $expense->qty   = $waintingTime;
                            $expense->price = $value;
                            $expense->subtotal = $waintingTime * $value;
                            $expense->unity = $unity;
                            $expense->date = date('Y-m-d');
                            $expense->save();

                            //UPDATE SHIPMENT TOTAL
                            ShipmentExpense::updateShipmentTotal($shipment->id);
                        }


                        //create automatic return
                        if(Setting::get('mobile_app_autoreturn') && is_array($shipment->has_return) && in_array('rpack', $shipment->has_return) && !empty(@$input['return_weight']) && !empty(@$input['return_volumes'])) {

                            $returnInput = [
                                'weight'  => @$input['return_weight'] ? @$input['return_weight'] : $shipment->weight,
                                'volumes' => @$input['return_volumes'] ? @$input['return_volumes'] : $shipment->volumes
                            ];

                            $shipment->createDirectReturn($returnInput, true);
                        }

                        //store user location
                        if(!empty($history->latitude) && !empty($history->longitude)) {
                            $this->storeUserLocation($user, $history->latitude, $history->longitude);
                        }

                        try {
                            $history->sendEmail(false, false, true);
                        } catch (\Exception $e) {}


                        $result[] = [
                            'error' => '',
                            'shipment' => $shipment->tracking_code,
                            'feedback' => 'Status changed successfully',
                        ];
                    } else {
                        $result[] = [
                            'error' => '-003',
                            'shipment' => $shipment->tracking_code,
                            'feedback' => trans('api.shipments.errors.status.-003'),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $result[] = [
                'error' => '-999',
                'shipment' => $shipment->tracking_code,
                'feedback' => $e->getMessage()
            ];
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Set shipment as readed by operator when operator opens the service
     *
     * @param Request $request
     * @return mixed
     */
    public function setReadedByOperator(Request $request) {

        //validate user
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        //check tracking code
        $trackingCodes = $request->get('tracking');
        $trackingCodes = array_filter(explode(',', $trackingCodes));
        if(empty($trackingCodes)) {
            return $this->responseError('status', '-001') ;
        }

        //find shipment
        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        $operatorId = $user->id;
        $shipments = Shipment::where('tracking_code', $trackingCodes)
            ->where(function($q) use($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            //->whereIn('operator_id', $operatorsIds)
            ->get();

        if($shipments->isEmpty()) {
            return $this->responseError('status', '-002') ;
        }

        try {

            //estado pendente estafeta. Altera para "lido pelo motorista"
            $statusAfterRead = Setting::get('mobile_app_status_after_read_operator') ? Setting::get('mobile_app_status_after_read_operator') : ShippingStatus::READ_BY_COURIER_OPERATOR;
            $statusPendingOperator = Setting::get('mobile_app_status_pending_operator') ? Setting::get('mobile_app_status_pending_operator') : [ShippingStatus::PENDING_OPERATOR];

            $result = [];
            foreach ($shipments as $shipment) {
                //altera só se o estado atual está na lista de estados para alteração e se o estado não é o mesmo que o definido após a leitura
                if (in_array($shipment->status_id, $statusPendingOperator) && $shipment->status_id != $statusAfterRead) {

                    $shipment->status_id = $statusAfterRead;
                    $shipment->save();

                    $history = new ShipmentHistory();
                    $history->shipment_id = $shipment->id;
                    $history->operator_id = $operatorId;
                    $history->agency_id = $shipment->sender_agency_id;
                    $history->status_id = $shipment->status_id;
                    $history->latitude = $lat;
                    $history->longitude = $lng;
                    $history->save();

                    $result[] = [
                        'error' => '',
                        'shipment' => $shipment->tracking_code,
                        'feedback' => 'Saved Sucessfuly'
                    ];
                } else {
                    $result[] = [
                        'error' => '-006',
                        'shipment' => $shipment->tracking_code,
                        'feedback' => trans('api.shipments.errors.status.-006')
                    ];
                }
            }

        } catch (\Exception $e) {
            return $this->responseError('status', '-999', $e->getMessage()) ;
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment status
     *
     * @param Request $request
     * @return mixed
     */
    public function setPickuped(Request $request) {

        $acceptedFields = ['weight', 'volumes', 'latitude', 'longitude', 'receiver', 'uploaded_file', 'obs', 'signature', 'status', 'wainting_time'];
        $input = $request->only($acceptedFields);

        //validate user
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        //store User Location
        $lat = $input['latitude'];
        $lng = $input['longitude'];
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        //check tracking code
        $trackingCodes = $request->get('tracking');
        $trackingCodes = array_filter(explode(',', $trackingCodes));
        if(empty($trackingCodes)) {
            return $this->responseError('status', '-001') ;
        }

        //find shipment
        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        $operatorId   = $user->id;
        $operatorsIds = [$operatorId];
        $shipments = Shipment::whereIn('tracking_code', $trackingCodes)
            ->where(function($q) use($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            ->whereIn('operator_id', $operatorsIds)
            ->get();

        if($shipments->isEmpty()) {
            return $this->responseError('status', '-002') ;
        }

        //validate incidence
        $incidenceId = null;
        if($input['status'] == OperatorTask::STATUS_INCIDENCE) {

            if(!$request->get('incidence_id')) {
                return $this->responseError('status', '-004');
            }

            $incidence = IncidenceType::whereId($request->get('incidence_id'))->first();
            if(!$incidence) {
                return $this->responseError('status', '-003') ;
            }

            $incidenceId = $incidence->id;
        }

        try {
            $statusPickuped    = Setting::get('mobile_app_status_pickuped') ? Setting::get('mobile_app_status_pickuped') : ShippingStatus::SHIPMENT_PICKUPED; //transporte
            $statusAfterPickup = Setting::get('mobile_app_status_after_pickup') ? Setting::get('mobile_app_status_after_pickup') : null; //transporte

            if($input['status'] == OperatorTask::STATUS_INCIDENCE) {
                $statusPickuped = ShippingStatus::PICKUP_FAILED_ID;
            }

            $statusAvailableToPickup = [
                ShippingStatus::PENDING_ID,
                ShippingStatus::ACCEPTED_ID,
                ShippingStatus::PENDING_OPERATOR,
                ShippingStatus::READ_BY_COURIER_OPERATOR,
                ShippingStatus::PICKUP_ACCEPTED_ID,
                ShippingStatus::IN_PICKUP_ID,
                ShippingStatus::PICKUP_REQUESTED_ID,
                ShippingStatus::ACCEPTED_DELIVERY,
            ];

            $result = [];
            foreach ($shipments as $shipment) {

                if (in_array($shipment->status_id, $statusAvailableToPickup) && $shipment->status_id != $statusPickuped) {

                    //get file if exists
                    $filepath = $filename = '';
                    if(!empty($request->get('uploaded_file'))) {

                        $fileContent = $request->get('uploaded_file');
                        $folder = ShipmentHistory::DIRECTORY;

                        if(!File::exists(public_path($folder))) {
                            File::makeDirectory(public_path($folder));
                        }

                        $filename = strtolower(str_random(8).'.png');
                        $filepath = $folder.'/'.$filename;
                        File::put(public_path($filepath), base64_decode($fileContent));
                    }

                    $history = new ShipmentHistory();
                    $history->shipment_id   = $shipment->id;
                    $history->status_id     = $statusPickuped;
                    $history->operator_id   = $operatorId;
                    $history->latitude      = $lat;
                    $history->longitude     = $lng;
                    $history->obs           = $request->get('obs');
                    $history->receiver      = $request->get('receiver');
                    $history->signature     = $request->get('signature');
                    $history->vat           = $request->get('vat');
                    $history->incidence_id  = $incidenceId;

                    if($filepath) {
                        $history->filehost = config('app.url');
                        $history->filepath = @$filepath;
                        $history->filename = @$filename;
                    }

                    $history->save();

                    if ($statusAfterPickup) {
                        $history = new ShipmentHistory();
                        $history->shipment_id = $shipment->id;
                        $history->status_id   = $statusAfterPickup;
                        $history->operator_id = $operatorId;
                        $history->latitude    = $lat;
                        $history->longitude   = $lng;
                        $history->save();
                    }

                    if ($input['volumes']) {
                        $shipment->volumes = $input['volumes'];
                    }

                    if ($input['weight']) {
                        $shipment->weight = $input['weight'];
                    }

                    if ($input['volumes'] || $input['weight']) {
                        $prices = Shipment::calcPrices($shipment);
                        if ($prices) {
                            $shipment->cost_price     = @$prices['cost'];
                            $shipment->total_price    = @$prices['total'];
                            $shipment->fuel_tax       = @$prices['fuelTax'];
                            $shipment->fuel_tax       = @$prices['fuelTax'];
                            $shipment->extra_weight   = @$prices['extraKg'];
                        }
                    }

                    if(!empty(@$input['wainting_time'])) {

                        $waintingTime = $input['wainting_time'];
                        $customerComplementarServices = $shipment->customer->complementar_services;

                        $waintingTimeExpense = ShippingExpense::filterSource()->whereType('wainting_time')->first();
                        $zonesArr = $waintingTimeExpense["zones_arr"];
                        $zones = array_flip($zonesArr);
                        $zone  = $shipment->zone;

                        $customerValue = null;
                        $key = $waintingTimeExpense->id;

                        if(!empty($customerComplementarServices[$key][$zone]) || !empty($customerComplementarServices[$key]["qqz"])) {
                            $priceQqz = (float) @$customerComplementarServices[$key]["qqz"];
                            $customerValue = (float) @$customerComplementarServices[$key][$zone];

                            if(empty($customerValue) && !empty($priceQqz)) {
                                $customerValue = $priceQqz;
                            }
                        }

                        $key = 0;
                        if(isset($zones[$zone])) {
                            $key = $zones[$zone];
                        } elseif(isset($zones['qqz'])) {
                            $key = $zones['qqz'];
                        }

                        $value   = (float) @$waintingTimeExpense["values_arr"][$key];
                        $value   = $customerValue ? $customerValue : $value;
                        $unity   = @$waintingTimeExpense["unity_arr"][$key];

                        if($unity == 'percent') {
                            $value = $value / 100; //converte % em numérico
                        }

                        //STORE EXPENSE
                        $expense = ShipmentExpense::firstOrNew([
                            'shipment_id' => $shipment->id,
                            'expense_id'  => $waintingTimeExpense->id
                        ]);

                        $expense->qty   = $waintingTime;
                        $expense->price = $value;
                        $expense->subtotal = $waintingTime * $value;
                        $expense->unity = $unity;
                        $expense->date = date('Y-m-d');
                        $expense->save();

                        //UPDATE SHIPMENT TOTAL
                        ShipmentExpense::updateShipmentTotal($shipment->id);
                    }


                    $shipment->status_id = $history->status_id;
                    $shipment->save();

                    $result[] = [
                        'error'    => '',
                        'shipment' => $shipment->tracking_code,
                        'feedback' => 'Saved Sucessfuly'
                    ];

                } else {
                    $result[] = [
                        'error'    => '-007',
                        'shipment' => $shipment->tracking_code,
                        'feedback' => trans('api.shipments.errors.status.-007')
                    ];
                }
            }

        } catch (\Exception $e) {
            return $this->responseError('status', '-999', $e->getMessage()) ;
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment status
     *
     * @param Request $request
     * @return mixed
     */
    public function transferOperator(Request $request) {

        $acceptedFields = ['operator'];
        $input = $request->only($acceptedFields);

        //validate user
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        if(!$request->has('operator')) {
            return $this->responseError('status', '-008');
        }

        if(empty($input['operator'])) {
            $input['operator'] = null; //enviar para o escritório
        }

        //check tracking code
        $trackingCodes = $request->get('tracking');
        $trackingCodes = array_filter(explode(',', $trackingCodes));
        if(empty($trackingCodes)) {
            return $this->responseError('status', '-001') ;
        }

        //find shipment
        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        try {
            $result = Shipment::whereIn('tracking_code', $trackingCodes)
                ->where(function($q) use($sourceAgencies) {
                    $q->whereIn('agency_id', $sourceAgencies);
                    $q->orWhereIn('recipient_agency_id', $sourceAgencies);
                })
                //->where('operator_id', $user->id)
                ->update(['operator_id' => $input['operator']]);

            if(!$result) {
                return $this->responseError('status', '-009');
            }

            $result = [
                'error'    => '',
                'feedback' => 'Transfered successfuly'
            ];

        } catch (\Exception $e) {
            return $this->responseError('status', '-999', $e->getMessage()) ;
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update user information
     *
     * @param Request $request
     * @return mixed
     */
    public function storeMassive(Request $request) {

        $content = $request->get('content');

        if(empty($content)) {
            return $this->responseError('status', '-002');
        }

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

        $totalRows = count(@$content);
       /* $trace = LogViewer::getTrace(null, 'History massive for user: ' . $user->name. '. Total '. $totalRows. ' rows.');
        Log::debug(br2nl($trace));*/

        $errors = [];
        foreach ($content as $data) {

            try {
                $data['user'] = $user;
                $this->store($request, $data);
            } catch (\Exception $e) {
                $errors[] = $data['tracking'];
            }
        }

        if($errors) {
            $result = [
                'error'    => '-001',
                'message'  => 'Alguns envios não foram gravados.',
                'failures' => $errors
            ];
        } else {
            $result = [
                'error'    => '',
                'message'  => 'Data saved successfully.',
                'failures' => null
            ];
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Retrieves the possibility to attempt resolving a shipment incidence
     * @param $trackingCode
     * 
     * @return bool
     */
    private function scheduleAttempts($trackingCode)
    {
        if (config('app.source') !== 'viadireta') {
            return false;
        }

        $deliveryAttemptShipment = Shipment::with('history')->where('provider_tracking_code', $trackingCode)->first();     
        

        if (empty($deliveryAttemptShipment->history)) {
            return false;
        }

        $numIncidences = $deliveryAttemptShipment->history
            ->filter(function ($history) {
                return $history->status_id == ShippingStatus::SHIPMENT_WAINT_EXPEDITION;
            })
            ->count();
            
        if(is_null($deliveryAttemptShipment['delivery_attempts'])){
            return false;
        }

        return $numIncidences >= $deliveryAttemptShipment['delivery_attempts'] ;
    }



    /**
     * Send sms pin for for shipment delivery verification
     * @param Request $request
     * 
     * @return mixed
     */
    private function sendSmsPin(Request $request){
        
        $content = $request->all();
        
        //validate phone
        $phone = validateNotificationMobiles($content['phone']);
        $phone = $phone['valid'];
        
        //shipment
        $shipment   = Shipment::where('tracking_code', $content['trackingCode'])->first();
        
        if(config('app.source') == 'viadireta'){
           $shipment   = Shipment::where('provider_tracking_code', $content['trackingCode'])->first(); 
        }
        
        
        if(!empty($phone)){
            try {
                
                 $sms = new Sms();
                 $sms->to = implode(';', $phone);
                 $sms->message = 'O codigo de validacao de entrega: '.$shipment->sms_code;
                 $sms->source_id = $shipment->id;
                 $sms->source_type = 'Validiton Pin';
                 $sms->send();
                 
                $result = [
                        'error'    => '',
                        'message'  => 'Sms enviado com sucesso',
                        'failures' => null
                ];
                 
            } catch (\Exception $e) {
                $result = [
                            'error'    => '-001',
                            'message'  => 'Falhou o envio da SMS. Tente de novo.',
                          ];
           }
        }
    
        return response($result, 200)->header('Content-Type', 'application/json');
        
        
    }

}