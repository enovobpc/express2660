<?php

namespace App\Models\GpsGateway\Inosat;

use Jenssegers\Date\Date;
use Setting, File;

class Itenerary extends \App\Models\GpsGateway\Inosat\Base {

    /**
     * Get the itinerary for a certain vehicle for a certain day.
     * @param $vehicleId
     * @param $date
     * @return mixed
     * @throws \Exception
     */
    public function getRoute($vehicleId, $startDate, $endDate) {

        $urlMethod = 'get_viat_old_data.json?api_key=' . $this->apiKey;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $data = [
            'user'    => $this->username,
            'pwd'     => $this->password,
            'viat_id' => $vehicleId,
            'dh_ini'  => $startDate.' 00:00',
            'dh_end'  => $endDate.' 23:59'
        ];


        $history = $this->execute($urlMethod, $data, $headers);

        if($history['data']) {

            $locations = $history['data'];

            $route = [];
            foreach ($locations as $key => $location) {
                $location = $this->mappingRouteData($location);
                $location['license_plate']  = $history['viat_id']; 
                $location['gps_id']         = $history['viat_matricula']; 
                $route[] = $location;
            }

            return $route;
        }

        return null;
    }



    /**
     * Get the itinerary for a certain vehicle for a certain day.
     * @param $vehicleId
     * @param $date
     * @return mixed
     * @throws \Exception
     */
    public function getItenerary($vehicleId, $date) {

        $authId = $this->login();

        $data = [
            'aspNetSessionId'   => $authId,
            'vehicleId'         => $vehicleId,
            'itineraryDate'     => $date
        ];

        $response = $this->execute('ItineraryService.asmx/GetItinerary', $data);

        return $this->mappingVehicleData($response);
    }


    /**
     * Mapping vehicle data
     * @param $vehicle
     */
    public function mappingRouteData($lastLocation) {

        $mappedData['gps_id']         = $lastLocation['viat_id'];
        $mappedData['license_plate']  = $lastLocation['viat_matricula'];
        $mappedData['name']           = $lastLocation['viat_matricula'];

        $mappedData['last_location']  = new Date($lastLocation['datahora']);
        $mappedData['latitude']       = $lastLocation['viat_lat'];
        $mappedData['longitude']      = $lastLocation['viat_lon'];
        $mappedData['gps_city']       = $lastLocation['viat_rua'];
        $mappedData['is_ignition_on'] = $lastLocation['viat_ignicao'];
        $mappedData['speed']          = (float) $lastLocation['viat_velocidade'];
        $mappedData['fuel_level']     = 0;
        $mappedData['km']             = 0;

        return $mappedData;
    }

}