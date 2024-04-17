<?php

namespace App\Models\EcommerceGateway;

class Base
{
    public $gateway        = null;
    public $gateway_config = null;

    /**
     * Constructor
     * @param $gateway
     */
    public function __construct($customerEcommerceGateway)
    {
        $this->gateway_config = $customerEcommerceGateway;

        try {
            $this->gateway = '\App\Models\EcommerceGateway\\' . ucwords(camel_case($customerEcommerceGateway->method));
            $this->gateway = new $this->gateway(
                $customerEcommerceGateway->endpoint,
                $customerEcommerceGateway->user,
                $customerEcommerceGateway->password,
                $customerEcommerceGateway->key,
                $customerEcommerceGateway->secret
            );

            $this->gateway->gateway_config = $this->gateway_config;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine());
        }
    }

    /**
     * List gateway carriers
     * 
     * @throws \Exception
     * @return array
     */
    public function listCarriers() {
        try {
            /**
             * Obligatory return structure
             * --
             * array[]
             *  - code => string|int
             *  - name => string
             */

            return $this->gateway->listCarriers();
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * List gateway orders
     * 
     * @throws \Exception
     * @return array
     */
    public function listOrders() {
        try {
            /**
             * Obligatory return structure
             * --
             * array[]
             *  - code               => string|int
             *  - name               => string
             *  - recipient_name     => string
             *  - recipient_address  => string
             *  - recipient_zip_code => string
             *  - recipient_city     => string
             *  - recipient_country  => string
             *  - recipient_phone    => string
             *  - volumes            => int
             *  - weight             => float
             *  - obs                => string|null
             *  - pack_dimensions    => array[] -> (optional)
             *      - type        => string
             *      - description => string
             *      - qty         => int
             *      - width       => float
             *      - height      => float
             *      - length      => float
             *      - weight      => float
             */

            return $this->gateway->listOrders();
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * List gateway orders status
     * 
     * @throws \Exception
     * @return array
     */
    public function listOrdersStatus() {
        try {
            /**
             * Obligatory return structure
             * --
             * array[]
             *  - code => string|int
             *  - name => string
             */

            return $this->gateway->listOrdersStatus();
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update order status (sent, delivered, ...)
     * 
     * @param int|string $code
     * @param int|string $statusCode
     * @throws \Exception
     * @return bool
     */
    public function updateOrderStatus($code, $statusCode, $fullfillmentCode = null) {
        try {
            /**
             * Obligatory return structure
             * --
             * bool
             */

            return $this->gateway->updateOrderStatus($code, $statusCode, $fullfillmentCode);
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    public function handleShipmentCreated($shipment) {
        try {
            return $this->gateway->handleShipmentCreated($shipment);
        } catch (\Exception $e) {
            \Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

}
