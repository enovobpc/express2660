<?php

namespace App\Models\InvoiceGateway\SageX3;

use Date, Response;

class Product extends \App\Models\InvoiceGateway\SageX3\Base {

    /**
     * Sage X3 webservice Method
     * @var string
     */
    public $method = 'YWSITM';

    /**
     * Verifica se um produto já existe na base de dados.
     *
     * @param $ref referência do produto
     * @return string customer code
     * @throws \Exception
     */
    public function productExists($ref)
    {
        return false;
    }

    /**
     * Indica o número total de produtos gravados na base de dados
     *
     * @return int numero de registos
     * @throws \Exception
     */
    public function countProducts()
    {
        return false;
    }


    /**
     * Obtém uma lista de (25) produtos, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsProducts($offset = 0)
    {
        return $this->get($this->method);
    }

    /**
     * Obtém uma lista de (25) products, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function searchProducts($search)
    {
       return false;
    }

    /**
     * Devolve os dados de um artigo, usando ref como chave de pesquisa.
     *
     * @param $ref identificador do artigo
     * @return mixed
     * @throws \Exception
     */
    public function getProduct($ref)
    {
        $data = [
            'ITMREF' => $ref
        ];

        return $this->get($this->method, $data);
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateProduct($ref, $designation, $taxId, $price, $isService = 1, $hasStocks = 0, $active = null, $shortName = null, $obs = null, $shortDesc = null, $longDesc = null, $vendorRef = null)
    {
        return false;
    }

    /**
     * Indica outra taxa de IVA por omissão, por ID, para o artigo indicado.
     * Pode ser útil para o caso em que indica uma taxa de IVA 0 mas é atribuída uma taxa 0 com o Motivo de Isenção errado.
     *
     * @param $ref identificador do artigo
     * @param $taxId identificador da taxa de IVA (ver o método getTaxes na secção TAELAS)
     * @return mixed
     * @throws \Exception
     */
    public function changeProductTax($ref, $taxId)
    {
        return true;
    }

    /**
     * Remove um produto, usando o ref como chave de pesquisa.
     *
     * @param $ref identificador do artigo
     * @return mixed
     * @throws \Exception
     */
    public function destroyProduct($ref)
    {
        return false;
    }
}