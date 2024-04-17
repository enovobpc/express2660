<?php

namespace App\Models\InvoiceGateway\KeyInvoice;

use App\Models\PaymentCondition;
use Date, Response;

class Customer extends \App\Models\InvoiceGateway\KeyInvoice\Base {


    /**
     * Verifica se um cliente (NIF) já existe na base de dados.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return string customer code
     * @throws \Exception
     */
    public function customerExists($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => utf8_encode(trim($nif))
        ];

        try {
            $customerCode = $this->execute('clientExists', $data);
            return $customerCode;
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
    public function countCustomers()
    {
        $data = [
            'sid' => $this->session_id,
        ];

        return $this->execute('countClients', $data);
    }


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsCustomers($offset = 0)
    {
        $data = [
            'sid'    => $this->session_id,
            'offset' => $offset
        ];

        return $this->execute('listClients', $data);
    }

    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function searchCustomers($search)
    {
        $data = [
            'sid'  => $this->session_id,
            'term' => $search
        ];

        return $this->execute('searchClients', $data);
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getCustomer($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('getClient', $data, true);
    }

    /**
     * Obtém informação adicional sobre o cliente criado no sistema. Contacte o nosso suporte se pretender
     * obter informação que não está disponível via API.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getCustomerDetails($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('getClientInfo', $data, true);
    }

    /**
     * Obtém o resumo de todos os documentos para um determinado cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getCustomerHistory($nif)
    {
        if($nif == '999999999' || $nif == '999999990') {
            $nif = '';
        }

        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        $history = $this->execute('getClientHistory', $data);
        return $history;
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateCustomer($nif, $code, $name, $address = null, $postalCode = null, $locality = null, $phone = null, $fax = null, $email = null, $obs = null, $country = 'pt', $paymentCondition=null, $customerCollection = null)
    {
        $country = strtoupper($country);

        if($country == 'PT' || $country == 'pt') {
            $data = [
                'sid'        => $this->session_id,
                'nif'        => $nif,
                'name'       => utf8_encode(str_replace("'", "’", $name)),
                'address'    => utf8_encode(str_replace("'", "’", $address)),
                'postalCode' => utf8_encode(str_replace("'", "’", $postalCode)),
                'locality'   => utf8_encode(str_replace("'", "’", $locality)),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
                'code'       => $code
            ];
        } else {
            $data = [
                'sid'        => $this->session_id,
                'nif'        => $nif,
                'country'    => $country,
                'name'       => utf8_encode(str_replace("'", "’", $name)),
                'address'    => utf8_encode(str_replace("'", "’", $address)),
                'postalCode' => utf8_encode(str_replace("'", "’", $postalCode)),
                'locality'   => utf8_encode(str_replace("'", "’", $locality)),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
                'client_id'  => $code
            ];
        }

        if(empty($nif) || $nif == '999999999' || $nif == '999999990') {
            throw new \Exception('NIF inválido. Não é possível criar ou atualizar no programa de faturação.');
        }

        $customerCode = $this->customerExists($nif);

        if($customerCode) { //cliente existe

            if($country != 'PT') {
                unset($data['country']); //remove variavel country antes de atualizar
            }

            $response = $this->execute('updateClient', $data);
        } else {
            if($country == 'PT') {
                return $this->execute('insertClientById', $data);
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

                $response = $this->execute('insertForeignClientById', $data);
            }
        }


        if($response) {

            if(!empty($paymentCondition)) {
                $update = false;
                if (in_array($paymentCondition, ['prt', 'dbt'])) {
                    $days = PaymentCondition::getDays($paymentCondition);
                    $update = true;
                } elseif (str_contains($paymentCondition, 'd')) {
                    $days = str_replace('d', '', $paymentCondition);
                    $update = true;
                }

                if ($update) {
                    $this->setCustomerField($nif, 'paymentterms', $days);
                }
            }

            if(!empty($customerCollection) && @$customerCollection->mobile) {
                $this->setCustomerField($nif, 'mobile', @$customerCollection->mobile);
            }
        }

        return $response;
    }

    /**
     * Insere ou atualiza um campo da ficha de cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function setCustomerField($nif, $field, $value)
    {
        $data = [
            'sid'       => $this->session_id,
            'nif'       => $nif,
            'fieldname' => $field,
            'value'     => $value
        ];

        return $this->execute('setClientField', $data);
    }

    /**
     * Remove um cliente, usando o nif como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function destroyCustomer($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('deleteClient', $data);
    }



    /**
     * Lista as delegações de um cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function listEntities($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('listEntities', $data);
    }

    /**
     * Insere ou atualiza uma delegação de um cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function createOrUpdateEntity($nif, $internalRef, $name, $address=null, $postalCode=null, $locality=null, $phone=null, $fax=null, $email=null, $obs=null)
    {

        //atualiza
        if($internalRef) {
            $data = [
                'sid'        => $this->session_id,
                'parentNif'  => $nif,
                'nif'        => $nif,
                'name'       => utf8_encode(str_replace("'", "’", $name)),
                'address'    => utf8_encode(str_replace("'", "’", $address)),
                'postalCode' => utf8_encode(str_replace("'", "’", $postalCode)),
                'locality'   => utf8_encode(str_replace("'", "’", $locality)),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
            ];

            return $this->execute('insertEntity', $data);

        } else {
            $data = [
                'sid'         => $this->session_id,
                'internalRef' => $internalRef,
                'nif'         => $nif,
                'name'       => utf8_encode(str_replace("'", "’", $name)),
                'address'    => utf8_encode(str_replace("'", "’", $address)),
                'postalCode' => utf8_encode(str_replace("'", "’", $postalCode)),
                'locality'   => utf8_encode(str_replace("'", "’", $locality)),
                'phone'      => $phone,
                'fax'        => $fax,
                'email'      => $email,
                'obs'        => $obs,
            ];

            return $this->execute('updateEntity', $data);
        }



    }

    /**
     * Remove uma delegação de um cliente
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function destroyEntity($internalRef)
    {
        $data = [
            'sid' => $this->session_id,
            'internalRef' => $internalRef
        ];

        return $this->execute('deleteEntity', $data);
    }

}