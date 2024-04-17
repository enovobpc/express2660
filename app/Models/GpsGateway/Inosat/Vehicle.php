<?php

namespace App\Models\GpsGateway\Inosat;

use Jenssegers\Date\Date;
use Setting, File;

class Vehicle extends \App\Models\GpsGateway\Inosat\Base {

    /**
     * List all vehicles
     * @return mixed|void
     */
    public function listVehicles() {

        $authId = $this->login();

        $data = [
            'aspNetSessionId' => $authId
        ];

        $response = $this->execute('VehicleService.asmx/GetVehicles', $data);
        $vehicles = @$response['VehicleWrapper'];

        if($vehicles) {
            $allVehicles = [];
            foreach ($vehicles as $vehicle) {
                $vehicle = (array) $vehicle;
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

        $authId = $this->login();

        $data = [
            'aspNetSessionId' => $authId,
            'licensePlate' => $licensePlate
        ];

        $response = $this->execute('VehicleService.asmx/GetVehicleByLicensePlate', $data);
        return $this->mappingVehicleData($response);
    }

    /**
     * Get vehicles location
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getVehicleLocation($vehicleId) {
        $authId = $this->login();

        $data = [
            'aspNetSessionId' => $authId,
            'vehicleId' => $vehicleId
        ];

        $response = $this->execute('VehicleService.asmx/GetLastLocation', $data);
        return $this->mappingLastLocationData($response);
    }

    /**
     * Mapping vehicle data
     * @param $vehicle
     */
    public function mappingVehicleData($vehicleData) {
        $lastLocation = (array) $vehicleData['LastLocation'];
        $mappedData = $this->mappingLastLocationData($lastLocation);
        $mappedData['gps_id']        = $vehicleData['VehicleId'];
        $mappedData['license_plate'] = $vehicleData['LicensePlate'];
        $mappedData['name']          = $vehicleData['Designation'];
        return $mappedData;
    }
    /**
     * Mapping vehicle data
     * @param $vehicle
     */
    public function mappingLastLocationData($lastLocation) {

        $mappedData['last_location'] = new Date($lastLocation['LocationDate']);
        $mappedData['latitude']      = $lastLocation['Latitude'];
        $mappedData['longitude']     = $lastLocation['Longitude'];
        $mappedData['gps_zip_code']  = $lastLocation['PostCode'];
        $mappedData['gps_city']      = $lastLocation['Town'];
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