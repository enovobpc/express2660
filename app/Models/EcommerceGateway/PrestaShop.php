<?php

namespace App\Models\EcommerceGateway;
use App\Models\EcommerceGateway\Base;

class PrestaShop extends Base
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
        $carriers = @$this->execute('GET', '/carriers', ['display' => 'full'])['carriers'] ?? [];
        $carriers = $this->mappingResult($carriers, 'carriers');
        return $carriers;
    }

    public function listOrdersStatus() {
        $orderStatus = @$this->execute('GET', '/order_states')['order_states'] ?? [];
        foreach ($orderStatus as &$status) {
            $status = $this->getStatus($status['id']);
        }

        $orderStatus = $this->mappingResult($orderStatus, 'status');
        return $orderStatus;
    }

    public function listOrders() {
        $filters = [ 'display' => 'full' ];
        if (!empty($this->gateway_config->settings['status']['list'])) {
            $status = implode('|', $this->gateway_config->settings['status']['list']);
            $filters['filter[current_state]'] = '['. $status .']';
        }

        if (!empty($this->gateway_config->settings['carrier'])) {
            $filters['filter[id_carrier]'] = '['. $this->gateway_config->settings['carrier'] .']';
        }

        $orders = @$this->execute('GET', '/orders', $filters)['orders'] ?? [];
        foreach ($orders as $key => &$order) {
            $mappedOrder = [];

            // dd($order);

            $deliveryAddress = $this->getDeliveryAddress($order['id_address_delivery']);
            if (!$deliveryAddress) {
                unset($orders[$key]);
                continue;
            }

            $deliveryAddressCountry = $this->getCountry($deliveryAddress['id_country']);
            if (!$deliveryAddressCountry) {
                unset($orders[$key]);
                continue;
            }

            $volumes = 0;
            $weight  = 0;
            $packDimensions = [];
            if (@$order['associations']['order_rows']) {
                foreach ($order['associations']['order_rows'] as $row) {
                    $product = $this->getProduct($row['id']);
                    if (!$product) {
                        continue;
                    }

                    $volumes += $row['product_quantity'];
                    $weight  += $row['product_quantity'] * $product['weight'];

                    $packDimensions[] = [
                        'type'        => 'box',
                        'description' => $row['product_name'],
                        'qty'         => $row['product_quantity'],
                        'width'       => round($product['width'], 2),
                        'height'      => round($product['height'], 2),
                        'length'      => round($product['depth'], 2),
                        'weight'      => round($product['weight'], 2),
                    ];
                }
            }

            $volumes = ($volumes < 1) ? 1 : $volumes;
            $weight  = ($weight <= 0.0) ? 1 : $weight;

            $mappedOrder = [
                'code'               => $order['id'],
                'reference'          => $order['reference'],
                'recipient_name'     => $deliveryAddress['firstname'] . ' ' . $deliveryAddress['lastname'],
                'recipient_address'  => $deliveryAddress['address1'] . ' ' . $deliveryAddress['address2'],
                'recipient_zip_code' => $deliveryAddress['postcode'],
                'recipient_city'     => $deliveryAddress['city'],
                'recipient_country'  => strtolower($deliveryAddressCountry['iso_code']),
                'recipient_phone'    => $deliveryAddress['phone'],
                'volumes'            => $volumes,
                'weight'             => $weight,
                'obs'                => $order['note'],
                'pack_dimensions'    => $packDimensions
            ];

            $order = $mappedOrder;
            // dd($mappedOrder);
        }

        return $orders;
    }

    public function updateOrderStatus($code, $statusCode, $fullfillmentCode = null) {
        $xml = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <order_history>
                <id_order_state><![CDATA['. $statusCode .']]></id_order_state>
                <id_order><![CDATA['. $code .']]></id_order>
            </order_history>
        </prestashop>';

        $this->execute('POST', '/order_histories', $xml);
        return true;
    }

    public function handleShipmentCreated($shipment) {
        return true;
    }
    
    /**
     * Helper functions
     */

    protected function getStatus($id) {
        return @$this->execute('GET', '/order_states/' . $id)['order_state'];
    }

    protected function getOrder($id) {
        return @$this->execute('GET', '/orders/' . $id)['order'];
    }

    protected function getDeliveryAddress($id) {
        return @$this->execute('GET', '/addresses/' . $id)['address'];
    }

    protected function getCountry($id) {
        return @$this->execute('GET', '/countries/' . $id)['country'];
    }

    protected function getProduct($id) {
        return @$this->execute('GET', '/products/' . $id)['product'];
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

            $arr[] = mapArrayKeys($row, (array) trans('admin/ecommerce-gateway.mapping.prestashop.' . $mappingArray));
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
            'Authorization: Basic ' . base64_encode($this->key . ':'),
            'Accept: application/xml',
            'Content-type: application/xml'
        ];

        $curl = curl_init();
        if ($method == 'GET') {
            $url .= '?' . http_build_query($data);
        }

        $url .= str_contains($url, '?') ? '&output_format=JSON' : '?output_format=JSON';

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
            return json_decode($response, true);
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception('Falha ao obter informação do pedido.');
        }
    }

}