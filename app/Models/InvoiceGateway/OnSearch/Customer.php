<?php

namespace App\Models\InvoiceGateway\OnSearch;

use Date, Response;

class Customer extends \App\Models\InvoiceGateway\OnSearch\Base {


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsCustomers()
    {
        return $this->execute('/ons3api/BusinessPartners/GetAllBusinessPartners');
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getCustomer($customerCode)
    {

        $action = '/ons3api/BusinessPartners/GetBusinessPartner';

        $data = [
            'pPartnerID'     => $customerCode,
            'pPartnerTypeID' => 'C'
        ];

        return $this->execute($action, $data);
    }


    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateCustomer($customerCollection)
    {

        $data = [
            "PartnerType" => [
                "PartnerTypeID"   => "C",
                "PartnerTypeDesc" => "Cliente"
            ],
            "PartnerID"     => $customerCollection->code,
            "PartnerName"   => $customerCollection->name,
            "VATNo"         => $customerCollection->vat,
            "Address"       => $customerCollection->address,
            "City"          => $customerCollection->city,
            "PostalCode"    => $customerCollection->zip_code,
            "Country"       => $customerCollection->country,
            "ContactPerson" => $customerCollection->responsable,
            "Phone"         => $customerCollection->phone,
            "MobilePhone"   => $customerCollection->mobile,
            "Email"         => $customerCollection->email,
            "Language"      => "pt",

           /* "PaymentType" => [
                "PaymentTypeID" => "string",
                "PaymentDesc" => "string",
                "NDays" => 0,
                "Discount" => 0
            ],

            "Market" => "string",
            "SalesCondType" => [
                "SalesCondTypeID" => "string",
                "SalesCondTypeDescr" => "string"
            ],
            "AgentCommission" => 0,
            "CreditLimit" => 0,
            "CurrentCreditValue" => 0,
            "BaseCurrency" => "string",
            "TransportTypeID" => "string",
            "IDIntegration" => "string",*/
        ];

        $data = json_encode($data);

        $exists = $this->getCustomer($customerCollection->code);

        if($exists) {
            return $this->execute('/ons3api/BusinessPartners/Update', $data, 'POST');
        }
        return $this->execute('/ons3api/BusinessPartners/Create', $data, 'POST');
    }
}
