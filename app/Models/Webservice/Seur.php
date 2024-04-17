<?php

namespace App\Models\Webservice;

use App\Models\Agency;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, File, Setting;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class Seur extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $urlCrear    = 'https://wspre.seur.com/webseur/services/WSCrearRecogida?wsdl';
    private $urlPublico  = 'https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/seur/';

    /**
     * @var null
     */
    private $shipperId = null;

    /**
     * @var null
     */
    private $user;

    /**
     * @var null
     */
    private $password;

    /**
     * Gls constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null,  $department=null, $endpoint=null, $debug=false)
    {
        $this->user      =  $user;
        $this->password  =  $password;
        $this->accountNumber = $agencia;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($dateMin, $dateMax, $trakingCode)
    {
        $data = [
            'in0' => 'S', //Tipo expedicao (S=salidas, L=llegadas)
            'in1' => '',
            'in2' => '',
            'in3' => '',
            'in4' => $this->accountNumber,
            'in5' => $dateMin,
            'in6' => $dateMax,
            'in7' => '',
            'in8' => '',
            'in9' => '',
            'in10' => $trakingCode,
            'in11' => '0',
            'in12' => $this->user,
            'in13' => $this->password,
            'in14' => 'N',
        ];

        $url = 'https://ws.seur.com/webseur/services/WSConsultaExpediciones?wsdl';
        $content = $this->execute($url, $data, 'consultaExpedicionesStr');

        $content = @$content['EXPEDICION'];

        if(empty($content)) {
            return false;
        }

        $content = (array) $content;
        $history = (array) @$content['SITUACIONES'];
        $history = @$history['SITUACION'];

        $historyArr = $this->mappingResult($history, 'status');

        $history = [];
        $history['history'] = $historyArr;
        $history['weight'] = @$content['PESO'];
        $history['volumetric_weight'] = @$content['PESO_VOLUMETRICO'];

        return $history;
    }


    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($dateMin, $dateMax, $trakingCode)
    {
        $data = [
            'in0' => '', //Tipo expedicao (S=salidas, L=llegadas)
            'in1' => $trakingCode,
            'in2' => '',
            'in3' => $this->accountNumber,
            'in4' => $dateMin,
            'in5' => $dateMax,
            'in6' => '',
            'in7' => '',
            'in8' => '',
            'in9' => '',
            'in10' => $this->user,
            'in11' => $this->password,
            'in12' => 'N',
        ];

        $url = 'https://ws.seur.com/webseur/services/WSConsultaRecogidas?wsdl';
        $content = $this->execute($url, $data, 'consultaRecogidasStr');

        $content = @$content['RECOGIDA'];

        if(empty($content)) {
            return false;
        }

        $content = (array) $content;
        $history = (array) @$content['SITUACIONES'];
        $history = @$history['SITUACION'];

        $historyArr = $this->mappingResult($history, 'status');

        $history = [];
        $history['history'] = $historyArr;
        $history['weight'] = @$content['PESO'];
        $history['volumetric_weight'] = @$content['PESO_VOLUMETRICO'];

        return $history;
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
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode) {}

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
     * Devolve a lista de povoações associadas ao código postal
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trakingCode
     */
    public function getCities($zipCode, $city = '') {

        $data = [
            'in0' => '', //codigo povoacao
            'in1' => $city, //nome povoacao
            'in2' => $zipCode,
            'in3' => '', //data (Y-m-d)
            'in4' => '', //Incluye sinónimos, Si está informado con ‘S’ devuelve también los sinónimos de población que cumplan los filtros anteriores
            'in5' => $this->user,
            'in6' => $this->password,
        ];

        return $this->execute($this->urlPublico, $data, 'infoPoblacionesCortoStr');
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
     * Permite consultar os dados das recolhas numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getRecolhasByDate($date){
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
        $data = [
            'in0' => 'S', //Tipo expedicao (S=salidas, L=llegadas)
            'in1' => $trakingCode,
            'in2' => '',
            'in3' => '',
            'in4' => $this->accountNumber,
            'in5' => '',
            'in6' => '',
            'in7' => '',
            'in8' => '',
            'in9' => '',
            'in10' => '',
            'in11' => '0',
            'in12' => $this->user,
            'in13' => $this->password,
            'in14' => 'N',
        ];

        $url = 'https://ws.seur.com/webseur/services/WSConsultaExpediciones?wsdl';
        $content = $this->execute($url, $data, 'consultaExpedicionesStr');

        $content = @$content['EXPEDICION'];

        if(empty($content)) {
            return false;
        }

        $content = (array) $content;

        return $content;
    }

    /**
     * Permite consultar uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode) {
        return false;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data, $agency)
    {
        $xml = "<recogida>
            <usuario>".$this->user."</usuario>
            <password>".$this->password."</password>
            <razonSocial>".substr($agency->company, 0, 25)."</razonSocial>
            <nombreEmpresa>".substr($agency->company, 0, 25)."</nombreEmpresa>
            <nombreContactoOrdenante>.</nombreContactoOrdenante>
            <apellidosContactoOrdenante>.</apellidosContactoOrdenante>
            <prefijoTelefonoOrdenante>351</prefijoTelefonoOrdenante>
            <telefonoOrdenante>".$agency->phone."</telefonoOrdenante>
            <prefijoFaxOrdenante />
            <faxOrdenante />
            <nifOrdenante>".$agency->vat."</nifOrdenante>
            <paisNifOrdenante>PT</paisNifOrdenante>
            <mailOrdenante>".$agency->email."</mailOrdenante>
            <tipoViaOrdenante>CL</tipoViaOrdenante>
            <calleOrdenante>".substr($agency->address, 0, 70)."</calleOrdenante>
            <tipoNumeroOrdenante>N.</tipoNumeroOrdenante>
            <numeroOrdenante>0</numeroOrdenante>
            <escaleraOrdenante />
            <pisoOrdenante />
            <puertaOrdenante />
            <codigoPostalOrdenante>".$this->formatZipCode($agency->zip_code)."</codigoPostalOrdenante>
            <poblacionOrdenante>".$agency->city."</poblacionOrdenante>
            <provinciaOrdenante>".$agency->province."</provinciaOrdenante>
            <paisOrdenante>PT</paisOrdenante>
            <diaRecogida>".$data['date_day']."</diaRecogida>
            <mesRecogida>".$data['date_month']."</mesRecogida>
            <anioRecogida>".$data['date_year']."</anioRecogida>
            <servicio>".$data['service']."</servicio>
            <horaMananaDe>".@$data['dtHoraRecIni']."</horaMananaDe>
            <horaMananaA>".@$data['dtHoraRecFin']."</horaMananaA>
            <numeroBultos>".$data['volumes']."</numeroBultos>
            <mercancia>2</mercancia>
            <horaTardeDe>".@$data['dtHoraRecIniTarde']."</horaTardeDe>
            <horaTardeA>".@$data['dtHoraRecFinTarde']."</horaTardeA>
            <tipoPorte>Q</tipoPorte>
            <observaciones>".$data['obs']."</observaciones>
            <tipoAviso>E</tipoAviso>
            <idiomaContactoOrdenante>PT</idiomaContactoOrdenante>
            <razonSocialDestino>".$data['recipient_name']."</razonSocialDestino>
            <nombreContactoDestino>".$data['recipient_attn']."</nombreContactoDestino>
            <apellidosContactoDestino>.</apellidosContactoDestino>
            <telefonoDestino>".$data['recipient_phone']."</telefonoDestino>
            <tipoViaDestino>CL</tipoViaDestino>
            <calleDestino>".$data['recipient_address']."</calleDestino>
            <tipoNumeroDestino>N.</tipoNumeroDestino>
            <numeroDestino>0</numeroDestino>
            <escaleraDestino />
            <pisoDestino />
            <puertaDestino />
            <codigoPostalDestino>".$data['recipient_zip_code']."</codigoPostalDestino>
            <poblacionDestino>".$data['recipient_city']."</poblacionDestino>
            <provinciaDestino>".$data['recipient_province']."</provinciaDestino>
            <paisDestino>".$data['recipient_country']."</paisDestino>
            <prefijoTelefonoDestino>351</prefijoTelefonoDestino>
            <razonSocialOrigen>".$data['sender_name']."</razonSocialOrigen>
            <nombreContactoOrigen>.</nombreContactoOrigen>
            <apellidosContactoOrigen>.</apellidosContactoOrigen>
            <telefonoRecogidaOrigen>".$data['sender_phone']."</telefonoRecogidaOrigen>
            <tipoViaOrigen>CL</tipoViaOrigen>
            <calleOrigen>".$data['sender_address']."</calleOrigen>
            <tipoNumeroOrigen>N.</tipoNumeroOrigen>
            <numeroOrigen>0</numeroOrigen>
            <escaleraOrigen />
            <pisoOrigen />
            <puertaOrigen />
            <codigoPostalOrigen>".$data['sender_zip_code']."</codigoPostalOrigen>
            <poblacionOrigen>".$data['sender_city']."</poblacionOrigen>
            <provinciaOrigen>".$data['sender_province']."</provinciaOrigen>
            <paisOrigen>".$data['sender_country']."</paisOrigen>
            <prefijoTelefonoOrigen>351</prefijoTelefonoOrigen>
            <producto>2</producto>
            <entregaSabado>".$data['sabado']."</entregaSabado>
            <entregaNave>N</entregaNave>
            <tipoEnvio>N</tipoEnvio>
            <valorDeclarado>0.0</valorDeclarado>
            <listaBultos>1;1;1;1;1/</listaBultos>
            <cccOrdenante>".$this->accountNumber."</cccOrdenante>
            <numeroReferencia>".$data['reference']."</numeroReferencia>
            <ultimaRecogidaDia />
            <nifOrigen></nifOrigen>
            <paisNifOrigen></paisNifOrigen>
            <aviso>N</aviso>
            <cccDonde />
            <cccAdonde></cccAdonde>
            <tipoRecogida></tipoRecogida>
        </recogida>";

        $data = [
            'in0' => utf8_encode($xml)
        ];

        $url = 'https://ws.seur.com/webseur/services/WSCrearRecogida?wsdl';

        $response = $this->execute($url, $data, 'crearRecogida');

        $trk = @$response->NUM_RECOGIDA;
        $localizador = @$response->LOCALIZADOR;

        return $trk;
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data, $agency, $labelFormat = 'ZEBRA')
    {

        $xml = "<root><exp>";
        for($i = 1 ; $i<= $data['volumes'] ; $i++) {

            $xml .= "<bulto> 
                <ci>".$agency->vat."</ci>
                <nif>".$agency->vat."</nif>
                <ccc>" . $data['ccc'] . "</ccc>
                <servicio>" . $data['service'] . "</servicio>
                <producto>2</producto>
                <total_bultos>" . $data['volumes'] . "</total_bultos>
                <total_kilos>" . $data['weight'] . "</total_kilos>
                <pesoBulto>" . $data['weight'] . "</pesoBulto>
                <observaciones>" . $data['obs'] . "</observaciones>
                <referencia_expedicion>" . $data['reference'] . "</referencia_expedicion>
                <ref_bulto>" . $data['reference'] . "-" .$i . "</ref_bulto>
                <clavePortes>" . $data['payment_recipient'] . "</clavePortes>
                <claveReembolso>F</claveReembolso>
                <valorReembolso>" . $data['charge_price'] . "</valorReembolso>
                <nombre_consignatario>" . $data['recipient_name'] . "</nombre_consignatario>
                <direccion_consignatario>" . $data['recipient_address'] . "</direccion_consignatario>
                <tipoVia_consignatario>CL</tipoVia_consignatario>
                <tNumVia_consignatario>N.</tNumVia_consignatario>
                <numVia_consignatario>0</numVia_consignatario>
                <escalera_consignatario>.</escalera_consignatario>
                <piso_consignatario>.</piso_consignatario>
                <puerta_consignatario>.</puerta_consignatario>
                <poblacion_consignatario>" . $data['recipient_city'] . "</poblacion_consignatario>
                <codPostal_consignatario>" . $data['recipient_zip_code'] . "</codPostal_consignatario>
                <pais_consignatario>" . $data['recipient_country'] . "</pais_consignatario>
                <email_consignatario></email_consignatario>
                <telefono_consignatario>" . $data['recipient_phone'] . "</telefono_consignatario>
                <atencion_de>" . $data['recipient_attn'] . "</atencion_de>
            </bulto>";
        }
        $xml.= "</exp></root>";

        if($labelFormat == 'ZEBRA') {
            $request = [
                'in0' => 'wsecomm4858',
                'in1' => 'ws4858ecomm',
                'in2' => 'ZEBRA',
                'in3' => 'LP2844-Z',
                'in4' => '1C',
                'in5' => utf8_encode($xml),
                'in6' => 'file.xml',
                'in7' => $agency->vat,
                'in8' => $data['franquicia'],
                'in9' => '-1',
                'in10' => '',
            ];
        } else {
            $request = [
                'in0' => 'wsecomm4858',
                'in1' => 'ws4858ecomm',
                'in2' => utf8_encode($xml),
                'in3' => 'file.pdf',
                'in4' => $agency->vat,
                'in5' => $data['franquicia'],
                'in6' => '-1',
                'in7' => '',
            ];
        }

        $url = 'http://cit.seur.com/CIT-war/services/ImprimirECBWebService?wsdl';
        $result = $this->execute($url, $request, $labelFormat == 'ZEBRA' ? 'impresionIntegracionConECBWS' : 'impresionIntegracionPDFConECBWS');

        if($result->mensaje != 'OK') {
            throw new Exception($result->mensaje);
        } else {
            $refExp = @$result->refExped->string;
            $trk    = @$result->ECB->string;

            if(is_array($trk)) {
                $trk = implode(',', $trk);
            }

            if($labelFormat == 'ZEBRA') {
                $label = @$this->convertZPL2PDF(@$result->traza, $trk, $data['volumes']);
            } else {
                $label = @$result->PDF;
            }

            if(!empty($label)) {
                $result = File::put(public_path() . $this->upload_directory . $trk . '_labels.txt', $label);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta do envio.');
                }
            }

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
        $file = File::get(public_path(). $this->upload_directory . $trackingCode.'_labels.txt');
        return $file;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {
        return true;
        $data = [
            'in0' => 'S', //Tipo expedicao (S=salidas, L=llegadas)
            'in1' => $trackingCode,
            'in2' => $this->user,
            'in3' => $this->password,
        ];

        return $this->execute($this->urlCrear, $data, 'anularRecogida');
    }


    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     * 
     ======================================================*/

    /**
     * @param $url
     * @param (array) $data
     * @return mixed
     */
    private function execute($url, $data, $method)
    {
        $soap = new \SoapClient($url);

        try {
            $response = $soap->{$method}($data);
        } catch (SoapFault $fault) {
            throw new \Exception($fault->faultstring, $fault->faultcode);
        }

        if(in_array($method, ['impresionIntegracionPDFConECBWS', 'impresionIntegracionConECBWS'])) {
            $response = $response->out;
            return $response;
        } else {
            $xml = new \SimpleXMLElement($response->out);
            $xml = (array) $xml;
            unset($xml['@attributes']);
            return $xml;
        }
    }


    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        $date = new Date($shipment->date);
        $dateMin = $date->subDays(2)->format('d-m-Y');
        $dateMax = $date->addDays(20)->format('d-m-Y');

        if($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($dateMin, $dateMax, $shipment->provider_tracking_code);
        } else {
            $data = self::getEstadoEnvioByTrk($dateMin, $dateMax, $shipment->provider_tracking_code);
        }


        $webserviceWeight  = null;
        $receiverSignature = null;

        if($data) {

            if(isset($data['weight'])) {
                $webserviceWeight  = $data['weight'];
            }

            if(isset($data['volumetric_weight']) && $data['volumetric_weight'] > $webserviceWeight) {
                $webserviceWeight  = $data['volumetric_weight'];
            }

            if(isset($data['signature'])) {
                $receiverSignature  = $data['signature'];
            }

            $data = $data['history'];

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }

            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                $date = new Carbon($item['created_at']);
                $item['created_at'] = $date;

                try {
                    if($shipment->is_collection) {
                        $mappingStatus = config('shipments_import_mapping.seur-status');
                        $item['status_id'] = @$mappingStatus[$item['type_code']];
                    } else {
                        $mappingStatus = config('shipments_import_mapping.seur-status');
                        $item['status_id'] = @$mappingStatus[$item['status']];
                    }

                } catch (\Exception $e) {
                    throw new Exception('Não foi encontrado mapeamento para o estado com o código '. $item['status']);
                }


                if(in_array($item['type'], ['I'])) { //incidencias
                    $item['status_id'] = 9;

                    $mappingStatus = config('shipments_import_mapping.seur-incidences');
                    $row['incidence_id'] = @$mappingStatus[$item['status']];
                } else {
                    $item['obs'] = '';
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($data);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $shipment->addPickupFailedExpense();
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            /**
             * Update shipment weight
             */
            if($webserviceWeight > $shipment->weight) {
                $shipment->weight   = $webserviceWeight;

                $tmpShipment = $shipment;
                $prices = Shipment::calcPrices($tmpShipment);

                $shipment->volumetric_weight = @$prices['volumetricWeight'];
                $shipment->total_price  = @$prices['total'];
                $shipment->fuel_tax     = @$prices['fuel_tax'];
                $shipment->cost_price   = @$prices['cost'];
                $shipment->fuel_tax     = @$prices['fuelTax'];
                $shipment->extra_weight = @$prices['extraKg'];
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
            $shipment->save();
            return true;
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

        $service = $this->getProviderService($shipment);

        $reference =  $shipment->reference ? ' - '.$shipment->reference : '';

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $returnPack = 0;
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 1;
        }

        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $sabado = $returnGuide = 0;
        if(!empty($shipmentComplementarServices)) {
            //check service sabado
            if(in_array('sabado', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)) {
                $sabado = 1;
            }

            //return guide
            if(in_array('rguide', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['rguide'], $shipmentComplementarServices)) {
                $returnGuide = 1;
            }
        }


        $date = new Date($shipment->date);
        $date = new Date('2018-08-09');

        //$agency = Agency::findOrFail($shipment->agency_id);
        $agency = Agency::findOrFail(23);

        $senderProvince = $this->getCities($this->formatZipCode($shipment->sender_zip_code), $shipment->sender_city);
        if(isset($senderProvince['REG1'])) {
            $senderProvince = (array) @$senderProvince['REG1'];
            $senderProvince = $senderProvince['NOM_PROVINCIA'];
        } else {
            throw new Exception('Não foi possível encontrar relação entre o código postal e localidade do remetente. Verifique a ortografia.');
        }


        $recipientProvince = $this->getCities($this->formatZipCode($shipment->recipient_zip_code), $shipment->recipient_city);
        if(isset($recipientProvince['REG1'])) {
            $recipientProvince = (array) @$recipientProvince['REG1'];
            $recipientProvince = $recipientProvince['NOM_PROVINCIA'];
        } else {
            throw new Exception('Não foi possível encontrar relação entre o código postal e localidade do destinatário. Verifique a ortografia.');
        }

        $ordenanteProvince = $this->getCities($this->formatZipCode($agency->zip_code), $agency->city);
        if(isset($ordenanteProvince['REG1'])) {
            $ordenanteProvince = (array) @$ordenanteProvince['REG1'];
            $ordenanteProvince = $ordenanteProvince['NOM_PROVINCIA'];
        } else {
            throw new Exception('Não foi possível encontrar relação entre o código postal e localidade da '.config('app.source').'. Verifique a ortografia.');
        }

        $agency->province = $ordenanteProvince;
        $agency->vat = '510923909'; //TESTE

        $accountNumber = explode('-', $this->accountNumber);
        $ccc = $accountNumber[0];
        $franquicia = $accountNumber[1];

        $data = [
            'ccc'                => $ccc,
            'franquicia'         => $franquicia,
            'sender_nif'         => $agency->vat,
            'recipient_nif'      => $agency->vat,
            'date_year'          => $date->year,
            'date_month'         => str_pad($date->month, 2, '0', STR_PAD_LEFT),
            'date_day'           => str_pad($date->day, 2, '0', STR_PAD_LEFT),
            'service'            => $service,
            'customer_code'      => @$shipment->customer->code,
            'sender_name'        => utf8_decode(str_replace('&', 'e', trim($shipment->sender_name))),
            'sender_address'     => utf8_decode(str_replace('&', 'e', trim($shipment->sender_address))),
            'sender_city'        => utf8_decode(str_replace('&', 'e', trim($shipment->sender_city))),
            'sender_zip_code'    => $this->formatZipCode($shipment->sender_zip_code),
            'sender_province'    => $senderProvince,
            'sender_country'     => strtoupper($shipment->sender_country),
            'sender_phone'       => $shipment->sender_phone,
            'recipient_attn'     => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_attn))),
            'recipient_name'     => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_name))),
            'recipient_address'  => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_address))),
            'recipient_city'     => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_city))),
            'recipient_zip_code' => $this->formatZipCode($shipment->recipient_zip_code),
            'recipient_province' => $recipientProvince,
            'recipient_country'  => strtoupper($shipment->recipient_country),
            'recipient_phone'    => $shipment->recipient_phone,
            'volumes'            => $shipment->volumes,
            'weight'             => $shipment->weight,
            'charge_price'       => $shipment->charge_price ? $shipment->charge_price : 0,
            'retorno'            => $returnPack ? 'S' : 'N', //com retorno?
            'obs'                => utf8_decode(str_replace('&', 'e', trim($shipment->obs))),
            'reference'          => 'TRK'.$shipment->tracking_code. $reference,
            'rguide'             => $returnGuide ? 'S' : 'N', //com guia assinada?
            'sabado'             => $sabado ? 'S' : 'N',
            'payment_recipient'  => $isCollection ? ($shipment->payment_at_recipient ? 'D' : 'P') : ($shipment->payment_at_recipient ? 'D' : 'F')
        ];


        if($isCollection) {

            $data['dtHoraRecIniTarde'] = '16:00';
            $data['dtHoraRecFinTarde'] = '19:00';
            $data['dtHoraRecIni'] = '08:00';
            $data['dtHoraRecFin'] = '13:00';
            $data['obs_internal'] = $shipment->obs_internal;

            if(!empty($shipment->start_hour)) {
                $startHourInteger = intval(str_replace(':', '', $shipment->start_hour));

                $startHour = $shipment->date.' ' . $shipment->start_hour.':00';
                $endHour   = $shipment->date.' ' . $shipment->end_hour.':00';

                if($startHourInteger > 1300) {
                    $data['dtHoraRecIniTarde'] = $startHour;
                    $data['dtHoraRecFinTarde'] = $endHour;
                } else {
                    $data['dtHoraRecIni'] = $startHour;
                    $data['dtHoraRecFin'] = $endHour;
                }
            }
        }

        if($isCollection) {
            return $this->storeRecolha($data, $agency);
        } else {
            return $this->storeEnvio($data, $agency);
        }
    }

    /**
     * Format zip codes
     *
     * @param $zipCode
     */
    public function formatZipCode($zipCode) {

        $zip = str_replace(' ', '', trim($zipCode));
        $zip = explode('-', $zip);

        return @$zip[0];
    }

    /**
     * Convert a ZPL file to PDF
     */
    public function convertZPL2PDF($zpl, $trk, $volumes) {

        $listFiles = [];
        $curl = curl_init();

        for ($i = 0 ; $i < $volumes ; $i++) {

            curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/5.5x3.5/".$i."/");
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf", "X-Rotation:90"));
            $result = curl_exec($curl);

            $fileData = $result;

            $folder = public_path() . '/uploads/labels/seur/';
            if(!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            $filepath = $folder . $trk . '_label_' . $i . '.pdf';
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
        $filepath = '/uploads/labels/seur/' . $trk .'_labels.pdf';
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

            $row = mapArrayKeys($row, config('webservices_mapping.seur.'.$mappingArray));

            //mapping and process status
            if($mappingArray == 'status') {
                if (isset($row)) {
                    $arr[] = $row;
                }
            }
        }

        return $arr;
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
                $mapping = config('shipments_export_mapping.seur-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço SEUR.');
        }

        return $providerService;
    }
}