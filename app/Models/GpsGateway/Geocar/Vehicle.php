<?php

namespace App\Models\GpsGateway\Geocar;

use Jenssegers\Date\Date;
use Setting, File;

class Vehicle extends \App\Models\GpsGateway\Geocar\Base {

    /**
     * List all vehicles
     * @return mixed|void
     */
    public function listVehicles() {

        $urlMethod = 'get_viats_data.json?api_key=' . $this->apiKey;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $data = [
            'user' => $this->username,
            'pwd'  => $this->password
        ];


        $response = $this->execute($urlMethod, $data, $headers);
        $vehicles = $response;
 
        if($vehicles) {
            $allVehicles = [];
            foreach ($vehicles as $vehicle) {
                 /* if($vehicle['viat_ignicao']) {
                    dd($vehicle);
                }  */
                $allVehicles[] = $this->mappingVehicleData($vehicle);
            }

            return $allVehicles;
        }

        return null;
    }

    /**
     * Get Vehicle By Id
     * @return mixed|void
     */
    public function getVehicleById($id) {

        $urlMethod = 'get_current_data.json?api_key=' . $this->apiKey;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $data = [
            'user' => $this->username,
            'pwd'  => $this->password,
            'viat_id' => $id
        ];


        $response = $this->execute($urlMethod, $data, $headers);

        $vehicle = $this->mappingVehicleData($response);
        
        return $vehicle;
    }

    /**
     * Get vehicle by license plate
     * @return mixed|void
     */
    public function getVehicleByLicensePlate($licensePlate) {
        return $this->getVehicleById($licensePlate);
    }

    /**
     * Get vehicles location
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getVehicleLocation($vehicleId) {
        return $this->getVehicleById($vehicleId);
    }

    /**
     * Mapping vehicle data
     * @param $vehicleData
     */
    public function mappingVehicleData($vehicleData) {

        $mappedData['gps_id']        = $vehicleData['viat_id'];
        $mappedData['license_plate'] = $vehicleData['viat_matricula'];
        $mappedData['name']          = $vehicleData['viat_matricula'];

        $mappedData['last_location'] = new Date($vehicleData['datahora']);
        $mappedData['latitude']      = $vehicleData['viat_lat'];
        $mappedData['longitude']      = $vehicleData['viat_lon'];
        $mappedData['gps_city']       = $vehicleData['viat_rua'];
        $mappedData['is_ignition_on'] = $vehicleData['viat_ignicao'];
        $mappedData['speed']          = (float) $vehicleData['viat_velocidade'];
        $mappedData['fuel_level']     = 0;
        $mappedData['km']             = 0;

        return $mappedData;
    }


}