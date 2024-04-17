<?php

namespace App\Models\Webservice;

use App\Models\ZipCode\AgencyZipCode;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\ZipCode;
use Carbon\Carbon;
use Date, Response, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;
use File, DB;

class GlsZeta extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    //private $url = 'http://www.asmred.com/websrvs/b2b.asmx?wsdl';
    private $url = 'https://wsclientes.asmred.com/b2b.asmx?wsdl'; //195.57.17.184
    private $externalEndoint = false; //mudar para true caso se pretenda que os pedidos sejam processados fora do servidor.

    /**
     * @var null
     */
    private $session_id = null;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * GLS constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $cliente = null, $password = null, $sessionId, $department = null, $endpoint = null, $debug = false)
    {
        $this->session_id  = $sessionId;
        $this->debug       = $debug;

        /*\DB::connection('mysql_core')
            ->table('webservice_logs')
            ->insert([
                'source'    => config('app.source'),
                'method'     => 'gls',
                'agency'     => $agencia,
                'user'       => $cliente,
                'password'   => $password,
                'session_id' => $sessionId,
                'url'        => \Request::url(),
                'params'     => empty(\Request::except('_token')) ? null : json_encode(\Request::except('_token')),
                'created_at' => date('Y-m-d H:i:s')
            ]);*/
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        $data = [
            'codigo' => $trakingCode ? $trakingCode : '#emptyTRKerror#',
            'uid'    => $this->session_id
        ];

        $request = $this->buildRequest('GetExpCli', $data);

        $xml = $this->buildXml($request);
        $resultXml = $this->request($this->url, $xml);

        $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");

        if (is_bool($xml)) {
            throw new \Exception('O webservice não devolveu resposta ou falhou a leitura da resposta. O envio pode ter sido submetido mesmo assim.');
        }

        $xml->registerXPathNamespace("asm", "http://www.asmred.com/");


        $bultos = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult/expediciones/exp/detallebultos/bulto");
        $bultos = (array) $bultos;

        $arr = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult/expediciones/exp/tracking_list/tracking");
        $data = (array) $arr;

        $arr = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult/expediciones/exp/digitalizaciones/digitalizacion");
        $images = (array) $arr;
        $data['images'] = $images;

        $data = $this->mappingResult($data, 'status');

        $arr = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult/expediciones/exp");
        $shipment = (array) $arr;

        //sort status by date
        $sort = [];
        foreach ($data as $key => $value) {
            $date = Date::createFromFormat('d/m/Y H:i:s', $value['created_at'])->toDateTimeString();
            $data[$key]['created_at'] = $date;
            $sort[$key] = strtotime($date);
        }

        array_multisort($sort, SORT_ASC, $data);

        if (!empty($shipment)) {
            $data['weight'] = (float) str_replace(',', '.', $shipment[0]->kgs);
            $data['fator_m3'] = (float) str_replace(',', '.', $shipment[0]->vol);
            $data['superior_limit'] = false;

            $zpCode = (string) @$shipment[0]->cp_dst;
            $zpCode = explode('-', $zpCode);
            $zpCode = @$zpCode[0];

            $dimensionsObs = '';

            if(config('app.source') == 'rapidix' || config('app.source') == '2660express') {
                $data['weight'] = 0;
                $data['fator_m3'] = 0;
            }

            //03/05/2023 - chamada telefonica com paulo. tirado do array a tct a pedido do joão andrada que falou com alguem na TCT e pediram para tirar para faturar sempre cubicagem
            $agenciesAuthorized = in_array(config('app.source'), ['postlog', 'tartarugaveloz', 'glsmontijo', 'rapex', 'rimaalbe', 'avatrans', 'transcapital', 'trpexpress', 'lousaestradas', 'perfilinteligente']);
            if (!in_array($zpCode, $this->islandsZipCode()) && $agenciesAuthorized) {
                $superiorLimit = false;
                foreach ($bultos as $bulto) {

                    $bulto = (array)$bulto;

                    $width  = (float)str_replace(',', '.', @$bulto['ancho']);
                    $length = (float)str_replace(',', '.', @$bulto['largo']);
                    $height = (float)str_replace(',', '.', @$bulto['alto']);
                    //$weight = (float) str_replace(',', '.', @$bulto['kilos']);

                    $cmLinear  = $width + $length + $height;
                    //$cmLinear2 = ($height * 2) + ($length * 2) + $width;

                    $senderCountry = @$shipment[0]->codpais_org;
                    $recipientCountry = @$shipment[0]->codpais_dst;
                    $senderCountry = $senderCountry == '351' ? 'pt' : ($senderCountry == '34' ? 'es' : $senderCountry);
                    $recipientCountry = $recipientCountry == '351' ? 'pt' : ($recipientCountry == '34' ? 'es' : $recipientCountry);

                    $dimensionsObs .= "\n[" . $width . "x" . $length . "x" . $height . ']';

                    $zone = Shipment::getBillingZone($senderCountry, $recipientCountry);

                    if ($zone == 'pt' && $cmLinear >= 150) {
                        $superiorLimit = true;
                        $data['superior_limit'] = $cmLinear;
                    } elseif ($zone != 'pt' && $cmLinear >= 100) {
                        $superiorLimit = true;
                        $data['superior_limit'] = $cmLinear;
                    }
                }

                @$data[0]['obs'] .= $dimensionsObs;

                if (!$superiorLimit) {
                    $data['fator_m3'] = 0;
                }
            }

            if (Setting::get('shipments_round_up_weight')) {
                $data['weight'] = roundUp($data['weight']);
            }
        }

        return $data;
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

        $trakingCode = $trakingCode ? $trakingCode : '#emptyTRKerror#';

        //get estado recolha
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:asm="http://www.asmred.com/">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <asm:Tracking>
                         <asm:docIn>
                            <Servicios uidcliente="' . $this->session_id . '">
                                <Recogida codrecogida="' . $trakingCode . '" />
                            </Servicios>
                         </asm:docIn>
                      </asm:Tracking>
                   </soapenv:Body>
                </soapenv:Envelope>';

        $resultXml = $this->request($this->url, $xml);


        $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
        $arr = $xml->xpath("//asm:TrackingResponse/asm:TrackingResult/Servicios/Recogida/Tracking/TrackingCliente");
        $data = (array) $arr;

        $data = $this->mappingResult($data, 'status-collection');


        //sort status by date
        foreach ($data as $key => $value) {
            $date = Date::createFromFormat('d/m/Y H:i:s', $value['created_at'])->toDateTimeString();
            $data[$key]['created_at'] = $date;
            $sort[$key] = strtotime($date);
        }

        array_multisort($sort, SORT_ASC, $data);

        $lastStatus = end($data);

        /*if($lastStatus['status'] == '18') { //18 = recolha falhada. Apaga o envio gerado caso ele exista.
            if($shipment->children_tracking_code) {
                Shipment::where('tracking_code', $shipment->children_tracking_code)->delete();
                $shipment->update([
                    'children_tracking_code' => null,
                    'children_type' => null
                ]);
            }
        } else {*/
        //dd($lastStatus);
        /*//Testa se existe envio gerado desde que o ultimo estado não seja "realizado com incidencia" (não realizada)
            //isto porque a gls gera um envio ficticio se o estado for realizado com incidencia
            if($lastStatus['status'] != '18') { //18 = recolha falhada*/
        $this->createShipmentFromPickup($trakingCode, $shipment);
        /*}*/
        /* }*/

        return $data;
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date)
    {
        return false;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
        return self::getEstadoEnvioByTrk(null, null, $referencia);
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date)
    {
        return false;
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
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
    }

    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {

        $minDate = new Carbon($date);
        $maxDate = new Carbon($date);
        $maxDate = $maxDate->addDay(1);

        $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
                   <soap:Header/>
                   <soap:Body>
                      <asm:GetManifiesto>
                         <asm:docIn>
                            <Servicios uidcliente="' . $this->session_id . '">
                               <FechaDesde>' . $minDate->format('d/m/Y') . '</FechaDesde>
                               <FechaHasta>' . $maxDate->format('d/m/Y') . '</FechaHasta>
                               <CodigoPlazaCliente></CodigoPlazaCliente>
                                <CodigoCliente>144</CodigoCliente>
                            </Servicios>
                         </asm:docIn>
                      </asm:GetManifiesto>
                   </soap:Body>
                </soap:Envelope>';

        $resultXml = $this->request($this->url, $xml);

        $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
        $arr = $xml->xpath("//asm:GetManifiestoResponse/asm:GetManifiestoResult/Servicios/Envios/Envio");
        $data = (array) $arr;

        $data = $this->mappingResult($data, 'status-collection');

        return $data;
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
            'codigo' => $trakingCode,
            'uid'    => $this->session_id
        ];

        $request = $this->buildRequest('GetExpCli', $data);
        $xml = $this->buildXml($request);
        $resultXml = $this->request($this->url, $xml);

        $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
        $arr = $xml->xpath("//asm:GetExpCliResponse/asm:GetExpCliResult/expediciones/exp");

        if ($arr) {
            $data = (array) $arr[0];
            $data = $this->mappingResult([$data], 'shipment');
            return $data[0];
        }

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
    public function createShipmentFromPickup($trakingCode, $originalShipment)
    {
        $data = $this->getEnvioByTrk(null, null, $trakingCode);

        if ($data['volumes'] == 1 && $data['volumes'] > $originalShipment->volumes) {
            $originalShipment->volumes = $data['volumes'];
        }

        if ($data['weight'] == 1.00 && $data['weight'] > $originalShipment->weight) {
            $originalShipment->weight  = $data['weight'];
        }

        $originalShipment->save();

        $trk = null;
        if (!empty($data)) {
            $shipment = Shipment::firstOrNew(['provider_tracking_code' => $data['provider_tracking_code']]);

            if(!$shipment->exists) {
                $date = Date::createFromFormat('d/m/Y h:i:s', $data['date']);
                $date = $date->format('Y-m-d');

                $collectionTrk = explode(' ', $data['reference']);
                $collectionTrk = $collectionTrk[0];
                $collectionTrk = str_replace('TRK', '', $collectionTrk);

                $service  = Service::filterSource()->where('id', $originalShipment->service_id)->first();

                $shipment->fill($data);
                $shipment->recipient_email      = $originalShipment->recipient_email;
                $shipment->charge_price         = $originalShipment->charge_price;
                $shipment->date                 = $date;
                $shipment->customer_id          = $originalShipment->customer_id;
                $shipment->agency_id            = $originalShipment->agency_id;
                $shipment->sender_agency_id     = $originalShipment->sender_agency_id;
                $shipment->recipient_agency_id  = $originalShipment->recipient_agency_id;
                $shipment->type                 = Shipment::TYPE_PICKUP;
                $shipment->parent_tracking_code = $collectionTrk;

                $shipment->provider_id          = $originalShipment->provider_id;
                $shipment->service_id           = @$service->id;
                $shipment->status_id            = $originalShipment->status_id == 18 ? 18 : 15; //rec. falhada ou aguarda sync

                $shipment->webservice_method    = $originalShipment->webservice_method;
                $shipment->submited_at          = Date::now();

                $trk = $shipment->setTrackingCode();
            }

            if ($originalShipment->total_price_after_pickup > 0.00) {
                $shipment->shipping_price = $originalShipment->total_price_after_pickup;
                $shipment->price_fixed    = true;
            }

            if($shipment->charge_price) {
                
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

            //adiciona taxa de cobrança ao envio gerado manualmente - Método antes da versao nova do programa
            /* if($shipment->charge_price) {
                $expense = ShippingExpense::where('type', 'charge')
                    ->first();
                    
                $expenseId = @$expense->id;


                if($expenseId) {
                    $expenseShipment = ShipmentExpense::firstOrNew([
                        'shipment_id' => $shipment->id,
                        'expense_id'  => $expenseId
                    ]);
 
                    $expenseShipment->shipment_id       = $shipment->id;
                    $expenseShipment->expense_id        = $expenseId;
                    $expenseShipment->qty               = 1;
                    $expenseShipment->price             = @$expense->values_arr[$shipment->zone] ? $expense->values_arr[$shipment->zone] : @$expense->values_arr[0];
                    $expenseShipment->subtotal          = @$expense->values_arr[$shipment->zone] ? $expense->values_arr[$shipment->zone] : @$expense->values_arr[0];
                    $expenseShipment->created_by        = null;
                    $expenseShipment->date              = date('Y-m-d');
                    $result = $expenseShipment->save();
                }
            } */


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

            return $trk;
        }

        return false;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment)
    {

        if ($shipment->is_collection) {
            $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:asm="http://www.asmred.com/">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <asm:Anula>
                         <asm:docIn>
                           <Servicios uidcliente="' . $this->session_id . '">
                                <Recogida codigo="' . $shipment->provider_tracking_code . '" />
                            </Servicios>
                         </asm:docIn>
                      </asm:Anula>
                   </soapenv:Body>
                </soapenv:Envelope>';

            $resultXml = $this->request($this->url, $xml);

            $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
            $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
            $arr = $xml->xpath("//asm:AnulaResponse/asm:AnulaResult/Servicios/Recogida/Resultado");
        } else {
            $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:asm="http://www.asmred.com/">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <asm:Anula>
                         <asm:docIn>
                           <Servicios uidcliente="' . $this->session_id . '">
                                <Envio codbarras="' . $shipment->provider_tracking_code . '"/>
                            </Servicios>
                         </asm:docIn>
                      </asm:Anula>
                   </soapenv:Body>
                </soapenv:Envelope>';

            $resultXml = $this->request($this->url, $xml);

            //dd($resultXml);
            $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
            $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
            $arr = $xml->xpath("//asm:AnulaResponse/asm:AnulaResult/Servicios/Envio/Resultado");
        }

        $data = (array) $arr[0];
        $result = @$data['@attributes']['return'];

        if ($result == '0') {
            return true;
        } else {
            $error = $this->getDeleteErrors($result);
            throw new Exception($error);
        }
    }

    /**
     * Grava uma resolução a um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveIncidenceResolution($incidenceResolution, $isCollection = false)
    {

        $mapping = config('shipments_export_mapping.gls_zeta-incidences');
        if ($isCollection) {
            $mapping = config('shipments_export_mapping.gls_zeta-incidences-collection');
        }

        $code = @$mapping[$incidenceResolution->resolution->code];

        $data = [];
        $data['tracking'] = @$incidenceResolution->shipment->provider_tracking_code;
        $data['code']     = $code;
        $data['obs']      = $incidenceResolution->obs;

        return $this->storeIncidenciaResolution($data);
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

        if (empty($this->session_id)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $aduana = '';
        if (!empty($data['incoterm'])) { //quem paga o destinatario
            $aduana = '<Aduanas><Incoterm>' . $data['incoterm'] . '</Incoterm></Aduanas>';
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>
         <soap12:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
             <soap12:Body>
                 <GrabaServicios  xmlns="http://www.asmred.com/">
                 <docIn>
                <Servicios uidcliente="' . $this->session_id . '">
                    <Recogida codrecogida="">
                        <Horarios><Fecha dia="' . $data["fecha"] . '"><Horario desde="' . $data["startHour"] . '" hasta="' . $data["endHour"] . '"/></Fecha></Horarios>
                        <RecogerEn>
                            <ContactOrg>' . removeAccents($data["senderAttn"]) . '</ContactOrg>
                            <Nombre>' . removeAccents($data["nombreOrg"]) . '</Nombre>
                            <Direccion>' . removeAccents($data["direccionOrg"]) . '</Direccion>
                            <Poblacion>' . removeAccents($data["poblacionOrg"]) . '</Poblacion>
                            <Provincia>' . removeAccents($data["poblacionOrg"]) . '</Provincia>
                            <Pais>' . $data["codPaisOrg"] . '</Pais>
                            <CP>' . $data["cpOrg"] . '</CP>
                            <Telefono>' . $data["tfnoOrg"] . '</Telefono>
                            <Movil>' . $data["tfnoOrg"] . '</Movil>
                            <emailDst>' . $data["emailDst"] . '</emailDst>
                            <Observaciones>' . removeAccents($data["observaciones"]) . '</Observaciones>
                        </RecogerEn>
                        <Entregas>
                            <Envio>
                                <Retorno>' . ($data["retorno"] == 'S' ? 1 : 0) . '</Retorno>
                                <FechaPrevistaEntrega></FechaPrevistaEntrega>
                                <Portes>' . $data["portes"] . '</Portes>
                                <Servicio>' . $data["servicio"] . '</Servicio>
                                <Horario>' . $data["horario"] . '</Horario>
                                <Destinatario>
                                    <ATT>' . removeAccents($data["attn"]) . '</ATT>
                                    <Nombre>' . removeAccents($data["nombreDst"]) . '</Nombre>
                                    <Direccion>' . removeAccents($data["direccionDst"]) . '</Direccion>
                                    <Poblacion>' . removeAccents($data["poblacionDst"]) . '</Poblacion>
                                    <Provincia>' . removeAccents($data["poblacionDst"]) . '</Provincia>
                                    <Pais>' . $data["codPaisDst"] . '</Pais>
                                    <CP>' . $data["cpDst"] . '</CP>
                                    <Telefono>' . $data["tfnoDst"] . '</Telefono>
                                    <Movil>' . $data["movil"] . '</Movil>
                                    <emailDst>' . $data["emailDst"] . '</emailDst>
                                    <Observaciones>' . removeAccents($data["observacionesEnv"]) . '</Observaciones>
                                    ' . $aduana . '
                                </Destinatario>
                                <Importes>
                                    <Reembolso>' . $data["reem"] . '</Reembolso> <!-- [optional] Refunt amount, CAD: cash on delivery (when <Portes>=P) -->
                                </Importes>
                            </Envio>
                        </Entregas>
                        <Referencias>
                            <Referencia tipo="C">' . $data["RefC"] . '</Referencia>
                        </Referencias>
                    </Recogida>
                    </Servicios>
                 </docIn>
                 </GrabaServicios>
             </soap12:Body>
         </soap12:Envelope>';


        $response = $this->request($this->url, $xml);


        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            file_put_contents(public_path() . '/dumper/request.txt', $xml);
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }

        if (empty($response)) {
            //O webservice não devolveu nenhuma resposta. Verifique se existem caracteres especiais como + ou &.
            throw new \Exception('Falha na ligação com a rede. Contacte a ENOVO.');
        }


        $xml = simplexml_load_string($response, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace('asm', 'http://www.asmred.com/');
        $arr = $xml->xpath("//asm:GrabaServiciosResponse/asm:GrabaServiciosResult");

        if (empty($arr)) {
            throw new Exception('Sem resposta do webservice. Verifique se o cliente possui o webservice GLS-Zeta bem configurado.');
            return false;
        }
        $ret = $arr[0]->xpath("//Servicios/Recogida");
        $return = $ret[0]->xpath("//Servicios/Recogida/Resultado/@return");

        $errorId  = (string) $return[0];

        if ($errorId) {
            throw new \Exception($this->getShipmentErrors($errorId));
        } else {
            $trk = $ret[0]->xpath("//Servicios/Recogida/@codigo");
            $trk = (array) $trk[0];
            $trk = (string) $trk['@attributes']["codigo"];

            $result = $trk;
        }

        return $result;
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {

        if (empty($this->session_id)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $aduana = '';
        if (!empty($data['incoterm'])) { //quem paga o destinatario
            $aduana = '<Aduanas><Incoterm>' . $data['incoterm'] . '</Incoterm></Aduanas>';
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>
         <soap12:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
             <soap12:Body>
                 <GrabaServicios  xmlns="http://www.asmred.com/">
                 <docIn>
                    <Servicios uidcliente="' . $this->session_id . '" xmlns="http://www.asmred.com/">
                        <Envio codbarras="' . $data["codbarras"] . '">
                           <Fecha>' . $data["fecha"] . '</Fecha> <!-- [mandatory] Shipment date, format DD/MM/YYYY, >= today and usually today -->
                           <Servicio>' . $data["servicio"] . '</Servicio>
                           <Horario>' . $data["horario"] . '</Horario>
                           <Bultos>' . $data["bultos"] . '</Bultos>
                           <Peso>' . $data["peso"] . '</Peso>
                           <Portes>' . $data["portes"] . '</Portes>
                           <Retorno>' . $data["retorno"] . '</Retorno> <!-- [optional] SWAP, collect goods on delivery (0:No, 1:Yes) -->
                           <Pod>' . $data["pod"] . '</Pod> <!-- [optional] RCS, Document in packing list to be returned (N:No, S:Yes) -->
                           <Importes>
                                <Debidos>' . $data["debidos"] . '</Debidos> <!-- [optional] always 0 or missing, ASM decides the price of deliver the shipment -->
                                <Reembolso>' . $data["reem"] . '</Reembolso> <!-- [optional] Refunt amount, CAD: cash on delivery (when <Portes>=P) -->
                           </Importes>
                           <Remite>
                                <Plaza></Plaza> <!-- Agencia origem -->
                                <Nombre>' . removeAccents($data["nombreOrg"]) . '</Nombre>
                                <Direccion>' . removeAccents($data["direccionOrg"]) . '</Direccion>
                                <Poblacion>' . removeAccents($data["poblacionOrg"]) . '</Poblacion>
                                <Pais>' . $data["codPaisOrg"] . '</Pais>
                                <CP>' . $data["cpOrg"] . '</CP>
                                <Departamento>' . $data["depart"] . '</Departamento>
                           </Remite>
                           <Destinatario>
                                <Plaza></Plaza> <!-- Agencia destino -->
                                <ATT>' . removeAccents($data["attn"]) . '</ATT>
                                <Nombre>' . removeAccents($data["nombreDst"]) . '</Nombre>
                                <Direccion>' . removeAccents($data["direccionDst"]) . '</Direccion>
                                <Poblacion>' . removeAccents($data["poblacionDst"]) . '</Poblacion>
                                <Pais>' . $data["codPaisDst"] . '</Pais>
                                <CP>' . $data["cpDst"] . '</CP>
                                <Telefono>' . $data["tfnoDst"] . '</Telefono>
                                <Movil>' . $data["movil"] . '</Movil>
                                <Email>' . $data["emailDst"] . '</Email>
                                <NIF></NIF> <!-- [optional] TAX ID number / VAT -->
                                <Observaciones>' . removeAccents($data["observaciones"]) . '</Observaciones>
                                ' . $aduana . '
                           </Destinatario>
                           <Referencias>
                                <Referencia tipo="C">' . $data["RefC"] . '</Referencia>
                           </Referencias>
                           <DevuelveAdicionales>
                                <Etiqueta tipo="PDF"></Etiqueta> <!-- Shipment label: Format to return, possible values: EPL,DPL,JPG,PNG,PDF -->
                           </DevuelveAdicionales>
                            <Cliente>
                                <Codigo></Codigo> <!-- [optional] Asm Customer Code (when customer have several codes in Asm)-->
                                <Plaza></Plaza>   <!-- [optional] Asm Agency Code -->
                                <Agente></Agente> <!-- [optional] Asm Agent Code -->
                            </Cliente>
                        </Envio>
                    </Servicios>
                    </docIn>
                 </GrabaServicios>
             </soap12:Body>
         </soap12:Envelope>';

        //File::put(public_path().'/uploads/xml/gls_zeta.xml', $xml);

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

        $xml = simplexml_load_string($response, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace('asm', 'http://www.asmred.com/');
        $arr = $xml->xpath("//asm:GrabaServiciosResponse/asm:GrabaServiciosResult");

        if (empty($arr)) {
            throw new Exception('Sem resposta do webservice. Verifique se o cliente possui o webservice GLS-Zeta bem configurado.');
            return false;
        }

        $ret = $arr[0]->xpath("//Servicios/Envio");

        $return = $ret[0]->xpath("//Servicios/Envio/Resultado/@return");
        $errorId  = (string) $return[0];

        if ($errorId) {
            throw new \Exception($this->getShipmentErrors($errorId));
        } else {

            //COD BARRAS
            if ($data["codPaisDst"] == 'es') {
                $trk = $ret[0]->xpath("//Servicios/Envio/@codbarras");
                $trk = (string) $trk[0]["codbarras"];
            } else {
                //UID
                /*$uid = $ret[0]->xpath("//Servicios/Envio/@uid");
                $uid = (string) $uid[0]["uid"];*/

                //COD EXPEDIÇÃO
                /*$trk = $ret[0]->xpath("//Servicios/Envio/@codexp");
                $trk = (string) $trk[0]["codexp"];*/

                //REFERENCIAS
                $refs = @$ret[0]->Referencias->Referencia;
                $refs = (array) $refs;

                $refN = @$refs[1];
                //$refC = @$refs[2];
                //$refG = @$refs[3];

                $trk = $refN;
            }

            $result = $trk;
        }

        return $result;
    }

    /**
     * Permite gravar uma incidencia
     * @param $data
     * @return type
     * @throws \Exception
     */
    public function storeIncidenciaResolution($data)
    {

        if (empty($this->session_id)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:asm="http://www.asmred.com/">
           <soapenv:Header/>
           <soapenv:Body>
              <asm:GrabaSolucion>
                 <asm:uid>' . $this->session_id . '</asm:uid>
                 <asm:codigo>' . $data["tracking"] . '</asm:codigo>
                 <asm:codSolucion>' . $data["code"] . '</asm:codSolucion>
                 <asm:observaciones>' . $data["obs"] . '</asm:observaciones>
              </asm:GrabaSolucion>
           </soapenv:Body>
        </soapenv:Envelope>';

        $response = $this->request($this->url, $xml);

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

        $xml = simplexml_load_string($response, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace('asm', 'http://www.asmred.com/');
        $arr = $xml->xpath("//asm:GrabaSolucionResponse/asm:GrabaSolucionResult");

        if (empty($arr)) {
            throw new Exception('Sem resposta do webservice. Verifique se o cliente possui o webservice GLS-Zeta bem configurado.');
            return false;
        }

        if (empty($arr)) {
            throw new Exception('Sem resposta do webservice. Verifique se o cliente possui o webservice GLS-Zeta bem configurado.');
            return false;
        }

        $arr = (array) $arr[0];
        $arr = $arr[0];

        $arr = str_replace('[', '', $arr);
        $arr = explode(']', $arr);

        if ($arr[0] < 0) {
            throw new \Exception($arr[1]);
        }

        return true;
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
        $data = [
            'codigo' => $trackingCode,
            'tipoEtiqueta' => 'PDF'
        ];

        $request = $this->buildRequest('EtiquetaEnvio', $data);
        $xml    = $this->buildXml($request);
        $resultXml = $this->request($this->url, $xml);

        $result = strpos($resultXml, '<base64Binary>');

        if ($result == false) {
            //Não foi retornada nenhuma etiqueta. O envio poderá não existir ou já se encontrar entregue.
            $result = null;
        } else {
            $xml = simplexml_load_string($resultXml, NULL, NULL, "http://http://www.w3.org/2003/05/soap-envelope");
            $xml->registerXPathNamespace("asm", "http://www.asmred.com/");
            $arr = $xml->xpath("//asm:EtiquetaEnvioResponse/asm:EtiquetaEnvioResult/asm:base64Binary");

            $result = (string) $arr[0];
        }

        return $result;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getAgency($country, $zipCode)
    {
        $data = [
            'codPais' => $country,
            'cp'      => $zipCode
        ];

        $request   = $this->buildRequest('GetPlazaXCP', $data);
        $xml       = $this->buildXml($request);
        $resultXml = $this->request($this->url, $xml);
        $response  = xml2Arr($resultXml);
        $response  = $response['GetPlazaXCPResponse']['GetPlazaXCPResult']['Plaza'];

        $result = [
            'zone'          => @$response['Zona'],
            'agency'        => @$response['Codigo'],
            'name'          => @$response['Nombre'],
            'nemonico'      => @$response['Nemonico'],
            'agency'        => @$response['Codigo'],
            'responsable'   => @$response['Responsable'],
            'phone'         => @$response['Telefono'],
            'email'         => @$response['Mail'],
            'address'       => @$response['Direccion'],
            'city'          => @$response['Poblacion'],
            'zip_code'      => @$response['CodigoPostal'],
            'horary'        => @$response['Horario'],
        ];

        return $result;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * @param $url
     * @param $xml
     * @return mixed
     */
    public function request($url, $xml)
    {
        if($this->externalEndoint) {
            return $this->requestExternal($url, $xml);
        }

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
     * Redireciona os pedidos para outra máquina
     *
     * @param $url
     * @param $xml
     * @return bool|string
     */
    private function requestExternal($url, $xml)
    {
        $urlExternal = 'https://asfaltolargo.pt/endpoint/gls';

        $data = [
            'source' => config('app.source'),
            'xml'    => $xml,
            'url'    => $url,
            'key'    => $this->session_id
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $urlExternal,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * @param $request
     * @return string
     */
    private function buildXml($request)
    {

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                <soap12:Body>
                ' . $request . '
                </soap12:Body>
                </soap12:Envelope>';

        return $xml;
    }

    /**
     * @param $service
     * @param $method
     * @param $parameters
     * @return string
     */
    public static function buildRequest($method, $parameters)
    {
        $res = '<' . $method . ' xmlns="http://www.asmred.com/">';
        foreach ($parameters as $key => $value) {
            $res .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $res .= '</' . $method . '>';
        return $res;
    }


    /**
     * @return array
     */
    public function login()
    {
        return $this->session_id;
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

        $data = (array) $data;
        $arr = [];
        $images = null;
        $imagesAttachment = null;

        if (isset($data['images'])) {
            $images = $data['images'];
            foreach ($images as $key => $image) {
                $url = (string) $image->imagen;
                $imagesAttachment .= '<br/><a href="' . $url . '" target="_blank">Digitalização ' . ($key + 1) . '</a>';
            }
            unset($data['images']);
        }



        foreach ($data as $row) {

            $secounds = '00';

            if (!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.gls_zeta.' . $mappingArray));

            //mapping shipment fields
            if ($mappingArray == 'shipment') {
                $row['sender_country']    = config('webservices_mapping.gls_zeta.convert_country.' . $row['sender_country']);
                $row['recipient_country'] = config('webservices_mapping.gls_zeta.convert_country.' . $row['recipient_country']);

                $row['date']     = $row['date'];
                $row['volumes']  = (int) $row['volumes'];
                $row['weight']   = (float) $row['weight'];
                $row['fator_m3'] = (float) $row['fator_m3'];
            }

            //mapping and process status
            elseif ($mappingArray == 'status') {

                if ($row['type'] == 'ESTADO') {
                    $mappingStatus = config('shipments_import_mapping.gls_zeta-status');
                    $row['status_id'] = $mappingStatus[$row['status_id']];

                    //if status is "entregue", find on other rows by the type "ENTREGA", to attach description
                    if ($row['status_id'] == 5) {
                        foreach ($data as $tmp) {

                            if ((string) $tmp->tipo == 'ENTREGA') {
                                $row['obs'] = (string) $tmp->evento;
                                $row['obs'] .= $imagesAttachment;
                            }
                        }
                    } else {
                        $row['obs'] = '';
                    }
                } elseif ($row['type'] == 'INCIDENCIA') {

                    $mappingStatus = config('shipments_import_mapping.gls_zeta-incidences');

                    $row['incidence_id'] = @$mappingStatus[$row['status_id']] ? @$mappingStatus[$row['status_id']] : 21; //outras incidencias
                    $row['status_id'] = 9; //status = incidence
                    $obs =  explode('--', $row['obs']);
                    $row['obs'] = trim(@$obs[1]);

                    $description = isset($row['description']) ? $row['description'] : '';
                    $row['obs'] = $row['obs'] . ' ' . $description;
                } elseif ($row['type'] == 'RECANALIZACION') {
                    $row['status_id'] = 11; //status = recanalizado
                } elseif ($row['type'] == 'ENTREGA') {
                    unset($row); //destroy this row
                } elseif ($row['type'] == 'SOLUCION') {
                    unset($row); //destroy this row
                } elseif ($row['type'] == 'POD') {
                    unset($row); //destroy this row
                } elseif ($row['type'] == 'URLPARTNER') {
                    unset($row); //destroy this row
                } elseif ($row['type'] == 'SEGUIMIENTO TX') {
                    unset($row); //destroy this row
                } else {
                    unset($row); //destroy this row
                }
            }

            //mapping and process status
            elseif ($mappingArray == 'status-collection') {

                $type = $row['type'];

                if ($type == 'Estado') {
                    $mappingStatus = config('shipments_import_mapping.gls_zeta-collections-status');
                    $row['status_id'] = @$mappingStatus[$row['status']];
                } elseif ($type == 'Incidencia') {
                    $mappingStatus = config('shipments_import_mapping.gls_zeta-incidences-collections');
                    $row['incidence_id'] = @$mappingStatus[$row['status']];
                    $row['status_id'] = 9; //status = incidence

                    $description = isset($row['description']) ? $row['description'] : '';
                    $row['obs'] = $row['obs'] . ' ' . $description;
                } elseif (in_array($type, ['Seguimiento'])) {
                    unset($row); //destroy this row
                }

                if ($type != 'Seguimiento') {

                    if (@$row['status_id'] == 18 || @$row['status_id'] == 19) {
                        $secounds = '30'; //força para que o estado de recolha seja sempre após o estado de incidencia, caso os 2 ocorram na mesma hora
                    }

                    @$row['created_at'] = @$row['created_at'] . ' ' . @$row['hour'] . ':' . $secounds;
                }
            }


            if (isset($row)) {
                $arr[] = $row;
            }
        }

        //cdd($arr);
        return $arr;
    }


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
            $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        }

        $webserviceWeight  = null;
        $webserviceFatorM3 = null;
        $superiorLimit     = @$data['superior_limit'];

        unset($data['superior_limit']);

        /*$shipmentLinked = false;
        if ($shipment->linked_tracking_code) {
            $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
        }*/

        if ($data) {
            // Gets the weight from GLS weighing, if not in array
            if ((isset($data['weight']) || isset($data['fator_m3'])) && !in_array(config('app.source'), ['rapidix'])) {
                $webserviceWeight  = $data['weight'];
                $webserviceFatorM3 = $data['fator_m3'];
            }
            unset($data['weight'], $data['fator_m3']);

            foreach ($data as $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $historyExists = $history->exists; //por causa de nao contabilizar varias vezes recolhas falhadas

                $history->fill($item);
                $history->provider_agency_code = @$item['provider_agency_code'];
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID && $shipment->children_tracking_code) { //apaga envio gerado se existir
                    Shipment::where('tracking_code', $shipment->children_tracking_code)->delete();
                    $shipment->update([
                        'children_tracking_code' => null,
                        'children_type' => null
                    ]);
                }

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID && !$historyExists) {
                    Customer::flushCache(Customer::CACHE_TAG);
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price, true); //discount payment
                }
            }


            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {}

            //cria devolução automática se o envio foi devolvido
            if ($shipment->stauts_id == ShippingStatus::DEVOLVED_ID && empty($shipment->parent_tracking_code)) {
                try {
                    $devolutionShipment = $shipment->createDirectDevolution();

                    $devolutionShipment->update([
                        'provider_tracking_code' => $shipment->provider_tracking_code,
                        'webservice_method'      => $shipment->webservice_method,
                        'submited_at'            => Date::now()
                    ]);

                    $shipment->update([
                        'children_tracking_code' => $devolutionShipment->tracking_code,
                        'children_type'          => Shipment::TYPE_DEVOLUTION,
                        'charge_price'           => null
                    ]);

                } catch (\Exception $e) {
                    throw new Exception('Não foi possível criar a devolução do envio ' . $shipment->tracking_code);
                }
            }

            //update shipment price
            $weightChanged = ($webserviceWeight > $shipment->weight || $webserviceFatorM3 || (!$superiorLimit && !empty($shipment->fator_m3)));

            if (($weightChanged && hasModule('account_wallet') && $shipment->ignore_billing)
                || ($weightChanged && !$shipment->is_blocked && !$shipment->invoice_id)
                || $shipment->stauts_id == ShippingStatus::DEVOLVED_ID) {


                $shipment->weight   = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;
                $shipment->fator_m3 = $webserviceFatorM3;
                $oldPrice = $shipment->billing_total;

                //calcula preços do envio
                $prices = Shipment::calcPrices($shipment);
                $shipment->volumetric_weight = @$prices['parcels']['volumetric_weight'] ? @$prices['parcels']['volumetric_weight'] : $shipment->volumetric_weight;
                if(@$prices['fillable'] && (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id)) {
                    $shipment->fill($prices['fillable']);
                    $shipment->storeExpenses($prices);
                }

                //Se o envio tem uma recolha associada,
                //atualiza-se o peso e preço da recolha
                if($shipment->type == 'P' && $shipment->parent_tracking_code) {

                    $pickup = Shipment::where('tracking_code', 'like', substr($shipment->parent_tracking_code, 0, -2).'%')->first();
                    if($pickup) {
                        $pickup->weight = $shipment->weight;
                        $pickup->fator_m3 = $shipment->fator_m3;

                        $prices = Shipment::calcPrices($pickup);
                        if (@$prices['fillable'] && (!$pickup->price_fixed && !$pickup->is_blocked && !$pickup->invoice_id)) {
                            $pickup->fill($prices['fillable']);
                            $shipment->storeExpenses($prices);
                        }
                        $pickup->save();
                    }
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

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;

            if (config('app.source') === "aveirofast") { // store the real status in the shipment record
                $newestShipmentHistory = ShipmentHistory::select('status_id', 'created_at')->where('shipment_id', $shipment->id)->orderBy('created_at', 'desc')->first();
                $shipment->status_id = $newestShipmentHistory->status_id ?? $history->status_id;
                $shipment->status_date = $newestShipmentHistory->created_at ?? $history->created_at->format('Y-m-d H:i:s');;
            }

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

        $service = $this->getProviderService($shipment);
        $horario = $service['horario'];
        $service = $service['service'];

        $reference =  $shipment->reference ? ' - ' . $shipment->reference : '';

        /*    $shipment->recipient_zip_code = explode('-', $shipment->recipient_zip_code);
            $shipment->recipient_zip_code = str_replace('-', '', $shipment->recipient_zip_code[0]);*/

        $date = new Date($shipment->date);

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return pack
        $returnPack = 'N';
        if ($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 'S';
        }

        //return pack
        $returnGuide = 'N';
        if ($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 'S';
        }

        $incoterm = null;
        if (
            $shipment->incoterm
            || (!$shipment->is_collection && in_array($shipment->recipient_country, ['no', 'is', 'tr', 'ch', 'ba', 'al', 'fo', 'mk', 'va', 'rs', 'cs', 'gb']))
            || ($shipment->is_collection && in_array($shipment->sender_country, ['no', 'is', 'tr', 'ch', 'ba', 'al', 'fo', 'mk', 'va', 'rs', 'cs', 'gb']))
        ) {
            $incoterm = '18';
            if($shipment->incoterm == 'ddp') {
                $incoterm = '18';
            } elseif($shipment->incoterm == 'dap') {
                $incoterm = '20';
            }
        }

        /*$expenses = [];
        if(!empty($shipment->complementar_services)) {
            $expenses = ShippingExpense::whereIn('id', $shipment->complementar_services)
                ->pluck('type', 'id')
                ->toArray();
        }

        $fragil = false;
        if(in_array('fragile', $expenses)) {
            $fragil = 1;
        }

        $sabado = false;
        if(in_array('sabado', $expenses)) {
            $sabado = 1;
        }*/

        $weight = $shipment->weight;

        if (in_array(config('app.source'), ['aveirofast', 'gigantexpress'])) {
            $weight = $weight > 1 ? 1 : $weight;
        } elseif(config('app.source') == '2660express') {
            $weight = '0.5';
        }

        $cpDest = $shipment->recipient_zip_code;
        if ($shipment->recipient_country == 'lv' && str_contains('lv', $shipment->recipient_zip_code)) {
            $cpDest = substr($shipment->recipient_zip_code, 3);
        } else if($shipment->recipient_country == 'lu') {
            $cpDest = str_replace('L-', '', $shipment->recipient_zip_code);
        }

        $cpOrg = $shipment->sender_zip_code;
        if ($shipment->sender_country == 'lv' && str_contains('lv', $shipment->sender_zip_code)) {
            $cpDest = substr($shipment->sender_zip_code, 3);
        } else if($shipment->sender_country == 'lu') {
            $cpDest = str_replace('L-', '', $shipment->sender_zip_code);
        }

        $email = 'noreply@gls-group.com';
        //if (in_array(config('app.source'), ['weexpress', 'morluexpress', 'xkl', 'velozrotina','glsmontijo','2660express', 'gigantexpress', 'rapex', 'trpexpress', 'jpsff'])) { //envia o e-mail direto para o cliente
            $email = $shipment->recipient_email ? $shipment->recipient_email : $email;
        //}

        if (config('app.source') === 'lousaestradas' && $shipment->customer_id == 444) { //envia o e-mail direto para o cliente
            $email = $shipment->recipient_email;
        }

        //dd($cpDest);

        $data = [
            "codbarras"     => "", //cod barras
            "fecha"         => $date->format('d/m/Y'), //$shipment->date,
            "startHour"     => $shipment->start_hour ? $shipment->start_hour : '08:00',
            "endHour"       => $shipment->end_hour ? $shipment->end_hour : '18:00',
            "servicio"      => $service,
            "horario"       => $horario,
            "bultos"        => $shipment->volumes,
            "peso"          => $weight,
            "reem"          => $shipment->charge_price ? forceDecimal($shipment->charge_price) : 0,
            "retorno"       => $returnPack, //com retorno?
            "pod"           => $returnGuide, //com guia assinada?
            "debidos"       => $shipment->cod == 'D' ? $shipment->billing_total : 0,
            "senderAttn"    => $shipment->sender_attn,
            "nombreOrg"     => $shipment->sender_name,
            "direccionOrg"  => $shipment->sender_address,
            "poblacionOrg"  => $shipment->sender_city,
            "codPaisOrg"    => $shipment->sender_country,
            "cpOrg"         => $cpOrg,
            "tfnoOrg"       => $shipment->sender_phone,
            "attn"          => $shipment->recipient_attn,
            "nombreDst"     => $shipment->recipient_name,
            "direccionDst"  => $shipment->recipient_address,
            "poblacionDst"  => $shipment->recipient_city,
            "codPaisDst"    => $shipment->recipient_country,
            "cpDst"         => $cpDest,
            "tfnoDst"       => $shipment->recipient_phone,
            "movil"         => $shipment->recipient_phone,
            "emailDst"      => $email,
            "observaciones" => $shipment->obs,
            "observacionesEnv" => $shipment->obs_delivery,
            "portes"        => $shipment->cod == "D" ? "D" : "P", //P=prepaid / D=cod
            "RefC"          => 'TRK' . $shipment->tracking_code . $reference,
            //"RefO"          => $reference, //referencia cliente
            "depart"        => "", //departamento
            "incoterm"      => $incoterm
        ];

        if (config('app.source') == 'glsmontijo' && in_array($shipment->customer_id, [6855, 11069])) {
            $data['observaciones'] = '[' . $shipment->reference . '] ' . $shipment->obs;
        }

        /**
         * @author Daniel Almeida
         * 
         * Commented at 29/06/2023
         * This code is already done in the base webservice model
         */
        //force sender data to hide on labels
        // if ((Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels')) && !($isCollection || $shipment->is_collection)  && $shipment->customer_id != 18065) {

        //     if (Setting::get('hidden_recipient_on_labels')) {
        //         $data['nombreOrg'] = str_replace('&', 'e', $shipment->agency->company);
        //     }

        //     $data['direccionOrg'] = $shipment->agency->address;
        //     $data['cpOrg']        = $shipment->agency->zip_code;
        //     $data['poblacionOrg'] = $shipment->agency->city;
        //     $data['codPaisOrg']   = $shipment->agency->country;
        //     $data['tfnoOrg']      = $shipment->agency->phone;
        // }
        /**-- */

        if (config('app.source') == 'gigantexpress' && !($isCollection || $shipment->is_collection)) {
            $data['direccionOrg'] = 'Agencia';
            $data['cpOrg']        = '4935';
            $data['poblacionOrg'] = 'Neiva';
            $data['codPaisOrg']   = 'pt';
        }

        if ($isCollection || $shipment->is_collection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
        }
    }

    /**
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = [])
    {
    }

    /**
     * Mapping shipment errors
     *
     * @param $errorId
     * @return mixed
     */
    public function getShipmentErrors($errorId)
    {

        if (Setting::get('app_country') == 'es') {
            $errors = [
                -1 => 'Error de autenticación o no fue posible comunicarse con GLS. Compruebe que los datos de conexión del cliente al webservice son correctos.',
                -3 => 'O código de barras do envio já existe.',
                -33 => 'Erro, Vários motívos',
                -48 => 'El número de bultos debe ser siempre 1.',
                -49 => 'El peso debe ser <= 30Kg',
                -50 => 'Não pode haver RCS (cópia com carimbo de retorno).',
                -51 => 'Não pode haver retorno.',
                -52 => 'El país del destinatario no está incluido en el servicio.',
                -53 => 'A agência não está autorizada a inserir serviços EuroEstandar/EBP.',
                -53 => 'Código do destinatário é o código do ponto ParcelShop, é obrigatório e não está informado.',
                -54 => 'É obrigatório indicar o e-mail do destinatário.',
                -55 => 'Es obligatorio indicar el número de teléfono del destinatario.',
                -69 => 'Impossível Canalizar o envio, código postal do destinatário incorreto.',
                -70 => 'A referência do envio já existe para esta data e cliente.',
                +38 => 'El número de móvil del destinatario no es válido.',
                -88 => 'Compruebe el código postal de origen o destino.',
                -81 => 'Código postal de origen o destino incorrecto o desconocido. Compruebe que es correcto. (-81)',
                -93 => 'Erro 93. Erro desconhecido, contacte a GLS.',
                -94 => 'Erro 94. Erro desconhecido, contacte a GLS.',
                -95 => 'Erro 95. Erro desconhecido, contacte a GLS.',
                -96 => 'Erro 96. Erro desconhecido, contacte a GLS.',
                -97 => 'Portes não podem ser "D", Reembolso não pode ser > 0.',
                -98 => 'Erro 98. Erro desconhecido, contacte a GLS.',
                -99 => 'Erro 99. Erro desconhecido, contacte a GLS.',
                -100 => 'Erro 100. Erro desconhecido, contacte a GLS.',
                -101 => 'Atualização sem alterações (tudo a NULL)',
                -102 => 'Tentativa de atualização de um registo que não existe ou está eliminado.',
                -103 => 'Agência pagadora não encontrada. Verifique se o código postal existe ou está correto.',
                -104 => 'Agência de origem não encontrada. Verifique se o código postal existe ou está correto.',
                -105 => 'La fecha de recogida no es válida',
                -106 => 'Erro, Código de Cliente vazia.',
                -107 => 'Erro, CodCliRed es null (alta).',
                -108 => 'Telefone em falta ou o nome do remetente deve ter pelo menos 3 caractéres.',
                -109 => 'A morada do remetente deve ter pelo menos 3 caractéres.',
                -110 => 'A localidade do remetente deve ter pelo menos 3 caractéres.',
                -111 => 'O código postal do remetente deve ter pelo menos 3 caractéres.',
                -112 => 'Codigo solicitud de la plaza no valido.',
                -113 => 'Não são permitidos códigos postais genéricos.',
                -114 => 'Sólo se permiten intervalos mayor de 2 entre horas.',
                -115 => 'Corrija a hora da recolha. Não é possível fazer recolhas antes das 8h',
                -116 => 'Corrija a hora da recolha. As recolhas só podem ser marcadas para as 22h.',
                -117 => 'los locales solo en la plaza de origen para la web.',
                -118 => 'A referência do cliente está duplicada. O envio já foi criado no Zeta mas não foi possível obter o código do envio.',
                -119 => 'exception, uncontrolled error.',
                -120 => 'Código postal de destino incorrecto',
                -121 => 'Os dados de login do cliente não existem ou foram eliminados. Verifique os dados do conector do cliente.',
                -122 => 'No tiene permisos para grabar a esa plaza cliente',
                -123 => 'Erro 123. Erro desconhecido, contacte a GLS.',
                -124 => 'Erro 124. Erro desconhecido, contacte a GLS.',
                -125 => 'Não se podem solicitar recolhas em dias festivos.',
                -126 => 'É necesário um telefone',
                -127 => 'Erro 127. Erro desconhecido, contacte a GLS',
                -126 => 'É necessário indicar o telefone ou telemóvel.',
                -128 => 'É obrigatório indicar o telefone ou telemóvel.',
                -129 => 'A morada do destinatário deve ter pelo menos 3 caractéres.',
                -130 => 'A localidade do destinatário deve ter pelo menos 3 caractéres.',
                -131 => 'O código postal do destinatário deve ter pelo menos 3 caractéres.',
                -504 => 'O código postal não existe na base de dados da GLS.',
                -676 => 'Erro geral. Verifique se existem espaços nos códigos postais, se preencheu os telefones ou se na data da recolha não é feriado na localidade de recolha.',
                36 => 'Formato errado para o código postal do destinatário',
            ];
        } else {
            $errors = [
                -1 => 'Erro de autenticação ou não foi possível comunicar com a GLS. Verifique se os dados de ligação do cliente ao webservice estão corretos.',
                -3 => 'O código de barras do envio já existe.',
                -33 => 'Erro, Vários motívos',
                -48 => 'O número de volumes deve ser sempre 1.',
                -49 => 'O peso deve ser <= 30Kg',
                -50 => 'Não pode haver RCS (cópia com carimbo de retorno).',
                -51 => 'Não pode haver retorno.',
                -52 => 'O país do destinatário não está incluido no serviço.',
                -53 => 'A agência não está autorizada a inserir serviços EuroEstandar/EBP.',
                -53 => 'Código do destinatário é o código do ponto ParcelShop, é obrigatório e não está informado.',
                -54 => 'É obrigatório indicar o e-mail do destinatário.',
                -55 => 'É obrigatório indicar o telemóvel do destinatário.',
                -69 => 'Impossível Canalizar o envio, código postal do destinatário incorreto.',
                -70 => 'A referência do envio já existe para esta data e cliente.',
                +38 => 'O número de telemóvel do destinatário é inválido.',
                -88 => 'Verifique o código postal de origem ou de destino',
                -81 => 'Código postal de origem ou de destino errado ou desconhecido. Verifique se está correto. (-81)',
                -93 => 'Erro 93. Erro desconhecido, contacte a GLS.',
                -94 => 'Erro 94. Erro desconhecido, contacte a GLS.',
                -95 => 'Erro 95. Erro desconhecido, contacte a GLS.',
                -96 => 'Erro 96. Erro desconhecido, contacte a GLS.',
                -97 => 'Portes não podem ser "D", Reembolso não pode ser > 0.',
                -98 => 'Erro 98. Erro desconhecido, contacte a GLS.',
                -99 => 'Erro 99. Erro desconhecido, contacte a GLS.',
                -100 => 'Erro 100. Erro desconhecido, contacte a GLS.',
                -101 => 'Atualização sem alterações (tudo a NULL)',
                -102 => 'Tentativa de atualização de um registo que não existe ou está eliminado.',
                -103 => 'Agência pagadora não encontrada. Verifique se o código postal existe ou está correto.',
                -104 => 'Agência de origem não encontrada. Verifique se o código postal existe ou está correto.',
                -105 => 'A data da recolha é inválida',
                -106 => 'Erro, Código de Cliente vazia.',
                -107 => 'Erro, CodCliRed es null (alta).',
                -108 => 'Telefone em falta ou o nome do remetente deve ter pelo menos 3 caractéres.',
                -109 => 'A morada do remetente deve ter pelo menos 3 caractéres.',
                -110 => 'A localidade do remetente deve ter pelo menos 3 caractéres.',
                -111 => 'O código postal do remetente deve ter pelo menos 3 caractéres.',
                -112 => 'Codigo solicitud de la plaza no valido.',
                -113 => 'Não são permitidos códigos postais genéricos.',
                -114 => 'Sólo se permiten intervalos mayor de 2 entre horas.',
                -115 => 'Corrija a hora da recolha. Não é possível fazer recolhas antes das 8h',
                -116 => 'Corrija a hora da recolha. As recolhas só podem ser marcadas para as 22h.',
                -117 => 'los locales solo en la plaza de origen para la web.',
                -118 => 'A referência do cliente está duplicada. O envio já foi criado no Zeta mas não foi possível obter o código do envio.',
                -119 => 'exception, uncontrolled error.',
                -120 => 'Codigo postal destino incorrecto ',
                -121 => 'Os dados de login do cliente não existem ou foram eliminados. Verifique os dados do conector do cliente.',
                -122 => 'No tiene permisos para grabar a esa plaza cliente',
                -123 => 'Erro 123. Erro desconhecido, contacte a GLS.',
                -124 => 'Erro 124. Erro desconhecido, contacte a GLS.',
                -125 => 'Não se podem solicitar recolhas em dias festivos.',
                -126 => 'É necesário um telefone',
                -127 => 'Erro 127. Erro desconhecido, contacte a GLS',
                -126 => 'É necessário indicar o telefone ou telemóvel.',
                -128 => 'É obrigatório indicar o telefone ou telemóvel.',
                -129 => 'A morada do destinatário deve ter pelo menos 3 caractéres.',
                -130 => 'A localidade do destinatário deve ter pelo menos 3 caractéres.',
                -131 => 'O código postal do destinatário deve ter pelo menos 3 caractéres.',
                -504 => 'O código postal não existe na base de dados da GLS.',
                -676 => 'Erro geral. Verifique se existem espaços nos códigos postais, se preencheu os telefones ou se na data da recolha não é feriado na localidade de recolha.',
                36 => 'Formato errado para o código postal do destinatário',
            ];
        }

        if (@$errors[$errorId]) {
            return @$errors[$errorId];
        }

        return 'Erro ' . $errorId . '. Erro desconhecido para a ENOVO. Será necessário contactar a GLS.';
    }

    /**
     * Mapping delete shipment errors
     *
     * @param $errorId
     * @return mixed
     */
    public function getDeleteErrors($errorId)
    {

        $errors = [
            '0'  => 'Envio/Recolha anulado com sucesso.',
            '-1' => 'O Envio/Recolha não existe.',
            '-2' => 'O estado do envio/recolha não permite o seu cancelamento.',
            '-3' => 'Não pode cancelar uma recolha quando a data de solicitação é igual à data da recolha.',
            '-4' => 'Não pode cancelar uma recolha de hoje.'
        ];

        return @$errors[$errorId];
    }

    /**
     * @return array
     */
    public function islandsZipCode()
    {
        return [
            "9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225",
            "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385", "9400", "9580", "9500", "9504",
            "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684", "9700", "9701", "9760", "9880", "9800",
            "9804", "9850", "9875", "9930", "9934", "9940", "9944", "9950", "9900", "9901", "9904", "9960", "9970", "9980"
        ];
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment)
    {
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

            $providerService = explode('#', $providerService);
            $horario         = @$providerService[1];
            $providerService = @$providerService[0];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {
                $mapping = config('shipments_export_mapping.gls_zeta-services');
                $providerService = $mapping[$shipment->service->code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->display_code . ' não tem correspondência com nenhum serviço GLS.');
        }

        /*$mapping = config('shipments_export_mapping.gls_zeta-horarios');
        $horario = isset($mapping[$shipment->service->code]) ? $mapping[$shipment->service->code] : 3; //by default use ASM24*/

        return [
            'service' => $providerService,
            'horario' => $horario
        ];
    }
}
