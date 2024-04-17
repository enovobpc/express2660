<?php
namespace App\Models\InvoiceGateway;

use App\Models\CustomerBalance;
use Setting;

class Base {

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null) {}

    /**
     * Return a namespace to a given Class
     *
     * @return string
     * @throws Exception
     */
    public static function getNamespaceTo($class = null){

        $method = ucwords(Setting::get('invoice_software'));
        $method = $method ? $method : 'KeyInvoice';
        
        if(empty($method)) {
            throw new \Exception('Não está configurada nenhuma ligação ao software de faturação.');
        }

        if($class) {
            return 'App\Models\InvoiceGateway\\' . $method . '\\'.$class;
        }

        return 'App\Models\InvoiceGateway\\' . $method . '\Base';
    }

    /**
     * Import documents from gateway
     *
     * @param [type] $class
     * @return void
     */
    public static function importDocuments($customerId){
        return CustomerBalance::syncBalanceAll($customerId);
    }
}