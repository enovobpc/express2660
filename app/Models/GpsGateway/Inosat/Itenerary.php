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

        $authId = $this->login();

        $data = [
            'aspNetSessionId'   => $authId,
            'vehicleId'         => $vehicleId,
            'fromDate'          => $startDate.' 00:00:00',
            'toDate'            => $endDate.' 23:59:59'
        ];

        $response = $this->execute('/VehicleService.asmx/GetRoute', $data);
        $locations = @$response['LocationWrapper'];

        if($locations) {
            $route = [];
            foreach ($locations as $key => $location) {
                $location = (array) $location;
                $location = $this->mappingRouteData($location);
                $location['id'] = 'p_'.$vehicleId.'_'.$key;
                $location['gps_id'] = $vehicleId;
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

        $mappedData['last_location'] = new Date($lastLocation['LocationDate']);
        $mappedData['latitude']      = $lastLocation['Latitude'];
        $mappedData['longitude']     = $lastLocation['Longitude'];
        $mappedData['gps_zip_code']  = $lastLocation['PostCode'];
        $mappedData['gps_city']      = ($lastLocation['TownPart'] ? $lastLocation['TownPart'].', ' : '') . $lastLocation['Town'] ;
        $mappedData['gps_country']   = trim(strtolower($lastLocation['CountryCode']));

        $mappedData['is_ignition_on']       = $lastLocation['IsIgnitionOn'] == 'true' ? true : false;
        $mappedData['is_immobilization_on'] = $lastLocation['IsImmobilizationOn'] == 'true' ? true : false;
        $mappedData['speed']                = (float) $lastLocation['Speed'];
        $mappedData['fuel_level']           = (float) $lastLocation['FuelLevel'];
        $mappedData['km']                   = (float) $lastLocation['TotalKms'];
        $mappedData['voltage']              = (float) $lastLocation['Voltage'];

        return $mappedData;
    }

}