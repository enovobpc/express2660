<?php

namespace App\Models\EcommerceGateway;
use App\Models\EcommerceGateway\Base;

class Shopify extends Base
{
    private $endpoint;
    private $key;

    /**
     * Constructor
     * @param $debug
     */
    public function __construct($endpoint = null, $user = null, $password = null, $key = null, $secret = null)
    {
        $this->endpoint = $endpoint;
        $this->key      = $key;
    }

    /**
     * Required functions
     */

    public function listCarriers() {
        return [];
    }

    public function listOrdersStatus() {
        return [
            [
                'code' => 'out_for_delivery',
                'name' => 'Out for Delivery',
            ],
            [
                'code' => 'delivered',
                'name' => 'Delivered',
            ],
        ];
    }

    public function listOrders() {
        $orders = @$this->execute('GET', '/admin/api/2023-07/orders.json?status=any')['orders'] ?? [];

        $mappedOrders = [];
        foreach ($orders as $order) {
            $address = $order['shipping_address'];
            if (empty($address)) {
                $address = $order['billing_address'];
            }
            
            if (empty($address)) {
                continue;
            }
            
            $name = trim($address['first_name'] . ' ' . $address['last_name']);
            if (empty($name)) {
                $name = @$address['company'];
            }

            $volumes = 0;
            $weight = round(($order['total_weight'] / 1000), 2);
            $packDimensions = [];
            foreach (($order['line_items'] ?? []) as $line) {
                $volumes += $line['quantity'];

                $packDimensions[] = [
                    'type'        => 'box',
                    'description' => $line['name'],
                    'qty'         => $line['quantity'],
                    'width'       => 0,
                    'height'      => 0,
                    'length'      => 0,
                    'weight'      => round(($line['grams'] / 1000), 2),
                ];
            }

            $mappedOrders[] = [
                'code'               => $order['id'],
                'reference'          => $order['order_number'],
                'recipient_name'     => $name,
                'recipient_address'  => $address['address1'] . ' ' . $address['address2'],
                'recipient_zip_code' => $address['zip'],
                'recipient_city'     => $address['city'],
                'recipient_country'  => strtolower($address['country_code']),
                'recipient_phone'    => $address['phone'],
                'volumes'            => $volumes,
                'weight'             => $weight <= 0.00 ? 1 : $weight,
                'obs'                => $order['note'],
                'pack_dimensions'    => $packDimensions
            ];
        }

        return $mappedOrders;
    }

    public function updateOrderStatus($code, $statusCode, $fullfillmentCode = null) {
        @$this->execute('POST', '/admin/api/2023-07/orders/'. $code .'/fulfillments/'. $fullfillmentCode .'/events.json', [
            'event' => [
                'status' => $statusCode,
            ]
        ]);

        return true;
    }

    public function handleShipmentCreated($shipment) {
        if (!$shipment->ecommerce_gateway_order_code) {
            return false;
        }

        $fullfillmentOrders = @$this->execute('GET', '/admin/api/2023-07/orders/'. $shipment->ecommerce_gateway_order_code .'/fulfillment_orders.json')['fulfillment_orders'] ?? [];

        $lines = [];
        foreach ($fullfillmentOrders as $fOrder) {
            if ($fOrder['status'] != 'open') {
                continue;
            }

            $lines[] = [
                'fulfillment_order_id' => $fOrder['id'],
            ];
        }

        $fullfillment = @$this->execute('POST', '/admin/api/2023-07/fulfillments.json', [
            'fulfillment' => [
                'line_items_by_fulfillment_order' => $lines,
                'tracking_info' => [
                    'company' => config('app.name'),
                    'number' => $shipment->tracking_code,
                    'url' => route('tracking.index', ['tracking' => $shipment->tracking_code])
                ]
            ]
        ]);

        if (@$fullfillment['fulfillment']['id']) {
            $shipment->ecommerce_gateway_fullfillment_code = @$fullfillment['fulfillment']['id'];
            $shipment->save();
        }

        // dd($fullfillment);
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

            $arr[] = mapArrayKeys($row, (array) trans('admin/ecommerce-gateway.mapping.shopify.' . $mappingArray));
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
    protected function execute($method, $url, $data = []) {
        $url = $this->endpoint . $url;

        $headers = [
            'X-Shopify-Access-Token: ' . $this->key,
            'Accept: application/json',
            'Content-type: application/json'
        ];

        $curl = curl_init();
        if ($method == 'GET') {
            $url .= '?' . http_build_query($data);
        } else {
            $data = json_encode($data);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_TIMEOUT         => 60
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            \Log::error($err);
            throw new \Exception($err);
        }

        try {
            $response = json_decode($response, true);
            if (@$response['errors']) {
                if (@$response['errors'][0]) {
                    throw new \Exception($response['errors'][0]);
                }

                throw new \Exception($response['errors']);
            }

            return $response;
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

}