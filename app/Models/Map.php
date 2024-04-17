<?php

namespace App\Models;

use Auth, Setting;
use Carbon\Carbon;

class Map extends BaseModel
{

    /**
     * Get maps api key
     *
     * @return void
     */
    public static function getMapsApiKey() {
        return getGoogleMapsApiKey();
    }

    /**
     * Get distance between two points
     *
     * @param $origin
     * @param $destination
     * @param array $params
     * @return array
     */
    public static function getDistance($senderZipCode, $recipientZipCode, $senderCountry='pt',$recipientCountry='pt'){

        $originZipCodeParts = explode('-', $senderZipCode);
        $destinationZipCodeParts = explode('-', $recipientZipCode);

        if(@$originZipCodeParts[1]) {
            $originZipCodeParts[1] = $originZipCodeParts[1][0].'00';
        }

        if(@$destinationZipCodeParts[1]) {
            $destinationZipCodeParts[1] = $destinationZipCodeParts[1][0].'00';
        }

        //origin zip code
        $originZipCode = ZipCode::where('country', $senderCountry)
            ->where('zip_code', $originZipCodeParts[0]);

        if($senderCountry == 'pt' && @$originZipCodeParts[1]) {
            $originZipCode = $originZipCode ->where('zip_code_extension', $originZipCodeParts[1]);
        }

        $originZipCode = $originZipCode->first();


        //Destination Zip Code
        $destinationZipCode = ZipCode::where('country', $senderCountry)
            ->where('zip_code', $destinationZipCodeParts[0]);

        if($senderCountry == 'pt' && @$destinationZipCodeParts[1]) {
            $destinationZipCode = $destinationZipCode ->where('zip_code_extension', $destinationZipCodeParts[1]);
        }

        $destinationZipCode = $destinationZipCode->first();


        if(Setting::get('shipments_km_calc_auto')) {

            $originZp       = $senderZipCode;
            $senderCountry  = $senderCountry;
            $originCity     = @$originZipCode->postal_designation;

            $destinationZp      = $recipientZipCode;
            $recipientCountry   = $recipientCountry;
            $destinationCity    = @$destinationZipCode->postal_designation;

            $params = [
                'source'              => config('app.source'),
                'origin'              => $originZp.' '.$originCity,
                'destination'         => $destinationZp.' '.$destinationCity,
                'origin_zp'           => $originZp,
                'origin_city'         => $originCity,
                'origin_country'      => $senderCountry,
                'destination_zp'      => $destinationZp,
                'destination_city'    => $destinationCity,
                'destination_country' => $recipientCountry,
                'triangulation'       => false,
                'return'              => Setting::get('shipments_km_return_back'),
            ];

            $url = config('app.core') . '/helper/maps/distance?'.http_build_query($params);
            $response = json_decode(file_get_contents($url), true);

        } else {

            $agencyZipCode = AgencyZipCode::where('country', $recipientCountry)
                ->where('zip_code', $destinationZipCode)
                ->first();

            $response = [
                'result'         => true,
                'distance'       => money(@$agencyZipCode->kms).' km',
                'distance_value' => @$agencyZipCode->kms ? @$agencyZipCode->kms : 0,
                'time'           => '',
                'time_value'     => ''
            ];
        }

        return $response;
    }

