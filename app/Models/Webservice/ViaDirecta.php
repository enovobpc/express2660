<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\ShipmentHistory;
use App\Models\WebserviceLog;
use Mockery\Exception;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Date, Response, File, Setting, View;

class ViaDirecta extends \App\Models\Webservice\Base
{


    //nova api: https://documenter.getpostman.com/view/16674217/UVJZodfz
    //username= EnovoDEV
    //password= vbf2!aP@GNTK
    //Rapidix=4106
    //2660Exp=7173
    //ver email com o assunto "API ViaDirecta - Novos campos no tracking"


    /**
     * @var string
     */
    //private $url = 'https://www.viadirectanet.pt/WebServices_Prd/WebServicesInbound/service.asmx?wsdl';
    private $url = 'https://www.viadirectanet.pt/WebServices_Prd/WebServicesInboundV2/service.asmx?wsdl';


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/viadirecta/';

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
    private $clientCode;

    /**
     * @var null
     */
    private $trkPrefix;

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
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        if (config('app.env') == 'local') {
            $this->trkPrefix   = 'VD';
            $this->clientCode  = '5791';
            $this->user        = 'wsvdinbound';
            $this->password    = 'p3gb#s2%a21';
        } else {
            $this->trkPrefix   = empty($sessionId) ? 'VD' : $sessionId;
            $this->clientCode  = $agencia;
            $this->user        = $user;
            $this->password    = $password;
        }

        if (config('app.source') == 'activos24') {
            $this->trkPrefix = 'ACT';
        } elseif (config('app.source') == 'scandilog') {
            $this->trkPrefix = 'SCN';
        } elseif (config('app.source') == 'rapidix') {
            $this->trkPrefix = 'RPD';
        }

        if (config('app.source') == 'activos24') {
            $this->url = 'https://www.viadirectanet.pt/WebServices_Prd/WebServicesInbound/service.asmx?wsdl';
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
        $url = 'http://www.viadirectanet.pt/webservices_prd/wstracking/Service.asmx?WSDL';

        if (config('app.source') == 'activos24') {
            $this->user        = 'wsact24';
            $this->password    = 'k742#k34$2';
        }

        $data = [
            'request' => [
                'Cod_Cliente' => $this->clientCode,
                'PEDIDO' => [
                    'WsSrvPedido' => [
                        'Cod_Objecto' => $trackingCode
                    ],
                ],
                'LOGIN' => [
                    'Password' => $this->password,
                    'Username' => $this->user
                ]
            ]
        ];

        $client = new \SoapClient($url);
        $result = $client->TRACKING($data);
        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = $result['TRACKINGResult']['WsSrvResposta'];

        if ($result['Cod_Objeto'] == '000') {
            throw new \Exception($result['Desc_Tracking']);
        }

        
        $result = $this->mappingResult([$result], 'status');
        return $result;
    }


    /**
     * Obtém vários estados de envio
     *
     * @param $params ['trackings]
     * @return type|false|mixed|string
     * @throws \Exception
     */
    public function getEstadoEnvioMassive($trks)
    {

        $searchTrackings = explode(',', $trks);

        $trks = [];
        foreach ($searchTrackings as $trk) {
            $trks[] = ['Cod_Objecto' => $trk];
        }

        $url = 'http://www.viadirectanet.pt/webservices_prd/wstracking/Service.asmx?WSDL';

        if (config('app.source') == 'activos24') {
            $this->user        = 'wsact24';
            $this->password    = 'k742#k34$2';
        }

        $data = [
            'request' => [
                'Cod_Cliente' => $this->clientCode,
                'PEDIDO' => [
                    'WsSrvPedido' => $trks,
                ],
                'LOGIN' => [
                    'Password' => $this->password,
                    'Username' => $this->user
                ]
            ]
        ];

        $client = new \SoapClient($url);
        $result = $client->TRACKING($data);

        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = $result['TRACKINGResult']['WsSrvResposta'];

        if (count($searchTrackings) == 1) {
            $result = [$result];
        }

        $histories = [];
        foreach ($result as $row) {
            if (@$row['Cod_Objeto'] == '000') {
                $histories[] = [
                    'tracking' => false,
                    'obs'      => 'Objeto não encontrado.'
                ];
            } else {
                $histories[] = $this->mappingResult([$row], 'status');
            }
        }

        return $histories;
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
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode) {}

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
        return getEstadoEnvioByTrk(null, null, $referencia);
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
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $client = new \SoapClient($this->url);
        $result = $client->NewSERVICE($data);

        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = @$result['NewSERVICEResult']['WsSrvResposta'];

