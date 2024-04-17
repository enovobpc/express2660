<?php

namespace App\Models\GpsGateway\Verizon;

use Setting, Jenssegers\Date\Date, File;

class Base extends \App\Models\GpsGateway\Base {

    /**
     * @var string
     * Documentation: https://fleetapi-pt.cartrack.com/rest/redoc.php#section/Authentication
     */
    public $url = "https://fim.api.eu.fleetmatics.com/";

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
     * Generate a session id
     *
     * @return bool
     */
    public function login()
    {
        return base64_encode($this->username . ':' . $this->password);
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
    public function execute($method, $data, $headers = [])
    {
        $url = $this->url . $method;

        $curl = curl_init();

        $data = http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "GET",
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_TIMEOUT         => 60
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception($err);
        }

        if (is_string($response) && is_array(json_decode($response, true)) && (json_last_error() == JSON_ERROR_NONE)) {
            try {
                $response = json_decode($response, true);
                return $response;
            } catch (\Exception $e) {
                throw new \Exception('Falha ao obter informação do pedido.');
            }
        } else {
            return $response;
        }

        
    }
}
