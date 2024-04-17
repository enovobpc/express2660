<?php

namespace App\Models\Webservice;

use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;

class Chronopost extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $url = 'http://cliente.chronopost.pt:10002/Services/Services.asmx?wsdl';
    private $tracking_url = 'https://trace.chronopost.pt:7564/ChronoWSTraceGF_v2/GetTrace_v2Service?wsdl';
    private $collection_url = "https://webservices.chronopost.pt:7564/PickupsWS/rest/API/createJSON";


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/chronopost/';

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
    private $debug = false;

    /**
     * Gls constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = null)
    {
        /* if (config('app.env') == 'local') {
        if(config('app.env') == 'local') {
            $this->user      =  '02362501';
            $this->password  =  '02362501';
        } else {*/
        $this->user      =  $user;
        $this->password  =  $password;
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
        // Contexto para permitir encriptação SSL (Devido ao url ser via https)
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $client  = new \SoapClient($this->tracking_url, ['stream_context' => $context]);

        $trakingCodes = explode(';', $trackingCode);
        $fullHistory = [];
        $biggerTrackingStatus = @$trakingCodes[0];
        $counter = 0;
        foreach ($trakingCodes as $trackingCode) {
            $data = [
                'pSkybillNumber' => $trackingCode,
                'pLanguage' => 'PT'
            ];

            $result = $client->getSimpleTrace($data);
            //dd($result);
            if (!empty($result->return->traceEventsArr)) {
                $history = (array) $result->return->traceEventsArr;
                if (!empty($history['trace_Event_CODE']))
                    $history = [$history];

                $history = $this->mappingResult($history, 'status');

                $fullHistory[$trackingCode] = $history;
                $counter = count($history) > $counter ? count($history) : $counter;
                $biggerTrackingStatus = count($history) > $counter ? $trackingCode : $biggerTrackingStatus;
            } else {
                $fullHistory[$trackingCode] = [
                    'status_id'  => '16', //entrada em rede
                    'obs'        => 'Não há informação de estados.',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $counter = 1 > $counter ? 1 : $counter;
                $biggerTrackingStatus = 1 > $counter ? $trackingCode : $biggerTrackingStatus;
            }
            sleep(1);
        }

        $history = @$fullHistory[$biggerTrackingStatus];

        if (!isset($history[0])) {
            $history = [$history];
        }

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
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode)
    {
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
        return true;
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
    }

    /**
     * Grava uma resolução a um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveIncidenceResolution($incidenceResolution, $isCollection = false)
    {
        return true;
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

        $data = [
            'test'                  => 'Y',
            'flyby'                 => 'N',
            'account'               => $data['expedicao']['Conta'],
            'user'                  => $data['username'],
            'passwd'                => $data['password'],
            'pickup_reference'      => $data['expedicao']['Referencia'],
            'pickup_Comment'        => $data['ObservacoesLinha1'],
            'pickup_Date'           => $data['enviarEmail'],
            'pickup_Phone'          => $data['expedicao']['OrigemTelefone'],
            'pickup_MobilePhone'    => $data['expedicao']['OrigemTelefone'],
            'pickup_Email'          => $data['expedicao']['OrigemEmail'],
            'pickup_NVolumes'       => $data['expedicao']['NumeroVolumes'],
            'pickup' => [
                'pickup_Name'               => $data['expedicao']['OrigemMoradaNome'],
                'pickup_Address'            => $data['expedicao']['OrigemMoradaLinha1'] . ' ' . $data['expedicao']['OrigemMoradaLinha2'],
                'pickup_ZipCode'            => $data['expedicao']['OrigemMoradaCodigoPostal'],
                'pickup_Location'           => $data['expedicao']['OrigemMoradaLocalidade'],
                'pickup_Country'            => $data['expedicao']['OrigemMoradaPais'],
                'pickup_Contact'            => $data['expedicao']['OrigemTelefone'],
                'pickup_ContactEmail'       => $data['expedicao']['OrigemEmail'],
            ],
            'skybill' => [
                'skybill_Name'              => $data['expedicao']['DestinoMoradaNome'],
                'skybill_Address'           => $data['expedicao']['DestinoMoradaLinha1'] . ' ' . $data['expedicao']['DestinoMoradaLinha2'],
                'skybill_ZipCode'           => $data['expedicao']['DestinoMoradaCodigoPostal'],
                'skybill_Location'          => $data['expedicao']['DestinoMoradaLocalidade'],
                'skybill_Country'           => $data['expedicao']['DestinoMoradaPais'],
                'skybill_Contact'           => $data['expedicao']['DestinoContactoNome'],
                'skybill_MobilePhone'       => $data['expedicao']['DestinoContactoTelemovel'],
                'skybill_ContactEmail'      => $data['expedicao']['DestinoEmail'],
                'skybill_COD'               => $data['expedicao']['ValorCOD'],
                'skybill_BringSkybill'      => '2', // 1=Sim, 2=Não
                'skybill_Number'            => '',
            ],
        ];

        $result = $this->execute($this->collection_url, $data);

        if (empty($result['pickup_number'])) {
            throw new \Exception($result['result_code'] . ' - ' . $result['description']);
        }

        $trk = $result['pickup_number'];

        return $trk;
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {
        $client = new \SoapClient($this->url);
        $result = $client->RegistarExpedicao4($data);

        $result = (array) $result;

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $requestXml  = print_r($data, true);
            $responseXml = print_r($result, true);
            file_put_contents(public_path() . '/dumper/request.txt', $requestXml);
            file_put_contents(public_path() . '/dumper/response.txt', $responseXml);
        }

        if ($result['RegistarExpedicao4Result']->Codigo <= 0) {
            throw new \Exception($result['RegistarExpedicao4Result']->Descricao);
        } else {

            $trk   = $result['RegistarExpedicao4Result']->NrGuia;
            $label = $result['RegistarExpedicao4Result']->PDF;

            if (!File::exists(public_path() . $this->upload_directory)) {
                File::makeDirectory(public_path() . $this->upload_directory);
            }

            $filename = explode(';', $trk);
            $filename = @$filename[0];

            $result = File::put(public_path() . $this->upload_directory . $filename . '.txt', $label);

            if ($result === false) {
                throw new \Exception('Não foi possível gravar a etiqueta.');
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
        $filename = explode(';', $trackingCode);
        $filename = @$filename[0];

        $file = File::get(public_path() . '/uploads/labels/chronopost/' . $filename . '.txt');
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
    public function destroyEnvioByTrk($trackingCode, $service)
    {
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
            "Content-Type: application/json"
        ];


        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $header,
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    /**
     * @return array
     */
    public function login()
    {
        return $this->user;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

        if ($data) {

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                $date = new Carbon($item['created_at']);

                if (empty($item['status_id'])) {
                    $item['status_id'] = 9;
                }
                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    //'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($data);
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

        $reference =  $shipment->reference;

        $service = $this->getProviderService($shipment);


        $pickupPointId = '';
        if ($shipment->recipient_pudo_id) {
            $pickupPointId = $shipment->delivery_pudo->code;
            $shipment->recipient_attn = $shipment->recipient_name;
            $shipment->recipient_name = $shipment->delivery_pudo->name;
        }

        $senderName     = $shipment->sender_name;
        $senderAddress  = $shipment->sender_address;
        $senderZipCode  = $shipment->sender_zip_code;
        $senderCity     = $shipment->sender_city;
        $senderCountry  = $shipment->sender_country;
        $senderPhone    = $shipment->sender_phone;

        $senderAddress1 = substr($senderAddress, 0, 31);
        $senderAddress2 = substr($senderAddress, 32, 64);
        $senderAddress2 = empty($senderAddress2) ? '' : $senderAddress2;

        $recipientAddress1 = substr($shipment->recipient_address, 0, 31);
        $recipientAddress2 = substr($shipment->recipient_address, 32, 64);
        $recipientAddress2 = empty($recipientAddress2) ? '' : $recipientAddress2;

        $shipment->sender_phone = $shipment->sender_phone == '.' ? '' : $shipment->sender_phone;
        $shipment->recipient_phone = $shipment->recipient_phone == '.' ? '' : $shipment->recipient_phone;

        $email = 'trk@trk.com';
        if(config('app.source') == 'moovelogistica') {
            $email = 'contacto@moovelogistica.pt';
        }

        $data = [
            'username'  => $this->user,
            'password'  => $this->password,
            'expedicao' => [
                'Conta'                     => $service,
                'OrigemMoradaNome'          => str_limit($senderName, 29),
                'OrigemMoradaLinha1'        => $senderAddress1,
                'OrigemMoradaLinha2'        => $senderAddress2,
                'OrigemMoradaCodigoPostal'  => $senderZipCode,
                'OrigemMoradaLocalidade'    => $senderCity,
                'OrigemMoradaPais'          => strtoupper($senderCountry),
                'OrigemTelefone'            => str_replace(" ", "", $senderPhone),
                'OrigemTelemovel'           => '910000000',
                //'OrigemFax'               => '',
                'OrigemEmail'               => $email,
                //'OrigemContactoNome'      => '',
                //'OrigemContactoTelefone'  => '',
                //'OrigemContactoTelemovel' => '',
                //'OrigemContactoEmail'     => '',
                'TipoDestino'               => empty($pickupPointId) ? '1' : '2', //1=morada | 2=loja
                'DestinoLojaId'             => $pickupPointId,
                'DestinoMoradaNome'         => str_limit($shipment->recipient_name, 29),
                'DestinoMoradaLinha1'       => $recipientAddress1,
                'DestinoMoradaLinha2'       => $recipientAddress2,
                'DestinoMoradaCodigoPostal' => $shipment->recipient_zip_code,
                'DestinoMoradaLocalidade'   => $shipment->recipient_city,
                'DestinoMoradaPais'         => strtoupper($shipment->recipient_country),
                'DestinoTelefone'           => $shipment->recipient_phone,
                'DestinoTelemovel'          => $shipment->recipient_phone,
                //'DestinoFax'              => '',
                'DestinoEmail'              => $shipment->recipient_email,
                'DestinoContactoNome'       => $shipment->recipient_attn,
                'DestinoContactoTelemovel'  => $shipment->recipient_phone,
                //'DestinoContactoTelefone' => '',
                'DestinoContactoEmail'      => $email,
                'ObservacoesLinha1'         => substr($shipment->obs, 0, 31),
                'ObservacoesLinha2'         => substr($shipment->obs, 32, 64),
                'Peso'                      => round($shipment->weight) * 100, //kg em gramas
                'NumeroVolumes'             => $shipment->volumes,
                'Referencia'                => $reference,

                'ValorCOD'                  => empty($shipment->charge_price) ? '' : number_format($shipment->charge_price, 2, '', ''),
                'DataExpedicao'             => date('Y-m-d'), //$shipment->date,
                'EnviarEtiquetasEmail'      => '2', // 1=Sim, 2=Não
            ],
            'tipoResposta'  => '1', // 1=XML, 2=CSV, 3=HTML
            'enviarEmail'   => '2', // 1=Sim, 2=Não
            'email'         => $email,
            'zplResponse'   => '0',
            'tipoEtiqueta'  => '1', // 1=A4, 2=4x6
            'pdfResponse'   => '1', //retorna PDF Base 64, 2=não devolve
        ];

        //dd($data);
        if ($isCollection) {
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
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => 'https://webservices.chronopost.pt:7554/PUDOPoints/rest/PUDOPoints/Country/PT',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'GET',
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => 0,
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            throw new \Exception($error);
        }

        $resultArr = [];
        $pudos = xmlstr_to_array($response)['lB2CPointsArr'];
        $pudosMapped = $this->mappingResult($pudos, 'pudo');
        foreach ($pudosMapped as $pudo) {
            // Correções
            $pudo['phone']  = empty($pudo['phone'])  ? '' : $pudo['phone'];
            $pudo['mobile'] = empty($pudo['mobile']) ? '' : $pudo['mobile'];
            $pudo['door']   = empty($pudo['door'])   ? '' : $pudo['door'];
            // $pudo['address']  = empty($pudo['address']) ? '' : $pudo['address'];
            // $pudo['latitude']  = empty($pudo['latitude']) ? '' : $pudo['latitude'];
            // $pudo['longitude']  = empty($pudo['longitude']) ? '' : $pudo['longitude'];
            // $pudo['name']  = empty($pudo['name']) ? '' : $pudo['name'];
            // $pudo['zip_code']  = empty($pudo['zip_code']) ? '' : $pudo['zip_code'];
            // $pudo['city']  = empty($pudo['city']) ? '' : $pudo['city'];
            $pudo['country']    = empty($pudo['country']) ? '' : strtolower($pudo['country']);
            $pudo['email']      = empty($pudo['email'])   ? '' : '' . str_replace(',', ';', $pudo['email']);
            $pudo['delivery_saturday'] = $pudo['delivery_saturday'] == 'S' || $pudo['delivery_saturday'] == 'Y' ? 1 : 0;
            $pudo['delivery_sunday']   = $pudo['delivery_sunday']   == 'S' || $pudo['delivery_sunday']   == 'Y' ? 1 : 0;

            //Modificações
            $pudo['address'] = $pudo['address'] . ' ' . $pudo['door'];

            //Final
            $resultArr[] = $pudo;
        }

        return $resultArr;
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
        $data = empty($data['trace_Event_CODE']) ? $data : [$data]; // if not array with arrays make a Array with an Array
        foreach ($data as $row) {

            if (!is_array($row)) {
                $row = (array) $row;
            }


            $row = mapArrayKeys($row, config('webservices_mapping.chronopost.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status') {

                $ignoredStatus = ['700', '706'];

                if (!in_array($row['status_id'], $ignoredStatus)) {

                    $date = explode('/', str_replace(',', '', $row['created_at']));
                    $date = @$date[0] . '-' . convertMonth2Decimal(@$date[1], true) . '-' . @$date[2];
                    $row['created_at'] = $date;

                    $services = config('shipments_import_mapping.chronopost-status');
                    $row['status_id'] = @$services[$row['status_id']];


                    if ($row['status_id'] == 9 || empty($row['status_id'])) { //incidence
                        $row['status_id'] = 9;
                        $row['obs'] = $row['obs'] . ' ' . $row['description'];
                    }

                    $arr[] = $row;
                }
            } else if ($mappingArray == 'pudo') {
                $arr[] = $row;
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

            if ($shipment->recipient_pudo_id) {
                $serviceKey = $serviceKey . 'pudo';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {
                $code = $shipment->service->code;
                $services = config('shipments_export_mapping.chronopost-services.' . $source);

                if ($shipment->recipient_pudo_id) {
                    $code = 'PUDO#' . $code;
                } else if (in_array($shipment->recipient_country, ['pt', 'es'])) { //serviços PT e ES
                    $code = strtoupper($shipment->recipient_country) . '#' . $code;
                } else { //Serviços INT
                    $code = 'INT#' . $code;
                }

                $providerService = $services[$code];
            }

            if (in_array('rguide', $shipment->has_return) && config('app.source') === "packbox") {
                $providerService = "01733702";
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço DPD.');
        }

        return $providerService;
    }
}
