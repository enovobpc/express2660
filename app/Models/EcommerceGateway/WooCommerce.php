<?php

namespace App\Models\EcommerceGateway;

use App\Models\EcommerceGateway\Base;

class WooCommerce extends Base
{
    private $endpoint;
    private $key;
    private $secret;

    /**
     * Constructor
     * @param $debug
     */
    public function __construct($endpoint = null, $user = null, $password = null, $key = null, $secret = null)
    {
        $this->endpoint = $endpoint;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Required functions
     */

    public function listCarriers()
    {
        return [];
    }

    public function listOrdersStatus()
    {
        return [
            [
                'code' => 'pending',
                'name' => 'Pending payment'
            ],
            [
                'code' => 'processing',
                'name' => 'Processing'
            ],
            [
                'code' => 'on-hold',
                'name' => 'On hold'
            ],
            [
                'code' => 'completed',
                'name' => 'Completed'
            ],
            [
                'code' => 'cancelled',
                'name' => 'Cancelled'
            ],
            [
                'code' => 'refunded',
                'name' => 'Refunded'
            ],
            [
                'code' => 'failed',
                'name' => 'Failed'
            ],
        ];
    }

    public function listOrders()
    {
        $orders = @$this->execute('GET', '/wp-json/wc/v3/orders') ?? [];

        $mappedOrders = [];
        foreach ($orders as $order) {
            $address = $order['shipping'];
            if (empty($order['shipping']['address_1'])) {
                $address = $order['billing'];
            }

            $name = trim($address['first_name'] . ' ' . $address['last_name']);
            if (empty($name)) {
                $name = @$address['company'];
            }

            $volumes = 0;
            $weight  = 0;
            $packDimensions = [];
            foreach ($order['line_items'] as $line) {
                $volumes += $line['quantity'];
                $weight  += 1;

                $packDimensions[] = [
                    'type'        => 'box',
                    'description' => $line['name'],
                    'qty'         => $line['quantity'],
                    'width'       => null,
                    'height'      => null,
                    'length'      => null,
                    'weight'      => 1,
                ];
            }

            $mappedOrders[] = [
                'code'               => $order['id'],
                'reference'          => $order['id'],
                'recipient_name'     => $name,
                'recipient_address'  => $address['address_1'] . ' ' . $address['address_2'],
                'recipient_zip_code' => $address['postcode'],
                'recipient_city'     => $address['city'],
                'recipient_country'  => strtolower($address['country']),
                'recipient_phone'    => $address['phone'],
                'volumes'            => $volumes < 1 ? 1 : $volumes,
                'weight'             => $weight <= 0.00 ? 1 : $weight,
                'obs'                => $order['customer_note'],
                'pack_dimensions'    => $packDimensions
            ];
        }

        return $mappedOrders;
    }

    public function updateOrderStatus($code, $statusCode, $fullfillmentCode = null)
    {
        @$this->execute('PUT', '/wp-json/wc/v3/orders/'. $code, [
            'status' => $statusCode
        ]);

        return true;
    }

    public function handleShipmentCreated($shipment)
    {
        return true;
    }

    /**
     * Map array of results
     *
     * @param array $data
     * @param string $mappingArray
     * @return array
     */
    private function mappingResult($data, $mappingArray)
    {
        $arr = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                $row = (array) $row;
            }

            $arr[] = mapArrayKeys($row, (array) trans('admin/ecommerce-gateway.mapping.woocommerce.' . $mappingArray));
        }

        return $arr;
    }

    /**
     * Execute request
     * 
     * @param string $method
     * @param string $url
     * @param array|string $data
     * @throws \Exception
     * @return array
     */
    protected function execute($method, $url, $data = [])
    {
        $url = $this->endpoint . $url;

        // OAuth 1.0
        $nonce = uniqid();
        $timestamp = time();

        $oauthSignatureMethod = 'HMAC-SHA1';
        $hashAlgorithm = strtolower(str_replace('HMAC-', '', $oauthSignatureMethod)); // sha1
        $secret = $this->secret . '&';

        $base_request_uri = rawurlencode($url);
        $params = array('oauth_consumer_key' => $this->key, 'oauth_nonce' => $nonce, 'oauth_signature_method' => 'HMAC-SHA1', 'oauth_timestamp' => $timestamp);
        $queryString = $this->joinParams($params);

        $stringToSign = $method . '&' . $base_request_uri . '&' . $queryString;
        $oauthSignature = base64_encode(hash_hmac($hashAlgorithm, $stringToSign, $secret, true));

        $headers = [
            'Content-Type: application/json',
            "Authorization: OAuth oauth_consumer_key=\"" . $this->key . "\",oauth_signature_method=\"" . $oauthSignatureMethod . "\",oauth_timestamp=\"" . $timestamp . "\",oauth_nonce=\"" . $nonce . "\",oauth_signature=\"" . $oauthSignature . "\"",
        ];
        //--

        $curl = curl_init();
        if ($method == 'GET') {
            $url .= '?' . http_build_query($data);
        } else {
            $data = json_encode($data);
        }

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 60
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            \Log::error($err);
            throw new \Exception($err);
        }

        try {
            $response = json_decode($response, true);
            return $response;
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    protected function joinParams($params)
    {
        $queryParams = array();

        foreach ($params as $key => $value) {
            $string = $key . '=' . $value;
            $queryParams[] = str_replace(array('+', '%7E'), array(' ', '~'), rawurlencode($string));
        }

        return implode('%26', $queryParams);
    }

}