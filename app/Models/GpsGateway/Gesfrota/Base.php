<?php

namespace App\Models\GpsGateway\Gesfrota;

use Setting, File;

class Base extends \App\Models\GpsGateway\Base {

    /**
     * @var string
     * Documentation: http://gesfrotaservices.localeasy.pt/WebService.asmx
     */
    public $url = "http://gesfrotaservices.localeasy.pt/WebService.asmx";

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
        $this->apiKey   = Setting::get('gateway_gps_key');
        $this->username = Setting::get('gateway_gps_username');
        $this->password = Setting::get('gateway_gps_password');

        if(config('app.env') == 'local') {
            $this->apiKey   = 'nyNirb7OaoZ2efe2gCiD7nq5JAMX72';
            $this->username = '';
            $this->password = '';
        }
    }

    /**
     * Generate a session id
     *
     * @return bool
     */
    public function login()
    {
        return $this->apiKey;
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($method, $data, $headers = null)
    {
        $url = $this->url . $method;

        $data = http_build_query($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception($err);
        }

        try {
            $response = simplexml_load_string($response);
            $response = (array)$response;
            return $response;
        } catch (\Exception $e) {
            throw new \Exception('Falha ao obter informação do pedido.');
        }
    }
}