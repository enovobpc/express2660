<?php

namespace App\Models\InvoiceGateway\Primavera;

use Date, Response;

class Purchase extends \App\Models\InvoiceGateway\Primavera\Base {

    /**
     * Sage X3 webservice Method
     * @var string
     */
    public $method = 'YWSPIH';


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsInvoices($params = '')
    {
        $data = [
            'ACCDAT' => '20200218'
        ];

        $data = null;

        return $this->get($this->method, $data);
    }

    /**
     * Obtem uma fatura de compra pelo seu ID de documento
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getInvoice($docId)
    {
        $data = [
            'NUM' => $docId
        ];

        return $this->get($this->method, $data);
    }
}