<?php

namespace App\Models\Webservice;

use App\Models\CttDeliveryManifest;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\PickupPoint;
use Carbon\Carbon;
use Date, Response, File, Setting, Auth;
use App\Models\ShipmentHistory;
use LynX39\LaraPdfMerger\PdfManage;
use Mockery\Exception;
use Mpdf\Mpdf;

class Ctt extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $urlTest = 'http://cttexpressows.qa.ctt.pt/CTTEWSPool'; //'https://portal.cttexpresso.pt/webservicecttexpresso';
    private $url     = 'https://portal.cttexpresso.pt/webservicecttexpresso'; //194.65.92.59
 
    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/ctt/';

    /**
     * @var null
     */
    private $session_id = null;

    /**
     * @var null
     */
    private $cliente_hash;

    /**
     * @var null
     */
    private $cliente_id;

    /**
     * @var null
     */
    private $contrato;

    /**
     * @var null
     */
    private $request_id;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * Ctt constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $cliente = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        $this->debug = $debug;

        /*if (config('app.env') == 'local') {
            $this->session_id   = '3e5f2c16-12b9-4160-8158-f7cbcd26d7b6'; //authentication id
            $this->cliente_hash = '254d196b-ede4-4820-9439-bb2246db53cf'; //user id
            $this->request_id   = '051bc535-edf5-40d9-a40d-7d5f8bd155bc';
            $this->cliente_id   = '11838760';
            $this->contrato     = '300193911';
        } else {*/

        $this->session_id   = $sessionId; //AuthenticationID
        $this->cliente_hash = $password; //UserId
        $this->cliente_id   = $cliente;
        $this->contrato     = $agencia;

        /*
        if (config('app.source') == 'tartarugaveloz') {
            $this->session_id   = $sessionId; //AuthenticationID
            $this->cliente_hash = '592d0e6a-a75f-4809-9634-20903d46f845'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '051bc535-edf5-40d9-a40d-7d5f8bd155a9'; //não é informado pelos ctt
        } else if (config('app.source') == 'log24') {
            $this->session_id   = $sessionId;
            $this->cliente_hash = 'afb7c8b4-8b83-4247-9dd3-a59784e371b9'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '051bc535-edf5-40d9-a40d-7d5f8bd155a9'; //não é informado pelos ctt
        } else if (config('app.source') == 'viaxl') {
            $this->session_id   = $sessionId;
            $this->cliente_hash = 'a6985b76-a780-4112-b595-189af1b8ecf7'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '99df6888-56f2-473b-ac0e-86e3e9c90100';
        } else if (config('app.source') == 'nmxtransportes') {
            $this->session_id   = $sessionId;
            $this->cliente_hash = '0adf8dd9-d429-4a59-9af0-bc3468dcfbde'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '25dc0e8d-b3cc-4df7-8581-5c222af40e0d';
        } else if (config('app.source') == 'rlrexpress') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '2671a7f3-9423-4a26-919f-575245949ec9'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = 'f0d1f0f5-c4e7-486a-93d1-4934dd387a8b';
        } else if (config('app.source') == 'morluexpress') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '4cfd8f68-1eba-4e21-a2a3-1fbf73f7a3e4'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = 'f0d1f0f5-c4e7-486a-93d1-4934dd387a8b';
        } else if (config('app.source') == 'entregaki') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '9d2e4646-ec8a-48c5-ad7b-cf5a02fc90dc'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '9d2e4646-ec8a-48c5-ad7b-cf5a02fc90dc';
        } else if (config('app.source') == 'activos24') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '58A90ED8-6C45-4FF8-BF6D-C7542ABFC895'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '58A90ED8-6C45-4FF8-BF6D-C7542ABFC895';
        } else if (config('app.source') == 'aveirofast') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '66519429-ab8d-4f91-b664-2181729b2332'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '66519429-ab8d-4f91-b664-2181729b2332';
        } else if (config('app.source') == 'gigantexpress') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '2d1d63e5-3d5f-4a66-9909-99b27a0e6edf'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '2d1d63e5-3d5f-4a66-9909-99b27a0e6edf';
        } else if (config('app.source') == 'asfaltolargo') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = 'c868f4c5-4ae9-496f-99a9-cb761130f55e'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = 'c868f4c5-4ae9-496f-99a9-cb761130f55e';
        } else if (config('app.source') == 'ship2u') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '5ec3cb59-088a-4dd2-b285-5178071816d4'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '5ec3cb59-088a-4dd2-b285-5178071816d4';
        } else if (config('app.source') == 'mudacarga') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '07c1d3cc-50aa-46de-8d6f-e844c51e15e5'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '07c1d3cc-50aa-46de-8d6f-e844c51e15e5';
        } else if (config('app.source') == 'xkl') {
            $this->session_id   = $sessionId; //authentication id
            $this->cliente_hash = '9df919f1-958a-4455-b581-30e80a080477'; //user id
            $this->cliente_id   = $cliente;
            $this->contrato     = $agencia;
            $this->request_id   = '9df919f1-958a-4455-b581-30e80a080477';
        }

        /* }*/
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
        $url = $this->getUrl('EventosWS');

        $client = new \SoapClient($url);

        $trackingCodes  = explode(',', $trackingCode);
        $trackingCodes  = array_unique($trackingCodes);
        $totalTrackings = count($trackingCodes);
        $totalWeight    = 0;
        $totalWeightVol = 0;
        
        $data = [
            'ID'        => $this->session_id,
            'NObjectos' => $trackingCodes
        ];

        $response = $client->GetEventosObjectos_V3($data);

        if (!empty($response->GetEventosObjectos_V3Result->_erros)) {
            throw new Exception($response->GetEventosObjectos_V3Result->_erros->string);
        } else {

            if (empty((array) $response->GetEventosObjectos_V3Result->_Objectos)) {
                throw new Exception('Sem informação de estados.');
            } else {

                if($totalTrackings == 1) {
                    $object      = $response->GetEventosObjectos_V3Result->_Objectos->DadosObjectos_V3BE;
                    $history     = @$object->_Eventos->DadosEventos_V3BE;
                    $totalWeight = (float) @$history->_PesoReal;
                    $totalWeight = $totalWeight / 1000;
                    $totalWeightVol = (float) @$history->_PesoVolumetrico;
                    
                    $weightObs = '['.$totalWeight.'kg real / '.$totalWeightVol.' kg vol]';
                } else {
                    
                    $objects = $response->GetEventosObjectos_V3Result->_Objectos->DadosObjectos_V3BE;
                    $history = @$objects[0];
                    $history = $history->_Eventos->DadosEventos_V3BE;
    
                    $objectWeights = [];
                    $weightObs = '';
                    foreach($objects as $key => $object) {
                    
                        $trk = $object->_NObjecto;
                        $obj = (array) $object->_Eventos->DadosEventos_V3BE;
                        $totalEvents = count($obj);
                        $lastEvent   = 0; //$totalEvents-1;
                    
                        if($totalEvents > 1) { //quando o array tem +1 resultado
                            $peso    = (float) @$obj[$lastEvent]->_PesoReal;
                            $pesoVol = (float) @$obj[$lastEvent]->_PesoVolumetrico;
                    
                        } else { //se o array de eventos so tem 1 evento
                            $peso    = (float) $obj['_PesoReal'];
                            $pesoVol = (float) $obj['_PesoVolumetrico'];
                        }
                        
                        //converte gramas em kg
                        $peso = $peso / 1000;
                        
                        $totalWeight+= $peso;
                        $totalWeightVol+= $pesoVol;
                        
                        $weightObs = $trk.' ['.$peso.'kg real / '.$pesoVol.' kg vol]';
                        $objectWeights[] = [
                            'trk'  => $trk,
                            'real' => $peso,
                            'volumetric' => $pesoVol
                        ];
                    }
                    
                    //dd($objectWeights);
                    //$totalWeight = $totalWeight > $totalWeightVol ? $totalWeight : $totalWeightVol;
                }

                if (!empty($history)) { //eventos nacionais

                    if (!is_array($history)) {
                        $arr = [];
                        $arr[0] = $history;
                        $history = $arr;
                    }

                    $mappedStatus = $this->mappingResult($history, 'status', $totalWeight, $totalWeightVol);
                } else {

                    //testa eventos espanha
                    $histories = @$object;

                    $mappedStatus = [];
                    foreach ($histories as $history) {
                        $history = @$history->_Eventos->DadosObjectos_V3BE;

                        if (!is_array($history)) {
                            $arr = [];
                            $arr[0] = $history;
                            $history = $arr;
                        }

                        $mappedStatus = array_merge($mappedStatus, $this->mappingResult($history, 'status', $totalWeight, $totalWeightVol));
                    }
                }

                return $mappedStatus;
            }
        }
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
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param string $trakingCode Código da agência de Destino
     * @param \App\Models\Shipment $shipment Código da Agência de Origem
     * @return array
     */
    public function getEstadoRecolhaByTrk($trakingCode, $shipment)
    {
        $url = $this->getUrl('Recolhasws');

        $client = new \SoapClient($url);

        $trackingCodes = explode(',', $trakingCode);
        $trackingCodes = $trackingCodes[0];

        $data = [
            'NumCliente'  => $this->cliente_id,
            'NumContrato' => $this->contrato,
            'ID'          => $this->session_id,
            'NumRecolha'  => $trackingCodes
        ];


        $response = $client->PesquisaRecolhas($data);

        //dd($response);
        /*try {*/
        if (!empty($response->PesquisaRecolhasResult->_erros)) {
            throw new Exception($response->PesquisaRecolhasResult->_erros->string);
        } else {

            if (empty((array) $response->PesquisaRecolhasResult->_recolhas)) {
                throw new Exception('Sem informação de estados.');
            } else {
                $history = (array) $response->PesquisaRecolhasResult->_recolhas->RecolhasListBE;
                $mappedStatus = $this->mappingResult([$history], 'collection-status');

                //Testa se existe envio gerado desde que o ultimo estado não seja "realizado com incidencia" (não realizada)
                if (@$mappedStatus[0]['status_id'] == '14') {
                    $result = $this->storeEnvioByTrk($trakingCode, $shipment);

                    if ($result) {
                        $mappedStatus[] = [
                            "created_at" => date('Y-m-d H:i:s'),
                            "obs"        => "Gerado o Envio TRK" . $result->tracking_code,
                            "status_id"  => "19", //envio gerado
                        ];
                    }
                }

                return $mappedStatus;
            }
        }
        /* } catch (\Exception $e) {
             dd($e->getMessage());
         }*/
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
        return false;
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
     * @param string $trakingCode
     * @param \App\Models\Shipment $originalShipment
     * @return \App\Models\Shipment|null
     */
    public function storeEnvioByTrk($trakingCode, $originalShipment)
    {
        $trakingCode = explode(',', $trakingCode);
        $collectionTrk = @$trakingCode[0];
        $data = $this->getRecolhaByTrk($collectionTrk); //metodo antigo
        $data['provider_tracking_code'] = @$trakingCode[1];

        $cttWeight = (float) $data['weight'];
        $cttWeight = $cttWeight / 1000; //converte o peso de gr para kg

        $originalShipment->volumes = $data['volumes'];
        $originalShipment->weight  = $originalShipment->weight >= $cttWeight ? $originalShipment->weight : $cttWeight;
        $data['weight'] = $originalShipment->weight;

        $shipment = null;
        if (!empty($data)) {
            $shipment = Shipment::firstOrNew(['provider_tracking_code' => $data['provider_tracking_code']]);
            $shipment->fill($data);
            $shipment->date                = $data['date'];
            $shipment->recipient_email     = $originalShipment->recipient_email;
            $shipment->recipient_phone     = $originalShipment->recipient_phone;
            $shipment->charge_price        = $originalShipment->charge_price;
            $shipment->customer_id         = $originalShipment->customer_id;
            $shipment->agency_id           = $originalShipment->agency_id;
            $shipment->sender_agency_id    = $originalShipment->sender_agency_id;
            $shipment->sender_phone        = $originalShipment->sender_phone;
            $shipment->recipient_agency_id = $originalShipment->recipient_agency_id;
            $shipment->provider_id         = $originalShipment->provider_id;
            $shipment->service_id          = @$originalShipment->service_id;
            $shipment->status_id           = 15; //aguarda sync
            $shipment->webservice_method   = $originalShipment->webservice_method;
            $shipment->submited_at         = Date::now();

            $shipment->end_hour   = null;
            $shipment->start_hour = null;

            $shipment->type = 'P';
            $shipment->parent_tracking_code = $originalShipment->tracking_code;
            
            $shipment->setTrackingCode();
            
            if ($originalShipment->total_price_after_pickup > 0.00) {
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
            
            return $shipment;
        }

        // if ($shipment) {
        //     //adiciona taxa de recolha
        //     $shipment->insertOrUpdadePickupExpense($originalShipment); //add expense

        //     $originalShipment->children_type = 'P';
        //     $originalShipment->children_tracking_code = $shipment->tracking_code;
        //     $originalShipment->save();
        //     return $shipment;
        // }

        return null;
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param string $trakingCode Código de Encomenda
     * @return array
     */
    public function getRecolhaByTrk($trakingCode)
    {
        $url = $this->getUrl('Recolhasws');

        $client = new \SoapClient($url);

        $trackingCodes = explode(',', $trakingCode);
        $trackingCodes = $trackingCodes[0];

        $data = [
            'NumCliente'  => $this->cliente_id,
            'NumContrato' => $this->contrato,
            'ID'          => $this->session_id,
            'NumRecolha'  => $trackingCodes
        ];

        $response = $client->GetRecolha($data);


        if (!empty($response->GetRecolhaResult->_erros)) {
            throw new Exception($response->GetRecolhaResult->_erros->string);
        } else {

            if (empty((array) $response->GetRecolhaResult->_recolhas)) {
                throw new Exception('Sem informação de estados.');
            } else {
                $data    = (array) $response->GetRecolhaResult->_recolhas;

                $mappedData = $this->mappingResult([$data], 'collection');

                $mappedData['sender_country']    = strlen($mappedData['sender_zip_code']) == 4 ? 'pt' : 'es';
                $mappedData['recipient_country'] = strlen($mappedData['recipient_zip_code']) == 4 ? 'pt' : 'es';

                //get object tracking codes
                $volumes = $mappedData['volumes'];
                $shipmentTrks = [];
                for ($i = 0; $i <= $volumes; $i++) {
                    if (is_array(@$response->GetRecolhaResult->_recolhas->_Objectos->string)) {
                        $shipmentTrks = $response->GetRecolhaResult->_recolhas->_Objectos->string;
                    } else {
                        $shipmentTrks[] = @$response->GetRecolhaResult->_recolhas->_Objectos->string;
                    }
                }

                $shipmentTrk = implode(',', $shipmentTrks);

                $mappedData['provider_tracking_code'] = $shipmentTrk;

                return $mappedData;
            }
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
        $trackingCode = substr($trackingCode, 0, 13);
        $file = File::get(public_path() . '/uploads/labels/ctt/' . $trackingCode . '_labels.txt');
        return $file;
    }

    /**
     * Devolve a guia de transporte de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getGuiaTransporte($senderAgency, $trackingCode)
    {
        $trackingCode = substr($trackingCode, 0, 13);
        $file = File::get(public_path() . '/uploads/labels/ctt/' . $trackingCode . '_guide.txt');
        return $file;
    }

    /**
     * Devolve a folha de contra-reembolso
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getGuiaReembolso($trackingCode)
    {
        $trackingCode = substr($trackingCode, 0, 13);
        $file = File::get(public_path() . '/uploads/labels/ctt/' . $trackingCode . '_reimbursement.txt');
        return $file;
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

        $url = $this->getUrl('Recolhasws');

        $volumes = $data['volumes'];

        $originalZipCodeSender      = $data['sender_zip_code'];
        $originalZipCodeRecipient   = $data['recipient_zip_code'];
        $data['sender_zip_code']    = explode('-', $data['sender_zip_code']);
        $data['recipient_zip_code'] = explode('-', $data['recipient_zip_code']);

        if ($data['sender_country'] == 'pt' && (empty($data['sender_zip_code'][0]) || empty($data['sender_zip_code'][1]))) {
            $data['sender_zip_code'][1] = '000';
        }

        if ($data['recipient_country'] == 'pt' && (empty($data['recipient_zip_code'][0]) || empty($data['recipient_zip_code'][1]))) {
            $data['recipient_zip_code'][1] = '000';
        }

        if (empty($data['start_hour'])) {
            $data['start_hour'] = '07:00:00';
        }

        if (empty($data['end_hour'])) {
            $data['end_hour'] = '22:00:00';
        }


        $data['weight'] = (float) $data['weight'];
        $data['weight'] = number_format($data['weight'] * 1000, 2, '.', '');

        $client = new \SoapClient($url);

        $data = [
            'NumCliente'  => $this->cliente_id,
            'NumContrato' => $this->contrato,
            'ID'          => $this->session_id,
            'recolhaData' => [
                '_CP3Dest'          => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][1] : '',
                '_CP3Exp'           => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][1] : '',
                '_CP4Dest'          => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][0] : '',
                '_CP4Exp'           => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][0] : '',
                '_CPIntDest'        => $data['recipient_country'] != 'pt' ? $originalZipCodeRecipient : '',
                '_CPIntExp'         => $data['sender_country'] != 'pt' ? $originalZipCodeSender : '',
                '_CodProduto'       => $data['service'],
                '_Contacto'         => removeAccents($data['recipient_name']),
                '_ContactoExp'      => $data['sender_phone'],
                '_Data'             => $data['date'],
                '_Destinatario'     => removeAccents($data['recipient_name']),
                '_Dimensao'         => '',
                '_Email'            => $data['recipient_email'],
                '_Expedidor'        => removeAccents($data['sender_name']),
                '_GuiaTransporte'   => $data['guide_required'] ? '1' : '0',
                '_HoraFim'          => $data['end_hour'],
                '_HoraInicio'       => $data['start_hour'],
                '_LocalidadeDest'   => removeAccents($data['recipient_city']),
                '_LocalidadeExp'    => removeAccents($data['sender_city']),
                '_MoradaDest'       => removeAccents($data['recipient_address']),
                '_MoradaExp'        => removeAccents($data['sender_address']),
                '_ObsObj'           => removeAccents($data['obs']),
                '_PaisDest'         => strtoupper($data['recipient_country']),
                '_PaisExp'          => strtoupper($data['sender_country']),
                '_Peso'             => $data['weight'],
                '_PisoDest'         => '',
                '_PisoExp'          => '',
                '_PortaDest'        => '',
                '_PortaExp'         => '',
                '_QuantObj'         => empty($data['volumes']) ? 1 : $data['volumes'],
                '_RefCliente'       => $data['reference'],
                '_SolicitadaPor'    => $this->cliente_id,
                '_Telefone'         => $data['sender_phone'],
                '_TelefoneDest'     => $data['recipient_phone'],
                '_TelefoneExp'      => $data['sender_phone'],
                '_VariosDest'       => '0',
            ]
        ];

        $response = $client->MarcarRecolha($data);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $requestXml  = print_r($data, true);
            $responseXml = print_r($response, true);
            file_put_contents(public_path() . '/dumper/request.txt', $requestXml);
            file_put_contents(public_path() . '/dumper/response.txt', $responseXml);
        }

        $newDate =  $response->MarcarRecolhaResult->_NovaDataRecolha;

        if (!empty($newDate)) {
            throw new \Exception('A recolha já não pode ser marcada na data indicada. Remarque a recolha para dia ' . $newDate);
        } elseif (!empty((array) $response->MarcarRecolhaResult->_erros)) {
            $error = (array) $response->MarcarRecolhaResult->_erros->string;
            $error = @$error[0];
            throw new Exception($error);
        } else {
            $trk = $response->MarcarRecolhaResult->_IDRecolha;
            return $trk;
        }
    }


    /**
     * Submit a shipment
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function storeEnvio($data, $shipment)
    {
        $url = $this->getUrl('CTTShipmentProviderWS');
        $client = new \SoapClient($url);

        if (empty($this->session_id)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $requestId = uuid();

        $volumes = $data['volumes'];

        $originalZipCodeSender      = $data['sender_zip_code'];
        $originalZipCodeRecipient   = $data['recipient_zip_code'];
        $data['sender_zip_code']    = explode('-', $data['sender_zip_code']);
        $data['recipient_zip_code'] = explode('-', $data['recipient_zip_code']);

        if ($data['sender_country'] == 'pt' && (empty($data['sender_zip_code'][0]) || empty($data['sender_zip_code'][1]))) {
            $data['sender_zip_code'][1] = '000';
        }

        if ($data['recipient_country'] == 'pt' && (empty($data['recipient_zip_code'][0]) || empty($data['recipient_zip_code'][1]))) {
            $data['recipient_zip_code'][1] = '000';
        }

        if (@$data['recipient_phone'][0] == '9') {
            $recipientPhone = '';
            $recipientMobile = @$data['recipient_phone'];
        } else {
            $recipientPhone  = $data['recipient_phone'];
            //$recipientPhone  = str_replace('+', '00', $recipientPhone);
            $recipientMobile = '';
        }

        if ($data['recipient_country'] != 'pt') {
            $recipientPhone  = $data['recipient_phone'];
            $recipientMobile = $data['recipient_phone'];
        }

    /*    //charge price
        $specialServices = [];
        if (!empty($data['charge_price'])) {

            if (config('app.source') == 'activos24' || config('app.source') == 'entregaki') {
                $specialServices[] = [
                    'SpecialServiceType' => 'PostalObject',
                    'Value'              => $data['charge_price']
                ];
            } else {
                $specialServices[] = [
                    'SpecialServiceType' => 'AgainstReimbursement',
                    'Value'              => $data['charge_price']
                ];
            }
        }*/

        //charge price
        $specialServices = [];
        if (!empty($data['charge_price'])) {

           /* if (config('app.source') == 'activos24') {
                $specialServices[] = [
                    'SpecialServiceType' => 'PostalObject',
                    'Value'              => $data['charge_price']
                ];
            } else {*/

                /* if ($data['charge_price'] > 2500 || @$data['return_check']) { //comentado em 6/07/2023 pois fomos informados que os CTT já não têm check nominativo
                    $specialServices[] = [
                        'SpecialServiceType' => 'NominativeCheck',
                        'Value'              => $data['charge_price']
                    ];
                } else { */
                    $specialServices[] = [
                        'SpecialServiceType' => 'AgainstReimbursement',
                        'Value'              => $data['charge_price']
                    ];
                //}
           /* }*/
        }

        //fragil
        if ($data['fragil']) {
            $specialServices[] = ['SpecialServiceType' => 'Fragil'];
        }

        //sabado
        if ($data['sabado']) {
            $specialServices[] = ['SpecialServiceType' => 'Saturday'];
        }

        //sms
        if ((!in_array($data['service'], ['EMSF056.01']) && config('app.config') == 'rlrexpress') || (config('app.config') == 'trpexpress')) {
            $specialServices[] = ['SpecialServiceType' => 'SMS'];
        }

        //return pack
        if ($data['return_pack']) {
            $specialServices[] = ['SpecialServiceType' => 'Back'];
        }

        //return guide
        if ($data['return_guide']) {
            $specialServices[] = [
                'DDA' => [
                    'ShipperInstructions' => ''
                ],
                'SpecialServiceType' => 'ReturnDocumentSigned'
            ];
        }

        //return check
        if ($data['return_check']) {
            $specialServices[] = ['SpecialServiceType' => 'NominativeCheck'];
        }

        //set timewindow
        if ($data['timewindow']) {
            $specialServices[] = [
                'TimeWindow' => [
                    //'DeliveryDate' => '', obrigatorio se timewindow = 7
                    'TimeWindow'   => $data['timewindow']
                ],
                'SpecialServiceType' => 'TimeWindow',
            ];


            /* $specialServices[] = [

                 'TimeWindow' => [
                     //'DeliveryDate' => '', obrigatorio se timewindow = 7
                     'TimeWindow'   => $data['timewindow']
                 ]
             ];*/
        }

        if(in_array(@$data['service'], ['EMSF056.01', 'EMSF057.01'])) { 

            //serviçios para Amanhã e Em Dois dias têm de ter multiple home delivery
            $specialServices[] = [
                'MultipleHomeDelivery' => [
                    'AttemptsNumber'    => '1',
                    'InNonDeliveryCase' => 'PostOfficeNotiffied'
                ],
                'SpecialServiceType' => 'MultipleHomeDelivery',
            ];
        }

        //pickup point
        if($data['pudo_id']){
            $specialServices[] = [
                'SpecialServiceType' => 'DeliveryPoint',
                'DeliveryPoint' => [
                    'Code' => $data['pudo_code'],
                    'Name' => $data['pudo_name'],
                    'Type' => $data['pudo_type'],
                ]
            ];
        }


        $dimensions = $shipment->pack_dimensions->first() ?? [];
        $weight = (int) $data['weight'];
        $weight = $weight ? $weight : 1;
        $shipmentData = [
            'ATCode'         => '123456789',
            'ClientReference' => $data['reference'],
            'DeclaredValue'  => 0,
            'IsDevolution'   => 0,
            'Observations'   => $data['obs'],
            'Quantity'       => $data['volumes'],
            'Weight'         => $weight,
        ];

        if ($data['recipient_country'] != 'pt') {
            if (
                !in_array($data['recipient_country'], ['be', 'bg', 'cz', 'dk', 'de', 'ee', 'ie', 'el', 'es', 'fr', 'hr', 'it', 'cy', 'lv', 'lt', 'lu', 'hu', 'mt', 'nl', 'at', 'al', 'pt', 'ro', 'si', 'sk', 'fi', 'se', 'pl'])
                || @$data['service'] === 'EMSF003.02'
            ) {
                $shipmentData += [
                    'CustomsData' => [
                        'CustomsItemsData'     => [
                            'CustomsItemsData' => [
                                'ItemNumber'     => '1',
                                'Detail'         => @$data['goods_description'] ? @$data['goods_description'] : 'Items diversos',
                                'Quantity'       => $data['volumes'],
                                'Value'          => $data['goods_price'] ?: 0,
                                'Weight'         => $data['weight'],
                                'HarmonizedCode' => '000000',
                                'Currency'       => 'EUR',
                                'OriginCountry'  => $data['sender_country']
                            ]
                        ],
                        'ComercialInvoice'     => $data['at_code'] ? $data['at_code'] : 'Documents Attached',
                        'CustomsTotalItems'    => $data['volumes'],
                        'CustomsTotalValue'    => $data['goods_price'] ?: 0,
                        'CustomsTotalWeight'   => $data['weight'],
                        'NonDeliveryCase'      => 'GiveBack',
                        'SachetDocumentation'  => 1,
                        'VATExportDeclaration' => 1,
                        'VATRate'              => '23'
                    ],
                    'ExportType'  => 'Permanent',
                    'UPUCode'     => 'Others',
                ];

                if (@$data['service'] === 'EMSF003.02') {
                    // é obrigatorio ter dimensões nos CTT
                    $shipmentData['CustomsData'] += [
                        'Height' => $dimensions['height'],
                        'Length' => $dimensions['length'],
                        'Width'  => $dimensions['width'],
                    ];
                }
            } else {
                $shipmentData += [
                    'ExportType'  => 'Permanent',
                    'UPUCode'     => 'Others',
                ];
            }
        }

        $data = [
            'Input' => [
                'AuthenticationID' => $this->session_id,
                'DeliveryNote' => [
                    'ClientId'   => $this->cliente_id,
                    'ContractId' => $this->contrato,
                    'DistributionChannelId' => '99',
                    'ShipmentCTT' => [
                        'ShipmentCTT' => [
                            'HasSenderInformation' => $data['hasSenderInformation'],

                            'ReceiverData' => [
                                'Type'          => 'Receiver',
                                'Address'       => removeAccents($data['recipient_address']),
                                'City'          => removeAccents($data['recipient_city']),
                                'ContactName'   => removeAccents($data['recipient_attn']),
                                'Country'       => strtoupper($data['recipient_country']),
                                'MobilePhone'   => $recipientMobile,
                                'Name'          => removeAccents($data['recipient_name']),
                                'PTZipCode3'    => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][1] : '',
                                'PTZipCode4'    => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][0] : '',
                                'NonPTZipCode'   => $data['recipient_country'] != 'pt' ? $originalZipCodeRecipient : '',
                                'NonPTZipCodeLocation' => $data['recipient_country'] != 'pt' ? $data['recipient_city'] : '',
                                'Phone'         => $recipientPhone,
                                'Email'         => $data['recipient_email']
                            ],

                            'SenderData' => [
                                'Type'          => 'Sender',
                                'Address'       => removeAccents($data['sender_address']),
                                'City'          => removeAccents($data['sender_city']),
                                'ContactName'   => substr(removeAccents($data['sender_name']), 0, 60),
                                'Country'       => strtoupper($data['sender_country']),
                                'MobilePhone'   => '',
                                'Name'          => substr(removeAccents($data['sender_name']), 0, 60),
                                'PTZipCode3'    => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][1] : '',
                                'PTZipCode4'    => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][0] : '',
                                'NonPTZipCode'  => $data['sender_country'] != 'pt' ? $originalZipCodeSender : '',
                                'NonPTZipCodeLocation' => $data['sender_country'] != 'pt' ? $data['sender_country'] : '',
                                'Phone'         => $data['sender_phone'],
                            ],

                            'ShipmentData'    => $shipmentData,
                            'SpecialServices' => $specialServices,
                        ]
                    ],
                    'SubProductId' => $data['service'],
                ],
                'RequestID' => $requestId,
                'UserID'    => $this->cliente_hash
            ]
        ];

        $response = $client->CreateShipment($data);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $requestXml  = print_r($data, true);
            $responseXml = print_r($response, true);
            file_put_contents(public_path() . '/dumper/request.txt', $requestXml);
            file_put_contents(public_path() . '/dumper/response.txt', $responseXml);
        }

        if (!empty($response->CreateShipmentResult->ErrorsList->ErrorData)) {

            if (is_array($response->CreateShipmentResult->ErrorsList->ErrorData)) {
                throw new Exception($response->CreateShipmentResult->ErrorsList->ErrorData[0]->Message);
            } else {
                throw new Exception($response->CreateShipmentResult->ErrorsList->ErrorData->Message);
            }
        } else {

            $folder = public_path() . $this->upload_directory;
            if (!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            $firstTrk = $response->CreateShipmentResult->ShipmentData->ShipmentDataOutput->FirstObject;
            $lastTrk  = $response->CreateShipmentResult->ShipmentData->ShipmentDataOutput->LastObject;

            if ($firstTrk == $lastTrk) {
                $trk = $firstTrk;
            } else {
                $trk = $firstTrk . ',' . $lastTrk;
            }

            $documents = @$response->CreateShipmentResult->ShipmentData->ShipmentDataOutput->DocumentsList->DocumentData;
            $cttLabels = @$response->CreateShipmentResult->ShipmentData->ShipmentDataOutput->LabelList->LabelData;

            $reimbursement = $guide = $labels = null;
            if (!empty($documents)) { //se tem guia de transporte em vez de etiquetas autocolantes

                $documents = !is_array($documents) ? [$documents] : $documents;

                if (!$cttLabels) {
                    if (count($documents) == 3) {
                        $reimbursement = base64_encode($documents[0]->File);
                        $guide  = base64_encode($documents[1]->File);
                        $labels = base64_encode($documents[2]->File);
                    } else if (count($documents) == 2) {
                        $reimbursement = base64_encode($documents[0]->File);
                        $labels = base64_encode($documents[1]->File);
                    } else {
                        $labels = base64_encode($documents[0]->File);
                    }
                } else {
                    $reimbursement = base64_encode($documents[0]->File);
                }
            }

            if ($cttLabels) { //se tem etiquetas autocolantes

                //METODO ORIGINAL. DESCOMENTAR EM CASO DE PROBLEMAS
                /*$zplLabel = $cttLabels->Label;
                $labels = $this->convertZPL2PDF($zplLabel, $trk, $volumes);*/

                //NOVO MÉTODO
                $labels = $cttLabels->Label;
                $allTrks = [];
                File::put(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.txt', $labels);
                $labels = $this->convertLabel2Pdf($trk, $shipment, $allTrks); //a variavel $allTrks é uma variavel passada por referencia, para que no final da execução da função ela tenha o array de todos os trk
                $trk = implode(',', $allTrks);
            }

            //get reimbursement control file
            if ($reimbursement) {
                $result = File::put(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_reimbursement.txt', $reimbursement);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }
            }

            //get transport guide
            if ($guide) {
                $result = File::put(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_guide.txt', $guide);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a guia de transporte.');
                }
            }

            //get labels
            if ($labels) {
                $result = File::put(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.txt', $labels);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta.');
                }
            }

            //junta a guia de transporte 1 e as restantes guias no mesmo ficheiro caso seja um envio multiplo
            //ignora o processo se forem etiquetas
            if (!empty($documents) && $volumes > 1) {

                if ($guide) {
                    $doc1Pdf = public_path() . $this->upload_directory . substr($trk, 0, 13) . '_guide.pdf';
                    $doc2Pdf = public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.pdf';

                    $doc1 = base64_decode(File::get(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_guide.txt'));
                    $doc2 = base64_decode(File::get(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.txt'));
                } else {
                    $doc1Pdf = public_path() . $this->upload_directory . substr($trk, 0, 13) . '_reimbursement.pdf';
                    $doc2Pdf = public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.pdf';

                    try {
                        $doc1 = base64_decode(File::get(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_reimbursement.txt'));
                    } catch (\Exception $e) {
                        $doc1 = null;
                    }

                    $doc2 = base64_decode(File::get(public_path() . $this->upload_directory . substr($trk, 0, 13) . '_labels.txt'));
                }

                if ($doc1) {
                    File::put($doc1Pdf, $doc1);
                }

                File::put($doc2Pdf, $doc2);


                // Merge files
                $pdf = new PdfManage();
                if ($guide) {
                    $pdf->addPDF(public_path() . $this->upload_directory . $trk . '_guide.pdf', 'all');
                } else {
                    if (File::exists(public_path() . $this->upload_directory . $trk . '_reimbursement.pdf')) {
                        $pdf->addPDF(public_path() . $this->upload_directory . $trk . '_reimbursement.pdf', 'all');
                    }
                }

                $pdf->addPDF(public_path() . $this->upload_directory . $trk . '_labels.pdf', 'all', 'L');

                // Save Merged Files
                $outputFilepath = public_path() . $this->upload_directory . $trk . '_labels.txt';
                $fileContent = $pdf->merge('string'); //return string
                File::put($outputFilepath, base64_encode($fileContent));

                File::delete([$doc1Pdf, $doc2Pdf]);
            }


            return $trk;
        }
    }


    /**
     * Submit a shipment
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function storeEnvioComRecolha($data, $shipment)
    {
        $url = $this->getUrl('Recolhasws');
        $client = new \SoapClient($url);

        if (empty($this->session_id)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $volumes = $data['volumes'];

        $originalZipCodeSender      = $data['sender_zip_code'];
        $originalZipCodeRecipient   = $data['recipient_zip_code'];
        $data['sender_zip_code']    = explode('-', $data['sender_zip_code']);
        $data['recipient_zip_code'] = explode('-', $data['recipient_zip_code']);

        if ($data['sender_country'] == 'pt' && (empty($data['sender_zip_code'][0]) || empty($data['sender_zip_code'][1]))) {
            $data['sender_zip_code'][1] = '000';
        }

        if ($data['recipient_country'] == 'pt' && (empty($data['recipient_zip_code'][0]) || empty($data['recipient_zip_code'][1]))) {
            $data['recipient_zip_code'][1] = '000';
        }


        $phone = '210000000';
        if (config('app.source') == 'aveirofast') {
            $phone = '234343081';
        }

        $senderPhone = '';
        if (@$data['sender_phone'][0] == '9') {
            $senderPhone  = substr($data['sender_phone'], 0, 9);
            $senderMobile = $senderPhone;
        } elseif(@$data['sender_phone'][0] == '+') {
            $senderPhone  = '';
            $senderMobile = @$data['sender_phone'];
        } else {
            $senderPhone  = $data['sender_phone'];
            $senderMobile = '';
        }

        if ($data['sender_country'] != 'pt') {
            $senderPhone  = $data['sender_phone'];
            $senderMobile = $data['sender_phone'];
        }

        $recipientPhone = '';
        if (@$data['recipient_phone'][0] == '9') {
            $recipientPhone = '';
            $recipientMobile = substr(@$data['recipient_phone'], 0, 9);
        } elseif(@$data['recipient_phone'][0] == '+') {
            $recipientPhone  = '';
            $recipientMobile = @$data['recipient_phone'];
        } else {
            $recipientPhone  = $data['recipient_phone'];
            $recipientMobile = '';
        }

        if ($data['recipient_country'] != 'pt') {
            $recipientPhone  = $data['recipient_phone'];
            $recipientMobile = $data['recipient_phone'];
        }

        //charge price
        $specialServices = [];
        if (!empty($data['charge_price'])) {
            $specialServices[] = [
                'SpecialServiceType' => 'AgainstReimbursement',
                'Value'              => (float)$data['charge_price']
            ];
        }

        //fragil
        if ($data['fragil']) {
            $specialServices[] = ['SpecialServiceType' => 'Fragil'];
        }

        //sabado
        if ($data['sabado']) {
            $specialServices[] = ['SpecialServiceType' => 'Saturday'];
        }

        //sms
        //$specialServices[] = ['SpecialServiceType' => 'SMS'];

        //return pacl
        if ($data['return_pack']) {
            $specialServices[] = ['SpecialServiceType' => 'Back'];
        }

        //return guide
        if ($data['return_guide']) {
            $specialServices[] = ['SpecialServiceType' => 'ReturnDocumentSigned'];
        }

        //return check
        if ($data['return_check']) {
            $specialServices[] = ['SpecialServiceType' => 'NominativeCheck'];
        }

        //set timewindow
        /*if($data['timewindow']) {
            $specialServices[] = [
                'TimeWindow' => [
                    //'DeliveryDate' => '', obrigatorio se timewindow = 7
                    'TimeWindow'   => $data['timewindow']
                ]
            ];
        }*/

        $dimensions = $shipment->pack_dimensions->first() ?? [];
        $weight = (int) $data['weight'];
        $weight = $weight ? $weight : 1;
        $shipmentData = [
            'ATCode'         => '123456789',
            'ClientReference' => $data['reference'],
            'DeclaredValue'  => 0,
            'IsDevolution'   => 0,
            'Observations'   => $data['obs'],
            'Quantity'       => $data['volumes'],
            'Weight'         => (int) $weight,
        ];

        if ($data['recipient_country'] != 'pt') {
            if (
                !in_array($data['recipient_country'], ['be', 'bg', 'cz', 'dk', 'de', 'ee', 'ie', 'el', 'es', 'fr', 'hr', 'it', 'cy', 'lv', 'lt', 'lu', 'hu', 'mt', 'nl', 'at', 'al', 'pt', 'ro', 'si', 'sk', 'fi', 'se'])
                || @$data['service'] === 'EMSF003.02'
            ) {
                $shipmentData += [
                    'CustomsData' => [
                        'CustomsItemsData'     => [
                            'CustomsItemsData' => [
                                'ItemNumber'     => '1',
                                'Detail'         => $data['goods_description'] ?? 'Items diversos',
                                'Quantity'       => $data['volumes'],
                                'Value'          => $data['goods_price'] ?: 0,
                                'Weight'         => $data['weight'],
                                'HarmonizedCode' => '000000',
                                'Currency'       => 'EUR',
                                'OriginCountry'  => $data['sender_country']
                            ]
                        ],
                        'CustomsTotalItems'    => $data['volumes'],
                        'CustomsTotalValue'    => $data['goods_price'],
                        'CustomsTotalWeight'   => $data['weight'],
                        'NonDeliveryCase'      => 'GiveBack',
                        'SachetDocumentation'  => 1,
                        'VATExportDeclaration' => 1,
                        'VATRate'              => '23'
                    ],
                    'ExportType'  => 'Permanent',
                    'UPUCode'     => 'Others',
                ];

                if (@$data['service'] === 'EMSF003.02') {
                    // é obrigatorio ter dimensões nos CTT
                    $shipmentData['CustomsData'] += [
                        'Height' => $dimensions['height'],
                        'Length' => $dimensions['length'],
                        'Width'  => $dimensions['width'],
                    ];
                }
            } else {
                $shipmentData += [
                    'ExportType'  => 'Permanent',
                    'UPUCode'     => 'Others',
                ];
            }
        }

        $data = [
            'Input' => [
                'AuthenticationID' => $this->session_id,
                'DeliveryNotes' => [
                    'DeliveryNote' => [
                        'ClientId'   => $this->cliente_id,
                        'ContractId' => $this->contrato,
                        'DistributionChannelId' => '99',
                        'ShipmentCTT' => [
                            'ShipmentCTT' => [
                                'HasSenderInformation' => $data['hasSenderInformation'],
                                'ReceiverData' => [
                                    'Type'          => 'Receiver',
                                    'Address'       => removeAccents($data['recipient_address']),
                                    'City'          => removeAccents($data['recipient_city']),
                                    'ContactName'   => removeAccents($data['recipient_attn']),
                                    'Country'       => strtoupper($data['recipient_country']),
                                    'MobilePhone'   => $recipientMobile,
                                    'Name'          => removeAccents($data['recipient_name']),
                                    'PTZipCode3'    => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][1] : '',
                                    'PTZipCode4'    => $data['recipient_country'] == 'pt' ? @$data['recipient_zip_code'][0] : '',
                                    'NonPTZipCode'   => $data['recipient_country'] != 'pt' ? $originalZipCodeRecipient : '',
                                    'NonPTZipCodeLocation' => $data['recipient_country'] != 'pt' ? $data['recipient_city'] : '',
                                    'Phone'         => $recipientPhone,
                                    'Email'         => $data['recipient_email']
                                ],

                                'SenderData' => [
                                    'Type'          => 'Sender',
                                    'Address'       => removeAccents($data['sender_address']),
                                    'City'          => removeAccents($data['sender_city']),
                                    'ContactName'   => removeAccents($data['sender_name']),
                                    'Country'       => strtoupper($data['sender_country']),
                                    'MobilePhone'   => $senderMobile,
                                    'Name'          => removeAccents($data['sender_name']),
                                    'PTZipCode3'    => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][1] : '',
                                    'PTZipCode4'    => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][0] : '',
                                    'NonPTZipCode'  => $data['sender_country'] != 'pt' ? $data['sender_zip_code'][0] : '',
                                    'NonPTZipCodeLocation' => $data['sender_country'] != 'pt' ? $data['sender_country'] : '',
                                    'Phone'         => $senderPhone,
                                ],

                                'ShipmentData'    => $shipmentData,
                                'SpecialServices' => $specialServices,
                            ]
                        ],
                        'SubProductId' => $data['service'],
                    ],
                ],
                'PickUpModel' => [
                    'BiggerObjectHeight' => '1',
                    'BiggerObjectLenght' => '1',
                    'BiggerObjectWidth' => '1',
                    'ClientData' => [
                        'ClientID'    => $this->cliente_id,
                        'ContactName' => $data['recipient_name'],
                        'ContractID'  => $this->contrato,
                        'Phone'       => $phone, //$data['sender_phone'],
                        'RequestedBy' => $data['recipient_name']
                    ],
                    'ClientRef'     => $data['reference'],
                    'Date'          => $data['date'],
                    'Observations'  => removeAccents($data['obs']),
                    //'PickUpPeriod'  => '',
                    'PickupAddress' => [
                        'Address'   => removeAccents($data['sender_address']),
                        'CP3'       => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][1] : '',
                        'CP4'       => $data['sender_country'] == 'pt' ? @$data['sender_zip_code'][0] : '',
                        'CPInt'     => $data['sender_country'] != 'pt' ? $originalZipCodeSender : '',
                        'City'      => removeAccents($data['sender_city']),
                        'Contact'   => removeAccents(@$data['sender_attn'] ? @$data['sender_attn'] : $data['sender_name']),
                        'Country'   => $data['sender_country'] != 'pt' ? strtoupper($data['sender_country']) : 'PT',
                        'MobilePhone' => $senderMobile ? $senderMobile : '910000000',
                        'Name'      => removeAccents($data['sender_name']),
                        'Phone'     => $phone //$data['sender_phone']
                    ]
                ],
                'RequestID' => uuid(),
                'UserID'    => $this->cliente_hash
            ]
        ];

        $response = $client->CompletePickUp($data);
        //dd($data);
        $response = json_encode($response);
        $response = json_decode($response, true);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $requestXml  = print_r($data, true);
            $responseXml = print_r($response, true);

            file_put_contents(public_path() . '/dumper/request.txt', $requestXml);
            file_put_contents(public_path() . '/dumper/response.txt', $responseXml);
        }

        if ($response['CompletePickUpResult']['Status'] == 'Failure') {
            $errorsList = @$response['CompletePickUpResult']['ErrorList']['ErrorData'];
            $errors = [];

            if (isset($errorsList['Code'])) { //só 1 erro
                throw new \Exception(@$errorsList['Message']);
            } else {
                foreach ($errorsList as $error) {
                    $errors[] = @$error['Message'];
                }
                throw new \Exception(implode(' <br/> ', $errors));
            }
        }

        $shipment = $response['CompletePickUpResult']['ShipmentToPickUp']['ShipmentToPickUp']['ObjectNumber'];
        $pickup   = $response['CompletePickUpResult']['PickUpID'];

        $trk = $pickup . ',' . $shipment;

        return $trk;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/


    /**
     * Return connection url
     *
     * @param $url
     * @param $method
     * @return string
     */
    private function getUrl($method, $forceProduction = false)
    {
        /*if (config('app.env') == 'local' && !$forceProduction) {
            $url = $this->urlTest . '/' . $method . '.svc?wsdl';
        } else {*/
        $url = $this->url . '/' . $method . '.svc?wsdl';
        /*}*/

        return $url;
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
     * @param array $data Array of data
     * @param string $mappingArray
     * @return array
     */
    private function mappingResult($data, $mappingArray, $totalWeight=0, $totalWeightVol=0)
    {

        $arr = [];

        foreach ($data as $row) {

            if (!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.ctt.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {


                $row['codigoEvento'] = @$row['status_id'];
                $row['codigoMotivo'] = @$row['incidence_id'];


                $row['created_at'] = new Carbon($row['created_at']);

                $status = config('shipments_import_mapping.ctt-status');
                $row['status_id'] = @$status[$row['status_id']];

                if (!is_numeric(@$row['event_code'])) {
                    $row['obs'] = @$row['event_code'] . ' ' . $row['obs'];
                }


                if ($mappingArray == 'collection-status') {
                    $row['obs'] = @$row['incidence_id'];
                    if ($row['status_id'] == '18' && str_contains(@$row['incidence_id'], ['Cancelada'])) {
                        $row['status_id'] = '8';
                    }
                }

                if ($row['codigoEvento'] == 'EDF' || $row['codigoEvento'] == 'EAE') { //EDF - Em espera ou TRA - Em tratamento

                    $details = config('shipments_import_mapping.ctt-motivos');
                    $code = $row['codigoEvento'] . '-' . $row['codigoMotivo']; //codigo motivo

                    if (@$details[$code]) {
                        $row['obs'] = $row['obs'] . '<br/>' . @$details[$code];
                    }
                }

                if ($row['status_id'] == '9') { //incidencia
                    
                    $incidences = config('shipments_import_mapping.ctt-incidences');
                    $code = 'EMH-' . $row['incidence_id'];

                    if (isset($incidences[$code])) {
                        if (is_numeric(@$incidences[$code])) {
                            //ticket: https://enovo.pt/admin/tickets/12457
                            if(config('app.source') === "2660express"){
                                switch($incidences[$code]){
                                    case 10:
                                        $row['status_id'] = 64;
                                        break;
                                    case 11:
                                        $row['status_id'] = 47;
                                        break;
                                    case 13:
                                        $row['status_id'] = 73;
                                        break;
                                    default:
                                        $row['incidence_id'] = $incidences[$code];
                                }
                            }else{   
                                $row['incidence_id'] = @$incidences[$code];
                            }
                            
                        } else {
                            $row['incidence_id']  = null;
                            $row['incidence_obs'] = @$incidences[$code];
                            $row['obs'] = $row['obs'] . '<br/>' . $row['incidence_obs'];
                        }
                    }
                } else {
                    $row['incidence_id'] = null;
                }


                if (isset($row['weight']) || isset($row['volumetric_weight'])) {
                    /* $row['weight'] = (int) $row['weight'];
                    $row['weight'] = $row['weight'] * 0.001; */
                    $row['weight'] = $totalWeight;
                    $row['volumetric_weight'] = $totalWeightVol;
                } else {
                    $row['weight'] = 0;
                    $row['volumetric_weight'] = 0;
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
     * Atualiza estados de um envio
     *
     * @param \App\Models\Shipment $shipment
     */
    public function updateHistory($shipment)
    {

        if ($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code, $shipment);
        } else {
            $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        }

        $weight = @$data[0]['weight'];
        
        
        if($shipment->sender_country != $shipment->recipient_country) {
            $volumetricWeight = @$data[0]['volumetric_weight'];
            $weight = $weight > $volumetricWeight ? $weight : $volumetricWeight;
        }

        $weightChanged = true;
        if ($shipment->weight > $weight) {
            $weightChanged = false;
            $weight = $shipment->weight;
        }

        $volumetricWeight = $shipment->volumetric_weight;
        
        
        if(config('app.source') == '2660express') {
            //força para não atualizar pesos
            $totalWeight      = 0; 
            $volumetricWeight = 0;
            $weightChanged    = false;
        }
                
                
        if ($data) {
            $deliveredTrks = [];
            foreach ($data as $key => $item) {
                if (empty($item['status_id'])) {
                    continue;
                }

                if ($key == 0 && $weightChanged) {
                    //$item['obs'] = $item['obs'].'<br/>Peso atualizado. Original: '.$shipment->weight.' Correto: '.$weight;
                    $item['obs'] = $item['obs'] . '<br/>Peso atualizado para ' . $weight;
                }

                if ($item['status_id'] == 5) {
                    $item['obs'] = $item['receiver_name'];
                }

                if ($shipment->status_id == $item['status_id'] && $shipment->is_collection && $item['status_id'] == '21') { //ja existe o estado 21
                    $history = ShipmentHistory::where('shipment_id', $shipment->id)
                        ->where('status_id', '21')
                        ->orderBy('created_at', 'desc')
                        ->first();
                } else {

                    $tmpVolumetricWeight = (float)$item['volumetric_weight'];
                    if ($tmpVolumetricWeight != $volumetricWeight) { 
                        $volumetricWeight = $tmpVolumetricWeight;
                        if ($volumetricWeight > 0.00 && $volumetricWeight > $shipment->weight && $shipment->sender_country != $shipment->recipient_country) { //não atualiza peso volumetrico para portugal
                            if (!empty($item['obs'])) {
                                $item['obs'] .= '<br/>';
                            }

                            $item['obs'] .= 'Peso volumétrico atualizado para ' . $volumetricWeight;
                        }
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

                    if ($history->status_id == ShippingStatus::DELIVERED_ID) {
                        $deliveredTrks[$shipment->provider_tracking_code] = $shipment->provider_tracking_code;
                    }
                }
            }


            try {
                if ($history) {
                    $history->sendEmail(false, false, true);
                }
            } catch (\Exception $e) {
            }

            $shipment->weight            = $weight;
            $shipment->volumetric_weight = $volumetricWeight;

            if ($history) {
                $shipment->status_id   = $history->status_id;
                $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;

                if (config('app.source') === "aveirofast") {
                    $newestShipmentHistory = ShipmentHistory::select('status_id', 'created_at')->where('shipment_id', $shipment->id)->orderBy('created_at', 'desc')->first();
                    $shipment->status_id   = $newestShipmentHistory->status_id;
                    $shipment->status_date = $newestShipmentHistory->created_at;
                }
            }

            /**
             * Calcula o preço e custo do envio
             */
            if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id
                    && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id
                    && $shipment->agency_id && $shipment->customer_id && $weightChanged)) 
            {

                $serviceId = $shipment->service_id;
                if ($shipment->is_collection) {
                    $serviceId = @$shipment->service->assigned_service_id;
                }

                $tmpShipment = $shipment;
                $tmpShipment->service_id = $serviceId;

                $oldPrice = $shipment->billing_total;

                //calcula preços do envio
                $prices = Shipment::calcPrices($shipment);
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
                        if (@$prices['fillable']) {
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

            /* $prices = Shipment::calcPrices($tmpShipment);

            $oldPrice = $shipment->total_price;
            $shipment->cost_price = @$prices['cost'];

            if (!empty($data['total_price_for_recipient'])) {
                $shipment->total_price = 0;
                $shipment->payment_at_recipient = 1;
                $shipment->total_price_for_recipient = $data['total_price_for_recipient'];
            } else {
                $shipment->payment_at_recipient = 0;
                $shipment->total_price_for_recipient = 0;

                if (!$shipment->price_fixed) {
                    $shipment->total_price  = @$prices['total'];
                    $shipment->fuel_tax     = @$prices['fuelTax'];
                    $shipment->extra_weight = @$prices['extraKg'];
                }

                //DISCOUNT FROM WALLET THE DIFERENCE OF PRICE
                if (hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                    $diffPrice = $shipment->total_price - $oldPrice;
                    if ($diffPrice > 0.00) {
                        try {
                            \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                            $shipment->customer->subWallet($diffPrice);
                        } catch (\Exception $e) {
                        }
                    }
                } */
            }
            

            $shipment->save();

            if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                $price = $shipment->addPickupFailedExpense();
                $shipment->walletPayment(null, null, $price); //discount payment
            }

            //DELETE STORED LABELS
            if ($deliveredTrks) {
                foreach ($deliveredTrks as $trackingCode) {
                    $filepath = public_path() . $this->upload_directory . $trackingCode . '_labels.txt';
                    if (File::exists($filepath)) {
                        File::delete($filepath);
                    }

                    $filepath = public_path() . $this->upload_directory . $trackingCode . '_guide.txt';
                    if (File::exists($filepath)) {
                        File::delete($filepath);
                    }

                    $filepath = public_path() . $this->upload_directory . $trackingCode . '_reimbursement.txt';
                    if (File::exists($filepath)) {
                        File::delete($filepath);
                    }
                }
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
    public function saveShipment($shipment, $isCollection = false, $webserviceLogin = null)
    {

        //$reference = 'TRK' . $shipment->tracking_code;
        //$reference .=  $shipment->reference ? '-' . $shipment->reference : '';
        $reference = $shipment->reference ? $shipment->reference : 'TRK' . $shipment->tracking_code;
        $reference = substr($reference, 0, 21);

        $service = $this->getProviderService($shipment);
        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return pack
        $returnCheck = 0;
        if ($shipment->has_return && in_array('rcheck', $shipment->has_return)) {
            $returnCheck = 1;
        }

        //return guide
        $returnGuide = 0;
        if ($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 1;
        }

        //return pack
        $returnPack = 0;
        if ($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 1;
        }


        $expenses = [];
        if (!empty($shipment->optional_fields)) {
            $opt = $shipment->optional_fields;
            $opt = array_keys(json_decode($opt, true));

            $expenses = ShippingExpense::whereIn('id', $opt)
                ->pluck('type', 'id')
                ->toArray();
        }

        $fragil = false;
        if (in_array('fragile', $expenses)) {
            $fragil = 1;
        } 

        $sabado = false;
        if (in_array('sabado', $expenses)) {
            $sabado = 1;
        }

        $timewindow = false;
        if ($shipment->start_hour >= '19:00') {
            $timewindow = 'Delivery_19h00_22h00';
        } elseif ($shipment->start_hour >= '16:00') {
            $timewindow = 'Delivery_16h00_19h00';
        } elseif ($shipment->start_hour >= '13:00') {
            $timewindow = 'Delivery_13h00_16h00';
        } elseif ($shipment->start_hour >= '10:00') {
            $timewindow = 'Delivery_10h00_13h00';
        } elseif ($shipment->start_hour >= '08:00') {
            $timewindow = 'Delivery_08h00_10h00';
        }

        $weight = $shipment->weight;
        if (in_array(config('app.source'), ['aveirofast', 'ship2u'])) { //clientes querem que peso seja 1
            $weight = 1;
        } elseif(config('app.source') == '2660express') {
            $weight = '0.5';
        }

        $chargePrice = $shipment->charge_price ? $shipment->charge_price : 0;
        $chargePrice = $shipment->payment_at_recipient ? $chargePrice + $shipment->total_price_for_recipient : $chargePrice;

        //pudo
        $pickupPointName = $pickupPointCode = $pickupPointType = ""; 
        if($shipment->recipient_pudo_id){
           
            $pickupPoint = PickupPoint::find($shipment->recipient_pudo_id);
            
            $pickupPointName = $pickupPoint->name;
            $pickupPointCode = $pickupPoint->provider_code;
            
            $pickupPointType = "PostOffice";
            if($pickupPoint->country != 'pt'){
                $pickupPointType = 'Shop';
            }

            $shipment->recipient_address  = $pickupPoint->address;
            $shipment->recipient_zip_code = $pickupPoint->zip_code;
            $shipment->recipient_city     = $pickupPoint->city;
            $shipment->recipient_country  = $pickupPoint->country;
        }
      
        $recipientEmail = $shipment->recipient_email ?? 'noreply@ctt.pt';
        if (config('app.source') === 'fozpost') {
            $recipientEmail = 'geral@fozpost.com';
        }
        
        /**
         * remove code phone
        */
        $senderPhone    = removeDialCode(trim($shipment->sender_phone), $shipment->sender_country);
        $recipientPhone = removeDialCode(trim($shipment->recipient_phone), $shipment->recipient_country);
        

        $data = [
            "trk"                => $shipment->tracking_code,
            "date"               => $shipment->date,
            "service"            => $service,
            "volumes"            => $shipment->volumes,
            "weight"             => $weight,
            "charge_price"       => $chargePrice,
            "guide"              => $shipment->recipient_country != 'pt' ? '1' : '0', //com guia assinada?
            "sender_name"        => substr($shipment->sender_name, 0, 50),
            "sender_attn"        => substr($shipment->sender_attn, 0, 50),
            "sender_address"     => substr($shipment->sender_address, 0, 100),
            "sender_city"        => $shipment->sender_city,
            "sender_country"     => $shipment->sender_country,
            "sender_zip_code"    => $shipment->sender_zip_code,
            "sender_phone"       => $senderPhone,
            "recipient_attn"     => is_null($shipment->recipient_pudo_id) ? substr($shipment->recipient_attn, 0, 50) : $pickupPointName,
            "recipient_name"     => substr($shipment->recipient_name, 0, 50),
            "recipient_address"  => substr($shipment->recipient_address, 0, 100),
            "recipient_city"     => $shipment->recipient_city,
            "recipient_country"  => $shipment->recipient_country,
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_phone"    => $recipientPhone,
            "recipient_email"    => $recipientEmail,
            "obs"                => substr($shipment->obs, 0, 50),
            "reference"          => $reference,
            "hasSenderInformation" => ((!Setting::get('hidden_recipient_on_labels') && !Setting::get('hidden_recipient_addr_on_labels')) || @$webserviceLogin->force_sender) || in_array($service, ['EMSF015.01']) ? '1' : '0', //força o remetente se indicado ou se o envio for do tipo "cargo"
            "return_guide"      => $returnGuide,
            "return_check"      => $returnCheck,
            "return_pack"       => $returnPack,
            "start_hour"        => $shipment->start_hour,
            "end_hour"          => $shipment->end_hour,
            "guide_required"    => $shipment->guide_required == 'false' ? '0' : '1',
            //"hasSenderInformation" =>  in_array($shipment->customer_id, [2144, 3057,2501,2792,2089,2871,2461,2375,1959,2305,1585,2028]),
            "fragil"            => $fragil,
            "sabado"            => $sabado,
            "timewindow"        => $timewindow,
            "goods_price"       => $shipment->goods_price,
            'at_code'           => $shipment->at_code,
            "pudo_id"           => $shipment->recipient_pudo_id,
            "pudo_name"         => $pickupPointName,
            "pudo_code"         => $pickupPointCode,
            "pudo_type"         => $pickupPointType
        ];

        if (!in_array($data['recipient_country'], ['be', 'bg', 'cz', 'dk', 'de', 'ee', 'ie', 'el', 'es', 'fr', 'hr', 'it', 'cy', 'lv', 'lt', 'lu', 'hu', 'mt', 'nl', 'at', 'al', 'pt', 'ro', 'si', 'sk', 'fi', 'se'])) {
            $data["goods_description"] = @$shipment->pack_dimensions->first()->description;
            $data["goods_description"] = $data["goods_description"] ? $data["goods_description"] : 'Artigos diversos';
        }

        /**
         * @author Daniel Almeida
         * 
         * Commented at 25/07/2023
         * This code is already done in the base webservice model
         */
        //force sender data to hide on labels
        // if ((Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels')) && !($isCollection || $shipment->is_collection)) {

        //     if (Setting::get('hidden_recipient_on_labels')) {
        //         $data['sender_name'] = $shipment->agency->company;
        //     }

        //     $data['sender_address']  = $shipment->agency->address;
        //     $data['sender_zip_code'] = $shipment->agency->zip_code;
        //     $data['sender_city']     = $shipment->agency->city;
        //     $data['sender_country']  = $shipment->agency->country;
        //     $data['sender_phone']    = $shipment->agency->phone;
        // }

        if ($isCollection) {
            return $this->storeEnvioComRecolha($data, $shipment);
            //return $this->storeRecolha($data);
        } else {
            //$trk = $this->storeEnvioComRecolha($data);
            $trk = $this->storeEnvio($data, $shipment);

            if (config('app.source') == 'aveirofast') {
                $data['sender_phone'] = '234343081';
            }


            return $trk;
        }
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {
        return true;
    }

    /**
     * Grava uma resolução a um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveIncidenceResolution($incidenceResolution, $isCollection = false) {
        return true;
    }

        /**
     * Fecha os envios
     *
     * @param array $shipments
     */
    public function fechaEnvios($shipments)
    {

        /*if(config('app.source') == 'entregaki') {
            return $this->fechaEnviosComRecolha($shipments);
        }*/

        $folder = public_path() . '/uploads/labels/certificates/tmp';
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        ini_set('default_socket_timeout', 600000);

        $url = $this->getUrl('CTTShipmentProviderWS');
        $client = new \SoapClient($url, array("trace" => true, 'cache_wsdl' => WSDL_CACHE_NONE));

        $allServices = Service::filterSource()->pluck('code', 'id')->toArray();

        $dataArr      = [];
        $customerId   = null;
        $customersIds = [];
        foreach ($shipments as $shipment) {

            /* $mapping = config('shipments_export_mapping.ctt-services');

            if ($shipment['recipient_country'] != 'pt') {
                $mapping = config('shipments_export_mapping.ctt-services-internacional');
            }

            $serviceCode = @$allServices[$shipment['service_id']];
            if ($shipment['volumes'] > 1) {
                $serviceCode = 'M' . $serviceCode; //ex: M24H (multiplo 24)
            }

            $service = @$mapping[config('app.source')][$serviceCode];*/

            $service = $this->getProviderService($shipment);

            $customerId = $shipment['customer_id'];
            $customersIds[$customerId] = $customerId;

            $trk = explode(',', $shipment['provider_tracking_code']);
            $lastPos = count($trk) - 1;

            $firstTrk = $trk[0];
            $lastTrk  = isset($trk[$lastPos]) ? $trk[$lastPos] : $firstTrk;

            $dataArr[] =  [
                'FirstObjectCode' => $firstTrk,
                'LastObjectCode'  => $lastTrk,
                'SubProductCode'  => $service
            ];
        }

        if (count(@$customersIds) == 1) {
            $customerId = @$customersIds[$customerId];
        } else {
            $customerId = null;
        }

        $data = [
            'Input' => [
                'AuthenticationID'      => $this->session_id,
                'ClientId'              => $this->cliente_id,
                'ContractId'            => $this->contrato,
                'DistributionChannel'   => '99',
                'ListOfShipmentToClose' => $dataArr,
                'RequestID'             => uuid(),
                'UserID'                => $this->cliente_hash,
            ]
        ];

        $response = $client->CloseShipment($data);

        //dd($response);

        $status = @$response->CloseShipmentResult->Status;



        if (empty($status) || $status != 'Success') {
            $errorCode = @$response->CloseShipmentResult->ErrorsList->ErrorData->ErrorCode;
            $errorMsg  = @$response->CloseShipmentResult->ErrorsList->ErrorData->Message;

            if ($errorCode == 'EW0070') { //shipment already closed
                $errorMsg = str_replace('Shipment of object number ', '', $errorMsg);
                $trk = substr($errorMsg, 0, 13);


                foreach ($shipments as $shipment) {
                    if (strpos($shipment['provider_tracking_code'], $trk) !== false) {
                        Shipment::where('customer_id', $shipment['customer_id'])
                            ->where('provider_tracking_code', $shipment['provider_tracking_code'])
                            ->update(['is_closed' => 1]);
                    }
                }
            }
            throw new Exception($errorMsg);
        } else {

            $files = @$response->CloseShipmentResult->DocumentsList->DocumentData;

            if (!empty($files)) {

                foreach ($files as $key => $file) {
                    
                    //$filename = $file->Filename;
                    $file = @$file->File;

                    if ($key % 2) {
                        $folder = public_path() . '/uploads/labels/certificates/tmp/';

                        if (!File::exists($folder)) {
                            File::makeDirectory($folder);
                        }

                        $filename = 'ctt_' . $this->cliente_id . '_' . date('Y_m_d') . '_' . $key . '.pdf';
                        $filepath = $folder . $filename;

                        if (!File::exists($folder)) {
                            File::directory($folder);
                        }

                        $result = File::put($filepath, $file);

                        if ($result === false) {
                            throw new \Exception('Não foi possível gravar o certificado.');
                        } else {
                            $listFiles[] = $filepath;
                        }
                    }
                }

                // Merge files
                $pdf = new \LynX39\LaraPdfMerger\PdfManage;
                foreach ($listFiles as $filepath) {
                    $pdf->addPDF($filepath, 'all');
                }

                //Save merged file
                $filepath = '/uploads/labels/certificates/';
                $filename = 'ctt_' . $this->cliente_id . '_certificado_aceitacao_' . date('Y-m-d_His') . '.pdf';
                $outputFilepath = public_path() . $filepath . $filename;
                $result = $pdf->merge('file', $outputFilepath, 'P');

                $cttDeliveryManifest = new CttDeliveryManifest();
                $cttDeliveryManifest->customer_id = $customerId;
                $cttDeliveryManifest->title       = 'Certificado Aceitação';
                $cttDeliveryManifest->filepath    = $filepath . $filename;
                $cttDeliveryManifest->filename    = $filename;
                $cttDeliveryManifest->save();

                //Destroy temporary files
                if ($result) {
                    foreach ($listFiles as $item) {
                        File::delete($item);
                    }
                }

                return $filepath . $filename;
            }
        }
    }

    /**
     * Fecha os envios
     *
     * @param array $shipments
     */
    public function fechaEnviosComRecolha($shipments)
    {
        ini_set('default_socket_timeout', 600000);

        //check if folder exists
        $folder = public_path() . '/uploads/labels/certificates/tmp';
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $allServices = Service::filterSource()->pluck('code', 'id')->toArray();

        //agrupa envios por cliente (será necessário fechar os envios de acordo com o cliente
        $customers = [];
        $destinations = [];
        foreach ($shipments as $shipment) {
            $customers[$shipment['customer_id']][] = $shipment;
        }


        foreach ($customers as $customerId => $shipments) {

            $customer = @$shipments[0]['customer'];
            $originalZipCodeSender = $customer['zip_code'];
            $customer['zip_code']  = explode('-', $customer['zip_code']);
            $customer['zip_code'][1] = empty(@$customer['zip_code'][1]) ? '000' : $customer['zip_code'][1];

            $trks = [];
            $date = '2099-01-01';
            foreach ($shipments as $shipment) {

                $date = $date < @$shipment['date'] ? $date : @$shipment['date'];

                $trk = explode(',', $shipment['provider_tracking_code']);

                $firstTrk = $trk[0];
                $lastTrk = isset($trk[1]) ? $trk[1] : $firstTrk;

                $trks[] = $firstTrk;
            }

            try {
                $date = new \Jenssegers\Date\Date($date);
                if ($date->isWeekend()) {
                    if ($date->isSaturday()) {
                        $date = $date->addDays(2);
                    } else {
                        $date = $date->addDays(1);
                    }
                }

                $date = $date->format('Y-m-d');
            } catch (\Exception $e) {
                $date = date('Y-m-d');
            }

            //$date = '2020-12-11';

            $data = [
                'Input' => [
                    'AuthenticationID' => $this->session_id,
                    'PickUpData' => [
                        'BiggerObjectHeight' => '1',
                        'BiggerObjectLenght' => '1',
                        'BiggerObjectWidth' => '1',
                        'ClientData' => [
                            'ClientID'    => $this->cliente_id,
                            'ContactName' => $customer['name'],
                            'ContractID'  => $this->contrato,
                            'Phone'       => '232000000', //$data['sender_phone'],
                            'RequestedBy' => $customer['name']
                        ],
                        'ClientRef'     => 'N/A',
                        'Date'          => $date,
                        'DestinationList' => $destinations,
                        //'Observations'  => '',
                        //'PickUpPeriod'  => '',
                        'PickupAddress' => [
                            'Address'       => removeAccents($customer['address']),
                            'CP3'           => @$customer['country'] == 'pt' ? @$customer['zip_code'][1] : '',
                            'CP4'           => @$customer['country'] == 'pt' ? @$customer['zip_code'][0] : '',
                            'CPInt'         => @$customer['country'] != 'pt' ? $originalZipCodeSender : '',
                            'City'          => removeAccents(@$customer['city']),
                            'Contact'       => $customer['name'], //removeAccents(@$customer['responsable']),
                            'Country'       => $customer['country'] != 'pt' ? strtoupper($customer['country']) : 'PT',
                            'MobilePhone'   => $customer['phone'],
                            'Name'          => removeAccents($customer['name']),
                            'Phone'         => '232000000' //$data['sender_phone']
                        ],
                        'ShipmentList' => $trks,
                        /*'TotalObjects'      => '',
                        'TotalWeight'       => '',*/
                        'TransportDocument' => '0'
                    ]
                ]
            ];

            //call ws
            $url = $this->getUrl('Recolhasws');
            $client = new \SoapClient($url, array("trace" => true, 'cache_wsdl' => WSDL_CACHE_NONE));
            $response = $client->NewOfferPickUp($data);

            $status = @$response->NewOfferPickUpResult->Status;

            if (empty($status) || $status != 'Success') {
                $error = @$response->NewOfferPickUpResult->ErrorList->ErrorData;
                $error = json_decode(json_encode($error), true);

                if (@$error[0]) {
                    $errorCode = @$error[0]['Code'];
                    $errorMsg  = @$error[0]['Message'];
                } else {
                    $errorCode = @$response->NewOfferPickUpResult->ErrorList->ErrorData->Code;
                    $errorMsg  = @$response->NewOfferPickUpResult->ErrorList->ErrorData->Message;
                }

                if ($errorCode == 'EW0070') { //shipment already closed
                    $errorMsg = str_replace('Shipment of object number ', '', $errorMsg);
                    $trk = substr($errorMsg, 0, 13);

                    foreach ($shipments as $shipment) {
                        if (strpos($shipment['provider_tracking_code'], $trk) !== false) {
                            Shipment::where('customer_id', $shipment['customer_id'])
                                ->where('provider_tracking_code', $shipment['provider_tracking_code'])
                                ->update(['is_closed' => 1]);
                        }
                    }
                }

                throw new Exception($errorMsg);
            } else {

                $pickupTrk  = @$response->NewOfferPickUpResult->PickUpID;
                $files      = @$response->NewOfferPickUpResult->DocumentList->DocumentData;
                if (!empty($files)) {

                    $listFiles = [];
                    foreach ($files as $key => $file) {
                        $filename  = @$file->FileName ? $file->FileName : 'File' . $key . '.pdf';
                        $title     = $filename;
                        $file      = @$file->File;

                        //if($key%2) {
                        $folder = public_path() . '/uploads/labels/certificates/tmp/';

                        if (!File::exists($folder)) {
                            File::makeDirectory($folder);
                        }

                        $filename = 'ctt_' . $this->cliente_id . '_' . date('Y_m_d') . '_' . $key . '_' . $filename;
                        $filepath = $folder . $filename;

                        if (!File::exists($folder)) {
                            File::directory($folder);
                        }

                        $result = File::put($filepath, $file);

                        if ($result === false) {
                            throw new \Exception('Não foi possível gravar o certificado.');
                        } else {
                            $listFiles[] = $filepath;
                        }
                        //}
                    }

                    // Merge files
                    $pdf = new \LynX39\LaraPdfMerger\PdfManage;
                    foreach ($listFiles as $filepath) {
                        $pdf->addPDF($filepath, 'all');
                    }

                    //Save merged file
                    $filepath = '/uploads/labels/certificates/';
                    $filename = 'ctt_' . $this->cliente_id . '_certificado_aceitacao_' . date('Y-m-d_His') . '.pdf';
                    $outputFilepath = public_path() . $filepath . $filename;
                    $result = $pdf->merge('file', $outputFilepath, 'P');

                    $cttDeliveryManifest = new CttDeliveryManifest();
                    $cttDeliveryManifest->customer_id = $customerId;
                    $cttDeliveryManifest->title       = 'Certificado Aceitação';
                    $cttDeliveryManifest->filepath    = $filepath . $filename;
                    $cttDeliveryManifest->filename    = $filename;
                    $cttDeliveryManifest->pickup_trk  = $pickupTrk;
                    $cttDeliveryManifest->save();

                    //Destroy temporary files
                    if ($result) {
                        foreach ($listFiles as $item) {
                            File::delete($item);
                        }
                    }

                    return $filepath . $filename;
                }
            }
        }

        return true;
    }

    /**
     * Devolve lista de pontos de recolha
     * @param array $paramsArr
     * @return type|array
     */
    public function getPontosRecolha($paramsArr = [])
    {
        $url = $this->getUrl('ReferenciasWS', true);
        $client = new \SoapClient($url);

        $this->session_id = '573fdb5e-e909-45aa-a6d3-cf5314f12c71';
        $data = [
            'ID' => $this->session_id,
            'countryCode' => @$paramsArr['country'] ? $paramsArr['country'] : 'PT'
        ];

        $response = $client->ObterListaPontosEntregaComDesignacao($data);

        if (empty($response->ObterListaPontosEntregaComDesignacaoResult->PUDOExtensionOutput)) {
            throw new Exception($response->ObterListaPontosEntregaComDesignacaoResult->_x003C_Erros_x003E_k__BackingField->string);
        } else {

            if (empty((array) $response->ObterListaPontosEntregaComDesignacaoResult->PUDOExtensionOutput)) {
                throw new Exception('Sem informação de pontos entrega.');
            } else {

                $pudos = $response->ObterListaPontosEntregaComDesignacaoResult->PUDOExtensionOutput;

                $resultArr = [];
                foreach ($pudos as $pudo) {
                    $pudo = [$pudo];
                    $resultArr[] = $this->mappingResult($pudo, 'pudo');
                }

                return $resultArr;
            }
        }
    }

    /**
     * Convert a ZPL file to PDF
     */
    public function convertZPL2PDF($zpl, $trk, $volumes)
    {

        $listFiles = [];
        $curl = curl_init();

        for ($i = 0; $i < $volumes; $i++) {

            curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/" . $i . "/");
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf", "X-Rotation: 90"));
            $result = curl_exec($curl);

            $fileData = $result;

            $filepath = public_path() . '/uploads/labels/ctt/' . $trk . '_label_' . $i . '.pdf';
            File::put($filepath, $fileData);

            $listFiles[] = $filepath;
        }

        curl_close($curl);


        //ALGORITMO ADAPTADO PARA QUANDO HÁ MAIS DE 50 ETIQUETAS
        /*$listFiles  = [];
        $labelParts = [];
        $labelPartsSize = [];


        if($volumes <= 50) {
            $labelPartsSize[] = $volumes;
            $labelParts[] = $zpl;
        } else {
            $singleLabels = str_replace('^XZ', '', $zpl);
            $singleLabels = explode('^XA', $singleLabels); //separa as etiquetas individualmente
            $countBlock50Labels = ceil(count($singleLabels) / 50);

            for ($i = 0; $i <= $countBlock50Labels; $i++) {
                $parts = array_slice($singleLabels, $i * 50, 50); //separa as etiquetas 50 a 50

                //junta o array em string
                $labelStr = "";
                foreach ($parts as $labelZpl) {
                    $labelStr .= "^XA" . $labelZpl . "^XZ\n";
                }

                $labelPartsSize[] = count($parts);
                $labelParts[] = $labelStr;
            }
        }


        $curl = curl_init();

        $labelId = 0;
        foreach ($labelParts as $key => $zpl) {

            $volumes = @$labelPartsSize[$key];

            for ($i = 0; $i < $volumes; $i++) {

                curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/" . $labelId . "/");
                curl_setopt($curl, CURLOPT_POST, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf"));
                $result = curl_exec($curl);

                $fileData = $result;

                $filepath = public_path() . '/uploads/labels/ctt/' . $trk . '_label_' . $labelId . '.pdf';
                File::put($filepath, $fileData);

                $listFiles[] = $filepath;
                $labelId++;
            }
        }

        curl_close($curl);*/


        /**
         * Merge files
         */
        $pdf = new \LynX39\LaraPdfMerger\PdfManage;
        foreach ($listFiles as $filepath) {
            $pdf->addPDF($filepath, 'all');
        }

        /**
         * Save merged file
         */
        $filepath = '/uploads/labels/ctt/' . $this->cliente_id . '_labels.pdf';
        $outputFilepath = public_path() . $filepath;
        $result = base64_encode($pdf->merge('string', $outputFilepath, 'L'));

        if ($result) {
            foreach ($listFiles as $item) {
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

            if ($shipment->insurance_price) {
                $serviceKey = $serviceKey . 'seg';
            }

            if ($shipment->volumes > 1) {
                $serviceKey = $serviceKey . 'm';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {

                $mapping = config('shipments_export_mapping.ctt-services');

                if ($shipment->recipient_country != 'pt') {
                    $mapping = config('shipments_export_mapping.ctt-services-internacional');
                }

                $code = $shipment->service->code;


                if ($code == '24SEG' && config('app.source') == 'entregaki') {
                    // $this->cliente_id = '200041044'; // 04/05 | Pedido #DNU-792-8411
                    $this->cliente_id = '200049331';
                    $this->contrato = '300305276';
                }

                if ($code == '19SEG' && config('app.source') == 'rlrexpress') {
                    $this->cliente_id = '200051781';
                    $this->contrato = '300306751';
                    $this->session_id = '722d3714-a86d-48d0-8487-805bf80f5186';
                    $this->cliente_hash = '49929275-3de2-4e4f-91c9-effbca021dfb';
                }

                if ($shipment->volumes > 1) {
                    $code = 'M' . $code; //ex: M24H (multiplo 24)
                }


                $providerService = @$mapping[$source][$code];
            }

            if ($providerService == 'EMSF070.01' && $shipment->weight < 30) {
                $providerService = 'EMSF071.01'; //se o serviço é cargo paletes e tem menos 30kg, muda para cargo volumes
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço CTT Expresso.');
        }

        return $providerService;
    }

    /**
     * Grava etiqueta em PDF
     * @param $labelCode
     * @return string|void
     * @throws \Throwable
     */
    public function convertLabel2Pdf($trackingCode, $shipment, &$labelsTrk = null)
    {

        try {
            $trackingCode = substr($trackingCode, 0, 13);
            $file = file(public_path('uploads/labels/ctt/' . $trackingCode . '_labels.txt'));

            $labelsTrk = [];
            $rota      = null;
            $remessa   = null;
            $service   = null;
            $serviceFullName  = null;
            $expedicaoES      = null;
            $direcionamentoES = null;
            $showSourceLogo   = true; //in_array(config('app.source'), ['moovelogistica', 'asfaltolargo', 'ship2u']) ? true : false;

            foreach ($file as $line) {

                //ETIQUETA ANTIGA
                /*if (str_contains($line, '^FO260,30^BY3,2.5^BCB,100,N,N^FD')) {
                    $line = str_replace('^FO260,30^BY3,2.5^BCB,100,N,N^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $labelsTrk[] = trim($line);
                }

                if (!$rota && str_contains($line, '^FO720,70^A0B,130,65^FB1100,1,0,L,0^FD')) {
                    $line = str_replace('^FO720,70^A0B,130,65^FB1100,1,0,L,0^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $rota = trim($line);
                }

                if (!$remessa && str_contains($line, '^FO395,35^ADB,10,10^FB550,1,0,L,0^FD')) {
                    $line = str_replace('^FO395,35^ADB,10,10^FB550,1,0,L,0^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $remessa = trim($line);
                }

                if (!$service && str_contains($line, '^FO45,680^A0B,70,60^FB200,1,0,R,0^FD')) {
                    $line = str_replace('^FO45,680^A0B,70,60^FB200,1,0,R,0^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $service = trim($line);
                }

                if (!$service && str_contains($line, '^FO45,680^A0B,70,60^FB200,1,0,R,0^FD')) {
                    $line = str_replace('^FO45,680^A0B,70,60^FB200,1,0,R,0^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $service = trim($line);
                }

                if (!$expedicaoES && str_contains($line, '^FO205,50^ADB,20,10^FDExped: ')) {
                    $line = str_replace('^FO205,50^ADB,20,10^FDExped: ', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $expedicaoES = trim($line);
                }

                if (!$direcionamentoES && str_contains($line, '^FO690,30^ADB,10,10^FDDireccionamiento: ')) {
                    $line = str_replace('^FO690,30^ADB,10,10^FDDireccionamiento: ', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $direcionamentoES = trim($line);
                }*/

                if (str_contains($line, '^FO140,120,^ADB,15,15^FD*')) {
                    $line = str_replace('^FO140,120,^ADB,15,15^FD*', '', $line);
                    $line = str_replace('*^FS', '', $line);
                    $labelsTrk[] = trim($line);
                }

                if (!$rota && str_contains($line, '^FO730,30,^FWB,^A0,55,50^FD')) {
                    $line = str_replace('^FO730,30,^FWB,^A0,55,50^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $rota = trim($line);
                }

                if (!$remessa && str_contains($line, '^FO190,70,^A0B,25,25^FB550,1,0,L,0^FD')) {
                    $line = str_replace('^FO190,70,^A0B,25,25^FB550,1,0,L,0^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $remessa = trim($line);
                }

                if (!$service && str_contains($line, '^FO69,620,^A0B,40,40^FD')) {
                    $line = str_replace('^FO69,620,^A0B,40,40^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $service = trim($line);
                }

                if (!$serviceFullName && str_contains($line, '^FO30,620,^A0B,40,40^FD')) {
                    $line = str_replace('^FO30,620,^A0B,40,40^FD', '', $line);
                    $line = str_replace('^FS', '', $line);
                    $serviceFullName = trim($line);
                }

                if (!$expedicaoES && str_contains($line, '^FO387,160,^ADB,20,10^FD*')) {
                    $line = str_replace('^FO387,160,^ADB,20,10^FD*', '', $line);
                    $line = str_replace('*^FS', '', $line);
                    $expedicaoES = trim($line);
                }

                if (!$direcionamentoES && str_contains($line, '^FO655,75,^ADB,10,10^FD*')) {
                    $line = str_replace('^FO655,75,^ADB,10,10^FD*', '', $line);
                    $line = str_replace('*^FS', '', $line);
                    $direcionamentoES = trim($line);
                }
            }

             /*$expenses = [];
            if (!empty($shipment->complementar_services)) {
                $expenses = ShippingExpense::whereIn('id', $shipment->complementar_services)
                    ->pluck('type', 'id') 
                    ->toArray();
            } */

            $expenses = [];
            if (!empty($shipment->optional_fields)) {
                $opt = $shipment->optional_fields;
                $opt = array_keys(json_decode($opt, true));

                $expenses = ShippingExpense::whereIn('id', $opt)
                    ->pluck('type', 'id')
                    ->toArray();
            }

            $fragil = false;
            if (in_array('fragile', $expenses)) {
                $fragil = 1;
            }

            $mpdf = new Mpdf(getLabelFormat('default'));
            $mpdf->showImageErrors = true;
            $mpdf->shrink_tables_to_fit = 0;


            foreach ($labelsTrk as $volume => $barcode) {

                $data = [
                    'shipment'    => $shipment,
                    'remessa'     => $remessa,
                    'service'     => $service,
                    'route'       => $rota,
                    'barcode'     => $barcode,
                    'volume'      => $volume + 1,
                    'fragil'      => $fragil,
                    'contrato'    => $this->contrato,
                    'cliente'     => $this->cliente_id,
                    'serviceFullName'  => $serviceFullName,
                    'expedicaoES'      => $expedicaoES,
                    'direcionamentoES' => $direcionamentoES,
                    'source'           => 'admin',
                    'showSourceLogo'   => $showSourceLogo
                ];

                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels_ctt', $data)->render()); //write
            }

            $mpdf->debug = true;
            $labels = base64_encode($mpdf->Output('Etiquetas.pdf', 'S'));
            return $labels;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
