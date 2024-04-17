<?php

namespace App\Models\Webservice;

use App\Models\CustomerWebservice;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class Rangel extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url     = 'https://portaldocliente.correosexpress.pt/services/WEBWEBSHIPPINGservice.svc?wsdl';
    private $host    = 'portaldocliente.correosexpress.pt';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/rangel/';

    /**
     * @var null
     */
    private $user;

    /**
     * @var null
     */
    private $password;

    /**
     * @var null
     */
    private $session_id;

    /**
     * @var null
     */
    private $webservice_id;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * Gls constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        if(config('app.env') == 'local') {
            $this->user       = 'arturgaiola.ws';
            $this->password   = '#arturg#2k19#';
            $this->session_id = 'HsUuMET4Rm5w3MdKHMikzEM74mhRdnA0XeFbYcy40Vn7jndkBweNrJKSoAKqOJzSkmviZaYBzN0+1fz824+B6tXpLbs9jcSLjAyjb6ELqB7nhuYQ3zY9XeM6ncauI+4Q8uXIMJTX76obMQ+nOpGF0RnJ1UX+WueCuWLhOAvzEsYEiQsxRZXwugQiWSNDoZLOpN9URN3yd0U+0LB3zn6T22tvQ+cfUV4HpojYWe4pFuGbVieXKe6/h+fyJlKp1JqzMa6SgtE8l/hFM8Sty+0CJxYb4MZSH9+NgoZuI9G6peeaftKj92163/VB2QzLTL+PIrHqN/kjqkpWZS2WDtqAwi8GLx7SLkuKdxRbCCmIsDt63OHMhgUVIPJdv0nKzxqLUBVbv5G4gwCYDtScnSjSXgOdOQ8ZK1ie76CwRmoQ5h8=';
        } else {
            $this->user       = $user;
            $this->password   = $password;
            $this->session_id = $sessionId;
        }

        $this->debug = $debug;
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

        $url = 'http://webservices.rangel.com/RangelWebServicesPub.asmx?wsdl';
        //$url = 'https://portaldocliente.correosexpress.pt/RangelWebServicesPub.asmx?wsdl';

        $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:web="WebServicesGrupoRangel">
                   <soap:Header/>
                   <soap:Body>
                      <web:RangelPub_getLastTrackingByAWB>
                         <web:in_user>'.$this->user.'</web:in_user>
                         <web:in_Pass>'.$this->password.'</web:in_Pass>
                         <web:in_awb><![CDATA[<awbs><values><awb>'.$trackingCode.'</awb></values></awbs>]]></web:in_awb>
                         <web:in_lang>PRT</web:in_lang>
                      </web:RangelPub_getLastTrackingByAWB>
                   </soap:Body>
                </soap:Envelope>';

        $response = $this->request($url, $xml, "WebServicesGrupoRangel/RangelPub_getLastTrackingByAWB",  "webservices.rangel.com");

        $data = [
            'trk'        => get_string_between($response, '<awb>', '</awb>'),
            'ref'        => get_string_between($response, '<ref>', '</ref>'),
            'date'       => get_string_between($response, '<scandate>', '</scandate>'),
            'status'     => get_string_between($response, '<scancode>', '</scancode>'),
            'status_desc'=> get_string_between($response, '<desc_short>', '</desc_short>'),
            'city'       => get_string_between($response, '<scanlocation>', '</scanlocation>'),
            'country'    => get_string_between($response, '<countrycode>', '</countrycode>'),
            'obs'        => get_string_between($response, '<comments>', '</comments>'),
        ];

        if(@$data['status']) {
            return false;
        }

        $data = $this->mappingResult([$data], 'status');

        /*$data['weight']   = $response->weight;

        if(Setting::get('shipments_round_up_weight')) {
            $data['weight'] = roundUp($data['weight']);
        }*/

        return $data;
    }

    /**
     * Devolve a imagem do POD
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trakingCode
     * @return string
     * @throws \Exception
     */
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode)
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
        return getEstadoEnvioByTrk(null, null, $referencia);
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
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode) {
        return false;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $xml =
            '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://schemas.rangel.com/WEB.WEBSHIPPING.service/" xmlns:web1="http://schemas.rangel.com/WEB.WEBSHIPPING.Shipment_request" xmlns:web2="http://schemas.rangel.com/WEB.WEBSHIPPING.Morada"><soapenv:Header/>
   <soapenv:Body> 
   <web:CreateShipment> 
   <web1:AccessKey>'.$this->session_id.'</web1:AccessKey>
        <web1:LocalExpedicao> 
       	   <web2:Nome>'.$data['sender_name'].'</web2:Nome>
            <web2:CodPostal>'.$data['sender_zip_code'].'</web2:CodPostal>
            <web2:Localidade>'.$data['sender_city'].'</web2:Localidade>
            <web2:Morada>'.$data['sender_address'].'</web2:Morada>
            <web2:NIF></web2:NIF>
            <web2:NomeContacto>'.$data['sender_attn'].'</web2:NomeContacto>
            <web2:Telefone>'.$data['sender_phone'].'</web2:Telefone>
            <web2:Email></web2:Email>
            <web2:CodigoPais>'.strtoupper($data['sender_country']).'</web2:CodigoPais>
         </web1:LocalExpedicao>
         <web1:Destinatario> 
            <web2:Nome>'.$data['recipient_name'].'</web2:Nome>
            <web2:CodPostal>'.$data['recipient_zip_code'].'</web2:CodPostal>
            <web2:Localidade>'.$data['recipient_city'].'</web2:Localidade>
            <web2:Morada>'.$data['recipient_address'].'</web2:Morada>
            <web2:NIF></web2:NIF>
            <web2:NomeContacto>'.$data['recipient_attn'].'</web2:NomeContacto>
            <web2:Telefone>'.$data['recipient_phone'].'</web2:Telefone>
            <web2:Email></web2:Email>
            <web2:CodigoPais>'.strtoupper($data['recipient_country']).'</web2:CodigoPais>
         </web1:Destinatario>
         <web1:TermoPagamentoCode>'.$data['cod'].'</web1:TermoPagamentoCode>
         <web1:ServicoCode>'.$data['serviceCode'].'</web1:ServicoCode>
         <web1:TipoEmbalagem>V</web1:TipoEmbalagem>
         <web1:DevolucaoDocumentos>'.$data['rguide'].'</web1:DevolucaoDocumentos>
         <web1:DataEnvio>'.$data['date'].'</web1:DataEnvio>
         <web1:NumeroVolumes>'.$data['volumes'].'</web1:NumeroVolumes>
         <web1:TotalPesoEnvio>'.$data['weight'].'</web1:TotalPesoEnvio>
         <web1:ValorReembolso>'.$data['charge_price'].'</web1:ValorReembolso>
         <web1:Referencia>'.$data['reference'].'</web1:Referencia>
         <web1:Observacoes>'.$data['obs'].'</web1:Observacoes>
         <web1:PrintEtiquetas> 
            <web1:formato>PDF</web1:formato>
         </web1:PrintEtiquetas>
         <web1:EncerraEnvio>true</web1:EncerraEnvio>
      </web:CreateShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $response = $this->request($this->url, $xml, 'http://schemas.rangel.com/WEB.WEBSHIPPING.service/CreateShipment');

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            $request = $xml;
            file_put_contents (public_path().'/dumper/request.txt', $request);
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }

        $xml = simplexml_load_string($response, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");
        $xml->registerXPathNamespace('node', 'http://schemas.rangel.com/WEB.WEBSHIPPING.service/');
        $result = $xml->xpath("//node:CreateShipmentResponse");
        $result = json_decode(json_encode($result), true);

        if(empty($result)) {

            $result = $xml->xpath("//node:CreateShipmentFault");
            $result = json_decode(json_encode($result), true);

            if(!empty(@$result[0]['ErrorCode'])) {
                throw new \Exception(@$result[0]['ErrorCode']. ' - ' . @$result[0]['ErrorMessages']);
            } else {
                throw new \Exception('Erro de submissão. O conteúdo XML está mal formado.');
            }

        } else {

            $folder = public_path() . $this->upload_directory;
            if (!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            $result = @$result[0];
            $trk    = @$result['EnvioNumero'];
            $guide  = @$result['GuiaTransporteRangel']['FileContent'];
            $labels = @$result['FicheiroEtiquetas']['FileContent'];


            //get transport guide
            if ($guide) {
                $result = File::put(public_path() . $this->upload_directory . $trk . '_guide.txt', $guide);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a guia de transporte.');
                }
            }

            //get labels
            if ($labels) {
                $result = File::put(public_path() . $this->upload_directory . $trk . '_labels.txt', $labels);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta.');
                }
            }

            return $trk;
        }
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeRecolha($data)
    {
        $xml =
            '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://schemas.rangel.com/WEB.WEBSHIPPING.service/" xmlns:web1="http://schemas.rangel.com/WEB.WEBSHIPPING.Pickup_request" xmlns:web2="http://schemas.rangel.com/WEB.WEBSHIPPING.Morada">
<soapenv:Header/>
<soapenv:Body>
  <web:CreatePickup>
     <web1:accessKey>'.$this->session_id.'</web1:accessKey>
     <web1:LocalRecolha>
        <web2:Nome>'.$data['sender_name'].'</web2:Nome>
        <web2:CodPostal>'.$data['sender_zip_code'].'</web2:CodPostal>
        <web2:Localidade>'.$data['sender_city'].'</web2:Localidade>
        <web2:Morada>'.$data['sender_address'].'</web2:Morada>
        <web2:NIF></web2:NIF>
        <web2:NomeContacto>'.$data['sender_attn'].'</web2:NomeContacto>
        <web2:Telefone>'.$data['sender_phone'].'</web2:Telefone>
        <web2:Email></web2:Email>
        <web2:CodigoPais>'.$data['sender_country'].'</web2:CodigoPais>
     </web1:LocalRecolha>
     <web1:LocalDescarga>
        <web2:Nome>'.$data['recipient_name'].'</web2:Nome>
        <web2:CodPostal>'.$data['recipient_zip_code'].'</web2:CodPostal>
        <web2:Localidade>'.$data['recipient_city'].'</web2:Localidade>
        <web2:Morada>'.$data['recipient_address'].'</web2:Morada>
        <web2:NIF></web2:NIF>
        <web2:NomeContacto>'.$data['recipient_attn'].'</web2:NomeContacto>
        <web2:Telefone>'.$data['recipient_phone'].'</web2:Telefone>
        <web2:Email></web2:Email>
        <web2:CodigoPais>'.$data['recipient_country'].'</web2:CodigoPais>
     </web1:LocalDescarga>
     <web1:Referencia>'.$data['reference'].'</web1:Referencia>
     <web1:ServicoCode>'.$data['serviceCode'].'</web1:ServicoCode>
     <web1:TipoVolumes>V</web1:TipoVolumes>
     <web1:NumeroVolumes>'.$data['volumes'].'</web1:NumeroVolumes>
     <web1:TotalPeso>'.$data['weight'].'</web1:TotalPeso>
     <web1:DataRecolha>'.$data['date'].'</web1:DataRecolha>
     <web1:HoraDisponivel>'.$data['start_hour'].'</web1:HoraDisponivel>
     <web1:HoraFecho>'.$data['end_hour'].'</web1:HoraFecho>
     <web1:Observacoes>'.$data['obs'].'</web1:Observacoes>
     <web1:gerarrecolha>'.$data['rec_remota'].'</web1:gerarrecolha>
  </web:CreatePickup>
</soapenv:Body>
</soapenv:Envelope>';

        $response = $this->request($this->url, $xml, 'http://schemas.rangel.com/WEB.WEBSHIPPING.service/CreatePickup');

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            $request = $xml;
            file_put_contents (public_path().'/dumper/request.txt', $request);
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }

        $xml = simplexml_load_string($response, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");
        $xml->registerXPathNamespace('node', 'http://schemas.rangel.com/WEB.WEBSHIPPING.service/');
        $result = $xml->xpath("//node:CreatePickupResponse");
        $result = json_decode(json_encode($result), true);

        if(empty($result)) {

            $result = $xml->xpath("//node:CreatePickupFault");
            $result = json_decode(json_encode($result), true);

            if(!empty(@$result[0]['ErrorCode'])) {
                throw new \Exception(@$result[0]['ErrorCode']. ' - ' . @$result[0]['ErrorMessages']);
            } else {

                $result = $xml->xpath("//s:Fault/faultstring");
                $result = json_decode(json_encode($result), true);

                $error = @$result[0][0];
                $error = str_replace('System.Web.Services.Protocols.SoapException:', '', $error);
                $error = explode(' at ', $error);
                $error = @$error[0];

                throw new \Exception($error ? $error : 'Erro de submissão. O conteúdo XML está mal formado.');
            }

        } else {
            $result = @$result[0];
            $trk    = @$result['numeroRecolha'];
            return $trk;
        }
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
        $file = File::get(public_path().'/uploads/labels/rangel/'.$trackingCode.'_labels.txt');
        return $file;
    }

    /**
     * Permite eliminar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function destroyShipment($trackingCode)
    {
        return true;
    }


    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/


    /**
     * Auth user on API
     *
     * @param type $data
     * @return type
     */
    /*    public function auth()
        {

        }*/


    /**
     * @param $url
     * @param $xml
     * @return mixed
     */
    private function request($url, $xml, $action, $host = null)
    {

        $host = empty($host) ? $this->host : $host;

        $headers = array(
            "Content-type: text/xml;",
            "SOAPAction: " . $action,
            "Content-length: ".strlen($xml),
            "Host: " . $host,
            "Connection: Keep-Alive",
        );

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

        if($data) {

            $webserviceFatorM3 = null;
            $webserviceWeight  = null;

            $weightChanged = true;
            if(@$data['weight']) {
                $webserviceWeight = $data['weight'];
                unset($data['weight']);
                $weightChanged = false;
            }

            $shipmentLinked = false;
            if($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                $date = new Carbon($item['created_at']);

                if(empty( $item['status_id'])) {
                    $item['status_id'] = 31; //aguarda expedição
                }
                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    //'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            //update shipment price
            $weightChanged = ($webserviceWeight > $shipment->weight || $webserviceFatorM3);
            if((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || ($weightChanged && empty($shipment->invoice_id)
                    && empty($shipment->ignore_billing)
                    && empty($shipment->is_blocked))) {

                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                //$agencyId, $serviceId, $customerId, $providerId, $weight, $volumes, $charge = null, $volumeM3 = 0, $fatorM3 = 0, $zone = 'pt'
                $prices = Shipment::calcPrices($shipment);
                $oldPrice = $shipment->total_price;
                $shipment->fator_m3 = $webserviceFatorM3;
                $shipment->volumetric_weight  = $prices['volumetricWeight'];
                $shipment->cost_price  = $prices['cost'];

                if(!$shipment->price_fixed) {
                    $shipment->total_price = $prices['total'];
                }

                //DISCOUNT FROM WALLET THE DIFERENCE OF PRICE
                if(hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                    $diffPrice = $shipment->total_price - $oldPrice;
                    if($diffPrice > 0.00) {
                        try {
                            \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                            $shipment->customer->subWallet($diffPrice);
                        } catch (\Exception $e) {}
                    }
                }

                //update linked shipment
                if($shipmentLinked) {
                    $shipmentLinked->weight = $webserviceWeight > $shipmentLinked->weight ? $webserviceWeight : $shipmentLinked->weight;

                    $prices = Shipment::getPrice(
                        $shipmentLinked->agency_id,
                        $shipmentLinked->service_id,
                        $shipmentLinked->customer_id,
                        $shipmentLinked->provider_id,
                        $shipmentLinked->weight,
                        $shipmentLinked->volumes,
                        $shipmentLinked->charge_price,
                        null,
                        $webserviceFatorM3,
                        Shipment::getBillingCountry($shipmentLinked->sender_country, $shipmentLinked->recipient_country),
                        Shipment::getBillingZipCode($shipmentLinked->sender_zip_code, $shipmentLinked->recipient_zip_code, $shipmentLinked->is_collection),
                        $shipmentLinked->sender_zip_code,
                        $shipmentLinked->recipient_zip_code,
                        $shipmentLinked->sender_country,
                        $shipmentLinked->recipient_country,
                        $shipmentLinked->sender_agency_id,
                        $shipmentLinked->recipient_agency_id
                    );

                    $shipmentLinked->fator_m3 = $webserviceFatorM3;
                    $shipmentLinked->volumetric_weight  = $prices['volumetricWeight'];
                    $shipmentLinked->cost_price  = $prices['cost'];

                    if(!$shipmentLinked->price_fixed) {
                        $shipmentLinked->total_price = $prices['total'];
                    }
                }
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at;
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
    public function saveShipment($shipment, $isCollection = false) {

        $reference = 'TRK'.$shipment->tracking_code;
        $reference.=  $shipment->reference ? ' - '.$shipment->reference : '';

        $shipment = $this->getProviderService();
        try {

            $services = config('shipments_export_mapping.rangel-services');

            $code = $shipment->service->code;
            if($shipment->is_collection) {
                if(in_array($shipment->sender_country, ['pt','es'])) {
                    $code = strtoupper($shipment->sender_country).'#'.$code;
                }
            } else {
                if(in_array($shipment->recipient_country, ['pt','es'])) {
                    $code = strtoupper($shipment->recipient_country).'#'.$code;
                }
            }

            $service = $services[$code];
        } catch (\Exception $e) {
            throw new Exception('O serviço '. $code .' não tem correspondência com nenhum serviço Rangel.');
        }

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return guide
        $returnGuide = '0';
        if($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = '1';
        }


        $recRemota = 'false';
        if($shipment->sender_country != 'pt') {
            $recRemota = 'true';
        }

        $data = [
            "sender_name"     => $shipment->sender_name,
            "sender_zip_code" => $shipment->sender_zip_code,
            "sender_city"     => $shipment->sender_city,
            "sender_address"  => $shipment->sender_address,
            "sender_attn"     => $shipment->sender_attn,
            "sender_phone"    => $shipment->sender_phone,
            "sender_country"  => strtoupper($shipment->sender_country),

            "recipient_name"     => $shipment->recipient_name,
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_city"     => $shipment->recipient_city,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_attn"     => $shipment->recipient_attn,
            "recipient_phone"    => $shipment->recipient_phone,
            "recipient_country"  => strtoupper($shipment->recipient_country),

            "cod"           => $shipment->payment_at_recipiuent ? "Destinatario" : "Expedidor",
            "serviceCode"   => $service,
            "rguide"        => $returnGuide ? 'true' : 'false',
            "date"          => $shipment->date,
            "start_hour"    => $shipment->start_hour ? $shipment->start_hour.':00' : '08:00:00',
            "end_hour"      => $shipment->end_hour ? $shipment->end_hour.':00' : '19:00:00',
            "volumes"       => $shipment->volumes ? $shipment->volumes : '0',
            "weight"        => $shipment->weight ? $shipment->weight : '0',
            "charge_price"  => $shipment->charge_price ? $shipment->charge_price : '0',
            "reference"     => $reference,
            "obs"           => $shipment->obs,
            "rec_remota"    => $recRemota
        ];

        if($shipment->is_collection || $isCollection) {
            return $this->storeRecolha($data);
        }

        return $this->storeEnvio($data);
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

            if(!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.rangel.'.$mappingArray));

            //mapping and process status
            if($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $row['created_at'] = new Carbon($row['date']);

                $status = config('shipments_import_mapping.rangel-status');
                $row['status_id'] = @$status[$row['status']];

                if($row['status_id'] == '9') { //incidencia
                    /*$incidences = config('shipments_import_mapping.ctt-incidences');
                    $row['incidence_id'] = @$incidences[$row['incidence_id']];*/
                }

                if($row['status_id'] == '5') { //incidencia
                    $row['receiver'] = $row['obs'];
                    $row['obs'] = null;
                }

                if (isset($row)) {
                    $arr[] = $row;
                }

            } else {
                $arr = $row;
            }
        }

        return $arr;
    }

    /**
     * Convert a ZPL file to PDF
     */
    public function convertZPL2PDF($zpl, $trk, $volumes) {

        $listFiles = [];
        $curl = curl_init();

        for ($i = 0 ; $i < $volumes ; $i++) {

            curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/".$i."/");
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf"));
            $result = curl_exec($curl);

            $fileData = $result;

            $filepath = public_path() . '/uploads/labels/vasp/' . $trk . '_label_' . $i . '.pdf';
            File::put($filepath, $fileData);

            $listFiles[] = $filepath;
        }

        curl_close($curl);


        /**
         * Merge files
         */
        $pdf = new \LynX39\LaraPdfMerger\PdfManage;
        foreach($listFiles as $filepath) {
            $pdf->addPDF($filepath, 'all');
        }

        /**
         * Save merged file
         */
        $filepath = '/uploads/labels/rangel/' . $this->cliente_id .'_labels.pdf';
        $outputFilepath = public_path() . $filepath;
        $result = base64_encode($pdf->merge('string', $outputFilepath, 'P'));

        if($result) {
            foreach($listFiles as $item) {
                File::delete($item);
            }
        }

        return $result;
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment) {

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
            if($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if(!$providerService) {
                $mapping = config('shipments_export_mapping.nacex-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Nacex.');
        }

        return $providerService;
    }
}