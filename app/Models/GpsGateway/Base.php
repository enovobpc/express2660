<?php

namespace App\Models\GpsGateway;

use App\Models\FleetGest\Vehicle;
use Setting;

class Base extends \App\Models\BaseModel
{

    /**
     * @var null
     */
    public $gateway = null;

    /**
     * Base constructor.
     * @param null $gateway
     */
    public function __construct($gateway = null)
    {
        try {
            if(empty($gateway)) {
                $gateway = env('GPS_GATEWAY') ? env('GPS_GATEWAY') : Setting::get('gps_gateway');
            }

            $gateway = empty($gateway) ? 'Inosat' : $gateway;
            $this->gateway = '\App\Models\GpsGateway\\' . ucwords(camel_case($gateway));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine());
        }
    }

    public function getNamespaceTo($class) {
        return $this->gateway . '\\' . $class;
    }

    /**
     * Get payment details
     * @return mixed
     */
    public function login() {

        try {
            $class = $this->getNamespaceTo('Base');
            $class = new $class();
            $result = $class->login();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get payment details
     * @return mixed
     */
    public function listVehicles() {

        try {
            $class = $this->getNamespaceTo('Vehicle');
            $class = new $class();
            $result = $class->listVehicles();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get payment details
     * @return mixed
     */
    public function getVehicleById($id) {

        try {
            $class = $this->getNamespaceTo('Vehicle');
            $class = new $class();
            $result = $class->getVehicleById($id);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get payment details
     * @return mixed
     */
    public function getVehicleLocation($vehicleId) {

        try {
            $class = $this->getNamespaceTo('Vehicle');
            $class = new $class();
            $result = $class->getVehicleLocation($vehicleId);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get the itinerary for a certain vehicle for a certain day.
     * @param $vehicleId
     * @param $date
     * @return mixed
     * @throws \Exception
     */
    public function getItenerary($vehicleId, $date) {
        try {
            $class = $this->getNamespaceTo('Itenerary');
            $class = new $class();
            $result = $class->getItenerary($vehicleId, $date);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get the itinerary for a certain vehicle for a certain day.
     * @param $vehicleId
     * @param $date
     * @return mixed
     * @throws \Exception
     */
    public function getRoute($vehicleId, $startDate, $endDate) {
        try {
            $class = $this->getNamespaceTo('Itenerary');
            $class = new $class();
            $result = $class->getRoute($vehicleId, $startDate, $endDate);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Syncronize vehicles information
     * @return mixed
     */
    public function syncVehiclesInfo() {

        try {
            $class = $this->getNamespaceTo('Vehicle');
            $class = new $class();
            $allVehicles = $class->listVehicles();

            if($allVehicles) {
                foreach ($allVehicles as $vehicleGpsData) {
                    $vehicleGpsData['counter_km'] = $vehicleGpsData['km'];
                    unset($vehicleGpsData['name'], $vehicleGpsData['is_immobilization_on'], $vehicleGpsData['voltage']);

                    Vehicle::where('license_plate', $vehicleGpsData['license_plate'])
                        ->update($vehicleGpsData);
                }
            }
            return true;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
