<?php

namespace App\Models\Webservice;

use App\Models\ShippingStatus;
use Carbon\Carbon;
use Date, File, Setting;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\WebserviceConfig;
use Exception;
use LynX39\LaraPdfMerger\PdfManage;

class Ontime extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url = 'https://gtsontimepre.alertran.net/gts/seam/resource/restv1/auth/';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/ontime/';

    /**
     * @var null
     */
    private $agencia;

    /**
     * @var null
     */
    private $username;

    /**
     * @var null
     */
    private $password;

    /**
     * @var null
     */
    private $sessionId;

    /**
     * @var null
     */
    private $debug;

    /**
     * On Time constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug = false)
    {
        //if(config('app.env') == 'local') {
            $this->username     = $user;
            $this->password     = $password;
            $this->agencia      = $agencia;
            $this->sessionId    = $sessionId;

        //} else {
        //    $this->username = $user;
        //    $this->password = $password;
        //}

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
    public function getEstadoEnvioByTrk($shipments)
    {
        if(!$shipments) {
            return false;
        }

        $data = [
            'DETALLES_EXPEDICION' => [
                'VERSION' => '3',
                'DETALLE_EXPEDICION' => [
                    [
                        'CLIENTE'       => $this->sessionId,
                        'CENTRO'        => $this->agencia,
                        "EXPE_NUMERO"   => $shipments
                    ]
                ]
            ]
        ]; 

        $response = $this->execute('detalleExpedicioneService/detalles', $data);

        try {

            if (empty(@$response['0']['respuestaDetalleExpediciones']['listaEventos'])) {
                throw new \Exception('Sem informação de estados.');
            } else {

                $history = @$response['0']['respuestaDetalleExpediciones']['listaEventos'];
                $history = $this->mappingResult($history, 'status');

                return $history;
            }


        } catch (\Exception $e) {
            dd($e);
        }

        return $response;
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
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia){
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

    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data, $service)
    {
        
        $volumes = (int) $data['volumes'];
        $weight  = (float) $data['weight'];
        $date = Carbon::createFromFormat('Y-m-d', $data['date'])->format('d/m/Y');
        $recipientZipCode   = str_replace('-', '', $data['recipient_zip_code']);
        $SenderZipCode      = str_replace('-', '', $data['sender_zip_code']);

        
        $details =  [
            "NOMBRE"                => $data['recipient_name'],
            "CONTACTO"              => $data['recipient_attn'],
            "DIRECCION"             => $data['recipient_address'],
            "POBLACION"             => $data['recipient_city'],
            "TELEFONO"              => $data['recipient_phone'],
            "NIF"                   => $data['recipient_vat'],
            "EMAIL"                 => "trk@trk.com",
            "OBSERVACIONES"         => $data['obs'],
            "CODIGO_POSTAL"         => $recipientZipCode,
            "PAIS"                  => $data['recipient_country'],
            "NUMERO_BULTOS"         => $volumes,
            "PESO"                  => $weight,
            "VOLUMEN"               => $data['volumetric_weight'],
            "IMPORTE_REEMBOLSO"     => $data['charge_price'],
            "TIPO_PORTES"           => "",
            "VALOR_DECLARADO"       => "",
            "COBRADO"               => "",
            "CENTRO_DESTINO"        => "",
            "CLIENTE_DESTINO"       => "",
            "COMUNICA_SMS"          => "",
            "COMUNICA_MAIL"         => "S",
            "ENTREGA_SABADO"        => "",
            "RETORNO"               => "",
            "PRODUCTO"              => $service,
            "FECHA_ENTREGA"         => "",
        ];

        $data = [
            'RECOGIDA' => [
                "CLIENTE"           => $this->sessionId,
                "CENTRO"            => $this->agencia,
                "NOMBRE"            => $data['sender_name'],
                "DIRECCION"         => $data['sender_address'],
                "POBLACION"         => $data['sender_city'],
                "CODIGO_POSTAL"     => $SenderZipCode,
                "PAIS"              => $data['sender_country'],
                "CONTACTO"          => $data['sender_attn'],
                "REFERENCIA"        => $data['reference'],
                "TELEFONO"          => $data['sender_phone'],
                "EMAIL"             => "trk@trk.com",
                "OBSERVACIONES"     => $data['obs'],
                "FECHA"             => $date,
                "HORA_MA_DESDE"     => "09:00",
                "HORA_MA_HASTA"     => "10:00",
                "HORA_TARDE_DESDE"  => "15:00",
                "HORA_TARDE_HASTA"  => "16:00",
                "DETALLES_RECOGIDA" => [$details]
            ]
        ];

    $response = $this->execute('documentarRecogidaService/crearRecogida', $data);

    try {
        $trk = false;
        if ($response) {
            $trk = $response['respuestaRecogidas']['recogida'];
        }

    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }

    return $trk;
    }

    /**
     * Return pack type
     * @param $packType
     * @return mixed|string
     */
    private function getPackType($packType)
    {

        $mapping = [
            'BOX'       => 'PALET PLUMA'
        ];

        return @$mapping[$packType] ? $mapping[$packType] : 'PALET PLUMA';
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data, $service)
    {
        $volumes = (int) $data['volumes'];

        $recipientZipCode   = str_replace('-', '', $data['recipient_zip_code']);

        $packing = [];
        
        if ($service == 26 || $service == 27 && empty($data['packaging_type'])) {
            throw new \Exception('Obrigatório preencher volumes');
            
        } elseif (empty($data['packaging_type'])) {
            
            foreach (@$data['packaging_type'] as $volume) {
                $packing[] = [
                    "TIPO" => "Diversos",
                    "CANTIDAD" => $volume
                ];
            }
            
        } elseif ($data['packaging_type']) {
            
            foreach (@$data['packaging_type'] as $volume) {
                $packing[] = [
                    "TIPO" => $this->getPackType($data['packaging_type']),
                    "CANTIDAD" => $volume
                ];
            }
        }
        

        $dataEnvio1 = [
                "CODIGO_ADMISION"                => "1001",
                "NUMERO_BULTOS"                  => $volumes,
                "CLIENTE_REMITENTE"              => $this->sessionId,
                "CENTRO_REMITENTE"               => $this->agencia,
                "NOMBRE_REMITENTE"               => $data['sender_name'],
                "NOMBRE_DESTINATARIO"            => $data['recipient_name'],
                "DIRECCION_DESTINATARIO"         => $data['recipient_address'],
                "PAIS_DESTINATARIO"              => $data['recipient_country'],
                "CODIGO_POSTAL_DESTINATARIO"     => $recipientZipCode,
                "POBLACION_DESTINATARIO"         => $data['recipient_city'],
                "PERSONA_CONTACTO_DESTINATARIO"  => $data['recipient_attn'],
                "TELEFONO_CONTACTO_DESTINATARIO" => $data['recipient_phone'],
                "EMAIL_DESTINATARIO"             => "trk@trk.com",
                "CODIGO_PRODUCTO_SERVICIO"       => $service,
                "KILOS"                          => $data['taxable_weight'],
                "VOLUMEN"                        => @$data['fator_m3'],
                "CLIENTE_REFERENCIA"             => $data['tracking_code'],
                "IMPORTE_REEMBOLSO"              => $data['charge_price'],
                "TIPO_PORTES"                    => "P",
                "OBSERVACIONES1"                 => $data['obs'],
                "FECHA_ENTREGA_APLAZADA"         => "",
                "ENTREGA_APLAZADA"               =>"N",
                "TIPOS_BULTO"                    => $packing,
                "ENVIO_CON_RECOGIDA"             => "N",  //sempre N
                "IMPRIMIR_ETIQUETA"              => "S",
                "ENVIO_DEFINITIVO"               => "N",  //sempre N
                "TIPO_FORMATO"                   => "PDF"
        ];


        $data = [
            'DOCUMENTAR_ENVIOS' => [
                "DOCUMENTAR_ENVIO" => [$dataEnvio1]
            ]
        ];
        
        $response = $this->execute('documentarEnvio/json', $data);

        try {
            $trk = false;

            $senderAgency = "02";
            $trk = $response[0]['respuestaDocuemtarEnvio']['numero_envio'];

            //DOWNLOAD DA ETIQUETA
            try {
                if($response[0]['respuestaDocuemtarEnvio']['etiqueta']) {
                    $this->downloadLabel($senderAgency, $trk);
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
    public function getEtiqueta($agency = null, $trackingCode = null)
    {
        $trackingCode = explode(',', $trackingCode);
        $trackingCode = @$trackingCode[0];

        $file = File::get(public_path().'/uploads/labels/ontime/'.$trackingCode.'_labels.pdf');

        $file = base64_encode($file);
        return $file;
    }

    /**
     * Download and store on time label
     *
     * @param $senderAgency
     * @param $trackingCode
     * @return bool
     * @throws \Exception
     */
    public function downloadLabel($senderAgency, $trackingCode, $save = true) {

        $folder = public_path() . $this->upload_directory;
        

        if(!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
        $dataEtiqueta1 = [
            'CLIENTE'       => $this->sessionId,
            'CENTRO'        => $this->agencia,
            'EXPEDICION'    => $trackingCode,
            'FORMATO'       => "PDF",
            'POSICION'      => "0",
            'ETIQUETAR'     => ""
        ];

        $data = [
            'ETIQUETAS' => [
                "ETIQUETA" => [$dataEtiqueta1]
            ]
        ];

        $result = $this->execute('etiquetarService/etiquetar',$data);
        $label  = base64_decode(@$result['0']['respuestaEtiquetar']['etiqueta']);

        if ($label) {
            if($save) {
                $result = File::put($folder . $trackingCode . '_labels.pdf', $label);

                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta.');
                }
            }
        }

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
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode) {}


    /**
     * Permite eliminar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function destroyEnvioByTrk($trackingCode, $service)
    {
        return true;
    }


    /**
     * Permite eliminar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function destroyShipment($shipment)
    {
            $data = [
                'webExpediciones' => [
                    "numeroWebExpedicion"       => $shipment->provider_tracking_code,
                    "clienteOrigen"             => "90000076"
                ]
            ];

            $result = $this->execute('anularWebExpediciones/anular',$data);

            if (@$result['codError']) {
                throw new \Exception($result['codError'] . ' - ' . $result['mensError']);
            }

        return true;
    }

    /**
     * Fecha os envios
     *
     * @param array $shipments
     */
    public function fechaEnvios($shipments)
    {


        $folder = public_path() . '/uploads/labels/certificates/';
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
  
        ini_set('default_socket_timeout', 600000);

        $expeditions = [];

        foreach ($shipments as $shipment) {
            $trk = explode(',', $shipment['provider_tracking_code']);
            $firstTrk = $trk[0];
            $lastTrk = isset($trk[1]) ? $trk[1] : $firstTrk;
        
            $expeditions[] = [
                'EXPEDICION' => $firstTrk
            ];
        }
        
        $dataArr = [
            'ENTREGA' => [
                'CLIENTE'       => $this->sessionId,
                'CENTRO'        => $this->agencia,
                'EXPEDICIONES' => $expeditions,
                'ENVIO_CON_RECOGIDA' => 'N', //sempre N
                'MANIFIESTO' => 'S'
                
            ]
        ];
        
        $data = [
            'ENTREGAS' => $dataArr
        ];
        

        $result = $this->execute('entregaRecogedorService/entregaRecogidas',$data);
        $status = @$result[0]['respuestaEntregaRecogida']['resultado'];

 
            foreach ($shipments as $shipment) {
                    Shipment::where('customer_id', $shipment['customer_id'])
                        ->where('provider_tracking_code', $shipment['provider_tracking_code'])
                        ->update(['is_closed' => 1]);
                }
            
            $label  = base64_decode(@$result['0']['respuestaEntregaRecogida']['manifiesto']);

            if ($label) {
                $result = File::put($folder . 'ontime_'. $shipment['provider_tracking_code'] . '.pdf', $label);
                
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar o manifesto.');
                }
            }

    return '/uploads/labels/certificates/ontime_'. $shipment['provider_tracking_code'] . '.pdf';

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
    private function execute($url, $data = [], $method = 'POST')
    {
        $data = json_encode($data);

        $curl = curl_init();
  
        $header = [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($this->username.':'.$this->password),
        ];

        $url = $this->url . $url;

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $header,
        ));
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        $error = curl_error($curl);
        $errorNum = curl_errno($curl);

        curl_close($curl);

        if(!$response) {
            throw new \Exception('Error: "'. $error. '" - Code: '. $errorNum);
        } else {
            if(!empty(@$response['key'])) {
                throw new \Exception(@$response['key'] . ' - ' . $response['message'] . ' ' . (@$response['details'] ? json_encode(@$response['details']) : ''));
            }

            return $response;
        }
    }

    /**
     * @return array
     */
    public function login()
    {
        $data = [
            "username" => $this->username,
            "key" => $this->key,
        ];

        $result = $this->execute('authenticate/api-key', $data);
        return @$result['accessToken'];
    }

    /**
     * @return array
     */
    public function capabilities($data)
    {
        $data = [
            'fromCountry' => $data['sender_country'],
            'toCountry'   => $data['recipient_country']
        ];

        $queryString = http_build_query($data);

        $url = 'capabilities/business?'. $queryString;

        $result = $this->execute($url, [], 'GET');
        return $result;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {
        
        $data = self::getEstadoEnvioByTrk($shipment->provider_tracking_code);

        $webserviceWeight  = null;

        if ($data) {

            $data = array_reverse($data);

            foreach ($data as $item) {

                $onTimeStatus = config('shipments_import_mapping.ontime-status');

                $item['status_id']  = @$onTimeStatus[$item['status']];
                $item['created_at'] = new Date($item['delivery_date']);
                
                if (empty($item['status_id'])) {
                    throw new \Exception('Estado com o código ' . $item['status'] . ' sem mapeamento.');
                }

                if (@$item['status_id'] == '9') {

                    $onTimeIncidences = config('shipments_import_mapping.on-incidences');

                    $incidenceId = @$onTimeIncidences[$item['incidence']];
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
    public function saveShipment($shipment, $isCollection = false) {

        $service = $this->getProviderService($shipment);

        $data = $shipment->toArray();

        $data['sender_country']    = strtoupper($data['sender_country']);
        $data['recipient_country'] = strtoupper($data['recipient_country']);


        if(in_array($data['recipient_country'], ['PT', 'ES'])) {
            $data['service'] = 'EPL-DOM-IBERIA';
        } else if(in_array($data['recipient_country'],
            ['FR','DE','IT','BE','BG','CZ','DK','EE','IE','EL','HR',
                'CY','LV','LT','LU','HU','MT','NL','AT','PL', 'RO', 'SI',
                'SK','FI','SE','UK','NO','CH','TR','RS','AL','MK','ME'])) { //Internacional Europa
            $data['service'] = 'EPL-INT-IBERIA-SEU';
        } else {
            $data['service'] = 'EPL-INT-IBERIA-ESI'; //fora europa
        }

        if($shipment->is_collection || $isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data, $service);
        }


    }

    /**
     * Store shipment labels on server
     *
     * @param $shipmentMasterReference
     * @param $shipmentTrk
     * @return string
     * @throws \Exception
     */
    public function storeLabels($pieces, $shipmentTrk) {

        //obtem a informação de todas as etiquetas
        /* $url = 'labels?orderReferenceFilter=' . $shipmentMasterReference;
         $labelsInfo = $this->execute($url, [], 'GET');*/

  /*      $shipmentTrk = explode(',', $shipmentTrk);
        $shipmentTrk = @$shipmentTrk[0];*/

        $labelsInfo = $pieces;

        $labels = [];
        $i = 1;
        foreach ($labelsInfo as $labelInfo) {
            $labelId = $labelInfo['labelId'];
            $label = $this->execute('labels/' . $labelId, [], 'GET');

            $fileContent = base64_decode(@$label['pdf']);
            $outputFilepath = public_path().$this->upload_directory . $shipmentTrk . '_'.$i.'_labels.pdf';
            File::put($outputFilepath, $fileContent);

            $labels[] = $outputFilepath;
            $i++;
        }

        $pdf = new PdfManage();
        foreach ($labels as $labelUrl) {
            $pdf->addPDF($labelUrl);
        }

        $fileContent = $pdf->merge('string'); //return string

        // Save Merged Files
        $outputFilepath = public_path().$this->upload_directory . $shipmentTrk . '_labels.pdf';
        File::put($outputFilepath, $fileContent);

        foreach ($labels as $labelUrl) {
            File::delete($labelUrl);
        }

        return $outputFilepath;
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
            if(isset($row['@attributes'])) {
                $row = $row['@attributes'];
            }
            $arr[] = mapArrayKeys($row, config('webservices_mapping.ontime.'.$mappingArray));
        }

        return $arr;
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
                $mapping = config('shipments_export_mapping.ontime-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço On Time.');
        }

        return $providerService;
    }
}