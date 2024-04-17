<?php
namespace App\Models\InvoiceGateway\OnSearch;

use Response;

class Base {

    /**
     * @var string
     */
    public $url = '213.63.148.82:8090/'; //'http://activos24.virtualcloud.pt:8090/';

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;


    /**
     * KeyInvoice Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        $this->username = 'webservice';
        $this->password = sha1('pj87UwQnsFQ7Jh8U');
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($action, $data = null, $method = 'GET', $url = null)
    {

        if(!$url) {
            $url = $this->url . $action;
        }

        $curl = curl_init();


        if($method == 'GET') {

            if(!$url && !empty($data)) {
                $query = http_build_query($data);
                $url = $url . '?' . $query;
            }


            curl_setopt_array($curl, array(
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "User: " . $this->username,
                    "Pwd: " . $this->password
                ),
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => "",
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 0,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => "POST",
                CURLOPT_POSTFIELDS      => $data,
                CURLOPT_HTTPHEADER => array(
                    "User: " . $this->username,
                    "Pwd: " . $this->password,
                    "Content-Type: application/json"
                ),
            ));
        }

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);

        if(empty($response['Result'])) {
            return $response['Data'];
        } else {
            throw new \Exception($response['Message']);
        }
    }
}