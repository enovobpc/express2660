<?php

namespace App\Models\GpsGateway\Geocar;

use Setting, Jenssegers\Date\Date, File;

class Base extends \App\Models\GpsGateway\Base {

    /**
     * @var string
     * Documentation: https://fleetapi-pt.cartrack.com/rest/redoc.php#section/Authentication
     */
    public $url = "https://webservices.geocar.info/public/geocar/";
    //https://webservices.geocar.info/public/geocar/get_viats_data.json?api_key=d994cc6c-2ef4-492c-877b-6a1454bfae1c
    //"user": "TNosAPI", "pwd": "*Trans2020!"

    /**
     * @var null
     */
    public $sessionId = null;

    /**
     * @var null
     */
    public $apiKey = null;

    /**
     * @var null
     */
    public $username = null;

    /**
     * @var null
     */
    public $password = null;

    /**
     * KeyInvoice Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct()
    {
        $this->username = Setting::get('gps_gateway_username');
        $this->password = Setting::get('gps_gateway_password');
        $this->apiKey   = Setting::get('gps_gateway_apikey');

        // if(config('app.env') == 'local') {
        //     $this->username = 'TRAN00296';
        //     $this->password = 'Silvia.1';
        // }
    }

    /**
     * Execute a cURL request
     *
     * @param $method
     * @param $data
     * @param $headers
     * @return mixed
     * @throws \Exception
     */
    public function execute($method, $data, $headers = [], $curlMethod = 'POST')
    {
        
        $url = $this->url . $method;

        $curl = curl_init();

        $data = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $curlMethod,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_TIMEOUT         => 60
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception($err);
        }

  
        $response = json_decode($response, true);

        if($response['status'] == 0) {
            throw new \Exception(@$response['result']['code'].' - '.@$response['result']['message']);
        } else {
            return @$response['result'];
        }
        
    }
}