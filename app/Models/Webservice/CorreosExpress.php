<?php

namespace App\Models\Webservice;

use App\Models\CustomerWebservice;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, Response, File, Setting, Auth;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class CorreosExpress extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    //
    private $urlTest = 'https://www.test.cexpr.es/wsps/';
    private $url     = 'https://www.cexpr.es/wspsc/';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/correos/';

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
    private $conta;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * Correos Express constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        if (config('app.env') == 'local') {
            $this->user       = 'P30270002_WS'; //
            $this->password   = 'HH4ZW';
            $this->conta      = 'P30270002';
        } else {
            $this->user       = $user;
            $this->password   = $password;
            $this->conta      = $agencia;
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
    public function getEstadoEnvioByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode)
    {

        $params = [
            "codigoCliente" => $this->conta,
            "dato"          => $trackingCode,
            "idioma"        => "PT"
        ];

        $response = $this->request($params, "apiRestSeguimientoEnviosk8s/json/seguimientoEnvio");

        /*
        //atualizar massivo entre datas
        $params = [
            "keyCliFac"     => $this->conta,
            "fechaInicial"  => "2020-09-25",
            "fechaFinal"    => "2020-01-13",
            "idioma"        => "PT"
        ];

        $response = $this->request($params, "apiRestSeguimientoEnviosk8s/json/seguimientoEnviosFechas");
        */

        if (!@$response['error']) {
            
            $history = @$response['estadoEnvios'];

            $data = $this->mappingResult($history, 'status');
            $data['weight']   = forceDecimal($response['kilos']);
            $data['fator_m3'] = forceDecimal($response['volumen']);

            if (Setting::get('shipments_round_up_weight')) {
                $data['weight'] = roundUp($data['weight']);
            }

            return $data;
        } else {
            throw new \Exception(@$response['mensajeError']);
        }

        return [];
    }


    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode)
    {

        $params = [
            "codigoCliente" => $this->conta,
            "recogida"      => $trackingCode,
            //"fecRecogida"   => '01/04/2021',
            "idioma"        => "PT"
        ];

        $response = $this->request($params, "apiRestSeguimientoRecogidak8s/json/seguimientoRecogida");

        if (!@$response['codigoRetorno']) {
            $history = @$response['situaciones'];

            $data = $this->mappingResult($history, 'status-collections');

            return $data;
        } else {
            throw new \Exception($response['mensajeRetorno']);
        }

        return [];
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
        $startDate = $startDate->subDays(2)->format('dmY');

        //atualizar massivo entre datas
        $params = [
            "keyCliFac"     => $this->conta,
            "fechaInicial"  => $startDate,
            "fechaFinal"    => $startDate,
            "idioma"        => "PT"
        ];

        $response = $this->request($params, "apiRestSeguimientoEnviosk8s/json/seguimientoEnviosFechas");

        $histories = [];
        if (!@$response['error']) {
            $history = @$response['estadoEnvios'];

            $data = $this->mappingResult($history, 'status');
            $data['weight']   = forceDecimal($response['kilos']);
            $data['fator_m3'] = forceDecimal($response['volumen']);

            if (Setting::get('shipments_round_up_weight')) {
                $data['weight'] = roundUp($data['weight']);
            }

            $histories[] = $data;
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
        return false;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data, $isCollection)
    {
        

        $bultos = [];
        $volWeightAvg = $data['weight'] / $data['volumes'];
        for ($i = 1; $i <= $data['volumes']; $i++) {
            $bultos[] = [
                "observaciones" => "",
                "referencia"    => "",
                "volumen"       => "",
                "codBultoCli"   => "",
                "codUnico"      => "",
                "descripcion"   => "",
                "alto"          => "",
                "ancho"         => "",
                "largo"         => "",
                "kilos"         => (float) number_format($volWeightAvg, 3),
                "orden"         => $i,
            ];
        }

        $infoAdicional = [
            "tipoEtiqueta"       => "5",
            "etiquetaPDF"        => "",
            "logoCliente"        => @$data['logo']
        ];

        if ($isCollection) {
            $infoAdicional["creaRecogida"]       = "S"; //pede ao sistema correos para que omotorista deles recolha o volume
            $infoAdicional["fechaRecogida"]      = $data['date'];
            $infoAdicional["horaDesdeRecogida"]  = $data['start_hour'];
            $infoAdicional["horaHastaRecogida"]  = $data['end_hour'];
            $infoAdicional["referenciaRecogida"] = $data['reference'];
            $infoAdicional["obsRec"]             = @$data['obs_delivery'];
        }


        $params = [
            "solicitante"       => 'I' . $this->conta, //a pedido da correos deve ter um "I" no inicio
            //"password"        => $this->password,
            "canalEntrada"      => "",
            "ref"               => $data['reference'],
            "refCliente"        => "",
            "fecha"             => $data['date'],
            "codRte"            => $this->conta,
            "nomRte"            => @$data['sender_name'],
            //"nifRte"          => "",
            "dirRte"            => @$data['sender_address'],
            "pobRte"            => @$data['sender_city'],
            "codPosNacRte"      => in_array(@$data['sender_country'], ['ES', 'es']) ? @$data['sender_zip_code'] : "",
            "paisISORte"        => strtolower(@$data['sender_country']),
            "codPosIntRte"      => in_array(@$data['sender_country'], ['ES', 'es']) ? "" : @$data['sender_zip_code'],
            "contacRte"         => @$data['sender_attn'],
            "telefRte"          => @$data['sender_phone'],
            //"emailRte"        => "",
            "codDest"           => "",
            "nomDest"           => @$data['recipient_name'],
            //"nifDest"         => "",
            "dirDest"           => @$data['recipient_address'],
            "pobDest"           => @$data['recipient_city'],
            "codPosNacDest"     => in_array(@$data['recipient_country'], ['ES', 'es']) ? @$data['recipient_zip_code'] : "",
            "paisISODest"       => strtoupper(@$data['recipient_country']),
            "codPosIntDest"     => in_array(@$data['recipient_country'], ['ES', 'es']) ? "" : @$data['recipient_zip_code'],
            "contacDest"        => @$data['recipient_attn'] ? @$data['recipient_attn'] : @$data['recipient_name'],
            "telefDest"         => @$data['recipient_phone'],
            "emailDest"         => @$data['recipient_email'],
            //"contacOtrs"      => "",
            //"telefOtrs"       => "",
            //"emailOtrs"       => "",
            "observac"          => @$data['obs'],
            "numBultos"         => @$data['volumes'],
            "kilos"             => floatval(@$data['weight']),
            //"volumen"         => "",
            //"alto"            => "",
            //"largo"           => "",
            //"ancho"           => "",
            "producto"          => @$data['service_code'],
            "portes"            => @$data['cod'],
            "reembolso"         => @$data['charge_price'],
            "entrSabado"        => @$data['sabado'],
            "seguro"            => "",
            //"numEnvioVuelta"  => "",
            "listaBultos"       => $bultos,
            "codDirecDestino"   => "",
            "listaInformacionAdicional" => [$infoAdicional]
        ];

        if (@$data['trk']) {
            $params["numEnvio"] = $data['trk'];
        }
        

        // if(Auth::user()->isAdmin()){
        //     dd($params);
        // }

        // dd($params);
        $response = $this->request($params, 'apiRestGrabacionEnviok8s/json/grabacionEnvio');

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $request  = json_encode($params);
            $response = json_encode($params);
            file_put_contents(public_path() . '/dumper/request.txt', $request);
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }


        if (!empty(@$response['codigoRetorno']) || @$response['error'] == '1') {
            if(@$response['mensajeError']) {
                 throw new \Exception(@$response['error'] . ' - ' . $response['mensajeError']);
            } else {
                 throw new \Exception(@$response['codigoRetorno'] . ' - ' . $response['mensajeRetorno']);
            }
        } else {

            $folder = public_path() . $this->upload_directory;
            if (!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            $trk        = @$response['datosResultado'];
            $pickupTrk  = @$response['numRecogida'];
            $labels     = @$response['etiqueta'];


            $singleLabels = [];

            foreach ($labels as $key => $labelPdf) {
                if ($labelPdf) {

                    $labelPdf = $labelPdf['etiqueta1'];
                    $filepath = public_path() . $this->upload_directory . $trk . '_labels_' . $key . '.pdf';
                    $singleLabels[] = $filepath;
                    $doc = base64_decode(base64_decode($labelPdf));
                    $result = File::put($filepath, $doc);

                    if ($result === false) {
                        throw new \Exception('Não foi possível gravar a etiqueta.');
                    }
                }
            }

            /**
             * Merge files
             */
            $pdf = new \LynX39\LaraPdfMerger\PdfManage;
            foreach ($singleLabels as $filepath) {
                $pdf->addPDF($filepath, 'all');
            }

            //Save merged file
            $filepath = $this->upload_directory . $trk . '_labels.txt';
            $outputFilepath = public_path() . $filepath;
            $result = base64_encode($pdf->merge('string', $outputFilepath, 'L'));
            File::put($outputFilepath, $result);

            if ($result) {
                File::delete($singleLabels);
            }

            if ($pickupTrk) {
                return $trk . ',' . $pickupTrk;
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

        $params = [
            "solicitante"   => 'I' . $this->conta,
            "canalEntrada"  => "",
            "refRecogida"   => @$data['reference'],
            "fechaRecogida" => @$data['date'],
            "horaDesde1"    => @$data['start_hour'],
            "horaHasta1"    => @$data['end_hour'],
            //"horaDesde2"    => "",
            //"horaHasta2"    => "",
            "clienteRecogida" =>  $this->conta,
            "codRemit"      => $this->conta,
            "nomRemit"      => @$data['sender_name'],
            "nifRemit"      => "",
            "dirRecog"      => @$data['sender_address'],
            "poblRecog"     => @$data['sender_city'],
            "cpRecog"       => @$data['sender_zip_code'],
            "contRecog"     => @$data['sender_attn'] ? @$data['sender_attn'] : @$data['sender_name'],
            "tlfnoRecog"    => @$data['sender_phone'],
            "oTlfnRecog"    => "",
            "emailRecog"    => "",
            "observ"        => @$data['obs'],
            //"tipoServ"      => "",
            "codDest"       => @$data['recipient_attn'],
            "nomDest"       => @$data['recipient_name'],
            //"nifDest"       => "",
            "dirDest"       => @$data['recipient_address'],
            "pobDest"       => @$data['recipient_city'],
            "cpDest"        => @$data['recipient_zip_code'],
            "paisDest"      => @$data['recipient_country'],
            "cpiDest"       => @$data['recipient_zip_code'],
            "contactoDest"  => @$data['recipient_attn'],
            "tlfnoDest"     => @$data['recipient_phone'],
            "emailDest"     => @$data['recipient_email'],
            //"nEnvio"        => "", //envio associado
            "refEnvio"      => @$data['reference'],
            "producto"      => @$data['service_code'],
            "kilos"         => @$data['weight'],
            "bultos"        => @$data['volumes'],
            //"volumen"       => "",
            "tipoPortes"    => @$data['cod'],
            "importReembol" => @$data['charge_price'],
            //"valDeclMerc"   => "",
            //"infTec"        => "",
            //"nSerie"        => "",
            //"modelo"        => "",
        ];


        if (@$data['trk']) {
            $params['intKeyRecogida'] = @$data['trk'];
        }

        if (@$data['trk']) {
            $response = $this->request($params, 'apiRestGrabacionRecogidaEnviok8s/json/modificarRecogida');
        } else {
            $response = $this->request($params, 'apiRestGrabacionRecogidaEnviok8s/json/grabarRecogida');
        }

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $request  = json_encode($params);
            $response = json_encode($response);
            file_put_contents(public_path() . '/dumper/request.txt', $request);
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }


        if (!empty($response['codigoRetorno'])) {
            throw new \Exception($response['codigoRetorno'] . ' - ' . $response['mensajeRetorno']);
        } else {
            $trk = @$response['numRecogida'];
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
        $trackingCode = explode(',', $trackingCode);
        $trackingCode = @$trackingCode[0];

        $file = File::get(public_path() . '/uploads/labels/correos/' . $trackingCode . '_labels.txt');
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

        if (str_contains($trackingCode, ',') || strlen($trackingCode) == '9') { //so elimina recolhas
            $trackingCode = explode(',', $trackingCode);
            $trackingCode = $trackingCode[1];

            $params = [
                "solicitante"       => $this->conta,
                "keyRecogida"       => $trackingCode,
                "strTextoAnulacion" => "Anulacao",
                "strUsuario"        => "",
                "strReferencia"     => "",
                "strCodCliente"     => "",
                "strFRecogida"      => ""
            ];


            $response = $this->request($params, 'apiRestGrabacionRecogidaEnviok8s/json/anularRecogida');

            if (@$response['codError']) {
                throw new \Exception($response['codError'] . ' - ' . $response['mensError']);
            }
        }
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
    private function request($params, $action)
    {
        $url = $this->url . $action;
        /*if(env('APP_ENV') == 'local') {
            $url = $this->urlTest . $action;
        }*/

        $data = json_encode($params);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => array(
                'Authorization: Basic ' . base64_encode($this->user . ':' . $this->password),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        $responseCode = @$info['http_code'];

        curl_close($curl);

        if ($responseCode == '401') {
            $response = [
                'error'        => '1',
                'mensajeError' => 'Erro de autenticação. Dados login inválidos.'
            ];
        } else {
            $response = json_decode($response, true);
        }

        return $response;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        if ($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk(null, null, $shipment->provider_tracking_code);
        } else {
            $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        }

        if ($data) {

            $webserviceFatorM3 = $data['fator_m3'];
            $webserviceWeight = $data['weight'];
            unset($data['weight'], $data['fator_m3']);

            $shipmentLinked = false;
            if ($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                //$date = new Carbon($item['created_at']);

                if (empty($item['status_id'])) {
                    $item['status_id'] = 31; //aguarda expedição
                }
                
                if(config('app.source') == '2660express') {
                    
                    if(str_contains($item['incidence_name'], 'AUSENTE')) {
                        $item['status_id'] = '47'; //AUSENTE
                    } elseif(str_contains($item['incidence_name'], 'CAMBIO')) {
                        $item['status_id'] = '27'; //AGENDADO
                    } elseif(str_contains($item['incidence_name'], 'Scheduled')) {
                        $item['status_id'] = '27'; //AUSENTE
                    } elseif(str_contains($item['incidence_name'], 'REHUSADO')) {
                        // $item['status_id'] = '45'; //RECUSADO
                        //ticket: https://enovo.pt/admin/tickets/12457 Daniel Moreira
                        $item['status_id'] = '73'; //RECUSADO
                    } elseif(str_contains($item['incidence_name'], 'parcelshop')) {
                        $item['status_id'] = '63'; //LOJA MRW
                    } elseif(str_contains($item['incidence_name'], 'DIRECCIÓN')) {
                        $item['status_id'] = '64'; //MORADA ERRADA
                    } elseif(str_contains($item['incidence_name'], 'RECOGERÁN EN NAVE')) {
                        //$item['status_id'] = '26'; //MORADA ERRADA
                        //ticket: https://enovo.pt/admin/tickets/12457 Daniel Moreira
                        $item['status_id'] = '70';
                    }
      
                }

              /*  if(config('app.source') == '2660express' && $item['incidence_id'] == '') {
                    $item['status_id'] = '47'; //AUSENTE
                }*/


                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'obs' => @$item['obs'],
                    'incidence_id' => @$item['incidence_id'],
                    'created_at' => @$item['created_at'],
                    'status_id' => @$item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                } elseif ($history->status_id == ShippingStatus::DEVOLVED_ID) { 
                    if(!in_array(config('app.source'), ['horasambulantes', '2660express'])) { //ignora a criação de devoluções automaticas
                        $devolvedShipment = $shipment->createDirectDevolution(true);
                        $devolvedShipment->walletPayment(null, null, $devolvedShipment->billing_total); //discount payment
                    }
                }  elseif ($history->status_id == ShippingStatus::DELIVERED_ID && !empty($shipment->has_return)) {
                    $devolvedShipment = $shipment->createDirectReturn();
                    $devolvedShipment->walletPayment(null, null, $devolvedShipment->billing_total); //discount payment
                }
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            if ($history) {
                $shipment->status_id   = $history->status_id;
                $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
                $shipment->save();
            }

            //update shipment price
            $weightChanged = ($webserviceWeight > $shipment->weight || $webserviceFatorM3);
            if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || ($weightChanged && empty($shipment->invoice_id)
                    && empty($shipment->ignore_billing)
                    && empty($shipment->is_blocked))
            ) {

                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                if (!$shipment->price_fixed) {
                    //calcula preços
                   $prices = Shipment::calcPrices($shipment);
                   if(@$prices['fillable']) {
                       $shipment->fill($prices['fillable']);
       
                       //adiciona taxas
                       $shipment->storeExpenses($prices);
                   }
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
                }

                //update linked shipment
                if ($shipmentLinked) {
                    $shipmentLinked->weight = $webserviceWeight > $shipmentLinked->weight ? $webserviceWeight : $shipmentLinked->weight;

                    $tmpShipment = $shipmentLinked;
                    $tmpShipment->fator_m3 = $webserviceFatorM3;
                    $prices = Shipment::calcPrices($tmpShipment);

                    $shipmentLinked->fator_m3 = $webserviceFatorM3;
                    $shipmentLinked->volumetric_weight = $prices['volumetricWeight'];
                    $shipmentLinked->cost_price = $prices['cost'];

                    if (!$shipmentLinked->price_fixed) {
                        $shipmentLinked->total_price = @$prices['total'];
                        $shipmentLinked->fuel_tax = @$prices['fuelTax'];
                        $shipmentLinked->extra_weight = @$prices['extraKg'];
                        if (!$shipmentLinked->price_fixed) {
                            $shipmentLinked->total_price = $prices['total'];
                            $shipmentLinked->fuel_tax = @$prices['fuel_tax'];
                            $shipmentLinked->fill(@$prices['fillable']);
                        }
                    }
                }

                $shipment->status_id = $history->status_id;
                $shipment->status_date = $history->created_at;
                $shipment->save();
            }
            
            return @$history->status_id ? $history->status_id : true;
        }
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false)
    {

        //$reference = 'TRK'.$shipment->tracking_code;
        $reference =  $shipment->reference; // ? ' - '.$shipment->reference : '';

        $service = $this->getProviderService($shipment);

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return guide
        $returnGuide = 'N';
        if ($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 'S';
        }


        $recRemota = 'N';
        if ($shipment->sender_country != 'pt') {
            $recRemota = 'S';
        }

        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services ?? [];

        $sabado = 'N';
        //check service sabado
        if (
            in_array('sabado', array_keys($systemComplementarServices)) &&
            in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)
        ) {
            $sabado = 'S';
        }

        //return pack
        $returnCheck = 'N';
        if ($shipment->has_return && in_array('rcheck', $shipment->has_return)) {
            $returnGuide = 'S';
        }

        //return guide
        $returnGuide = 'N';
        if ($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 'S';
        }

        //return pack
        if ($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $service = '54';
        }

        $date = explode('-', $shipment->date);
        $date = $date[2] . $date[1] . $date[0];


        $recipientZipCode = trim($shipment->recipient_zip_code);
        $recipientZipCode = explode('-', $recipientZipCode);
        $recipientZipCode = @$recipientZipCode[0];

        $senderZipCode = trim($shipment->sender_zip_code);
        $senderZipCode = explode('-', $senderZipCode);
        $senderZipCode = @$senderZipCode[0];
        
        $weight = $shipment->weight ? $shipment->weight : '0';
        if(config('app.source') == '2660express') {
            $weight = '0.5';
        }

        $data = [
            "sender_name"        => substr($shipment->sender_name, 0, 40),
            "sender_zip_code"    => $senderZipCode,
            "sender_city"        => $shipment->sender_city,
            "sender_address"     => $shipment->sender_address,
            "sender_attn"        => $shipment->sender_attn,
            "sender_phone"       => str_replace(' ', '', $shipment->sender_phone),
            "sender_country"     => strtoupper($shipment->sender_country),

            "recipient_name"     => $shipment->recipient_name,
            "recipient_email"    => $shipment->recipient_email,
            "recipient_zip_code" => $recipientZipCode,
            "recipient_city"     => $shipment->recipient_city,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_attn"     => $shipment->recipient_attn,
            "recipient_phone"    => $shipment->recipient_phone,
            "recipient_country"  => strtoupper($shipment->recipient_country),

            "cod"           => $shipment->cod == 'D' ? "D" : "P",
            "service_code"  => $service,
            "rguide"        => $returnGuide ? 'true' : 'false',
            "date"          => $date, //format ddmmyyyy
            "start_hour"    => $shipment->start_hour ? $shipment->start_hour : '08:00',
            "end_hour"      => $shipment->end_hour ? $shipment->end_hour : '19:00',
            "volumes"       => $shipment->volumes ? $shipment->volumes : '0',
            "weight"        => $weight,
            "charge_price"  => $shipment->charge_price ? $shipment->charge_price : '0',
            "reference"     => $reference,
            "obs"           => $shipment->obs,
            "obs2"          => $shipment->obs_delivery,
            "rec_remota"    => $recRemota,
            "sabado"        => $sabado,
            
            "payment_at_recipient" => $shipment->cod == 'D' ? true : false
        ];


        if ($shipment->provider_tracking_code) {
            $data['trk'] = $shipment->provider_tracking_code;
        }

        if ($service == '54') {
            $data['sender_country']     = $shipment->sender_country;
            $data['recipient_country']  = $shipment->recipient_country;
        }


        if (config('app.source') == 'iben') {
            $path = asset('assets/img/logo/logo_correos.png');
        } else {
            $path = asset('assets/img/logo/logo_sm.png');
        }

        $filecontent  = file_get_contents($path);
        $data['logo'] = base64_encode($filecontent);

        if ($shipment->is_collection || $isCollection) {
            return $this->storeRecolha($data);
        }
        

        return $this->storeEnvio($data, $isCollection);
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

        $statusNotIncidence = [
            '7', //devolvido
            '33', //em armazem destino
        ];

        foreach ($data as $row) {

            $row = mapArrayKeys($row, config('webservices_mapping.correos_express.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'status-collections') {

                if($mappingArray == 'status-collections') {
                    $dateParts = explode(' ', trim($row['date']));
                    $date = explode('/', $dateParts[0]);
                    $date = $date[2].'-'.$date[1].'-'.$date[0];
                    $row['created_at'] = $date.' '.@$dateParts[1];
                } else {
                    $row['date'] = str_split($row['date'], 2);
                    $row['date'] = $row['date'][2] . $row['date'][3] . '-' . $row['date'][1] . '-' . $row['date'][0];
    
                    $row['hour'] = implode(':', str_split($row['hour'], 2));
                    $row['created_at'] = $row['date'] . ' ' . $row['hour'];
                }

                $status = config('shipments_import_mapping.correos_express-'.$mappingArray);
                $row['status_id'] = @$status[$row['status']];

                if ($row['incidence'] && !in_array($row['status_id'], $statusNotIncidence)) { //incidencia
                    $row['status_id'] = 9;

                    $incidences = config('shipments_import_mapping.correos_express-incidences');
                    $row['incidence_id'] = @$incidences[$row['incidence']];
                    $row['obs'] = @$row['incidence_name'];
                }

                /*if($row['status_id'] == '5') {
                    $row['receiver'] = $row['obs'];
                    $row['obs'] = null;
                }*/

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

                $services = config('shipments_export_mapping.correos-services');
                $code = @$shipment->service->code;

                $providerService = $services[$code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Correos Express.');
        }

        return $providerService;
    }
}
