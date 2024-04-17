<?php

namespace App\Models\InvoiceGateway\SageX3;

use Date, Response;

class Provider extends \App\Models\InvoiceGateway\SageX3\Base {

    /**
     * Sage X3 webservice Method
     * @var string
     */
    public $method = 'YWSBPS';

    /**
     * Verifica se um cliente (NIF) já existe na base de dados.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return string provider code
     * @throws \Exception
     */
    public function providerExists($nif)
    {
        return false;
    }

    /**
     * Indica o número total de clientes gravados na base de dados
     *
     * @return int numero de registos
     * @throws \Exception
     */
    public function countProviders()
    {
        return false;
    }


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsProviders()
    {

        $params = [
            'COD' => 'PT'
        ];

        $results = $this->get($this->method, $params);
        return $results;
    }

    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function searchProviders($search)
    {
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getProvider($code)
    {
        $data = [
            'BPCNUM' => $code,
            //'CRN'    => $vat
        ];

        $results = $this->get($this->method, $data);

        return $results;
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getProviderAddress($nif)
    {
        $data = [
            'BPANUM' => $nif,
            //'CRN'    => $vat
        ];

        $results = $this->get('YWSBPA', $data);

        return $results;
    }

    /**
     * Obtém informação adicional sobre o cliente criado no sistema. Contacte o nosso suporte se pretender
     * obter informação que não está disponível via API.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getProviderDetails($nif)
    {
        return false;
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
        return false;
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateProvider($nif, $code, $name, $address = null, $postalCode = null, $locality = null, $phone = null, $fax = null, $email = null, $obs = null, $country = 'pt', $providerCollection = null)
    {

        $xml = '<PARAM>
            <GRP ID="BPC0_1">
                <FLD NAME="BCGCOD" TYPE="Char">PT</FLD>
                <FLD NAME="BPCNUM" TYPE="Char">'. $code. '</FLD>
            </GRP>
            <GRP ID="BPRC_1">
                <LST NAME="BPRNAM" SIZE="2" TYPE="Char">
                    <ITM>'. strtoupper($name) .'</ITM>
                    <ITM></ITM>
                </LST>
                <FLD NAME="CRY" TYPE="Char">'.strtoupper($country).'</FLD>
                <FLD NAME="LAN" TYPE="Char">POR</FLD>
                <FLD NAME="CUR" TYPE="Char">EUR</FLD>
                <FLD NAME="CRN" TYPE="Char">'.$nif.'</FLD>
                <FLD NAME="EECNUM" TYPE="Char">'. strtoupper($country) . $nif .'</FLD>
            </GRP>
            <GRP ID="BPC3_2">
                <FLD NAME="VACBPR" TYPE="Char">DMG</FLD>
            </GRP>
            <GRP ID="BPC3_3">
                <FLD NAME="PTE" TYPE="Char"></FLD>
            </GRP>
            <TAB DIM="30" ID="BPAC_1" SIZE="1">
                <LIN NUM="1">
                    <FLD NAME="CODADR" TYPE="Char">001</FLD>
                    <FLD NAME="BPACRY" TYPE="Char">PT</FLD>
                    <FLD NAME="ADDLIG1" TYPE="Char">'. $address .'</FLD>
                    <FLD NAME="ADDLIG2" TYPE="Char"/>
                    <FLD NAME="ADDLIG3" TYPE="Char"/>
                    <FLD NAME="POSCOD" TYPE="Char">'. str_replace('-', '', $postalCode) .'</FLD>
                    <FLD NAME="CTY" TYPE="Char">'. strtoupper($country) .'</FLD>
                </LIN>
            </TAB>
        </PARAM>';

        $xml = '<![CDATA[' . $xml . ']]>';

        return $this->save('YWSBPC', $xml);
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
        return false;
    }

}