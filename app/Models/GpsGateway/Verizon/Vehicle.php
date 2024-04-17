<?php

namespace App\Models\GpsGateway\Verizon;

use Jenssegers\Date\Date;
use Setting, File;

class Vehicle extends \App\Models\GpsGateway\Verizon\Base {

    /**
     * get token
     * @return mixed|void
     */
    public function getToken() {

        $headers = [
            'Authorization: Basic ' . $this->login(),
            'Content-Type: application/json'
        ];
        $response = $this->execute('token', [], $headers);

        $token = @$response;

        return $token;
    }
    
    /**
     * List all vehicles
     * @return mixed|void
     */
    public function listVehicles() {

        //$token = $this->getToken();

        $headers = [
            'Authorization: Atmosphere atmosphere_app_id=' . $this->apiKey .', Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjJDREY5REVGNkI2RDMwNTM3QTdFRTZENzI3MDYxQzkxQzgzMTQ1MjkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJMTi1kNzJ0dE1GTjZmdWJYSndZY2tjZ3hSU2sifQ.eyJuYmYiOjE2OTUzMDYzOTksImV4cCI6MTY5NTM5Mjc5OSwiaXNzIjoiaHR0cHM6Ly9hdXRoLmV1LmZsZWV0bWF0aWNzLmNvbS90b2tlbiIsImF1ZCI6WyJodHRwczovL2F1dGguZXUuZmxlZXRtYXRpY3MuY29tL3Rva2VuL3Jlc291cmNlcyIsIm9wZW5pZCIsInByb2ZpbGUiLCJyZXZlYWwiXSwiY2xpZW50X2lkIjoibWFzdGVyIiwic3ViIjoiMzA2NGZjODgtMmNiYy00OWY1LTg4ZWYtMDhkYjY4M2IxY2JlIiwiYXV0aF90aW1lIjoxNjk1MzA2Mzk5LCJpZHAiOiJsb2NhbCIsInJldmVhbF9hY2NvdW50X2lkIjoiMTAzODU3NCIsInJldmVhbF91c2VyX2lkIjoiMjQ5OTAwMyIsInJldmVhbF91c2VyX3R5cGVfaWQiOiIzIiwidW5pcXVlX25hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJuYW1lIjoiUkVTVF9FTk9WT184NTE0QDEwMzg1NzQuY29tIiwiZW1haWwiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImp0aSI6IjkyYmIzNDI0MDVhNjNmZjBjZGIwNDk3YjVmMjE3NDg4IiwiaWF0IjoxNjk1MzA2Mzk5LCJzY29wZSI6WyJvcGVuaWQiLCJwcm9maWxlIiwicmV2ZWFsIl0sImFtciI6WyJwd2QiXX0.WCVI-NnezViBwZkpwwbjqqlzDFftFUNEEvA1VIB4G8rHBT_B4pXl27MWXII1covSEpvI1KTCYV0frSo6oPJohUmVP0rV4NIYZGr3g7pSBUxpe20GPZHR_iNJ0peqTOypeelbt8en5DY4YXgIBbRBHsGiLfX85p83P9naFUYyzLT4vIWUYtkQDTvFcurOmeEF71Z67ZwUQLMeaB_hwt8Wz3k5NHpM9RRzx8JJsFj4AiEHUAG0Hfww8d1wFNWyrqfY6RgWlomJz4QZ2CTUdTxIgDoSlTuZolD54fH0SFs65Nn6zYbLpdyQzo4z2m0CtZ5jmQzvm9uPZfATeEdDITb9xA'
        ];

        $response = $this->execute('cmd/v1/vehicles', [], $headers);

        $vehicles = @$response;

        if($vehicles) {
            $allVehicles = [];
            foreach ($vehicles as $vehicle) {
                $allVehicles[] = $this->getVehicleLocation($vehicle);
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

        //$token = $this->getToken();

        $headers = [
            'Authorization: Atmosphere atmosphere_app_id=' . $this->apiKey .', Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjJDREY5REVGNkI2RDMwNTM3QTdFRTZENzI3MDYxQzkxQzgzMTQ1MjkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJMTi1kNzJ0dE1GTjZmdWJYSndZY2tjZ3hSU2sifQ.eyJuYmYiOjE2OTUzMDYzOTksImV4cCI6MTY5NTM5Mjc5OSwiaXNzIjoiaHR0cHM6Ly9hdXRoLmV1LmZsZWV0bWF0aWNzLmNvbS90b2tlbiIsImF1ZCI6WyJodHRwczovL2F1dGguZXUuZmxlZXRtYXRpY3MuY29tL3Rva2VuL3Jlc291cmNlcyIsIm9wZW5pZCIsInByb2ZpbGUiLCJyZXZlYWwiXSwiY2xpZW50X2lkIjoibWFzdGVyIiwic3ViIjoiMzA2NGZjODgtMmNiYy00OWY1LTg4ZWYtMDhkYjY4M2IxY2JlIiwiYXV0aF90aW1lIjoxNjk1MzA2Mzk5LCJpZHAiOiJsb2NhbCIsInJldmVhbF9hY2NvdW50X2lkIjoiMTAzODU3NCIsInJldmVhbF91c2VyX2lkIjoiMjQ5OTAwMyIsInJldmVhbF91c2VyX3R5cGVfaWQiOiIzIiwidW5pcXVlX25hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJuYW1lIjoiUkVTVF9FTk9WT184NTE0QDEwMzg1NzQuY29tIiwiZW1haWwiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImp0aSI6IjkyYmIzNDI0MDVhNjNmZjBjZGIwNDk3YjVmMjE3NDg4IiwiaWF0IjoxNjk1MzA2Mzk5LCJzY29wZSI6WyJvcGVuaWQiLCJwcm9maWxlIiwicmV2ZWFsIl0sImFtciI6WyJwd2QiXX0.WCVI-NnezViBwZkpwwbjqqlzDFftFUNEEvA1VIB4G8rHBT_B4pXl27MWXII1covSEpvI1KTCYV0frSo6oPJohUmVP0rV4NIYZGr3g7pSBUxpe20GPZHR_iNJ0peqTOypeelbt8en5DY4YXgIBbRBHsGiLfX85p83P9naFUYyzLT4vIWUYtkQDTvFcurOmeEF71Z67ZwUQLMeaB_hwt8Wz3k5NHpM9RRzx8JJsFj4AiEHUAG0Hfww8d1wFNWyrqfY6RgWlomJz4QZ2CTUdTxIgDoSlTuZolD54fH0SFs65Nn6zYbLpdyQzo4z2m0CtZ5jmQzvm9uPZfATeEdDITb9xA'
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

        //$token = $this->getToken();
        
        $headers = [
            'Authorization: Atmosphere atmosphere_app_id=' . $this->apiKey .', Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjJDREY5REVGNkI2RDMwNTM3QTdFRTZENzI3MDYxQzkxQzgzMTQ1MjkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJMTi1kNzJ0dE1GTjZmdWJYSndZY2tjZ3hSU2sifQ.eyJuYmYiOjE2OTUzMDYzOTksImV4cCI6MTY5NTM5Mjc5OSwiaXNzIjoiaHR0cHM6Ly9hdXRoLmV1LmZsZWV0bWF0aWNzLmNvbS90b2tlbiIsImF1ZCI6WyJodHRwczovL2F1dGguZXUuZmxlZXRtYXRpY3MuY29tL3Rva2VuL3Jlc291cmNlcyIsIm9wZW5pZCIsInByb2ZpbGUiLCJyZXZlYWwiXSwiY2xpZW50X2lkIjoibWFzdGVyIiwic3ViIjoiMzA2NGZjODgtMmNiYy00OWY1LTg4ZWYtMDhkYjY4M2IxY2JlIiwiYXV0aF90aW1lIjoxNjk1MzA2Mzk5LCJpZHAiOiJsb2NhbCIsInJldmVhbF9hY2NvdW50X2lkIjoiMTAzODU3NCIsInJldmVhbF91c2VyX2lkIjoiMjQ5OTAwMyIsInJldmVhbF91c2VyX3R5cGVfaWQiOiIzIiwidW5pcXVlX25hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJuYW1lIjoiUkVTVF9FTk9WT184NTE0QDEwMzg1NzQuY29tIiwiZW1haWwiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImp0aSI6IjkyYmIzNDI0MDVhNjNmZjBjZGIwNDk3YjVmMjE3NDg4IiwiaWF0IjoxNjk1MzA2Mzk5LCJzY29wZSI6WyJvcGVuaWQiLCJwcm9maWxlIiwicmV2ZWFsIl0sImFtciI6WyJwd2QiXX0.WCVI-NnezViBwZkpwwbjqqlzDFftFUNEEvA1VIB4G8rHBT_B4pXl27MWXII1covSEpvI1KTCYV0frSo6oPJohUmVP0rV4NIYZGr3g7pSBUxpe20GPZHR_iNJ0peqTOypeelbt8en5DY4YXgIBbRBHsGiLfX85p83P9naFUYyzLT4vIWUYtkQDTvFcurOmeEF71Z67ZwUQLMeaB_hwt8Wz3k5NHpM9RRzx8JJsFj4AiEHUAG0Hfww8d1wFNWyrqfY6RgWlomJz4QZ2CTUdTxIgDoSlTuZolD54fH0SFs65Nn6zYbLpdyQzo4z2m0CtZ5jmQzvm9uPZfATeEdDITb9xA' 
        ];
        $url = 'rad/v1/vehicles/' . $vehicleId['VehicleNumber'] . '/location';
        $response = $this->execute($url, [], $headers);

        $vehicle = @$response;

        if($vehicle) {
            return $this->mappingLastLocationData($vehicle, $vehicleId);
        }

        return null;
    }

    /**
     * Get status
     * @param $vehicleData
     */
    public function getStatus($vehicleId) {

        //$token = $this->getToken();
        
        $headers = [
            'Authorization: Atmosphere atmosphere_app_id=' . $this->apiKey .', Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjJDREY5REVGNkI2RDMwNTM3QTdFRTZENzI3MDYxQzkxQzgzMTQ1MjkiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJMTi1kNzJ0dE1GTjZmdWJYSndZY2tjZ3hSU2sifQ.eyJuYmYiOjE2OTUzMDYzOTksImV4cCI6MTY5NTM5Mjc5OSwiaXNzIjoiaHR0cHM6Ly9hdXRoLmV1LmZsZWV0bWF0aWNzLmNvbS90b2tlbiIsImF1ZCI6WyJodHRwczovL2F1dGguZXUuZmxlZXRtYXRpY3MuY29tL3Rva2VuL3Jlc291cmNlcyIsIm9wZW5pZCIsInByb2ZpbGUiLCJyZXZlYWwiXSwiY2xpZW50X2lkIjoibWFzdGVyIiwic3ViIjoiMzA2NGZjODgtMmNiYy00OWY1LTg4ZWYtMDhkYjY4M2IxY2JlIiwiYXV0aF90aW1lIjoxNjk1MzA2Mzk5LCJpZHAiOiJsb2NhbCIsInJldmVhbF9hY2NvdW50X2lkIjoiMTAzODU3NCIsInJldmVhbF91c2VyX2lkIjoiMjQ5OTAwMyIsInJldmVhbF91c2VyX3R5cGVfaWQiOiIzIiwidW5pcXVlX25hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJuYW1lIjoiUkVTVF9FTk9WT184NTE0QDEwMzg1NzQuY29tIiwiZW1haWwiOiJSRVNUX0VOT1ZPXzg1MTRAMTAzODU3NC5jb20iLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImp0aSI6IjkyYmIzNDI0MDVhNjNmZjBjZGIwNDk3YjVmMjE3NDg4IiwiaWF0IjoxNjk1MzA2Mzk5LCJzY29wZSI6WyJvcGVuaWQiLCJwcm9maWxlIiwicmV2ZWFsIl0sImFtciI6WyJwd2QiXX0.WCVI-NnezViBwZkpwwbjqqlzDFftFUNEEvA1VIB4G8rHBT_B4pXl27MWXII1covSEpvI1KTCYV0frSo6oPJohUmVP0rV4NIYZGr3g7pSBUxpe20GPZHR_iNJ0peqTOypeelbt8en5DY4YXgIBbRBHsGiLfX85p83P9naFUYyzLT4vIWUYtkQDTvFcurOmeEF71Z67ZwUQLMeaB_hwt8Wz3k5NHpM9RRzx8JJsFj4AiEHUAG0Hfww8d1wFNWyrqfY6RgWlomJz4QZ2CTUdTxIgDoSlTuZolD54fH0SFs65Nn6zYbLpdyQzo4z2m0CtZ5jmQzvm9uPZfATeEdDITb9xA'
        ];
        $url = 'rad/v1/vehicles/' . $vehicleId['VehicleNumber'] . '/status';

        $response = $this->execute($url, [], $headers);

        $vehicleStatus = @$response;

        if($vehicleStatus) {
            return $this->mappingLastLocationData($vehicleStatus, $vehicleId);
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
    public function mappingLastLocationData($vehicleData, $vehicleId) {

        $vehicleStatus = $this->getStatus($vehicleId);

        if(isset($vehicleData[0])) {
            $vehicleData = $vehicleData[0];
        }
//dd($vehicleData);
        $mappedData['last_location']    = new Date($vehicleData['UpdateUTC']);
        $mappedData['latitude']         = $vehicleData['Latitude'];
        $mappedData['longitude']        = $vehicleData['Longitude'];
        $mappedData['gps_zip_code']     = $vehicleData['Address']['PostalCode'];
        $mappedData['gps_city']         = $vehicleData['Address']['AddressLine1'];
        $mappedData['license_plate']    = $vehicleId['VehicleNumber'];
        $mappedData['gps_country']      = $vehicleData['Address']['Country'];

        //$mappedData['is_ignition_on']     = $vehicleData['Stop'];
        $mappedData['is_immobilization_on'] = $vehicleStatus['DisplayState'] == 'Moving' ? true : false;
        $mappedData['speed']                = (float) $vehicleStatus['Speed'];
        $mappedData['fuel_level']           = (float) $vehicleId['TankCapacity'] ?? 0;
        $mappedData['km']                   = $vehicleStatus['CurrentOdometer'];
        //$mappedData['km']                   = '100';
        //$mappedData['voltage']              = (float) $vehicleData['Voltage'];

        return $mappedData;
    }

}
