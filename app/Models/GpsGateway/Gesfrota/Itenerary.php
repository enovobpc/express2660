<?php

namespace App\Models\GpsGateway\Gesfrota;

use Jenssegers\Date\Date;
use Setting, File;

class Itenerary extends \App\Models\GpsGateway\Gesfrota\Base {

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
            'key'       => $authId,
            'matricula' => $vehicleId,
            'data'      => $startDate,
        ];

        $response = $this->execute('/GetLocalizacaoViaturaPontos', $data);
        $locations = @$response['LocalizacaoPonto'];

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
        return false;
    }


    /**
     * Mapping vehicle data
     * @param $vehicle
     */
    public function mappingRouteData($vehicleData) {

        $fuel = (array) @$vehicleData['fuelLevelPercentageCanBus'];

        $mappedData['last_location'] = $vehicleData['dataComunicacao'];
        $mappedData['latitude']      = $vehicleData['latitude'];
        $mappedData['longitude']     = $vehicleData['longitude'];
        $mappedData['gps_zip_code']  = '';
        $mappedData['gps_city']      = '';
        $mappedData['gps_country']   = '';

        $mappedData['is_ignition_on']       = $vehicleData['ignition'] == 'true' ? true : false;
        $mappedData['is_immobilization_on'] = false;
        $mappedData['speed']                = (float) $vehicleData['speed'];
        $mappedData['fuel_level']           = @$fuel[0];
        $mappedData['km']                   = (float) $vehicleData['distanceOdometer'];
        $mappedData['voltage']              = '0';

        $mappedData['gps_id']        = $vehicleData['matricula'];
        $mappedData['license_plate'] = $vehicleData['matricula'];
        $mappedData['name']          = $vehicleData['matricula'];

        return $mappedData;
    }

}