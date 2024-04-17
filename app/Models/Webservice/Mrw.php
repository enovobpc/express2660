<?php

namespace App\Models\Webservice;

use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\WebserviceLog;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;
use Auth, Mail;

class Mrw extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $testUrl = 'http://sagec.mrw.es/MRWEnvio.asmx?wsdl';//'http://sagec-test.mrw.es/MRWEnvio.asmx?wsdl';
    private $url     = 'http://sagec.mrw.es/MRWEnvio.asmx?wsdl'; //194.224.110.25


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/mrw/';

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
    private $agencia;

    /**
     * @var null
     */
    private $abonado;

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
            $this->agency      = '08203';
            $this->abonado     = '110003';
            $this->departament = '';
            $this->user        = 'SGC8203DMLCunip';
            $this->password    = 'SGC8203@DMLCunip';
        } else {
            $userParts = explode('#', $sessionId);
            $this->agency         = $agencia;
            $this->abonado        = @$userParts[0];
            $this->departament    = @$userParts[1];
            $this->user           = $user;
            $this->password       = $password;
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
        $url = 'https://trackingservice.mrw.es/TrackingService.svc?wsdl';

        $data = [
            'login'             => $this->user,
            'pass'              => $this->password,
            'codigoAbonado'     => $this->abonado,
            'codigoFranquicia'  => $this->agency,
            'valorFiltroDesde'  => $trackingCode,
            /*'fechaDesde'        => '2020-07-01',
            'fechaHasta'        => '2020-07-15',*/
            'codigoIdioma'      => '2070',
            'tipoFiltro'        => '0',
            'tipoInformacion'   => '1'
        ];

        $client = new \SoapClient($url);
        $result = $client->GetEnvios($data);

        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = $result['GetEnviosResult'];

        if(!@$result['Seguimiento']) {
            throw new \Exception(@$result['MensajeSeguimiento']);
        }

        $result = @$result['Seguimiento']['Abonado']['SeguimientoAbonado']['Seguimiento'];

        if(isset($result['Estado'])) { //quando só tem 1 estado, o resultado vem diferente
            $result = [$result];
        }

        $result = $this->mappingResult($result, 'status');

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

        $trks = explode(',', $trks);

        $startDate = new Date();
        $startDate = $startDate->subDays(90)->format('d/m/Y');

        $url = 'https://trackingservice.mrw.es/TrackingService.svc?wsdl';

        $data = [
            'login'             => $this->user,
            'pass'              => $this->password,
            'codigoAbonado'     => $this->abonado,
            'codigoFranquicia'  => $this->agency,
            //'valorFiltroDesde'  => $trackingCode1,
            //'valorFiltroHasta'  => $trackingCode2,
            'fechaDesde'        => $startDate,
            'fechaHasta'        => date('d/m/Y'),
            'codigoIdioma'      => '2070',
            //'tipoFiltro'        => '0',
            'tipoInformacion'   => '0' //0 => só mostra ultimo estado // 1=> mostra todos o historico
        ];


        $client = new \SoapClient($url, ['cache_wsdl' => WSDL_CACHE_NONE]);
        $result = $client->GetEnvios($data);

        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = $result['GetEnviosResult'];


        if(!@$result['Seguimiento']) {
            throw new \Exception(@$result['MensajeSeguimiento']);
        }

        $result = @$result['Seguimiento']['Abonado']['SeguimientoAbonado']['Seguimiento'];

        if(isset($result['Estado'])) { //quando só tem 1 estado, o resultado vem diferente
            $result = [$result];
        }

        $histories = [];
        foreach ($result as $row) {
            if(in_array($row['NumAlbaran'], $trks)) {

                /*if($row['NumAlbaran'] == '080112924856') {
                   dd($row);
               }*/

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
        /*require_once base_path() . '/resources/helpers/DOMhtml.php';

        $url = 'http://sagec.mrw.es/panelseguimiento.aspx?usuario='.$this->user.'&pass='.$this->password.'&albaran='. $trakingCode;

        $html = file_get_html($url);

        $images = [];

        dd($html->plaintext);
        foreach($html->find('#tblImagenes') as $row) {

            dd($row->src);
            dd($row->getElementsByTagName('img'));
            $image = @$row->find('img', 0)->src;
        }

        dd($images);*/

        return false;
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
       
        try {
            $url = 'https://sagec.mrw.es/panelseguimiento.aspx?usuario='.$this->user.'&pass='.$this->password.'&albaran=' .$trackingCode;
            
            require_once base_path() . '/resources/helpers/DOMhtml.php';
            $html = file_get_html($url);


            $weight = $html->find('#lblKilos', 0)->plaintext;

            return [
                'weight' => $weight
            ];

        } catch(\Exception $e) {
            return [
                'weight' => null
            ];
        }
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {

        $headers = $this->getHeaders();

        //dd($headers);

        $client = new \SoapClient($this->getUrl());
        $client->__setSoapHeaders($headers);
        $result = $client->TransmEnvio($data);

        $result = json_encode($result);
        $result = json_decode($result, true);
        $result = @$result['TransmEnvioResult'];

        if(!@$result['Estado']) {
            throw new \Exception($result['Mensaje']);
        }

        $trk = $result['NumeroEnvio'];
        //$trk = $result['NumeroSolicitud'];
        //$url = $result['Url'];

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

        $data = [
            'request' => [
                'NumeroEnvio' => $trackingCode,
                'SeparadorNumerosEnvio' => '',
                'ReportTopMargin' => '0',
                'ReportLeftMargin' => '0'
            ]

            /*'NumerosEtiqueta' => 1,
            'TipoEtiquetaEnvio' => '',
            'SeparadorNumerosEnvio' => '',
            'FechaInicioEnvio' => '',
            'FechaFinEnvio' => '',
            'ReportTopMargin' => '',
            'ReportLeftMargin' => '',*/
        ];

        $headers = $this->getHeaders();

        $client = new \SoapClient($this->getUrl());
        $client->__setSoapHeaders($headers);
        $result = $client->EtiquetaEnvio($data);

        $result = (array) $result;
        $result = $result['GetEtiquetaEnvioResult'];

        if(!@$result->Estado) {
            throw new \Exception($result->Mensaje);
        }

        $labels = base64_encode($result->EtiquetaFile);
        return $labels;
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
     * Auth user on API
     *
     * @param type $data
     * @return type
     */
    public function getHeaders()
    {
        $auth = [
            'CodigoFranquicia'   => $this->agency,
            'CodigoAbonado'      => $this->abonado,
            'CodigoDepartamento' => $this->departament,
            'UserName'           => $this->user,
            'Password'           => $this->password,
        ];

        $header = new \SoapHeader('http://www.mrw.es/', 'AuthInfo', $auth);

        return $header;
    }

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
        if(!$rawData && !empty($data)) {
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
    public function updateHistory($shipment) {

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        return $this->storeShipmentHistory($shipment, $data);
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistoryMassive($shipments) {

        $trks = $shipments->pluck('provider_tracking_code')->toArray();
        $trks = implode(',', $trks);
        $now  = date('Y-m-d H:i:s');

        $errors = [];
        $logs   = [];
        try {

            $histories = self::getEstadoEnvioMassive($trks);

            foreach ($histories as $key => $historyData) {

                try {

                    $providerTrackingCode = @$historyData[0]['tracking_code'];

                    if($providerTrackingCode) {
                        $shipment = $shipments->filter(function ($q) use ($providerTrackingCode) {
                            return $q->provider_tracking_code == $providerTrackingCode;
                        })->first();

                        if ($shipment) {
                            $this->storeShipmentHistory($shipment, $historyData);

                            $logs[] = [
                                'source' => config('app.source'),
                                'webservice' => 'Mrw',
                                'method' => 'updateHistoryMassive',
                                'response' => $providerTrackingCode . ': OK',
                                'status' => 'success',
                                'created_at' => $now
                            ];
                        }
                    } else {
                        $logs[] = [
                            'source' => config('app.source'),
                            'webservice' => 'Mrw',
                            'method' => 'updateHistoryMassive',
                            'response' => $providerTrackingCode . ': Não encontrado',
                            'status' => 'error',
                            'created_at' => $now
                        ];
                    }


                } catch (\Exception $e) {
                    $logs[] = [
                        'source'     => config('app.source'),
                        'webservice' => 'Mrw',
                        'method'     => 'updateHistoryMassive',
                        'response'   => $providerTrackingCode . ': '. $e->getMessage(),
                        'status'     => 'error',
                        'created_at' => $now
                    ];
                }
            }

        } catch (\Exception $e) {
            $logs[] = [
                'source'     => config('app.source'),
                'webservice' => 'Mrw',
                'method'     => 'updateHistoryMassive',
                'response'   => 'Não obteve dados: '. $trks . ' - '. $e->getMessage(),
                'status'     => 'error',
                'created_at' => $now
            ];
        }

        WebserviceLog::insert($logs);

        if(!empty($errors)) {
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
    public function storeShipmentHistory($shipment, $histories) {

        if($histories) {

            /*$shipmentLinked = false;
            if($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }*/

            //sort status by date
            foreach ($histories as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $histories);

            foreach ($histories as $key => $item) {

                if(empty( $item['status_id'])) {
                    $item['status_id'] = 9;
                }

                //processa estados exclusivos de pedidos de recolha
                if($shipment->is_collection) {
                    if($item['status_id'] == ShippingStatus::SHIPMENT_PICKUPED || $item['status_id'] == ShippingStatus::DELIVERED_ID) {
                        $item['status_id'] = ShippingStatus::PICKUP_DONE_ID;
                    } elseif($item['status_id'] == ShippingStatus::INCIDENCE_ID) {
                        $item['status_id'] = ShippingStatus::PICKUP_FAILED_ID;
                    }
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;

                if($history->status_id == 9 || $history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $history->obs = @$item['status_name'];
                }

                if($history->status_id == ShippingStatus::DELIVERED_ID || $history->status_id == ShippingStatus::DEVOLVED_ID) { //atualiza o peso quando é entregue/devolvido
                    $shp = $this->getEnvioByTrk(null, null, $shipment->provider_tracking_code);
                    $weight = $shp['weight'];

                    if($weight > ceil($shipment->weight)) {
                        $history->obs     = $history->obs . ' Peso atualizado: '.$weight.'kg';
                        $shipment->weight = $weight;

                         //calcula preços
                        $prices = Shipment::calcPrices($shipment);
                        if(@$prices['fillable']) {
                            $shipment->fill($prices['fillable']);

                            //adiciona taxas
                            //$shipment->storeExpenses($prices);
                        }
                    }
                }

                $history->save();

                $history->shipment = $shipment;

                if($shipment->is_collection && $history->status_id == ShippingStatus::PICKUP_DONE_ID) {
                    $shipment->status_id   = $history->status_id;
                    $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');
                    $this->createShipmentFromPickup($shipment);

                     //previne continuar a atualizar estados seguintes à recolha pois a recolha foi concluida.
                    //a atualização deve prosseguir só no envio
                    break;
                } elseif($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');
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

        $reference = 'TRK' . $shipment->tracking_code;
        $reference =  $shipment->reference ? $reference. ' - '.$shipment->reference : $reference;

        $serviceCode = $this->getProviderService($shipment);

        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $returnType = 'N';
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnType = 'S'; //retorno mercadoria
        }

        //return pod
        if($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnType = 'D'; //retorno guia assinada
        }

        //return check
        if($shipment->has_return && in_array('rcheck', $shipment->has_return)) {
            $returnType = 'R'; //retorno cheque
        }

        $sabado = 'N';
        if(!empty($shipmentComplementarServices)) {
            //check service sabado
            if(in_array('sabado', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)) {
                $sabado = 'S';
            }
        }

        //na MRW se o codigo postal de origem não for da propria agencia, é gerada um pedido de recolha automaticamente.
        //para prevenir isto (porque as agencias não querem isso), então forçamos a que seja sempre assumido o codigo postal da agência do cliente
        //que está gravado nas definições gerais do programa.
        $senderAddress = $shipment->sender_address;
        $senderZipCode = $shipment->sender_zip_code;
        $senderCity    = $shipment->sender_city;
        $senderCountry = $shipment->sender_country;

        if(!$shipment->is_collection) {
            $source = config('app.source');

            if($source == 'dmlconsultoria') {
                $senderZipCode = '4410';
            } elseif($source == 'tortugaveloz') {
                $senderZipCode = '2810'; //agencia de setubal e guarda usam mesmo contrato
            } else {
                //força envios a sairem sempre pelos dados/codigo postal da agência.
                $senderAddress = trim(@$shipment->agency->address);
                $senderZipCode = trim(@$shipment->agency->zip_code);
                $senderCity    = trim(@$shipment->agency->city);
                $senderCountry = trim(@$shipment->agency->country);
            }
        }
        

        $scheduleHorary = [];
        if(!empty($shipment->start_hour) || !empty($shipment->end_hour)) {
            $scheduleHorary = [
                'Rangos' => [
                    'HorarioRangoRequest' => [
                        'Desde' => $shipment->start_hour ? $shipment->start_hour : '08:00',
                        'Hasta' => $shipment->end_hour ? $shipment->end_hour : '19:00'
                    ]
                ]
            ];
        }
        
        $bultos = [];
        $partialWeight = $shipment->weight / $shipment->volumes;

/*         if(in_array(config('app.source'), ['jpsff'])) { //na jpsff a MRW obriga a inserir dimensões. Opção exclusiva apenas para algiuns clientes para nos outros ser mais rapido. Lembrar que Activos24 documenta massivamente.
            if(!$shipment->pack_dimensions->isEmpty()) {
                    
                foreach($shipment->pack_dimensions as $packDimension) {
                    
                    $qtyTotal = !empty($packDimension->qty) ? $packDimension->qty : 1;
            
                    for($i=1; $i<=$qtyTotal; $i++) {
                        $ancho   = $packDimension->length > 0.00 ? $packDimension->length : 0.1;
                        $largo   = $packDimension->width  > 0.00 ? $packDimension->width  : 0.1;
                        $alto    = $packDimension->height > 0.00 ? $packDimension->height : 0.1;
                        //$bweight = $packDimension->weight > 0.00 ? $packDimension->weight : $partialWeight;
                        
                        $bultos[] = [
                            'Ancho' => (float) $ancho,
                            'Largo' => (float) $largo,
                            'Alto'  => (float) $alto,
                            //'Peso'  => (float) $bweight,
                        ];
                    }
                }
                
            } else {
                
                for($i=1 ; $i<$shipment->volumes ; $i++) {
                    $bultos[] = [
                        'Ancho' => 0.1,
                        'Largo' => 0.1,
                        'Alto'  => 0.1,
                        //'Peso'  => $partialWeight
                    ];
                }
            }
        } */

        $data = [
            'request' => [
                'DatosRecogida' => [
                    'Direccion' => [
                        'Via'           => $senderAddress,
                        'CodigoPostal'  => zipcodeCP4($senderZipCode),
                        'Poblacion'     => $senderCity,
                        'CodigoPais'    => $senderCountry,
                        /*
                        'Numero'        => '',
                        'CodigoDireccion' => '',
                        'CodigoTipoVia' => '',
                        'Provincia'     => '',
                        'Resto'         => '',
                        'Estado'        => '',
                        'TipoPuntoEntrega' => '',
                        'CodigoFranquiciaAsociadaPuntoEntrega' => '',
                        'TipoPuntoRecogida' => '',
                        'CodigoPuntoRecogida' => '',
                        'CodigoFranquiciaAsociadaPuntoRecogida' => '',
                        'Agencia' => '',*/
                    ],
                    //'Nif' => '',
                    'Nombre'   => $shipment->sender_name,
                    'Telefono' => $shipment->sender_phone,
                    'Contacto' => $shipment->sender_attn,
                    'Horario' => $shipment->is_collection ? $scheduleHorary : [],
                    'Observaciones' => $shipment->is_collection ? $shipment->obs : '',
                ],
                'DatosEntrega' => [
                    'Direccion' => [
                        'Via'           => $shipment->recipient_address,
                        'CodigoPostal'  => zipcodeCP4($shipment->recipient_zip_code),
                        'Poblacion'     => $shipment->recipient_city,
                        'CodigoPais'    => $shipment->recipient_country,
                        /*
                       'Numero'        => '',
                       'CodigoDireccion' => '',
                       'CodigoTipoVia' => '',
                       'Provincia'     => '',
                       'Resto'         => '',
                       'Estado'        => '',
                       'TipoPuntoEntrega' => '',
                       'CodigoFranquiciaAsociadaPuntoEntrega' => '',
                       'TipoPuntoRecogida' => '',
                       'CodigoPuntoRecogida' => '',
                       'CodigoFranquiciaAsociadaPuntoRecogida' => '',
                       'Agencia' => '',*/
                    ],
                    //'Nif'       => '',
                    'Nombre'    => $shipment->recipient_name,
                    'Telefono'  => $shipment->recipient_phone,
                    'Contacto'  => $shipment->recipient_attn,
                    'Horario' =>  $shipment->is_collection ? [] : $scheduleHorary,
                    'Observaciones' => $shipment->is_collection ? $shipment->obs_delivery : $shipment->obs,
                ],
                'DatosServicio' => [
                    'Fecha'          => $shipment->date,
                    'NumeroAlbaran'  => $shipment->provider_tracking_code,
                    'EnFranquicia'   => 'N', //recolha/entrega em agencia [R: com recolha agencia / E: entrega na agencia / A: recolha e entrega na agencia]
                    'Referencia'     => $reference,
                    'CodigoServicio' => $serviceCode,
                    'Bultos'         => $bultos,
                    'NumeroBultos'   => $shipment->volumes,
                    'Peso'           => str_replace('.', ',', $shipment->weight),
                    'EntregaSabado'  => $sabado,
                    'EntregaPartirDe'=> $shipment->start_hour,
                    'Gestion'        => $shipment->payment_at_recipient ? 'D' : 'N',
                    'Retorno'        => $returnType,
                    'CodigoServicioRetorno' => $serviceCode,
                    'Reembolso'             => $shipment->charge_price ? 'O' : 'N',
                    'ImporteReembolso'      => $shipment->charge_price ? str_replace('.', ',', $shipment->charge_price) : '',
                    'PortesDebidos'         => $shipment->total_price_for_recipient,
                   //'Bultos'        => $bultos,
                ],
            ],
        ];

        if(config('app.source') == 'activos24') {
            if($shipment->recipient_email) {
                $data['request']['DatosServicio']['Notificaciones'] = [
                    'NotificacionRequest' => [
                        'CanalNotificacion' => '1', //1=email / 2=sms
                        'TipoNotificacion'  => '2', //2=alteracao estados
                        'MailSMS'           => $shipment->recipient_email,
                    ]
                ];
            }
        }


        if(Auth::check() && Auth::user()->isAdmin()) {
            // dd($data);
        }

        $trk = $this->storeEnvio($data);
        $shipment->provider_tracking_code = $trk;

        $this->notifySenderAgencyByZipCode($shipment);

        return $trk;
    }

    /**
     * Permite obter um envio da base de dados  um envio pelo seu trk caso exista envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function createShipmentFromPickup($originalShipment)
    {

        $shipment = Shipment::firstOrNew([
            'provider_tracking_code' => $originalShipment->provider_tracking_code,
            'is_collection' => 0
        ]);

        if(!$shipment->exists) {

            $service  = Service::filterSource()->where('id', $originalShipment->service_id)->first();

            $shipment = $originalShipment->replicate();
            $shipment->is_collection        = false; 
            $shipment->tracking_code        = null; 
            $shipment->service_id           = @$service->id;
            $shipment->date                 = $originalShipment->status_date;
            $shipment->type                 = Shipment::TYPE_PICKUP;
            $shipment->parent_tracking_code = $originalShipment->tracking_code;
            $shipment->status_id            = $originalShipment->status_id == ShippingStatus::PICKUP_FAILED_ID ? ShippingStatus::PICKUP_FAILED_ID : ShippingStatus::WAINTING_SYNC_ID; //rec. falhada ou aguarda sync
            $shipment->submited_at          = Date::now();

            $trk = $shipment->setTrackingCode();
        }

        if ($originalShipment->total_price_after_pickup > 0.00) {
            $shipment->shipping_price = $originalShipment->total_price_after_pickup;
            $shipment->price_fixed    = true;
        }

        //adiciona taxa de recolha
        $shipment->insertOrUpdadePickupExpense($originalShipment); //add expense
        $originalShipment->update([
            'children_tracking_code' => $shipment->tracking_code,
            'children_type' => Shipment::TYPE_PICKUP,
            'status_id'     => ShippingStatus::PICKUP_DONE_ID
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

        return $trk;
    }

    /**
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = []) {
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

            $row = mapArrayKeys($row, config('webservices_mapping.mrw.'.$mappingArray));

            //mapping and process status
            if($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $row['created_at'] = new Date($row['created_at']);
                $row['created_at'] = $row['created_at']->format('Y-m-d H:i:s');

                $status = config('shipments_import_mapping.mrw-status');

                $row['status_id'] = @$status[$row['status']];

                if($row['status_id'] == '9') { //incidencia
                    $incidences = config('shipments_import_mapping.mrw-incidences');
                    $row['incidence_id'] = @$incidences[$row['status']];
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
     * Return url to correct context
     * @param $trk
     * @return string
     */
    public function getUrl() {

        if(config('app.env') == 'local') {
            return $this->testUrl;
        }

        return $this->url;
    }

    public function getZipCodeFunchal() {
        return [9000,9004,9020,9024,9030,9050,9054,9060,9064,9100,9125,9135,9200,9225,9230,9240,9270,9300,9304,9325,9350,9360,9370,9374,9385];
    }

    public function getZipCodeAcores() {
        return [9580,9500,9504,9545,9555,9560,9600,9625,9630,9650,9675,9680,9684,9700,9701,9760,9880,9800,9804,9850,9875,9930,9934,9940,9944,9950,9900,9901,9904,9960,9970,9980];
    }

    public function getZipCodePortoSanto() {
        return [9400];
    }

    public function notifySenderAgencyByZipCode($shipment) {

        $zipCode = explode('-', trim($shipment->sender_zip_code));
        $zipCode = $zipCode[0];

        $zipCodesList = [
            //paredes
            '4580' => '08202@grupomrw.com',
            '4585' => '08202@grupomrw.com',
            '4560' => '08202@grupomrw.com',
            '4575' => '08202@grupomrw.com',
            '4590' => '08202@grupomrw.com',
            '4595' => '08202@grupomrw.com',
            '4620' => '08202@grupomrw.com',

            //trofa
            '4780' => '08210@grupomrw.com',
            '4785' => '08210@grupomrw.com',
            '4795' => '08210@grupomrw.com',
            '4825' => '08210@grupomrw.com',
            '4745' => '08210@grupomrw.com',

            //gaia
            '4400' => '08203@grupomrw.com',
            '4405' => '08203@grupomrw.com',
            '4410' => '08203@grupomrw.com',
            '4415' => '08203@grupomrw.com',
            '4420' => '08203@grupomrw.com',
            '4430' => '08203@grupomrw.com',
            '4435' => '08203@grupomrw.com',
            '4440' => '08203@grupomrw.com',
            '4445' => '08203@grupomrw.com',
            '4510' => '08203@grupomrw.com',
            '4510' => '08203@grupomrw.com',
            '4515' => '08203@grupomrw.com',

            //alfena
            '' => '08220@grupomrw.com'
        ];

        $email = @$zipCodesList[$zipCode];

        try {
            //envia email a notificar a agencia
            if (!empty($email) && config('app.source') == 'dmlconsultoria') {

                Mail::send('emails.shipments.tracking_agency', compact('shipment'), function ($message) use ($email) {
                    $message->to($email);
                    $message->subject('Notificação: pedido de recolha');
                });

            }
            return false;

        } catch (\Exception $e) {}
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

            if($providerService == 'MARPT') {
                $zp4 = explode('-', $shipment->recipient_zip_code);
                $zp4 = $zp4[0];

                if (in_array($shipment->service->code, ['AI'])) {
                    if (in_array($zp4, $this->getZipCodeFunchal())) {
                        $providerService = '0221';
                    } else if (in_array($zp4, $this->getZipCodePortoSanto())) {
                        $providerService = '0222';
                    } else if (in_array($zp4, $this->getZipCodeAcores())) {
                        $providerService = '0223';
                    }
                }
            }


            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if(!$providerService) {
                $zp4 = explode('-', $shipment->recipient_zip_code);
                $zp4 = $zp4[0];

                if(in_array($shipment->service->code, ['AI'])) {
                    if(in_array($zp4, $this->getZipCodeFunchal())) {
                        $providerService = '0221';
                    } else if(in_array($zp4, $this->getZipCodePortoSanto())) {
                        $providerService = '0222';
                    } else if(in_array($zp4, $this->getZipCodeAcores())) {
                        $providerService = '0223';
                    }
                } else {
                    $services = config('shipments_export_mapping.mrw-services');

                    if($shipment->recipient_country == 'pt' || $shipment->recipient_country == 'es') {
                        $providerService = $services[strtoupper($shipment->recipient_country) . '#' . $shipment->service->code];
                    } else {
                        $providerService = $services['INT#' . $shipment->service->code];
                    }
                }
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço MRW.');
        }

        return $providerService;
    }
}