        if (@$result['Resultado'] == 'NOK') {
            throw new \Exception($result['Desc_Erro']);
        }

        $trk = $result['Cod_Objeto'];
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
        $shipment = Shipment::filterAgencies()->where('provider_tracking_code', $trackingCode)->first();

        if (!$shipment) {
            throw new \Exception('Envio com o código ' . $trackingCode . ' não encontrado.');
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100, 145],
            'margin_left'   => 2,
            'margin_right'  => 2,
            'margin_top'    => 2,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data['view']       = 'admin.printer.shipments.labels.label_via_directa';
        $data['shipment']   = $shipment;

        for ($count = 1; $count <= $shipment->volumes; $count++) {
            $data['count'] = $count;
            $mpdf->WriteHTML(View::make('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        $b64Doc = $mpdf->Output('Etiquetas.pdf', 'S'); //return pdf base64 string
        $b64Doc = base64_encode($b64Doc);
        return $b64Doc;
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
        return false;
    }


    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * Call API
     *
     * @param $url
     * @param null $headers
     * @param null $data
     * @return mixed
     */
    public function callApi($url, $method, $headers = null, $data = null, $rawData = false)
    {
        if (!$rawData && !empty($data)) {
            $data = http_build_query($data);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => strtoupper($method),
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $headers
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        }

        return json_decode($response);
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {
        $histories = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        return $this->storeShipmentHistory($shipment, $histories);
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistoryMassive($shipments)
    {

        $trks = $shipments->pluck('provider_tracking_code')->toArray();
        $trks = array_filter($trks);
        $trks = implode(',', $trks);
        $now  = date('Y-m-d H:i:s');

        $errors = [];
        $logs   = [];
        try {

            $histories = self::getEstadoEnvioMassive($trks);

            foreach ($histories as $key => $historyData) {

                try {

                    $providerTrackingCode = @$historyData[0]['tracking'];

                    if ($providerTrackingCode) {
                        $shipment = $shipments->filter(function ($q) use ($providerTrackingCode) {
                            return $q->provider_tracking_code == $providerTrackingCode;
                        })->first();

                        if ($shipment) {
                            $this->storeShipmentHistory($shipment, $historyData);

                            $logs[] = [
                                'source' => config('app.source'),
                                'webservice' => 'ViaDirecta',
                                'method' => 'updateHistoryMassive',
                                'response' => $providerTrackingCode . ': OK',
                                'status' => 'success',
                                'created_at' => $now
                            ];
                        }
                    } else {
                        $logs[] = [
                            'source' => config('app.source'),
                            'webservice' => 'ViaDirecta',
                            'method' => 'updateHistoryMassive',
                            'response' => $providerTrackingCode . ': Não encontrado',
                            'status' => 'error',
                            'created_at' => $now
                        ];
                    }
                } catch (\Exception $e) {
                    $logs[] = [
                        'source'     => config('app.source'),
                        'webservice' => 'ViaDirecta',
                        'method'     => 'updateHistoryMassive',
                        'response'   => $providerTrackingCode . ': ' . $e->getMessage(),
                        'status'     => 'error',
                        'created_at' => $now
                    ];
                }
            }
        } catch (\Exception $e) {
            $logs[] = [
                'source'     => config('app.source'),
                'webservice' => 'ViaDirecta',
                'method'     => 'updateHistoryMassive',
                'response'   => 'Não obteve dados: ' . $trks . ' - ' . $e->getMessage(),
                'status'     => 'error',
                'created_at' => $now
            ];
        }

        WebserviceLog::insert($logs);

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Store histories
     *
     * @param $shipment
     * @param $histories
     * @return bool
     */
    public function storeShipmentHistory($shipment, $histories)
    {
        if ($histories) {

            $webserviceFatorM3 = null;
            $webserviceWeight = @$histories['weight'];
            unset($histories['weight']);

            if(config('app.source') == '2660express') {
                $webserviceWeight = 0;
            }
            
            /*$shipmentLinked = false;
            if ($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }*/

            //sort status by date
            foreach ($histories as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $histories);


            foreach ($histories as $key => $item) {

                $date = new Carbon($item['created_at']);

                if (empty($item['status_id'])) {
                    $item['status_id'] = 9;
                }
                
                if(config('app.source') == '2660express') {
                    
                    if(str_contains($item['obs'], 'ausente')) {
                        $item['status_id'] = '47'; //AUSENTE
                    } elseif(str_contains($item['obs'], 'Recusado') &&  $item['status'] != "90") { //90-> troca
                        $item['status_id'] = '45'; //TROCA
                    }elseif(str_contains($item['obs'], 'Morada') && str_contains($item['obs'], 'Errada')){
                        //https://enovo.pt/admin/tickets/12457
                        $item['status_id'] = '64';
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

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            //update shipment price
            if ($webserviceWeight > $shipment->weight || $webserviceFatorM3) {
                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                $tmpShipment = $shipment;
                $tmpShipment->fator_m3 = $webserviceFatorM3;
                $prices = Shipment::calcPrices($tmpShipment);

                $shipment->fator_m3 = $webserviceFatorM3;
                $shipment->volumetric_weight = @$prices['volumetricWeight'];
                $shipment->cost_price        = @$prices['cost'];

                if (!$shipment->price_fixed) {
                    $shipment->total_price  = @$prices['total'];
                    $shipment->fuel_tax     = @$prices['fuelTax'];
                    $shipment->extra_weight = @$prices['extraKg'];
                }

                //update linked shipment
                /*if ($shipmentLinked) {
                    $shipmentLinked->weight = $webserviceWeight > $shipmentLinked->weight ? $webserviceWeight : $shipmentLinked->weight;

                    $tmpShipment = $shipmentLinked;
                    $tmpShipment->fator_m3 = $webserviceFatorM3;
                    $prices = Shipment::calcPrices($tmpShipment);


                    $shipmentLinked->fator_m3 = $webserviceFatorM3;
                    $shipmentLinked->volumetric_weight  = $prices['volumetricWeight'];
                    $shipmentLinked->cost_price  = $prices['cost'];

                    if (!$shipmentLinked->price_fixed) {
                        $shipmentLinked->total_price  = @$prices['total'];
                        $shipmentLinked->fuel_tax     = @$prices['fuelTax'];
                        $shipmentLinked->extra_weight = @$prices['extraKg'];
                    }
                }*/
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
            if (config('app.source') === "2660express") {
                $lastHistory = ShipmentHistory::select('status_id', 'created_at')->where('shipment_id', $shipment->id)->orderBy('created_at', 'desc')->first();
                $shipment->status_id   = $lastHistory->status_id;
                $shipment->status_date = $lastHistory->created_at->format('Y-m-d H:i:s');;
            }

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
    public function saveShipment($shipment, $isCollection = false)
    {

        $viaDirectaTRK = $this->trkPrefix . $shipment->tracking_code;

        $reference = 'TRK' . $shipment->tracking_code;
        $reference =  $shipment->reference ? $reference . ' - ' . $shipment->reference : $reference;
        $reference = substr($reference, 0, 30);

        /* try {
            $services = config('shipments_export_mapping.via_directa-services');
            $serviceCode = $services[strtoupper($shipment->recipient_country) . '#' . $shipment->service->code];
        } catch (\Exception $e) {
            throw new Exception('O serviço '. $shipment->service->code .' não tem correspondência com nenhum serviço MRW.');
        }*/

        /*
        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $returnType = 'N';
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnType = 'S'; //retorno mercadoria
        }

        $sabado = 'N';
        if(!empty($shipmentComplementarServices)) {
            //check service sabado
            if(in_array('sabado', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)) {
                $sabado = 'S';
            }

            //return guide
            if(in_array('rguide', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['rguide'], $shipmentComplementarServices)) {
                $returnType = 'D';
            }
        }*/

        //$date = new Date($shipment->date);
        $date = new Date($shipment->date);

        if ($date->lt(Date::today())) {
            throw new \Exception('A data do envio é inferior à data de hoje.');
        }

        $date = $date->addDay(1);
        if ($date->isWeekend()) {
            $date = $date->modify('next monday');
        }
        $date = $date->format('Y-m-d');
        
        $weight = $shipment->weight;
        if(config('app.source') == '2660express') {
            $weight = '0.5';
        }

        $data = [
            'request' => [
                'CABECALHO' => [
                    'Conta_CLIENTE' => $this->clientCode,
                    'Data_Envio'    => $shipment->date,
                    'Utilizador'    => $this->user,
                ],
                'SRV_PEDIDOS' => [
                    'WsSrvPedido' => [
                        'COD_Servico'  => $viaDirectaTRK,
                        'Data_Servico' => $date,
                        'ORIGEM' => [
                            'Nome_O'        => $shipment->sender_name,
                            'Morada_O'      => $shipment->sender_address,
                            'Cod_Postal_O'  => $shipment->sender_zip_code,
                            'Localidade_O'  => $shipment->sender_city,
                            'Pais_O'        => strtoupper($shipment->sender_country),
                            'Telefone_O'    => $shipment->sender_phone
                        ],
                        'DESTINO' => [
                            'Nome_D'        => $shipment->recipient_name,
                            'Morada_D'      => $shipment->recipient_address,
                            'Cod_Postal_D'  => $shipment->recipient_zip_code,
                            'Localidade_D'  => $shipment->recipient_city,
                            'Pais_D'        => strtoupper($shipment->recipient_country),
                            'Telefone_D'    => $shipment->recipient_phone
                        ],
                        'Referencia'  => $reference,
                        'Observacoes' => $shipment->obs,
                        'NumVolumes'  => $shipment->volumes,
                        'Peso_Servico' => $weight,
                        'Valor'        => $shipment->charge_price,
                        'Tipo_Servico' => 'E',
                        'Material_Recolha' => '',
                        'Material_Expede'  => '',
                    ],
                ],
                'LOGIN' => [
                    'Password' => $this->password,
                    'Username' => $this->user
                ]
            ],
        ];

        return $this->storeEnvio($data);
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

            if (!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.via_directa.' . $mappingArray));


            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {

                if ($row['status'] != '999') {
                    //throw new \Exception($row['obs']);

                    $dateParts = explode(' ', $row['date']);
                    $hour = @$dateParts[1];
                    $date = @$dateParts[0];
                    $date = explode('/', $date);
                    $date = @$date[2] . '-' . $date[1] . '-' . $date[0];
                    $row['created_at'] = $date . ' ' . $hour;

                    $status = config('shipments_import_mapping.via_directa-status');
                   
                    $row['status_id'] = @$status[$row['status']];

                    if ($row['status_id'] == '9') { //incidencia
                        /*$incidences = config('shipments_import_mapping.ctt-incidences');
                        $row['incidence_id'] = @$incidences[$row['incidence_id']];*/
                    }

                    if (isset($row)) {
                        $arr[] = $row;
                    }

                    //adiciona estado de devolução após o ultimo estado
                    if (!empty($row['devolution'])) {

                        $date = explode('/', $row['devolution']);
                        $date = @$date[2] . '-' . $date[1] . '-' . $date[0];

                        $arr[] = [
                            'tracking' => $row['tracking'],
                            'status' => 'dev',
                            'status_id' => '7',
                            'created_at' => $date . ' 00:00:00',
                            'obs' => ''
                        ];
                    }
                }
            } else {
                $arr = $row;
            }
        }
        return $arr;
    }

    /**
     * Return url to correct context
     * @param $trk
     * @return string
     */
    public function getUrl()
    {

        /*if(config('app.env') == 'local') {
            return $this->testUrl;
        }*/

        return $this->url;
    }
}
