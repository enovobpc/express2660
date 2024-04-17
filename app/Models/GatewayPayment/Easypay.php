<?php

namespace App\Models\GatewayPayment;

use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class Easypay extends \App\Models\GatewayPayment\Base {

    /**
     * @var string
     */
    private $url = 'https://api.prod.easypay.pt/2.0';

    /**
     * @var null
     */
    private $accountId;

    /**
     * @var null
     */
    private $accountKey;

    /**
     * @var null
     */
    private $keyCode;

    /**
     * Easypay constructor.
     * @param null $accountId
     * @param null $accountKey
     */
    public function __construct($accountId = null, $accountKey = null, $keyCode = null)
    {

        if(config('app.env') == 'local') {
            $this->url        = 'https://api.test.easypay.pt/2.0';
            $this->accountId  = '647c18a0-884f-4097-8b3d-c37c36df66ec';
            $this->accountKey = '7472194a-586c-4641-a9a2-c29326f76174';
            $this->apiKeyCode = 'edfc556cbeaaf95495c7d536dcc7701f';
        } else {
            /*$accountId  = '766c379f-970f-483a-88c7-08f3874a5978';
            $accountKey = 'ffc85aff-2bcf-4295-84fe-af5ae0f837ee';
            $keyCode    = '59850cfcf8793d5f552acae3602531c5';*/

            $this->accountId  = $accountId ? $accountId : Setting::get('easypay_account_id');
            $this->accountKey = $accountKey ? $accountKey : Setting::get('easypay_account_key');
            $this->apiKeyCode = $keyCode ? $keyCode :  Setting::get('easypay_key_code');
        }
    }

    /**
     *
     */
    public function createSinglePayment($method, $data)
    {
        $expirationTime = new Carbon($data['expiration_time']);

        $body = [
            "key"               => @$data['payment_key'], //ID da transação. Definido pelo programa (Ex. nº encomenda)
            "method"            => $method,
            "type"	            => 'sale',
            "value"	            => floatval(@$data['value']),
            "currency"	        => @$data['currency'],
            "expiration_time"   => $expirationTime->format('Y-m-d H:i'),

            "capture" => [ //obrigatorio sempre que o tipo seja sale
                "transaction_key"   => @$data['payment_key'], //Your internal key identifying this capture
                "descriptive"       => @$data['description'], //This will appear in the bank statement/mbway application
                "capture_date"      => date('Y-m-d'),
            ],

            "customer" => [
                "name"          => @$data['customer_name'],
                "email"         => @$data['customer_email'],
                "key"           => @$data['customer_code'],
                //"phone_indicative" => "+351",
                "phone"         => @$data['customer_phone'],
                "fiscal_number" => @$data['customer_vat'] ? @$data['customer_country'] . @$data['customer_vat'] : 'PT999999999',
            ],
        ];

        $result =  $this->executePost('single', $body);

        if(empty($result)) {
            return false;
        }

        if($result['status'] == 'error') {
            throw new Exception(@$result['message'][0], -1);
        }

        $result = [
            'payment_key'      => @$data['payment_key'],
            'transaction_id'   => @$result['id'],
            'type'             => @$body['type'],
            'method'           => @$result['method']['type'],
            'status'           => @$result['method']['status'], //"waiting" "pending" "paid" "active" "failed" "canceled" "deleted"
            'reference'        => @$result['method']['reference'],
            'entity'           => @$result['method']['entity'],
            'visa_url'         => @$result['method']['url'],
            'card_last_digits' => @$result['method']['last_four'],
            'card_type'        => @$result['method']['card_type'],
            'mbw_alias'        => @$result['method']['alias'],
            'expiration_time'  => @$data['expiration_time']
        ];

        if($method == 'cc') {
            //get reference and entity from visa url
            $parts = parse_url($result['visa_url']);
            parse_str($parts['query'], $queryStr);

            $result['entity']    = $queryStr['e'];
            $result['reference'] = $queryStr['r'];
        }

        return $result;
    }


    /**
     * Return single payment information
     * @param $paymentKey
     */
    public function getSinglePayment($paymentKey)
    {
        $url = $this->url . '/single/' . $paymentKey;

        $data = $this->executeGet($url);

        if($data) {

            $result = [
                'payment_key'      => @$data['key'],
                'transaction_id'   => @$data['id'],

                'value'            => @$data['value'],
                'method'           => strtolower(@$data['method']['type']),
                'status'           => @$data['method']['status'], //"waiting" "pending" "paid" "active" "failed" "canceled" "deleted"
                'reference'        => @$data['method']['reference'],
                'entity'           => @$data['method']['entity'],
                'visa_url'         => @$data['method']['url'],
                'card_last_digits' => @$data['method']['last_four'],
                'card_type'        => @$data['method']['card_type'],
                'mbw_alias'        => @$data['method']['alias'],
                'expiration_time'  => @$data['expiration_time'],

                'customer_name'    => @$data['customer']['name'],
                'customer_vat'     => substr(@$data['customer']['fiscal_number'], 2),
                'customer_country' => substr(@$data['customer']['fiscal_number'], 0, 2),
                'customer_email'   => @$data['customer']['email'],
                'customer_phone'   => @$data['customer']['phone'],
            ];

            return $result;
        }
    }

    /**
     * Delete single payment
     * @param $paymentKey
     */
    public function deleteSinglePayment($paymentKey)
    {
        $url = $this->url . '/single/' . $paymentKey;

        $result = $this->executeGet($url, $this->getHeaders());

        return $result;
    }

    /**
     * Get headers
     * @return array
     */
    public function getHeaders() {
        $headers = [
            "AccountId: " . $this->accountId,
            "ApiKey: " . $this->accountKey,
            'Content-Type: application/json',
        ];

        return $headers;
    }

    /**
     * Execute Post Method
     * @param $method
     * @param $body
     * @param null $headers
     * @return mixed
     */
    public function executePost($apiMethod, $body, $headers = null){

        $url = $this->url . '/' . $apiMethod;

        $headers = empty($headers) ? $this->getHeaders() : $headers;

        $curlOpts = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => 1,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_POSTFIELDS      => json_encode($body),
            CURLOPT_HTTPHEADER      => $headers,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $curlOpts);
        $response_body = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response_body, true);

        return $response;
    }

    /**
     * Execute Get Method
     * @param $method
     * @param $body
     * @param null $headers
     * @return mixed
     */
    public function executeGet($url, $headers = null){

        $headers = empty($headers) ? $this->getHeaders() : $headers;

        $curlOpts = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_HTTPHEADER      => $headers,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $curlOpts);
        $response_body = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response_body, true);

        return $response;
    }

    /**
     * Execute Delete Method
     * @param $method
     * @param $body
     * @param null $headers
     * @return mixed
     */
    public function executeDelete($url, $headers = null){

        $headers = empty($headers) ? $this->getHeaders() : $headers;

        $curlOpts = [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_CUSTOMREQUEST   => "DELETE"
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $curlOpts);
        $response_body = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response_body, true);

        return $response;
    }
}