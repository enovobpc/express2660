<?php

namespace App\Models\InvoiceGateway\Moloni;

use Date, Response;

class Product extends \App\Models\InvoiceGateway\Moloni\Base {


    /**
     * Verifica se um produto já existe na base de dados.
     *
     * @param $ref referência do produto
     * @return string customer code
     * @throws \Exception
     */
    public function productExists($ref)
    {
        $data = [
            'sid' => $this->session_id,
            'ref' => $ref
        ];

        try {
            $productRef = $this->execute('productExists', $data);
            return $productRef;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Indica o número total de produtos gravados na base de dados
     *
     * @return int numero de registos
     * @throws \Exception
     */
    public function countProducts()
    {
        $data = [
            'sid' => $this->session_id,
        ];

        return $this->execute('countProducts', $data);
    }


    /**
     * Obtém uma lista de (25) produtos, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsProducts($offset = 0)
    {
        $data = [
            'sid'    => $this->session_id,
            'offset' => $offset
        ];

        $data = $this->execute('listProducts', $data);

        return $this->mappingResult($data, 'products');
    }

    /**
     * Obtém uma lista de (25) products, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function searchProducts($search)
    {
        $data = [
            'sid'  => $this->session_id,
            'term' => $search
        ];

        return $this->execute('searchProducts', $data);
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
            'sid' => $this->session_id,
            'ref' => $ref
        ];

        return $this->execute('getProduct', $data, true);
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

        $data = [
            'sid'        => $this->session_id,
            'ref'        => $ref,
            'designation'=> utf8_encode($designation),
            'shortName'  => utf8_encode($shortName ? $shortName : $designation) ,
            'tax'        => $taxId,
            'obs'        => $obs,
            'isService'  => $isService,
            'hasStocks'  => $hasStocks,
            'active'     => $active,
            'shortDesc'  => utf8_encode($shortDesc),
            'longDesc'   => utf8_encode($longDesc),
            'price'      => $price,
            'vendorRef'  => $vendorRef
        ];

        
        $productRef = $this->productExists($ref);
        if($productRef) {
            return $this->execute('updateProduct', $data);
        } else {
            return $this->execute('insertProduct', $data);
        }
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
        $data = [
            'sid'   => $this->session_id,
            'ref'   => $ref,
            'taxid' => $taxId
        ];

        return $this->execute('changeProductTax', $data);
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
        $data = [
            'sid' => $this->session_id,
            'ref' => $ref
        ];

        return $this->execute('deleteProduct', $data);
    }

    /**
     * Map array of results
     *
     * @param type $data Array of data
     * @param type $mappingArray
     * @return type
     */
    private function mappingResult($data, $mappingArray) {
        $arr = [];

        foreach($data as $row) {

            $row = (array) $row;

            $target = config('webservices_mapping.keyinvoice.' . $mappingArray);

            $mapArray = [];
            foreach ($target as $sourceKey => $targetKey) {
                $value = isset($row[$sourceKey]) ? $row[$sourceKey] : '';
                $mapArray[$targetKey] = utf8_decode(trim($value));
            }

            $arr[] = $mapArray;
        }

        return $arr;
    }

}