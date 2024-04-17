<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, File, View, Setting;
use App\Models\ShipmentHistory;
use App\Models\WebserviceLog;

use Mpdf\Mpdf;
use Mockery\Exception;

class DbSchenker extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $testUrl     = 'https://eschenker-fat.dbschenker.com/webservice/bookingWebServiceV1_1?wsdl';
    private $url         = 'https://eschenker.dbschenker.com/webservice/bookingWebServiceV1_1?wsdl';
    private $urlTracking = 'https://eschenker.dbschenker.com/webservice/trackingWebServiceV2?wsdl';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/dbschenker/';

    /**
     * @var string
     */
    private $accessKey;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var string
     */
    private $debug;

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        if (config('app.env') == 'local') {
            $this->url        = $this->testUrl;
            $this->groupId    = '84538';
            $this->accessKey  = 'd725f094-ce8a-467d-9397-fb3909182a5d'; //user teste
        } else {
            $this->groupId    = $agencia;
            $this->accessKey  = $sessionId;
        }

        //dd($this->accessKey);
        $this->debug = $debug;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param string $codAgeCargo Código da agência de Destino
     * @param string $codAgeOri Código da Agência de Origem
     * @param string $trakingCode Código de Encomenda
     * @param Shipment $shipment Envio
     * @return array
     */
    public function getEstadoEnvioByTrk($codAgeCargo = null, $codAgeOri = null, $trakingCode, $shipment)
    {
        $transportNature = (@$shipment->service->is_maritime || @$shipment->service->is_air) ? 'INT' : 'EXP';

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.schenker.com/CustomerServices/eBusiness/ShipmentService/v2" xmlns:v4="http://www.schenker.com/SGI/v4_0">
           <soapenv:Header/>
           <soapenv:Body>
              <v2:getPublicServiceShipmentDetails>
                 <AccessKey>' . $this->accessKey . '</AccessKey>
                 <in>
                    <!--Optional:-->
                    <v4:ApplicationArea>
                        <v4:requestId>RQST' . $trakingCode . '</v4:requestId>
                    </v4:ApplicationArea>
                    <v4:referenceType>BID</v4:referenceType>
                    <v4:referenceNumber>' . $trakingCode . '</v4:referenceNumber>
                    <v4:transportNature>'. $transportNature .'</v4:transportNature>
                    <v4:shipmentDateFrom>'. $shipment->shipping_date->format('Y-m-d') .'</v4:shipmentDateFrom>
                    <v4:shipmentDateTo>'. $shipment->delivery_date->format('Y-m-d') .'</v4:shipmentDateTo>
                 </in>
              </v2:getPublicServiceShipmentDetails>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->request($this->urlTracking, $xml);
        $response = xml2Arr($response);

        $statusList = @$response['ns3getPublicShipmentDetailsResponse']['out']['ns2Shipment']['ns2ShipmentInfo']['ns2ShipmentBasicInfo']['ns2StatusEventList']['ns2StatusEvent'];

        if (empty($statusList)) {
            throw new \Exception('O webservice não devolveu resposta ou não há estados para o envio.');
        }

        $statusList = $this->mappingResult($statusList, 'status');
        return $statusList;
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
        return $this->getEstadoEnvioByTrk(null, null, $trakingCode, $shipment);
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date)
    {
    }

    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date)
    {
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
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
    }

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
        return $this->storeEnvio($data);
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data, $dimensions)
    {

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://www.schenker.com/Booking/v1_1">
           <soapenv:Header/>
           <soapenv:Body>
              <v1:' . $data['method'] . '>
                 <in>
                    <applicationArea>
                       <accessKey>' . $this->accessKey . '</accessKey>';

        if ($this->groupId) {
            $xml .= '<groupId>' . $this->groupId . '</groupId>';
        }

        $xml .= '<requestID>' . $data['reference'] . '</requestID>
                    </applicationArea>
                    <' . $data['service'] . '>
                       <barcodeRequest start_pos="1" separated="false">A6</barcodeRequest>
                       <address>
                          <name1>' . removeAccents($data['sender_name']) . '</name1>
                          <name2>' . (@$data['sender_attn'] ? 'A/C: ' . removeAccents($data['sender_attn']) : '') . '</name2>
                          <locationType>PHYSICAL</locationType>
                          <mobilePhone>' . $data['sender_phone'] . '</mobilePhone>
                          <personType>COMPANY</personType>
                          <postalCode>' . $data['sender_zip_code'] . '</postalCode>
                          <street>' . removeAccents($data['sender_address']) . '</street>
                          <city>' . removeAccents($data['sender_city']) . '</city>
                          <countryCode>' . $data['sender_country'] . '</countryCode>
                          <type>SHIPPER</type>
                       </address>
                       <address>
                          <name1>' . removeAccents($data['recipient_name']) . '</name1>
                          <name2>' . (@$data['recipient_attn'] ? 'A/C: ' . removeAccents($data['recipient_attn']) : '') . '</name2>
                          <locationType>PHYSICAL</locationType>
                          <mobilePhone>' . $data['recipient_phone'] . '</mobilePhone>
                          <personType>COMPANY</personType>
                          <postalCode>' . $data['recipient_zip_code'] . '</postalCode>
                          <street>' . removeAccents($data['recipient_address']) . '</street>
                          <city>' . removeAccents($data['recipient_city']) . '</city>
                          <countryCode>' . $data['recipient_country'] . '</countryCode>
                          <type>CONSIGNEE</type>
                       </address>
                       <incoterm>' . $data['incoterm'] . '</incoterm>
                       <incotermLocation>' . removeAccents($data['recipient_city']) . '</incotermLocation>
                       <productCode>' . $data['product_code'] . '</productCode>
                       <measurementType>METRIC</measurementType>
                       <cargoDescription>N/A</cargoDescription>

                       <!--<cargoInsurance>
                          <value>120</value>
                          <currency>EUR</currency>
                       </cargoInsurance>-->
                       ';

        if ($data['charge_price'] > 0.00) {
            $xml .= '<cashOnDelivery>
                      <value>' . $data['charge_price'] . '</value>
                      <currency>EUR</currency>
                   </cashOnDelivery>';
        }

        $xml .= '<customsClearance>0</customsClearance>

                       <grossWeight>' . $data['weight'] . '</grossWeight>
                       <indoorDelivery>0</indoorDelivery>
                       <pickupDates>
                          <pickUpDateFrom>' . $data['date_start'] . '</pickUpDateFrom>
                          <pickUpDateTo>' . $data['date_end'] . '</pickUpDateTo>
                       </pickupDates>

                       <reference>
                          <number>' . $data['reference'] . '</number>
                          <id>SHIPPER_REFERENCE_NUMBER</id>
                       </reference>

                        <handlingInstructions>' . removeAccents($data['obs']) . '</handlingInstructions>
                        <neutralShipping>false</neutralShipping>
                        <specialCargo>true</specialCargo>
                        <serviceType>D2D</serviceType>
                        <incotermDestinationType>CON</incotermDestinationType>

                       <shippingInformation>';

        $totalWeight = 0;
        $totalVolume = 0;
        if ($dimensions) {


            foreach ($dimensions as $dimension) {

                $volumeM3   = ($dimension->height * $dimension->length * $dimension->width) / 1000000;
                $volumeM3   = $volumeM3 > 0.01 ? $volumeM3 : 0.01; // Minimum DbSchenker value is 0.01
                $itemWeight = $dimension->weight; //($dimension->weight > 0.00 ? $dimension->weight : 1); //comentado em 16/02/2022

                $totalVolume += $volumeM3;
                $totalWeight += $itemWeight;

                $xml .= '
                                <shipmentPosition>
                                    <dgr>0</dgr>
                                    <cargoDesc>' . removeAccents($dimension->description) . '</cargoDesc>
                                    <length>' . $dimension->length . '</length>
                                    <width>' . $dimension->width . '</width>
                                    <height>' . $dimension->height . '</height>
                                    <volume>' . number($volumeM3) . '</volume>
                                    <grossWeight>' .  $itemWeight  . '</grossWeight>
                                    <packageType>' . $data['pack_type'] . '</packageType>
                                    <pieces>' . $dimension->qty . '</pieces>
                                    <stackable>0</stackable>
                                </shipmentPosition>';
            }
        }
        $xml .= '
                            <grossWeight>' . number($totalWeight) . '</grossWeight>
                            <volume>' . number($totalVolume, 2) . '</volume>
                        </shippingInformation>
                       <express>true</express>
                       <foodRelated>false</foodRelated>
                       <heatedTransport>false</heatedTransport>
                       <homeDelivery>false</homeDelivery>
                       <measureUnit>VOLUME</measureUnit>
                       <ownPickup>false</ownPickup>
                       <pharmaceuticals>false</pharmaceuticals>
                    </' . $data['service'] . '>
                 </in>
              </v1:' . $data['method'] . '>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->request($this->url, $xml);

        //dd($response);
        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            file_put_contents(public_path() . '/dumper/request.txt', $xml);
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }

        //dd($response);
        if (empty($response)) {
            throw new \Exception('O webservice não devolveu nenhuma resposta. Verifique se existem caracteres especiais como + ou &.');
        }

        $arr = xml2Arr($response);

        if (@$arr['soapFault']) {
            $error = @$arr['soapFault']['detail']['ns2schenkerServiceException']['error'];

            if (!empty($error)) {
                throw new \Exception($error['code'] . ' - ' . $error['message'], $error['code']);
            }
            throw new \Exception($arr['soapFault']['faultstring']);
        }

        $trk    = @$arr['ns2getBookingResponse']['out']['bookingId'];
        $label  = @$arr['ns2getBookingResponse']['out']['barcodeDocument'];

        if (empty($trk)) {
            throw new \Exception('Não foi devolvido código de envio.');
        }

        if (!empty($label)) {
            if (!File::exists(public_path() . $this->upload_directory)) {
                File::makeDirectory(public_path() . $this->upload_directory);
            }

            $result = File::put(public_path() . $this->upload_directory . $trk . '.txt', $label);
            if ($result === false) {
                throw new \Exception('Não foi possível gravar a etiqueta.');
            }
        }

        return $trk;
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

        /* $filepath = public_path().$this->upload_directory.$trackingCode.'.txt';
        if(File::exists($filepath)) {
            $file = File::get($filepath);
            return $file;
        }*/

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://www.schenker.com/Booking/v1_1">
           <soapenv:Header/>
           <soapenv:Body>
              <v1:getBookingBarcodeRequest>
                 <in>
                    <applicationArea>
                       <accessKey>' . $this->accessKey . '</accessKey>';

        if ($this->groupId) {
            $xml .= '<groupId>' . $this->groupId . '</groupId>';
        }

        $xml .= '<requestID>RQST' . $trackingCode . '</requestID>
                    </applicationArea>
                    <barcodeRequest>
                       <format start_pos="1" separated="false">A6</format>
                       <!--1 to 999 repetitions:-->
                       <bookingId>' . $trackingCode . '</bookingId>
                    </barcodeRequest>
                 </in>
              </v1:getBookingBarcodeRequest>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->request($this->url, $xml);
        $response = xml2Arr($response);

        if (@$response['soapFault']) {
            $error = @$response['soapFault']['detail']['ns2schenkerServiceException']['error'];

            if (!empty($error)) {
                throw new \Exception($error['code'] . ' - ' . $error['message'], $error['code']);
            }
            throw new \Exception($response['soapFault']['faultstring']);
        }

        $label = @$response['ns2getBookingBarcodeResponse']['out']['document'];

        if ($label) {
            if (!File::exists(public_path() . $this->upload_directory)) {
                File::makeDirectory(public_path() . $this->upload_directory);
            }

            $result = File::put(public_path() . $this->upload_directory . $trackingCode . '.txt', $label);
            if ($result === false) {
                throw new \Exception('Não foi possível gravar a etiqueta.');
            }
        }

        return $label;
    }

    /**
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }


    /**
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null)
    {
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
    public function updateHistory($shipment)
    {

        if ($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code, $shipment);
        } else {
            $data = self::getEstadoEnvioByTrk(
                $shipment->provider_cargo_agency,
                $shipment->provider_sender_agency,
                $shipment->provider_tracking_code,
                $shipment
            );
        }

        if ($data) {

            // $data = array_reverse($data);

            foreach ($data as $item) {

                $schenkerStatus = config('shipments_import_mapping.db_schenker-status');
                $item['status_id']  = @$schenkerStatus[$item['status']];
                $item['created_at'] = new Date($item['created_at']);
                $item['obs']        = $item['city'];

                if (empty($item['status_id'])) {
                    throw new \Exception('Estado com o código ' . $item['status'] . ' sem mapeamento.');
                }

                if ($item['status_id'] == '9') {

                    $schenkerIncidences = config('shipments_import_mapping.db_schenker-incidences');

                    $incidenceId = @$schenkerIncidences[$item['incidence']];
                    if ($incidenceId) {
                        $item['incidence_id'] = $incidenceId;
                    }
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'created_at'  => $item['created_at'],
                    'status_id'   => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
            $shipment->save();
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
    public function saveShipment($shipment, $isCollection = false)
    {

        $shipment->service->code = 'INT-T';

        $service = $this->getProviderService($shipment);
        $service = explode('#', $service);
        $product = @$service[1] ? $service[1] : '43';  //43 = service system (default)
        $service = $service[0];

        $method = null;
        if ($service == 'bookingLand') {
            $method = 'getBookingRequestLand';
        } elseif ($service == 'bookingAir') {
            $method = 'getBookingRequestAir';
        } elseif ($service == 'bookingOceanFCL') {
            $method = 'getBookingRequestOceanFCL';
        } elseif ($service == 'bookingOceanLCL') {
            $method = 'getBookingRequestOceanLCL';
        }

        $date  = new Carbon($shipment->date);
        $today = Carbon::today();

        $startDate = $date->format('Y-m-d') . 'T09:00:00.000+01:00';
        $endDate   = $date->format('Y-m-d') . 'T19:00:00.000+01:00';

        if ($date->lt($today)) {
            throw new \Exception('A data é anterior à data de hoje.');
        }


        $data = $shipment->toArray();
        $data['method']             = $method;
        $data['service']            = $service;
        $data['requestId']          = 'TRK' . $shipment->tracking_code;
        $data['reference']          = 'TRK' . $shipment->tracking_code . ($shipment->reference ? ' ' . $shipment->reference : '');
        $data['sender_country']     = strtoupper(@$data['sender_country']);
        $data['recipient_country']  = strtoupper(@$data['recipient_country']);
        $data['date_start']         = $startDate;
        $data['date_end']           = $endDate;
        $data['pack_type']          = $this->getPackType($shipment->packaging_type);
        $data['product_code']       = $product;
        $data['volume_m3']          = $shipment->volume_m3;

        $appCountry = Setting::get('app_country', 'pt');
        if (in_array($appCountry, ['ptmd', 'ptac'])) { $appCountry = 'pt'; }
        $data['incoterm'] = $shipment->sender_country != $appCountry ? 'EXW' : 'DAP';


        $dimensions = $shipment->pack_dimensions;

        if (!@$dimensions || @$dimensions->isEmpty()) {
            throw new \Exception('É obrigatório indicar as dimensões e peso de cada um dos volumes.');
        }

        return $this->storeEnvio($data, $dimensions);
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment)
    {

        return true;

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://www.schenker.com/Booking/v1_1">
           <soapenv:Header/>
           <soapenv:Body>
              <v1:getBookingCancelRequest>
                 <in>
                    <applicationArea>
                       <accessKey>' . $this->accessKey . '</accessKey>
                       <requestID>TRK' . $shipment->tracking_code . '</requestID>
                    </applicationArea>
                    <cancelRequest>
                       <bookingId>' . $shipment->provider_tracking_code . '</bookingId>
                    </cancelRequest>
                 </in>
              </v1:getBookingCancelRequest>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->request($this->url, $xml);
        $response = xml2Arr($response);

        dd($response);
    }


    /**
     * @param $url
     * @param $xml
     * @return mixed
     */
    private function request($url, $xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($xml));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Map array of results
     *
     * @param type $data Array of data
     * @param type $mappingArray
     * @return type
     */
    private function mappingResult($data, $mappingArray)
    {
        $arr = [];

        foreach ($data as $row) {

            if ($mappingArray == 'status') {
                $location  = @$row['ns2OccurredAt'];
                $incidence = @$row['ns2Reason'];
                unset($row['@attributes'], $row['ns2OccurredAt'], $row['ns2Reason']);
                $row['ns2LocationName'] = $location['ns2LocationName'];
                $row['ns2ReasonCode']   = @$incidence['ns2ReasonCode'];
                $row['ns2CodeDscrptn']  = @$incidence['ns2CodeDscrptn'];
            }

            $mappedArr = mapArrayKeys($row, config('webservices_mapping.db_schenker.' . $mappingArray));

            if ($mappingArray == 'status') {
                $mappedArr['created_at'] = $mappedArr['date'] . ' ' . $mappedArr['hour'];
                $arr[] = $mappedArr;
            }
        }

        return $arr;
    }

    /**
     * Return pack type
     * @param $packType
     * @return mixed|string
     */
    private function getPackType($packType)
    {

        $mapping = [
            'box'       => 'BX',
            'envelope'  => 'PK',
            'pallet'    => 'XP',
            'can'       => 'CI',
            'jaricam-5' => 'CI',
            'jaricam-25' => 'CI',
            'barrica'   => 'DR',
            'ibc'       => 'CI',
            'bidon'     => 'CI',
            'box10'     => 'BX',
            'box25'     => 'BX',
            'custom'    => 'ZZ',
            'multiple'  => 'ZZ'
        ];

        return @$mapping[$packType] ? $mapping[$packType] : 'BX';
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment)
    {

        $providerService = null;

        $source = config('app.source');

        $webserviceConfigs = WebserviceConfig::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceConfig::CACHE_TAG)
            ->where('source', $source)
            ->where('method', $shipment->webservice_method)
            ->where('provider_id', @$shipment->provider_id)
            ->first();

        try {

            $serviceKey = $shipment->recipient_country;
            if ($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {

                $mapping = config('shipments_export_mapping.db_schenker-services');
                $providerService = $mapping[$shipment->service->code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço DBSchenker.');
        }

        return $providerService;
    }


    /**
     * Retorna tempos transito
     */
    public function calcTransitTime($params = null)
    {

        // Request
        //https://eschenker.dbschenker.com/app/nges-portal/scheduling/search-scheduling?language_region=es-ES_ES
        $url = 'https://eschenker.dbschenker.com/nges-portal/api/visitor/nges/public/es-ES_ES/resources/scheduling/loadCollectionDeliveryScheduling';

        $pickupDate = str_replace('-', '', @$params['pickup_date']);
        $requestBody = [
            'fromCountry' => strtoupper($params['sender_country']),
            'fromPostCode' => @$params['sender_zip_code'],
            'fromCity' => @params['sender_city'],
            'toCountry' => strtoupper($params['recipient_country']),
            'toPostCode' => @$params['recipient_zip_code'],
            'toCity' => @$params['recipient_city'],
            'collectDeliveryDateString' => $pickupDate,
            'dateType' => 'C',
            'typeMode' => 'S',
            'considerADR' => '0'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: INGRESSCOOKIE=1666261017.478.171.622009|79efcf7f7970192aee0745028f20b4ba'
            ),
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        // --Request

        $response = json_decode($response, true);
        if (!@$response['productSchedule']) {
            return [
                'delivery_date' => null,
                'transit_days' => 0
            ];
        }

        $serviceCode = @explode('#', $params['service'])[1]; // 43, 44, etc...
        foreach ($response['productSchedule'] as $schedule) {
            $validSchedule = false;
            foreach ($schedule['product'] as $product) {
                if ($product['code'] == $serviceCode) {
                    $validSchedule = true; // Schedule has the service that we want
                    break;
                }
            }

            if ($validSchedule) {
                $dates = $schedule['lineDetail'][1]['cellDetail'];
                foreach ($dates as $date) {
                    if ($date['rawDate'] > $pickupDate) {
                        $date = explode('/', $date['date']);
                        $date = $date[2] . '-' . $date[1] . '-' . $date[0];

                        return [
                            'delivery_date' => $date,
                            'transit_days' => 0
                        ];
                    }
                }
            }
        }

        return [
            'delivery_date' => null,
            'transit_days' => 0
        ];
    }
}
