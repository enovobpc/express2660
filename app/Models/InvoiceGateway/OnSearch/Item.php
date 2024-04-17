<?php

namespace App\Models\InvoiceGateway\OnSearch;

use Date, Response;

class Item extends \App\Models\InvoiceGateway\OnSearch\Base {


    /**
     * Obtém a lista completa de todos os artigos
     *
     * @return mixed
     * @throws \Exception
     */
    public function listsItems($page = 0)
    {
        $action = '/ons3api/Items/GetAllItems';

        $url = $this->url . $action . '?pPage='.$page.'&pGetItemImages=true';

        return $this->execute($action, null, 'GET', $url);
    }

    /**
     * Devolve os dados de um artigo dada a sua referencia
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getItem($reference)
    {

        $action = '/ons3api/Items/GetItem';

        $data = [
            'pItemID'        => $reference,
            'pGetItemImages' => true
        ];

        $url = $this->url . $action. '?pItemID='.$reference.'&pGetItemImages=true';

        return $this->execute($action, $data, 'GET', $url);
    }
}
