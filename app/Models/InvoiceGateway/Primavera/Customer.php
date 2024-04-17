<?php

namespace App\Models\InvoiceGateway\Primavera;

use App\Models\Customer as ModelsCustomer;
use Date, Response;

class Customer extends \App\Models\InvoiceGateway\Primavera\Base {

    //https://v10api.primaverabss.com/html/api/base/IBasBS100.IBasBSClientes.html#IBasBS100_IBasBSClientes_LstClientesCriterios_System_String_System_String_System_String_System_String_

    /**
     * Verifica se um cliente (NIF) já existe na base de dados.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return string customer code
     * @throws \Exception
     */
    public function customerExists($vat)
    {
        //$url = 'Base/Clientes/Existe/'.$nif;
        $url = 'Base/Clientes/ExisteContribuinte/'.$vat;

        $response = $this->execute($url, null, 'GET');

        return $response;

    }

    /**
     * Indica o número total de clientes gravados na base de dados
     *
     * @return int numero de registos
     * @throws \Exception
     */
    public function countCustomers()
    {
        $response = $this->listsCustomers();
        return count($response);
    }


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsCustomers()
    {
        $response = $this->execute('Base/Clientes/LstClientes', null, 'GET');

        return $response;
    }

    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function searchCustomers($searchCode)
    {
        $url = 'Base/Clientes/LstClientes/'.$searchCode;

        $response = $this->execute($url, null, 'GET');

        return $response;
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
    public function getCustomerDetails($codigoCliente)
    {
        /* $customer = ModelsCustomer::where('vat', $codigoCliente)->first();
        $codigoCliente = @$customer->code;
        */
        $url = 'Base/Clientes/Edita/'.$codigoCliente;

        return $this->execute($url, null, 'GET');
    }

    /**
     * Obtém o resumo de todos os documentos para um determinado cliente
     *
     * @param $nif número de Ident ificação Fiscal do cliente
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

        if(empty($nif) || $nif == '999999999' || $nif == '999999990') {
            throw new \Exception('NIF inválido. Não é possível criar ou atualizar no programa de faturação.');
        }

        $customerExists = $this->customerExists($nif);

        //ver na documentação do postman o exemplo de update que tem vários campos possiveis preencher
        $data = [
                'EmModoEdicao'           => $customerExists ? true : false,
                'Cliente'                => $code,
                'NumContribuinte'        => $nif,
                'Nome'                   => utf8_encode(str_replace("'", "’", $name)),
                'Morada'                 => utf8_encode(str_replace("'", "’", $address)),
                'CodigoPostal'           => utf8_encode(str_replace("'", "’", $postalCode)),
                'Localidade'             => utf8_encode(str_replace("'", "’", $locality)),
                "Pais"                   => strtoupper($country),
                'Telefone'               => $phone,
                'Fax'                    => $fax,
                'email'                  => $email,
                "EnderecoWeb"            => "",
                "Distrito"               => "",
                "Observacoes"            => $obs,
                'Descricao'              => $name,
                "Moeda"                  => "EUR",
                "ModoPag"                => "",
                "CondPag"                => $paymentCondition,
                "Limitecredito"          => "",
                "Inactivo"               => false,
            ];
        

  /* 
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
        } */


       /*  if($response) {

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

        return $response; */
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
        $codCliente = $nif;

        return $this->execute('Remove/'.$codCliente, null, 'GET');
    }
}