    /**
     * @param $shipmentsIds
     * @param $params
     * @param string $returnMode
     * @return array
     * @throws \Exception
     */
    public static function optimizeDelivery($shipmentsIds, $params) {

        $origin      = Setting::get('company_zip_code').','.Setting::get('company_city');
        $destination = $origin;

        if(@$params['origin_lat'] && @$params['origin_lng']) {
            $origin = $params['origin_lat'].','.$params['origin_lng'];
        }

        if(@$params['destination_lat'] && @$params['destination_lng']) {
            $destination = $params['destination_lat'].','.$params['destination_lng'];
        }

        $returnMode = 'id';
        if(@$params['return_key']) {
            $returnMode = $params['return_key'];
        }

        if(count($shipmentsIds) > 25) {
            throw new \Exception('Não é possível traçar rota para mais de 25 paragens.');
        }

        $shipments = Shipment::whereIn('id', $shipmentsIds)
            ->get([
                'id',
                'tracking_code',
                'recipient_address',
                'recipient_city',
                'recipient_zip_code',
                'recipient_country'
            ]);

        //para melhorar o algoritmo, pode-se fazer um slug de cada morada para comparar realmente se existem moradas iguais

        $url ='https://maps.googleapis.com/maps/api/directions/json?key=' . getGoogleMapsApiKey();
        $url.= '&origin='.urlencode($origin);
        $url.= '&destination='.urlencode($destination).'&waypoints=optimize:true|';

        $shipmentsOriginalOrder = [];
        foreach ($shipments as $shipment) {
            $url.=urlencode($shipment->recipient_zip_code).','.urlencode($shipment->recipient_city);
            $url.='|';

            if($returnMode == 'trk') {
                $shipmentsOriginalOrder[] = $shipment->tracking_code;
            } else {
                $shipmentsOriginalOrder[] = $shipment->id;
            }

        }
        $url=substr($url, 0, -1); //removing the last |
        $url .='&sensor=false';

        $response = file_get_contents($url);
        $response = json_decode($response);

        if($response->status != 'OK') {
            throw new \Exception('Não foi possível traçar a rota. Uma ou mais moradas incorretas.');
        }

        $positions     = @$response->routes[0];
        $waypointOrder = $positions->waypoint_order;

        //ordena corretamente de acordo com o resultado devolvido.
        $correctShipmentsOrder = [];
        foreach ($waypointOrder as $key => $shipmentPos) {
            $correctShipmentsOrder[] = $shipmentsOriginalOrder[$shipmentPos];
        }

        //memoriza alteração
        foreach ($correctShipmentsOrder as $sort => $shipmentId) {
            if($returnMode == 'trk') {
                Shipment::where('tracking_code', $shipmentId)->update(['sort' => $sort]);
            } else {
                Shipment::where('id', $shipmentId)->update(['sort' => $sort]);
            }
        }

        return $correctShipmentsOrder;
    }

    /**
     * @param $addresses
     * @param $params
     * @param string $returnMode
     * @return array
     * @throws \Exception
     */
    public static function optimizeDeliveryFromAddresses($addresses, $params, $returnOnlyWaypointOrder = true) {

        $lastPos     = count($addresses)-1;
        $origin      = $addresses[0];
        $destination = $addresses[$lastPos];

        if(@$params['origin_lat'] && @$params['origin_lng']) {
            $origin = $params['origin_lat'].','.$params['origin_lng'];
        }

        if(@$params['destination_lat'] && @$params['destination_lng']) {
            $destination = $params['destination_lat'].','.$params['destination_lng'];
        }

        if(@$params['origin']) {
            $origin = $params['origin'];
        }

        if(@$params['destination']) {
            $destination = $params['destination'];
        }

        $optimize = 'true';
        if(isset($params['optimize'])) {
            $optimize = $params['optimize'] ? 'true' : 'false';
        }

        $returnMode = 'id';
        if(@$params['return_key']) {
            $returnMode = $params['return_key'];
        }

        if(count($addresses) > 25) {
            throw new \Exception('Não é possível traçar rota para mais de 25 paragens.');
        }

        $url ='https://maps.googleapis.com/maps/api/directions/json?key=' . getGoogleMapsApiKey();
        $url.= '&origin='.urlencode($origin);
        $url.= '&destination='.urlencode($destination).'&waypoints=optimize:'.$optimize.'|';


        if(@$params['return_back']) {
            unset($addresses[$lastPos]); //exclui o primeiro e ultimo endereço porque já estão considerados na variavel origin e destination
        }


        $shipmentsOriginalOrder = [];
        foreach ($addresses as $key => $address) {
            $url.= urlencode(strtolower($address));
            $url.='|';

            $shipmentsOriginalOrder[] = $key;
        }
        $url=substr($url, 0, -1); //removing the last |
        $url .='&sensor=false';

        $response = file_get_contents($url);
        $response = json_decode($response);

        if($response->status != 'OK') {
            throw new \Exception('Não foi possível traçar a rota. Uma ou mais moradas incorretas.');
        }

        $positions     = @$response->routes[0];
        $waypointOrder = $positions->waypoint_order;


        $positionsLatLng = [];
        foreach ($positions->legs as $key => $leg) {

            if($key > 0) {
                $positionsLatLng[] = [
                    'latitude'     => @$leg->start_location->lat,
                    'longitude'    => @$leg->start_location->lng,
                    'address'      => @$leg->start_address,
                    'distance'     => @$leg->distance->text,
                    'duration'     => @$leg->duration->text,
                    'distance_val' => @$leg->distance->value,
                    'duration_val' => @$leg->duration->value,
                ];
            }
        }

        if(@$params['return_back']) {
            $positionsLatLng[0]['distance'] = '0 km';
            $positionsLatLng[0]['distance_val'] = 0;
            $positionsLatLng[] = $positionsLatLng[0];
        }

        //ordena corretamente de acordo com o resultado devolvido.
        $correctShipmentsOrder = [];
        foreach ($waypointOrder as $key => $shipmentPos) {
            $correctShipmentsOrder[] = $shipmentsOriginalOrder[$shipmentPos];
        }

        if($returnOnlyWaypointOrder) {
            return $waypointOrder;
        } else {
            return $positionsLatLng;
        }
    }

