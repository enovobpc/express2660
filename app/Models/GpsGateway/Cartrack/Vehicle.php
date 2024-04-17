<?php

namespace App\Models\GpsGateway\Cartrack;

use Jenssegers\Date\Date;
use Setting, File;

class Vehicle extends \App\Models\GpsGateway\Cartrack\Base {

    /**
     * List all vehicles
     * @return mixed|void
     */
    public function listVehicles() {

        $headers = [
            'Authorization: Basic ' . $this->login()
        ];

        $response = $this->execute('vehicles/status', [], $headers);
        $vehicles = @$response['data'];
 
        if($vehicles) {
            $allVehicles = [];
            foreach ($vehicles as $vehicle) {
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
        return $this->getVehicleByLicensePlate($id);
    }

    /**
     * Get vehicle by license plate
     * @return mixed|void
     */
    public function getVehicleByLicensePlate($licensePlate) {
        $headers = [
            'Authorization: Basic ' . $this->login()
        ];

        $response = $this->execute('vehicles', [
            'registration' => $licensePlate
        ], $headers);

        $vehicle = @$response['data'];
        if($vehicle) {
            return $this->mappingVehicleData($vehicle);
        }

        return null;
    }

    /**
     * Get vehicles location
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getVehicleLocation($vehicleId) {

        $headers = [
            'Authorization: Basic ' . $this->login()
        ];

        $response = $this->execute('vehicles/status?filter[registration]='.$vehicleId, [
            'registration' => $vehicleId
        ], $headers);

        $vehicle = @$response['data'];
        if($vehicle) {
            return $this->mappingLastLocationData($vehicle);
        }

        return null;
    }

    /**
     * Mapping vehicle data
     * @param $vehicleData
     */
    public function mappingVehicleData($vehicleData) {
        $mappedData['gps_id']        = $vehicleData['vehicle_id'];
        $mappedData['license_plate'] = $vehicleData['registration'];
        $mappedData['name']          = $vehicleData['vehicle_name'] ?? $vehicleData['chassis_number'];

        return array_merge($mappedData, $this->mappingLastLocationData($vehicleData));
    }

    /**
     * Mapping vehicle location data
     * @param $vehicleData
     */
    public function mappingLastLocationData($vehicleData) {

        if(isset($vehicleData[0])) {
            $vehicleData = $vehicleData[0];
        }

        $mappedData['last_location'] = new Date($vehicleData['location']['updated']);
        $mappedData['latitude']      = $vehicleData['location']['latitude'];
        $mappedData['longitude']     = $vehicleData['location']['longitude'];
        //$mappedData['gps_zip_code']  = $vehicleData['PostCode'];
        $mappedData['gps_city']      = $vehicleData['location']['position_description'];
        //$mappedData['gps_country']   = trim(strtolower($vehicleData['CountryCode']));

        $mappedData['is_ignition_on']       = $vehicleData['ignition'];
        //$mappedData['is_immobilization_on'] = $vehicleData['IsImmobilizationOn'] == 'true' ? true : false;
        $mappedData['speed']                = (float) $vehicleData['speed'];
        $mappedData['fuel_level']           = (float) $vehicleData['fuel']['level'] ?? 0;
        $mappedData['km']                   = $vehicleData['odometer'] / 1000;
        //$mappedData['voltage']              = (float) $vehicleData['Voltage'];

        return $mappedData;
    }

}