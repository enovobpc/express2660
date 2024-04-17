<?php

namespace App\Models\InvoiceGateway\OnSearch;

use App\Models\Logistic\ShippingOrder;
use App\Models\LogViewer;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Date, Response, Log, Auth;

class Document extends \App\Models\InvoiceGateway\OnSearch\Base {


    /**
     * Obtém uma lista de (25) clientes, começando numa posição indicada em «offset».
     *
     * @param $offset número do registo/linha a partir do qual deve construir a resposta
     * @return mixed
     */
    public function listsDocumentsTypes()
    {
        return $this->execute('/ons3api/Documents/GetAllDocumentTypes');
    }

    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function getDocument($docId, $documentType)
    {
        $action = 'ons3api/Documents/GetDocument';

        if(substr($docId, 0, 1) == '2') {
            $docId = substr($docId, 1); //a partir de 2022 tira o primeiro digito
        }

        $url = $this->url . $action . '?pDocType='.$documentType.'&pOrderID='.$docId;

        return $this->execute($action, null, 'GET', $url);
    }


    /**
     * Devolve os dados de um cliente, usando o NIF como chave de pesquisa.
     *
     * @param $nif número de Identificação Fiscal do cliente
     * @return mixed
     * @throws \Exception
     */
    public function insertOrUpdateDocument($documentCollection, $documentType = null)
    {
        if($documentType == 'reception') {
            //ordens de recepção
            $docType = [
                "DocType"  => "ORE",
                "Title"    => "Rec. Externa",
                "DocDesc"  => "Rec. Externa",
                "PartnerType" => [
                    "PartnerTypeID"   => "C",
                    "PartnerTypeDesc" => "Cliente"
                ],
                "QualityCode" => "Activos24/SGQ/ENC",
            ];
        } else {
            //Ordens de saida
            $docType = [
                "DocType"  => "ENC",
                "Title"    => "Encomenda",
                "DocDesc"  => "Encomenda",
                "PartnerType" => [
                    "PartnerTypeID"   => "C",
                    "PartnerTypeDesc" => "Cliente"
                ],
                "QualityCode" => "Activos24/SGQ/ENC",
            ];

        }

        $customerCollection = $documentCollection->customer;


        $providerTrk = $documentCollection->provider_trk;
        if(!$providerTrk) {
            $providerTrk = @$documentCollection->shipment->provider_tracking_code ? @$documentCollection->shipment->provider_tracking_code : @$documentCollection->shipment->tracking_code;
        }

        if(str_contains($providerTrk, ',')) {
            $providerTrk = explode(',', $providerTrk);
            $providerTrk = $providerTrk[0];
        }

        $partnerArr = [
            "PartnerType" => [
                "PartnerTypeID"   => "C",
                "PartnerTypeDesc" => "Cliente"
            ],
            "PartnerID"     => $customerCollection->code.'-0',
            "PartnerName"   => substr(trim($customerCollection->name), 0, 50),
            "VATNo"         => $customerCollection->vat,
            "Address"       => substr(trim($customerCollection->address), 0, 100),
            "City"          => $customerCollection->city,
            "PostalCode"    => $customerCollection->zip_code,
            "Country"       => trans('country.' . $customerCollection->country),
            "ContactPerson" => $customerCollection->responsable,
            "Phone"         => $customerCollection->phone,
            "MobilePhone"   => $customerCollection->mobile,
            "Email"         => $customerCollection->email,
            "Language"      => "pt",
        ];

        $docLines = [];
        foreach ($documentCollection->lines as $key => $line) {

            $unity = @$line->product->unity ? @$line->product->unity : 'un';
            $unity = $unity == 'unity' ? 'un' : $unity;
            $unity = $unity == 'box' ? 'cx' : $unity;

            $row = [
                "DocType"       => $docType,
                "OrderID"       => substr($documentCollection->code, 1), //remove o primeiro digito para poder comunicar
                "OrderRow"      => $key + 1,
                "ItemID"        => @$line->product->sku,
                "QtyOrd"        => $line->qty,
                "ItemDesc"      => @$line->product->name,

                "UnitConvFact"  => 1,
                "Unit" => [
                    "UnitID"   => $unity,
                    "UnitDesc" => $unity
                ],
                "UnitStk" => [
                    "UnitID"    => $unity,
                    "UnitDesc"  => $unity
                ],

                //"InvVolNum"     => $key,
                //"IDIntegration" => $documentCollection->code.$key,
                "RefCli"        => @$documentCollection->document,
                "UnitPrice"     => (float) @$line->product->price,
                "TotValue"      => (float) @$line->product->price * $line->qty,
                /* "QtySatisf" => 0,
                 "Version"   => 0,
                 "VolNum"    => 0,
                 "QtyPicked" => 0,
                 "QtyVols"   => 0,
                 "QtyProd"   => 0,
                 "QtyPend"   => 0,
                 "QtyPVol"   => 0,

                 "ColorID" => "string",
                 "GridID" => "string",
                 "VariationCountry" => "string",
                 "StartDate" => "2020-09-02T20:35:33.819Z",
                 "EndDate" => "2020-09-02T20:35:33.819Z",
                 "EndDateReal" => "2020-09-02T20:35:33.819Z",
                */

                //"ItemDesc2"     => "string",
                /*"Discount"      => 0,
                "PerCstAdic"    => 0,
                "QtyWithDiscount"   => 0,
                "VAT"               => 0,
                "RateIncrease"      => 0,
                "RETax"             => 0,
                "CurrencyExchangeDate" => "2020-09-02T20:35:33.819Z",

                "Obs" => "string",
                "SalesCondType" => [
                    "SalesCondTypeID" => "string",
                    "SalesCondTypeDescr" => "string"
                ],

                "POClient" => "string",
                "WHIDOrig" => 0,
                "WHIDDest" => 0,*/
            ];

            //if((@$line->product->lote && $customerCollection->code == '1225')) {
            if(@$line->product->lote) {

                $row['Lot'] = @$line->product->lote;

                if($documentType == 'reception') {
                    $row['Lot'] = @$line->lote;
                }
            }

            if(@$line->product->serial_no) {
                $row['SerialNum'] = @$line->product->serial_no;
            }

            $docLines[]= $row;
        }


        $data = [
            "DocType"           => $docType,
            "RequesterID"       => $customerCollection->code.'-'.@$documentCollection->document,
            "OrderID"           => substr($documentCollection->code, 1),
            "CreationDateTime"  => date('Y-m-d H:i:s'),
            "OrderDateTime"     => date('Y-m-d H:i:s'), //"2020-09-02T20:35:33.819Z",
            "OrderDatePrev"     => date('Y-m-d H:i:s'), //"2020-09-02T20:35:33.819Z",

            "Partner"   => $partnerArr,
            "Obs"       => $documentCollection->obs,

            /*"ProductionStatus" => [
                "DocStatusID"    => "initial",
                "DocStatusDescr" => "initial",
                "IsInitial"      => true,
                "IsFinal"        => true,
                "IsProcessing"   => true,
                "IsAnulated"     => false
            ],*/
            "Currency"     => "EUR",
            "ExchangeRate" => 0,
            "RefCli"       => @$documentCollection->document,
            /*  "PaymentType" => [
                  "PaymentTypeID" => "string",
                  "PaymentDesc" => "string",
                  "NDays" => 0,
                  "Discount" => 0
              ],
*/
            "PartnerName2"  => substr(trim(@$documentCollection->shipment->recipient_name), 0, 50),
            "Address"       => substr(trim(@$documentCollection->shipment->recipient_address), 0, 100),
            "PostalCode"    => @$documentCollection->shipment->recipient_zip_code,
            "City"          => @$documentCollection->shipment->recipient_city,
            "Country"       => @$documentCollection->shipment->recipient_country ? trans('country.' . @$documentCollection->shipment->recipient_country) : null,
            "VATNo"         => @$documentCollection->shipment->recipient_vat,
            "Phone"         => @$documentCollection->shipment->recipient_phone,
            /* "Email" => "string",
           "SalesCondType" => [
               "SalesCondTypeID" => "string",
               "SalesCondTypeDescr" => "string"
           ],
           "PricesIncludeVAT" => false,
           "ShippingCompany" => [
               "ShippingCompanyID"   => "string",
               "ShippingCompanyName" => "string"
           ],
           "TotalQtyOrd"   => 0,
           "TotalQtyPend"  => 0,
           "UrgencyStatus" => [
               "UrgencyStatusID"   => 0,
               "UrgencyStatusName" => "string",
               "UrgencyOrder"      => 0,
               "OrderIndex"        => 0
           ],

           "TotalValue"        => 0,
           "TotalShipValue"    => 0,
           "PercDiscount"      => 0,
           "PercDiscount2"     => 0,*/
            "BarCode"           => $documentType == 'reception' ? $documentCollection->code : $providerTrk,
            "TrackingNumber"    => $documentType == 'reception' ? $documentCollection->code : 'ACT' . @$documentCollection->shipment->tracking_code,
            //"IDIntegration"     => "string",
            "DocLines"          => $docLines
        ];

        $data = json_encode($data);

        $trace = LogViewer::getTrace(null, 'ONS3 - '.@$documentCollection->shipment->tracking_code. ' ' . ($documentCollection->submited_at ? 'UPDATE' : 'CREATE'), ['data' => $data]);
        Log::info(br2nl($trace));


        try {


            //$exists = $this->getDocument($documentCollection->code, 'PENT');

            if ($documentCollection->submited_at) {
                $this->execute('/ons3api/Documents/Update', $data, 'POST');
            } else {
                try {
                    $this->execute('/ons3api/Documents/Create', $data, 'POST');
                } catch (\Exception $e) {
                    if($e->getMessage() != 'Client Order already exists!') {
                        throw new \Exception($e->getMessage());
                    }
                }

                unset($documentCollection->customer, $documentCollection->trk, $documentCollection->provider_trk);
                $documentCollection->submited_at = date('Y-m-d H:i:s');
                $documentCollection->save();
            }


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Sincroniza o estado das ordens de saída
     *
     * @return mixed
     * @throws \Exception
     */
    public function syncDocumentsStatus()
    {

        $shippingOrders = ShippingOrder::with('shipment')
            ->filterSource()
            ->where('status_id', '<>', ShippingOrder::STATUS_CONCLUDED)
            ->get();

        foreach ($shippingOrders as $shippingOrder) {

            $originalStatus = $shippingOrder->status_id;

            try {

                $document = $this->getDocument($shippingOrder->code, 'ENC');

                //$isInitial    = @$document['ProductionStatus']['IsInitial'];
                $isProcessing = @$document['ProductionStatus']['IsProcessing'];
                $isFinal      = @$document['ProductionStatus']['IsFinal'];
                $isAnulated   = @$document['ProductionStatus']['IsAnulated'];

                if($isAnulated) {
                    $shippingOrder->status_id = ShippingOrder::STATUS_CANCELED;
                    $shipmentStatus = ShippingStatus::CANCELED_ID;
                } elseif($isProcessing) {
                    $shippingOrder->status_id = ShippingOrder::STATUS_PROCESSING;
                    $shipmentStatus = ShippingStatus::SHIPMENT_PROCESSING;
                } elseif($isFinal) {
                    $shippingOrder->status_id = ShippingOrder::STATUS_CONCLUDED;
                    $shipmentStatus = ShippingStatus::SHIPMENT_WAINT_EXPEDITION;
                } else {
                    $shippingOrder->status_id = ShippingOrder::STATUS_PENDING;
                    $shipmentStatus = ShippingStatus::PENDING_ID;
                }

                if($shippingOrder->status_id != $originalStatus) {
                    $shippingOrder->save();

                    if($shippingOrder->shipment->status_id != ShippingStatus::DELIVERED_ID) {
                        $shippingOrder->shipment->status_id = $shipmentStatus;
                        $shippingOrder->shipment->save();

                        $history = new ShipmentHistory();
                        $history->shipment_id = @$shippingOrder->shipment->id;
                        $history->agency_id = @$shippingOrder->shipment->agency_id;
                        $history->status_id = $shipmentStatus;
                        $history->save();

                        $history->sendEmail(false, false, true);
                    }
                }

            } catch (\Exception $e) {
                //dd($e->getMessage());
            }
        }

        return true;
    }
}