    /**
     * Convert address into coordinates
     *
     * @param $address
     * @return array|mixed
     */
    public static function getCoordinatesFromAddress($address) {
        //replace all the white space with "+" sign to match with google search pattern
        $address = str_replace(" ", "+", strtolower($address));
        $address = str_replace("º", '', $address);
        $address = str_replace("ª", '', $address);

        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=$address&key=" . getGoogleMapsApiKey();

        $response = file_get_contents($url);
        $json = json_decode($response, TRUE);
        if (!empty(@$json['error_message'])) {
            return $json;
        }

        $result = [
            'lat' => $json['results'][0]['geometry']['location']['lat'],
            'lng' => $json['results'][0]['geometry']['location']['lng']
        ];

        return $result;
    }

    public static function processRoute($tripTimeMinutes, $daysWithOutInterrupt = 0, $startTripDate = null, $startTripTime = null, $NumberOfPauses = 1, $pauseTime = 30, $dailyWorkTime = 480, $distance = null){
        // 1440 minutes = 24 hours
        // 480 minutes = 8 hours
        // 540 minutes = 9 hours
        $totalTime = 0;

        if($pauseTime < 30){
            $pauseTime = 30;
        }
        
        $notWorkingPeriod = 1440 - (($pauseTime * $NumberOfPauses) + $dailyWorkTime);

        if ($startTripDate == null) {
            $startTripDate = Carbon::now()->format('Y-m-d');
        }

        if ($startTripTime == null) {
            $startTripTime = convertHoursToMinutes(Carbon::now()->format('H:i'));
        }

        $pauseCount = 0;
        $hasToPause = false;
        
        $action = '';
        $actionTime = 0;

        $day = 0;
        $timeAux = $startTripTime;

        $result = [];
        $allDetails = [];
        $arrayDay[] = [
            'action' => 'start',
            'time' => convertMinutesToHours($startTripTime),
        ];

        while ($tripTimeMinutes > 0) {
            if (!$hasToPause) {
                if ($tripTimeMinutes - ($dailyWorkTime / ($NumberOfPauses + 1)) > 0) {
                    $actionTime = ($dailyWorkTime / ($NumberOfPauses + 1));
                    $tripTimeMinutes = $tripTimeMinutes - $actionTime;
                    $action = 'driving';
                    $hasToPause = true;
                } else {
                    $actionTime = $tripTimeMinutes;
                    $tripTimeMinutes = 0;
                    $action = 'end';
                }
            }else{
                if ($pauseCount == $NumberOfPauses) {
                    $actionTime = $notWorkingPeriod;
                    $action = 'not_working';
                    $hasToPause = false;
                    $pauseCount = 0;
                } else {
                    $actionTime = $pauseTime;
                    $action = 'pause';
                    $pauseCount++;
                    $hasToPause = false;
                }
            }

            $timeAux = $timeAux + $actionTime;
            $totalTime = $totalTime + $actionTime;

            if($timeAux > 1440){
                $timeAux = $timeAux - 1440;
                $dateAux = Carbon::parse($startTripDate)->addDays($day)->format('Y-m-d');
                $addDetails[] = [
                    'date' => $dateAux,
                    'events' => $arrayDay,
                ];
                $arrayDay = [];

                $day++;
                $daysWithOutInterrupt++;

                if($daysWithOutInterrupt == 5){
                    $day = $day + 2;
                    $daysWithOutInterrupt = 0;
                }
            }

            $arrayDay[] = [
                'action' => $action,
                'time'   => convertMinutesToHours($timeAux)
            ];
        }

        if($timeAux <= 1440){
            $dateAux = Carbon::parse($startTripDate)->addDays($day)->format('Y-m-d');
            $addDetails[] = [
                'date' => $dateAux,
                'events' => $arrayDay,
            ];
        }

        $result = [
            $addDetails,
            'totalTime' => $totalTime
        ];

        return $result;
    }
}
