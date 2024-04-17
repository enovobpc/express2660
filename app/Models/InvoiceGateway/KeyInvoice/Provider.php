<?php

namespace App\Models\InvoiceGateway\KeyInvoice;

use Date, Response;

class Provider extends \App\Models\InvoiceGateway\KeyInvoice\Base {


    /**
     * Verifica se um cliente (NIF) já existe na base de dados.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return string provider code
     * @throws \Exception
     */
    public function providerExists($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        try {
            $providerCode = $this->execute('sellerExists', $data);
            return $providerCode;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Indica o número total de clientes gravados na base de dados
     *
     * @return int numero de registos
     * @throws \Exception
     */
    public function countProviders()
    {
        $data = [
            'sid' => $this->session_id,
        ];

        return $this->execute('countSellers', $data);
    }


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsProviders($offset = 0)
    {
        $data = [
            'sid'    => $this->session_id,
            'offset' => $offset
        ];

        return $this->execute('listSellers', $data);
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getProvider($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('getSeller', $data, true);
    }

    /**
     * Obtém o resumo de todos os documentos para um determinado cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getProviderHistory($nif)
    {
/*        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('getClientHistory', $data);*/
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateProvider($nif, $code, $name, $address = null, $postalCode = null, $locality = null, $phone = null, $fax = null, $email = null, $obs = null, $country = 'pt')
    {
        $country = strtoupper($country);

        if($country == 'PT' || $country == 'pt') {
            $data = [
                'sid'        => $this->session_id,
                'nif'        => $nif,
                'name'       => utf8_encode($name),
                'address'    => utf8_encode($address),
                'postalCode' => utf8_encode($postalCode),
                'locality'   => utf8_encode($locality),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
                'seller_id'  => $code
            ];
        } else {
            $data = [
                'sid'        => $this->session_id,
                'nif'        => $nif,
                'country'    => $country,
                'name'       => utf8_encode($name),
                'address'    => utf8_encode($address),
                'postalCode' => utf8_encode($postalCode),
                'locality'   => utf8_encode($locality),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
                'seller_id'  => $code
            ];
        }


        if(empty($nif) || $nif == '999999999' || $nif == '999999990') {
            throw new \Exception('NIF inválido. Não é possível criar ou atualizar no programa de faturação.');
        }

        $providerCode = $this->providerExists($nif);

        if($providerCode) { //cliente existe

            if($country != 'PT') {
                unset($data['country']); //remove variavel country antes de atualizar
            }

            return $this->execute('updateSeller', $data);
        } else {
            if($country == 'PT') {
                return $this->execute('insertSellerById', $data);
            } else {
                $countryData = [
                    'sid'  => $this->session_id,
                    'code' => $country,
                ];

                try {
                    $this->execute('countryExists', $countryData);
                } catch (\Exception $e) {
                    $countryData['name'] = utf8_encode(trans('country.'.strtolower($country)));
                    $this->execute('insertCountry', $countryData);
                }

                return $this->execute('insertSellerById', $data);
            }
        }
    }

    /**
     * Remove um cliente, usando o nif como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function destroyProvider($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('deleteSeller', $data);
    }

}