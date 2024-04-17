<?php

namespace App\Models\InvoiceGateway\Moloni;

use Date, Response;

class Customer extends \App\Models\InvoiceGateway\Moloni\Base {


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
            'nif' => $nif
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
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('getClientHistory', $data);
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateCustomer($nif, $code, $name, $address = null, $postalCode = null, $locality = null, $phone = null, $fax = null, $email = null, $obs = null, $country = 'pt')
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
                'code'       => $code
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
                //'client_id'  => $code
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

            return $this->execute('updateClient', $data);
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

                return $this->execute('insertForeignClientById', $data);
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
    public function destroyCustomer($nif)
    {
        $data = [
            'sid' => $this->session_id,
            'nif' => $nif
        ];

        return $this->execute('deleteClient', $data);
    }

}


/*array:183 [
    0 => "UNKNOWN documentation(string $command, string $param)"
  1 => "string responseMessage(string $responseCode, string $lang)"
  2 => "UNKNOWN authenticate(string $apikey)"
  3 => "CompanyResponse company(string $sid)"
  4 => "UNKNOWN verifyUserInsertionPricesWithVAT(string $sid)"
  5 => "UNKNOWN verifyPriceLevelVAT(string $sid)"
  6 => "UNKNOWN getSAFTfile(string $sid, string $year, string $month)"
  7 => "BrandResponse getBrands(string $sid)"
  8 => "TableResponse getColorsSizes(string $sid)"
  9 => "UNKNOWN insertColorSize(string $sid, string $type, string $name)"
  10 => "TableResponse getTaxes(string $sid)"
  11 => "TableResponse getPriceLevels(string $sid)"
  12 => "DiscountResponse getDiscounts(string $sid, UNKNOWN $refs, string $nif)"
  13 => "TableResponse listDocumentSeries(string $sid, UNKNOWN $docType)"
  14 => "TableResponse listAllDocumentSeries(string $sid, UNKNOWN $docType)"
  15 => "TableResponse listStores(string $sid)"
  16 => "TableResponse listWarehouse(string $sid)"
  17 => "WarehouseStockResponse listWarehouseStock(string $sid, string $warehouse)"
  18 => "UNKNOWN familySearch(string $sid, string $idContact)"
  19 => "FamilyResponse getFamilies(string $sid)"
  20 => "UNKNOWN countFamilies(string $sid)"
  21 => "FamilyResponse listFamilies(string $sid, string $offset)"
  22 => "UNKNOWN familyExists(string $sid, string $idfamily)"
  23 => "FamilyResponse getFamily(string $sid, string $idfamily)"
  24 => "UNKNOWN insertFamily(string $sid, string $name, string $ref, string $idparentfamily)"
  25 => "UNKNOWN updateFamily(string $sid, string $idfamily, string $name, string $ref, string $idparentfamily)"
  26 => "UNKNOWN deleteFamily(string $sid, string $idfamily)"
  27 => "UNKNOWN countryExists(string $sid, string $code)"
  28 => "UNKNOWN insertCountry(string $sid, string $code, string $name)"
  29 => "TableResponse getCountries(string $sid)"
  30 => "UNKNOWN countCurrencies(string $sid)"
  31 => "CurrencyResponse listCurrencies(string $sid, string $offset)"
  32 => "UNKNOWN currencyExists(string $sid, string $currency_id)"
  33 => "CurrencyResponse getCurrency(string $sid, string $currency_id)"
  34 => "UNKNOWN insertCurrency(string $sid, string $currency, string $name, string $integername, string $decimalname, string $symbol)"
  35 => "UNKNOWN updateCurrency(string $sid, string $currency_id, string $currency, string $name, string $integername, string $decimalname, string $symbol)"
  36 => "UNKNOWN deleteCurrency(string $sid, string $currency_id)"
  37 => "TableResponse listCurrencyConversions(string $sid, string $currency_id, string $offset)"
  38 => "UNKNOWN insertCurrencyConversion(string $sid, string $currency_id, string $conversiondate, string $conversionvalue)"
  39 => "UNKNOWN countClients(string $sid)"
  40 => "PersonResponse listClients(string $sid, string $offset)"
  41 => "UNKNOWN clientExists(string $sid, string $nif)"
  42 => "PersonResponse getClient(string $sid, string $nif)"
  43 => "UNKNOWN getClientInfo(string $sid, string $nif, string $fieldname)"
  44 => "UNKNOWN insertClient(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  45 => "UNKNOWN insertForeignClient(string $sid, string $nif, string $country, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  46 => "UNKNOWN updateClient(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  47 => "UNKNOWN setClientField(string $sid, string $nif, string $fieldname, string $value)"
  48 => "UNKNOWN deleteClient(string $sid, string $nif)"
  49 => "PersonResponse searchClients(string $sid, string $searchTerm)"
  50 => "PersonResponse_custom searchClients_custom(string $sid, string $CodigoCliente, string $Nome, string $Nif, string $Contacto)"
  51 => "ClientHistoryResponse getClientHistory(string $sid, string $nif)"
  52 => "UNKNOWN sendClientHistory2Email(string $sid, string $nif, string $email)"
  53 => "UNKNOWN getClientDetails(string $sid, string $nif)"
  54 => "UNKNOWN insertClientById(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs, string $client_id)"
  55 => "UNKNOWN insertForeignClientById(string $sid, string $nif, string $country, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs, string $client_id)"
  56 => "UNKNOWN countAltAddresses(string $sid, string $nif)"
  57 => "UNKNOWN listAltAddresses(string $sid, string $nif)"
  58 => "UNKNOWN insertAltAddress(string $sid, string $nif, string $address, string $postalCode, string $locality)"
  59 => "UNKNOWN updateAltAddress(string $sid, string $nif, string $addressRef, string $address, string $postalCode, string $locality)"
  60 => "UNKNOWN deleteAltAddress(string $sid, string $nif, string $addressRef)"
  61 => "UNKNOWN countEntities(string $sid, string $nif)"
  62 => "PersonResponse listEntities(string $sid, string $nif, string $offset)"
  63 => "UNKNOWN entityExists(string $sid, string $internalRef)"
  64 => "PersonResponse getEntity(string $sid, string $internalRef)"
  65 => "UNKNOWN insertEntity(string $sid, string $parentNif, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  66 => "UNKNOWN updateEntity(string $sid, string $internalRef, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  67 => "UNKNOWN deleteEntity(string $sid, string $internalRef)"
  68 => "UNKNOWN countSellers(string $sid)"
  69 => "PersonResponse listSellers(string $sid, string $offset)"
  70 => "UNKNOWN sellerExists(string $sid, string $nif)"
  71 => "PersonResponse getSeller(string $sid, string $nif)"
  72 => "UNKNOWN insertSeller(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  73 => "UNKNOWN updateSeller(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  74 => "UNKNOWN deleteSeller(string $sid, string $nif)"
  75 => "UNKNOWN countProducts(string $sid)"
  76 => "UNKNOWN countProductsSerie(string $sid)"
  77 => "ProductResponse listProducts(string $sid, string $offset)"
  78 => "ProductResponse listProductsSerie(string $sid, string $offset)"
  79 => "ProductResponse listProductsTitles(string $sid, string $offset)"
  80 => "UNKNOWN productExists(string $sid, string $ref)"
  81 => "ProductResponse getProduct(string $sid, string $ref)"
  82 => "ProductInfoResponse getProduct_additionalInfo(string $sid, string $ref)"
  83 => "UNKNOWN getProductInfo(string $sid, string $ref, string $fieldname)"
  84 => "TableResponse getRelatedProducts(string $sid, string $ref)"
  85 => "TableResponse getProductPrices(string $sid, string $ref)"
  86 => "UNKNOWN insertProduct(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
  87 => "UNKNOWN updateProduct(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
  88 => "UNKNOWN changeProductTax(string $sid, string $ref, string $taxid)"
  89 => "UNKNOWN changeProductFamily(string $sid, string $ref, string $idfamily)"
  90 => "UNKNOWN setProductField(string $sid, string $ref, string $fieldname, string $value)"
  91 => "UNKNOWN insertProductImageByURL(string $sid, string $ref, string $url)"
  92 => "UNKNOWN deleteProduct(string $sid, string $ref)"
  93 => "ProductResponse searchProducts(string $sid, string $searchTerm)"
  94 => "ProductResponse_custom searchProducts_custom(string $sid, string $searchTerm)"
  95 => "ProductResponseDetails getProductDetails(string $sid, string $ref)"
  96 => "UNKNOWN getCSInfo(string $sid, string $type, string $ref, string $fieldname)"
  97 => "UNKNOWN getCSFoto(string $sid, string $ref, string $id)"
  98 => "UNKNOWN countProductsCS(string $sid)"
  99 => "ProductResponse listProductsCS(string $sid, string $offset)"
  100 => "TableResponse getProductCSInfo(string $sid, string $ref)"
  101 => "TableResponse getProductCS(string $sid, string $ref)"
  102 => "TableResponse getProductCSstock(string $sid, string $ref)"
  103 => "UNKNOWN insertProductCS(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
  104 => "UNKNOWN insertProductColorSize(string $sid, string $ref, string $type, string $internalref)"
  105 => "UNKNOWN countDocuments(string $sid, string $docType)"
  106 => "UNKNOWN countAllDocuments(string $sid, string $date_from, string $date_to, string $read)"
  107 => "DocumentResponse documentsList(string $sid, string $docType, string $offset)"
  108 => "DocumentResponse documentsList_byClient(string $sid, string $docType, string $nif, string $offset)"
  109 => "DocumentResponse documentsList_custom(string $sid, string $docType, string $offset, string $order_by, string $sort_by, string $docseries)"
  110 => "DocumentResponse documentsList_byClient_custom(string $sid, string $docType, string $nif, string $offset, string $order_by, string $sort_by, string $docseries)"
  111 => "DocumentResponse documentsDraftsList(string $sid, string $docType, string $nif, string $offset)"
  112 => "AllDocsResponse listAllDocuments(string $sid, string $date_from, string $date_to, string $read, string $offset)"
  113 => "UNKNOWN documentExists(string $sid, string $idDoc, string $docType)"
  114 => "DocumentResponse getDocument(string $sid, string $idDoc, string $docType)"
  115 => "DocumentResponse getDocument_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  116 => "DocumentDetailsResponse getDocumentDetails_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  117 => "UNKNOWN getDocumentInfo(string $sid, string $idDoc, string $docType, string $fieldname)"
  118 => "DocumentCertificateResponse getDocumentCertificate(string $sid, string $idDoc, string $docType)"
  119 => "DocumentCertificateInfoResponse getDocumentCertificateInfo(string $sid, string $idDoc, string $docType)"
  120 => "UNKNOWN getDocumentPDF(string $sid, string $idDoc, string $docType)"
  121 => "UNKNOWN getDocumentPDFLink(string $sid, string $idDoc, string $docType)"
  122 => "UNKNOWN getDocumentPDFLink_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  123 => "UNKNOWN sendDocumentPDF2Email(string $sid, string $idDoc, string $docType, string $email_destinations, string $email_subject, string $email_body)"
  124 => "UNKNOWN sendDocumentPDF2Email_bySeries(string $sid, string $idDoc, string $docType, string $docSeries, string $email_destinations, string $email_subject, string $email_body)"
  125 => "UNKNOWN insertDocumentHeader(string $sid, string $nif, string $docType, string $obs, string $opt_name, string $opt_nif, string $opt_address, string $opt_locality, string $opt_postalCode, string $docRef)"
  126 => "UNKNOWN insertEntityOrder(string $sid, string $internalRef, string $obs)"
  127 => "UNKNOWN insertDocumentHeader_additionalInfo(string $sid, string $idDocTemp, string $docType, string $printComment, string $docRef, string $pickupDateTime, string $pickupLocation, string $deliveryDateTime, string $deliveryLocationTxt, string $licencePlate, string $opt_deliveryLocation_address, string $opt_deliveryLocation_postalCode, string $opt_deliveryLocation_city, string $opt_deliveryLocation_countryCode)"
  128 => "UNKNOWN setDocumentClosedSituation(string $sid, string $idDoc, string $docType, string $docSerie, string $situationId)"
  129 => "UNKNOWN setDocumentHeaderField(string $sid, string $idDocTemp, string $docType, string $fieldname, string $fieldvalue)"
  130 => "UNKNOWN insertDocumentAlternativeCurrency(string $sid, string $idDocTemp, string $docType, string $currency_id, string $conversionvalue)"
  131 => "UNKNOWN closeDocument(string $sid, string $idDocTemp, string $docType)"
  132 => "UNKNOWN closeDocument_bySeries(string $sid, string $idDocTemp, string $docType, string $docSeries)"
  133 => "UNKNOWN markDocument(string $sid, string $idDoc, string $docType, string $read)"
  134 => "UNKNOWN markDocument_bySeries(string $sid, string $idDoc, string $docType, string $docSeries, string $read)"
  135 => "UNKNOWN documentCommunication(string $sid, string $idDoc, string $docType)"
  136 => "UNKNOWN documentCommunication_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  137 => "MBRefResponse generateMBRef(string $sid, string $idDoc, string $docType)"
  138 => "UNKNOWN addMBRef(string $sid, string $idDoc, string $docType, string $RefMB, string $rascunho)"
  139 => "UNKNOWN settleInvoice(string $sid, string $idDoc, string $docSeries)"
  140 => "UNKNOWN checkIfSettle(string $sid, string $idDoc, string $docSeries)"
  141 => "UNKNOWN setDocumentVoid(string $sid, string $docType, string $docSeries, string $idDoc, string $c_series, string $c_date, string $c_reason)"
  142 => "UNKNOWN deleteDocumentDraft(string $sid, string $idDocTemp, string $docType)"
  143 => "UNKNOWN insertStockDocumentHeader(string $sid, string $docSeries, string $date, string $docRef, string $obs, string $project, string $warehouse)"
  144 => "UNKNOWN closeStockDocument(string $sid, string $docSeries, string $idDocTemp)"
  145 => "DocumentLineResponse getDocumentLines(string $sid, string $idDoc, string $docType)"
  146 => "DocumentLineResponse getDocumentLines_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  147 => "DocumentLineDetailsResponse getDocumentLinesDetails_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
  148 => "TableResponse getDocumentLineCS(string $sid, string $idDoc, string $docType, string $idDocLine)"
  149 => "UNKNOWN getDocumentLineInfo(string $sid, string $idDoc, string $docType, string $idDocLine, string $fieldname)"
  150 => "UNKNOWN insertDocumentLine(string $sid, string $idDocTemp, string $docType, string $ref, string $qt, string $price, string $tax, string $prodDesc, string $discount)"
  151 => "UNKNOWN insertDocumentLineCS(string $sid, string $idDocTemp, string $docType, string $line, string $colorref, string $sizeref)"
  152 => "UNKNOWN changeDocumentLineTax(string $sid, string $idDocTemp, string $docType, string $idDocLineTemp, string $taxid)"
  153 => "UNKNOWN insertDocumentLine_bySeries(string $sid, string $idDocTemp, string $docType, string $ref, string $qt, string $docSeries)"
  154 => "UNKNOWN DocumentTransport(string $sid, string $idDocTemp, string $docType, string $docSeries, string $HoraCarga, string $MoradaCarga, string $LocalidadeCarga, string $CodPostalCarga, string $CodigoPaisCarga, string $DataHoraDescarga, string $MoradaDescarga, string $LocalidadeDescarga, string $CodPostalDescarga, string $CodigoPaisDescarga, string $MatriculaViatura)"
  155 => "SalesmanResponse listSalesman(string $sid, string $offset)"
  156 => "UNKNOWN countSalesmans(string $sid)"
  157 => "UNKNOWN countContacts(string $sid)"
  158 => "ContactResponse listContacts(string $sid, string $offset)"
  159 => "UNKNOWN contactSearch(string $sid, string $idContact)"
  160 => "UNKNOWN contactExists(string $sid, string $idContact)"
  161 => "ContactResponse getContact(string $sid, string $idContact)"
  162 => "UNKNOWN getContactDetails(string $sid, string $nif)"
  163 => "UNKNOWN getContactInfo(string $sid, string $idContact, string $fieldname)"
  164 => "UNKNOWN insertContact(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  165 => "UNKNOWN updateContact(string $sid, string $idContact, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
  166 => "UNKNOWN convertContact(string $sid, string $idContact)"
  167 => "UNKNOWN setContactField(string $sid, string $idContact, string $fieldname, string $value)"
  168 => "UNKNOWN deleteContact(string $sid, string $idContact)"
  169 => "UNKNOWN getStatistics(string $sid, string $type, string $params)"
  170 => "UNKNOWN insertProductBatch(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
  171 => "UNKNOWN productBatchNumberExists(string $sid, string $ref, string $batchnumber)"
  172 => "TableBatchNumResponse getProductBatchNumbersStock(string $sid, string $ref, string $warehouse)"
  173 => "UNKNOWN insertBatchNumber(string $sid, string $ref, string $batchnumber, string $name, string $manufacturingDate, string $expirationDate, string $hidden)"
  174 => "UNKNOWN insertDocumentLineBatchNumber_bySeries(string $sid, string $docType, string $docSeries, string $idDocTemp, string $ref, string $batchnumber, string $qt)"
  175 => "AllDocsResponseTESTE listAllDocumentsTESTE(string $sid, string $date_from, string $date_to, string $read, string $offset)"
  176 => "DocResponse insertOrderHeader_byStore(string $sid, string $store_name, string $obs, string $opt_name, string $opt_nif, string $opt_address, string $opt_locality, string $opt_postalCode, string $docRef)"
  177 => "UNKNOWN registerCard(string $sid, string $designacao, string $morada, string $local, string $codpost, string $codpais, string $nif, string $tlf, string $tlm, string $email)"
  178 => "UNKNOWN loginCard(string $sid, string $email, string $password)"
  179 => "UNKNOWN passwordCard(string $sid, string $email)"
  180 => "UNKNOWN getCard(string $sid, string $email)"
  181 => "UNKNOWN historyCard(string $sid, string $CodigoCartao)"
  182 => "UNKNOWN closeDocumentCard(string $sid, string $idDocTemp, string $docType, string $CodigoCartao, string $CartaoDescontoVal)"
]*/