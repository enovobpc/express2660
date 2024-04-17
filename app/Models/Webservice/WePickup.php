<?php

namespace App\Models\Webservice;

use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;

class WePickup extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    //private $url = 'https://staging.wepickup.pt/'; //testes
    private $url = 'https://portal.wepickup.pt/';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/wepickup/';

    /**
     * @var null
     */
    private $debug = false;

    /**
     * @var null
     */
    private $apiKey = null;

    /**
     * Ctt constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $cliente = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug = false)
    {
        $this->apiKey = $sessionId;
        $this->debug  = $debug;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {
        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsev="https://portal.wepickup.pt/wsevents">
           <soapenv:Header/>
           <soapenv:Body>
              <wsev:getEvents soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                 <authenticationId xsi:type="xsd:anyType">'.$this->apiKey.'</authenticationId>
                 <shiptracking xsi:type="xsd:anyType">'.$trackingCode.'</shiptracking>
              </wsev:getEvents>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->execute('wsevents', $xml);
        $response = $response['ns1:getEventsResponse']['return'];

        if(empty($response['success']['@content'])) {
            throw new \Exception($response['errorCode']['@content'] .' - '.$response['errorDescription']['@content'], $response['errorCode']['@content']);
        } else {

            $weight    = forceDecimal($response['weight']['@content']);
            $trackings = @$response['trackings']['item'];

            $histories = [];
            foreach ($trackings as $tracking) {

                $statusCode = @$tracking['statusId']['@content'];
                $statusName = @$tracking['status']['@content'];
                $createdAt  = new Carbon(@$tracking['createdAt']['@content']);
                $obs        = @$tracking['obs']['@content'];

                $services = config('shipments_import_mapping.wepickup-status');
                $statusId = @$services[$statusCode];

                $obs = str_replace('WePickUp', '', $obs);

                if($statusId == ShippingStatus::INCIDENCE_ID) {
                    $obs = $statusName. ' ' .$obs;
                }

                $histories[] = [
                    'status_id'   => $statusId,
                    'created_at'  => $createdAt->format('Y-m-d H:i:s'),
                    'obs'         => $obs,
                    'status_name' => $statusName,
                    'status_code' => $statusCode,
                ];
            }

            aasort($histories, 'created_at');

            return [
                'weight'    => $weight,
                'histories' => $histories
            ];
        }

        return null;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode, $shipment)
    {
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date) {
        return false;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia){
        return false;
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date) {
        return false;
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode) {
        return false;
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date) {
        return false;
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
    }

    /**
     * Permite obter um envio da base de dados  um envio pelo seu trk caso exista envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    /**
     * Permite obter um envio da base de dados  um envio pelo seu trk caso exista envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeEnvioByTrk($trakingCode, $originalShipment)
    {

        $shipment = $originalShipment->replicate(); //$this->getEnvioByTrk(null, null, $trakingCode);

        $service  = Service::filterSource()->where('id', $originalShipment->service_id)->first();

        $shipment->reset2replicate();
        $shipment->is_collection        = 0;
        $shipment->recipient_email      = $originalShipment->recipient_email;
        $shipment->charge_price         = $originalShipment->charge_price;
        $shipment->date                 = date('Y-m-d');
        $shipment->customer_id          = $originalShipment->customer_id;
        $shipment->agency_id            = $originalShipment->agency_id;
        $shipment->sender_agency_id     = $originalShipment->sender_agency_id;
        $shipment->recipient_agency_id  = $originalShipment->recipient_agency_id;
        $shipment->type                 = Shipment::TYPE_PICKUP;
        $shipment->parent_tracking_code = $originalShipment->tracking_code;

        $shipment->provider_id          = $originalShipment->provider_id;
        $shipment->service_id           = @$service->id;
        $shipment->status_id            = $originalShipment->status_id == 18 ? 18 : 15; //rec. falhada ou aguarda sync

        $shipment->webservice_method       = $originalShipment->webservice_method;
        $shipment->provider_tracking_code  = $originalShipment->provider_tracking_code;
        $shipment->submited_at             = Date::now();

        $trk = $shipment->setTrackingCode();

        if ($originalShipment->total_price_after_pickup) {
            $shipment->shipping_price = $originalShipment->total_price_after_pickup;
            $shipment->price_fixed    = true;
        }

        //adiciona taxa de recolha
        $shipment->insertOrUpdadePickupExpense($originalShipment); //add expense
        $originalShipment->update([
            'children_tracking_code' => $shipment->tracking_code,
            'children_type' => Shipment::TYPE_PICKUP,
            'status_id'     => ShippingStatus::PICKUP_CONCLUDED_ID
        ]);

        //calcula preços
        $prices = Shipment::calcPrices($shipment);
        if(@$prices['fillable']) {
            $shipment->fill($prices['fillable']);
            //adiciona taxas
            $shipment->storeExpenses($prices);
        }
        $shipment->save();

        //desconta preço do envio da wallet
        if (hasModule('account_wallet') && !@$originalShipment->customer->is_mensal) {
            $price = $shipment->billing_total;

            if ($price > 0.00) {
                try {
                    $shipment->walletPayment();
                } catch (\Exception $e) {
                }
            }
        }

        return $shipment->tracking_code;

    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode)
    {
        return false;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode)
    {
        $file = File::get(public_path() . '/uploads/labels/wepickup/' . $trackingCode . '_labels.txt');
        return $file;
    }

    /**
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode) {}

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data)
    {

        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wss="https://portal.wepickup.pt/wspicking">
               <soapenv:Header/>
               <soapenv:Body>
                  <wss:createPicking soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                     <pickingRequest xsi:type="xsd:anyType">
                        <authenticationId>'.$this->apiKey.'</authenticationId>
                        <internalReference>'.$data['reference'].'</internalReference>
                        <shipFromName>'.$data['sender_name'].'</shipFromName>
                        <shipFromVat>'.$data['sender_vat'].'</shipFromVat>
                        <shipFromPhone>'.$data['sender_phone'].'</shipFromPhone>
                        <shipFromAddress>'.$data['sender_address'].'</shipFromAddress>
                        <shipFromZipCode>'.$data['sender_zip_code'].'</shipFromZipCode>
                        <shipFromCity>'.$data['sender_city'].'</shipFromCity>
                        <shipFromCountry>'.strtoupper($data['sender_country']).'</shipFromCountry>
                        <shipFromEmail>'.$data['sender_email'].'</shipFromEmail>
                        <shipToName>'.$data['recipient_name'].'</shipToName>
                        <shipToVat>'.$data['recipient_vat'].'</shipToVat>
                        <shipToEmail></shipToEmail>
                        <shipToPhone>'.$data['recipient_phone'].'</shipToPhone>
                        <shipToAddress>'.$data['recipient_address'].'</shipToAddress>
                        <shipToZipCode>'.$data['recipient_zip_code'].'</shipToZipCode>
                        <shipToCity>'.$data['recipient_city'].'</shipToCity>
                        <shipToCountry>'.strtoupper($data['recipient_country']).'</shipToCountry>
                        <volumes>'.$data['volumes'].'</volumes>
                        <weight>'.$data['weight'].'</weight>
                        <merchandiseDesignation>'.$data['goods_description'].'</merchandiseDesignation>
                        <obs>'.$data['obs'].'</obs>
                        <generateShipping>1</generateShipping>
                        <pickingDate>'.$data['date'].'</pickingDate>
                     </pickingRequest>
                  </wss:createPicking>
               </soapenv:Body>
            </soapenv:Envelope>';

        $response = $this->execute('wspicking', $xml);
        $response = $response['ns1:createPickingResponse']['return'];

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', print_r($data, true));
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }


        if(empty($response['success']['@content'])) {
            throw new \Exception($response['errorCode']['@content'] .' - '.$response['errorDescription']['@content'], $response['errorCode']['@content']);
        } else {
            $trk = $response['idPick']['@content'];
            return $trk;
        }

        return null;
    }


    /**
     * Submit a shipment
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function storeEnvio($data)
    {
        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wss="https://portal.wepickup.pt/wsshipping">
               <soapenv:Header/>
               <soapenv:Body>
                  <wss:createShippingV2 soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                     <shippingRequest xsi:type="xsd:anyType">
                        <authenticationId>'.$this->apiKey.'</authenticationId>
                        <internalReference>'.$data['reference'].'</internalReference>
                        <shipFromName>'.$data['sender_name'].'</shipFromName>
                        <shipFromVat>'.$data['sender_vat'].'</shipFromVat>
                        <shipFromPhone>'.$data['sender_phone'].'</shipFromPhone>
                        <shipFromAddress>'.$data['sender_address'].'</shipFromAddress>
                        <shipFromZipCode>'.$data['sender_zip_code'].'</shipFromZipCode>
                        <shipFromCity>'.$data['sender_city'].'</shipFromCity>
                        <shipFromCountry>'.strtoupper($data['sender_country']).'</shipFromCountry>
                        <shipFromEmail>'.$data['sender_email'].'</shipFromEmail>
                        <shipToName>'.$data['recipient_name'].'</shipToName>
                        <shipToVat>'.$data['recipient_vat'].'</shipToVat>
                        <shipToEmail>'.$data['sender_email'].'</shipToEmail>
                        <shipToPhone>'.$data['recipient_phone'].'</shipToPhone>
                        <shipToAddress>'.$data['recipient_address'].'</shipToAddress>
                        <shipToZipCode>'.$data['recipient_zip_code'].'</shipToZipCode>
                        <shipToCity>'.$data['recipient_city'].'</shipToCity>
                        <shipToCountry>'.strtoupper($data['recipient_country']).'</shipToCountry>
                        <volumes>'.$data['volumes'].'</volumes>
                        <weight>'.$data['weight'].'</weight>
                        <merchandiseDesignation>'.$data['goods_description'].'</merchandiseDesignation>
                        <obs>'.$data['obs'].'</obs>
                        <refundValue>'.($data['charge_price'] ? $data['charge_price'] : 0.00).'</refundValue>
                        <returnBack>'.$data['rpack'].'</returnBack>
                        <!-- <product>'.$data['service'].'</product>-->
                        <docReturn>'.$data['rguide'].'</docReturn>
                        <fragile>'.$data['fragil'].'</fragile>
                        <!--<generatePicking>'.$data['date'].'</generatePicking>-->
                     </shippingRequest>
                  </wss:createShippingV2>
               </soapenv:Body>
            </soapenv:Envelope>';

        $response = $this->execute('wsshipping', $xml);

        $response = @$response['ns1:createShippingV2Response']['return'];

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', $xml);
            file_put_contents (public_path().'/dumper/response.txt', print_r($response,1));
        }


        if(empty($response['success']['@content'])) {
            throw new \Exception($response['errorCode']['@content'] .' - '.$response['errorDescription']['@content'], $response['errorCode']['@content']);
        } else {

            $trk    = @$response['idShip']['@content'];
            $labels = @$response['base64File']['@content'];

            if (!File::exists(public_path() . '/uploads/labels/wepickup/')) {
                File::makeDirectory(public_path() . '/uploads/labels/wepickup/');
            }
            File::put(public_path() . $this->upload_directory . $trk . '_labels.txt', $labels);

            return $trk;
        }

        return null;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

        if($data) {

            $weight = $data['weight'];
            $data   = $data['histories'];
            $weightChanged = $shipment->weight < $weight ? true : false;
            $deliveredTrks = [];

            foreach ($data as $key => $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);


                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                if($shipment->is_collection && $item['status_id'] == 36) { //recolha recolhida, muda para envio gerado

                    $trk = $this->storeEnvioByTrk($shipment->provider_tracking_code, $shipment);

                    $history = new ShipmentHistory();
                    $history->shipment_id = $shipment->id;
                    $history->status_id = ShippingStatus::PICKUP_CONCLUDED_ID; //recolha finalizada
                    $history->obs = 'Gerado envio ' . $trk;
                    $history->save();
                }

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::DELIVERED_ID) {
                    $deliveredTrks[$shipment->provider_tracking_code] = $shipment->provider_tracking_code;
                }

            }

            try {
                if($history) {
                    $history->sendEmail(false, false, true);
                }
            } catch (\Exception $e) {}


            if($history) {
                $shipment->status_id = $history->status_id;
            }

            /**
             * Calcula o preço e custo do envio
             */
            if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id
                    && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id
                    && $shipment->agency_id && $shipment->customer_id && $weightChanged)) {

                $serviceId = $shipment->service_id;
                if($shipment->is_collection) {
                    $serviceId = @$shipment->service->assigned_service_id;
                }

                $tmpShipment = $shipment;
                $tmpShipment->service_id = $serviceId;
                $prices = Shipment::calcPrices($tmpShipment);
                dd(@$prices['fillable']);
                if(@$prices['fillable'] && (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id)) {
                    $shipment->fill($prices['fillable']);
                    $shipment->storeExpenses($prices);
                }
                

                //descontar preço da wallet
                if (hasModule('account_wallet') && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                    $diffPrice = $shipment->billing_total - $oldPrice;

                    if ($diffPrice > 0.00) {
                        try {
                            \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                            $shipment->customer->subWallet($diffPrice);
                        } catch (\Exception $e) {}
                    }
                }
            }

            $shipment->save();

            if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                $price = $shipment->addPickupFailedExpense();
                $shipment->walletPayment(null, null, $price); //discount payment
            }

            return $history->status_id ? $history->status_id : true;
        }

        return false;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false, $webserviceLogin = null) {

        $data = $shipment->toArray();

        $data['service'] = '1';

        $data['reference'] = 'TRK'.$data['tracking_code'];
        //dd($shipment);

        $data['sender_email']      = 'trk@trk.pt';//$shipment->agency->email ? $shipment->agency->email : 'trk@trk.com';
        $data['recipient_vat']     = $data['recipient_vat'] ? $data['recipient_vat'] : '999999990';
        $data['goods_description'] = 'Outros';

        $date = new Date($data['date']);
        $data['date'] = $date->format('d-m-Y');

        if($data['recipient_country'] == 'pt') {
            if(strlen($data['recipient_zip_code']) == 4) {
                $data['recipient_zip_code'] = $data['recipient_zip_code'].'-000';
            }

        }

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $data['rpack'] = 0;
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $data['rpack'] = 1;
        }

        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $data['rguide'] = 0;
        $data['fragil'] = 0;
        if(!empty($shipmentComplementarServices)) {

            //return guide
            if(in_array('rguide', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['rguide'], $shipmentComplementarServices)) {
                $data['rguide'] = 1;
            }

            if (in_array('fragile', $shipmentComplementarServices)) {
                $data['fragil'] = 1;
            }
        }

        if($isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
        }
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {
        return false;
    }

    /**
     * @param $method
     * @param $data
     * @return mixed
     */
    public function execute($method, $xml) {

        $curl = curl_init();

        $url = $this->url . $method;

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $xml,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_HTTPHEADER      => array(
                'Content-Type: application/xml'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = xmlstr_to_array($response);

        return @$response['SOAP-ENV:Body'];
    }
}