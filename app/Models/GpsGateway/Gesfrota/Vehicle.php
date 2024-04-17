<?php

namespace App\Models\GpsGateway\Gesfrota;

use Jenssegers\Date\Date;
use Setting, File;

class Vehicle extends \App\Models\GpsGateway\Gesfrota\Base {

    /**
     * List all vehicles
     * @return mixed|void
     */
    public function listVehicles() {

        $authId = $this->login();

        $data = [
            'key' => $authId
        ];

        $response = $this->execute('/GetLocalizacaoViaturasTodas', $data);
        $vehicles = @$response['LocalizacaoViatura'];

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
        return false;
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
            'key' => $authId,
            'matricula' => $vehicleId
        ];

        $response = $this->execute('/GetLocalizacaoViatura', $data);
        return $this->mappingVehicleData($response);
    }

    /**
     * Mapping vehicle data
     * @param $vehicle
     */
    public function mappingVehicleData($vehicleData) {
        $fuel = (array) @$vehicleData['fuelLevelPercentageCanBus'];

        $timestamp = substr($vehicleData['dataUltimaActualizacao'], '0','-3');
        $datetime  = date("Y-m-d H:i:s", $timestamp);
        $mappedData['last_location'] = $datetime;
        $mappedData['latitude']      = $vehicleData['latitude'];
        $mappedData['longitude']     = $vehicleData['longitude'];
        $mappedData['gps_zip_code']  = '';
        $mappedData['gps_city']      = '';
        $mappedData['gps_country']   = '';

        $mappedData['is_ignition_on']       = $vehicleData['ignition'] == 'true' ? true : false;
        $mappedData['is_immobilization_on'] = false;
        $mappedData['speed']                = (float) $vehicleData['speed'];
        $mappedData['fuel_level']           = @$fuel[0];
        $mappedData['km']                   = (float) $vehicleData['km'];
        $mappedData['voltage']              = '0';

        $mappedData['gps_id']        = $vehicleData['matricula'];
        $mappedData['license_plate'] = $vehicleData['matricula'];
        $mappedData['name']          = $vehicleData['matricula'];

        return $mappedData;
    }

}