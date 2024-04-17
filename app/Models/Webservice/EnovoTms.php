<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Date, File, View, Setting;
use Mockery\Exception;

class EnovoTms extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var null
     */
    private $secret = null;

    /**
     * @var null
     */
    private $token = null;

    /**
     * @var string
     */
    private $debug;

    /**
     * @var null
     */
    private $version = null;

    /**
     * @var string
     */
    private $targetPartner = null;

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($clientId = null, $user = null, $password = null, $secret = null, $department=null, $endpoint=null, $debug=false)
    {
        $endpoint = trim($endpoint);
        if (substr($endpoint, 0, -1) != '/'){
            $endpoint.= '/';
        }

        $this->version  = 'v1';
        $this->url      = $endpoint;
        $this->clientId = $clientId;
        $this->user     = $user;
        $this->password = $password;
        $this->secret   = $secret;
        $this->debug    = $debug;

        $targetPartner = parse_url($this->url);
        $targetPartner = @$targetPartner['host'];
        $targetPartner = str_replace(['api.','.enovo','.quickbox','.com','.pt','.net'], '', $targetPartner);
        $targetPartner = str_replace('.', '_', $targetPartner);

        $this->targetPartner = $targetPartner;

        if(config('app.env') == 'local') {
            $this->url      = 'https://api.asfaltolargo.pt/';
            $this->clientId = '2';
            $this->secret   = 'QAYNZukYRB8uxovkpPbM4MROzr5NXtoDshqVT94D';
            $this->user     = 'testes@asfaltolargo.pt';
            $this->password = 'testes';
            $this->targetPartner = 'asfaltolargo';
        }
    }

    /**
     * Returns tracking history for a shipment.
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trackingCode
     * @return mixed
     * @throws \Exception
     */
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {
        $url = $this->url . $this->version . '/shipments/'.$trackingCode.'/history';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'GET', $headers);
        return $response;
    }

    /**
     * Returns tracking history for multiple shipments.
     *
     * @param $trackingCode
     * @return mixed
     * @throws \Exception
     */
    public function getEstadoEnvioMassive($trackingCode)
    {
        try {
            $url = $this->url . $this->version . '/shipments/history/massive?trackings=' . $trackingCode;

            $headers = [
                "Content-Type: application/x-www-form-urlencoded",
                "cache-control: no-cache",
                "Authorization: Bearer " . $this->getToken(),
            ];


            $response = $this->execute($url, 'GET', $headers);


        } catch(\Exception $e) {
            throw new \Exception($e->getMessage(). ' TRKS='.$trackingCode);
        }

        return $response;
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
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio [multiplos envios separados por vírgula]
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode)
    {
        $url = $this->url . $this->version . '/shipments/'. $trackingCode .'/labels';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'GET', $headers);
        return @$response['label'];
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment) {

        $serviceCode = $this->getProviderService($shipment);

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return guide
        $returnGuide = 0;
        if($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 1;
        }

        //return pack
        $returnPack = 0;
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 1;
        }

        $data = [
            'source_partner'    => config('app.source'), //incida à API que esta é uma transação entre parceiros
            'source_tracking'   => $shipment->tracking_code,

            'date'              => $shipment->date,
            'is_collection'     => $shipment->is_collection,
            'start_hour'        => $shipment->start_hour,
            'end_hour'          => $shipment->end_hour,
            'service'           => $serviceCode,

            'sender_vat'        => $shipment->sender_vat,
            'sender_attn'       => $shipment->sender_attn,
            'sender_name'       => $shipment->sender_name,
            'sender_address'    => $shipment->sender_address,
            'sender_zip_code'   => $shipment->sender_zip_code,
            'sender_city'       => $shipment->sender_city,
            'sender_country'    => $shipment->sender_country,
            'sender_phone'      => $shipment->sender_phone,

            'recipient_vat'     => $shipment->recipient_vat,
            'recipient_attn'    => $shipment->recipient_attn,
            'recipient_name'    => $shipment->recipient_name,
            'recipient_address' => $shipment->recipient_address,
            'recipient_zip_code'=> $shipment->recipient_zip_code,
            'recipient_city'    => $shipment->recipient_city,
            'recipient_country' => $shipment->recipient_country,
            'recipient_phone'   => $shipment->recipient_phone,

            'volumes'           => $shipment->volumes,
            'weight'            => $shipment->weight,
            'kms'               => $shipment->kms,
            'fator_m3'          => $shipment->fator_m3,
            'volumetric_weight' => $shipment->volumetric_weight,
            'charge_price'      => $shipment->charge_price,
            'reference'         => $shipment->reference,
            'cod'               => $shipment->total_price_for_recipient,
            'payment_at_recipient'  => $shipment->payment_at_recipient,
            'obs'               => $shipment->obs,
            'obs_delivery'      => $shipment->obs_delivery,

            'return_pack'       => $returnPack,
            'return_guide'      => $returnGuide,

            'dimensions' => $shipment->pack_dimensions->toArray(),
        ];

        if($shipment->provider_tracking_code) {
            $data['tracking_code'] = $shipment->provider_tracking_code;
        }

        return $this->storeEnvio($data);
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {
        $url = $this->url . $this->version . '/shipments/store';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'POST', $headers, $data);

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', print_r($data, 1));
            file_put_contents (public_path().'/dumper/response.txt', print_r($response,1));
        }

        $trk = @$response['tracking_code'];
        return $trk;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {

        $url = $this->url . $this->version . '/shipments/' . $shipment->provider_tracking_code;

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'DELETE', $headers);

        if(empty($response['error'])) {
            return true;
        }

        return false;
    }

    /**
     * Permite gravar uma incidencia
     * @param $data
     * @return type
     * @throws \Exception
     */
    public function saveIncidenceResolution($incidenceResolution)
    {
        $url = $this->url . $this->version . '/shipments/incidences/resolve';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $data = [
            "tracking"      => @$incidenceResolution->shipment->provider_tracking_code,
            "action"        => @$incidenceResolution->type->code,
            "obs"           => $incidenceResolution->obs,
        ];

        $response = $this->execute($url, 'POST', $headers, $data);
        return $response;
    }

    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
        $url = $this->url . $this->version . '/shipments/list?date=' . $date;

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'GET', $headers);
        return $response;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        try {
            $historyData = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

            return $this->storeShipmentHistory($shipment, $historyData);

        } catch (\Exception $e) {
            throw new Exception($shipment->tracking_code . ': '. $e->getMessage());
        }
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistoryMassive($shipments) {

        $trks = $shipments->pluck('tracking_code')->toArray();
        $trks = implode(',', $trks);

        $errors = [];

        try {

            $histories = self::getEstadoEnvioMassive($trks);

            foreach ($histories as $trackingCode => $historyData) {


                try {
                    $shipment = $shipments->filter(function ($q) use ($trackingCode) {
                        return $q->tracking_code == $trackingCode;
                    })->first();

                    $lastStatusId = $this->storeShipmentHistory($shipment, $historyData);

                    //store devolution expense
                    if($lastStatusId == ShippingStatus::DEVOLVED_ID) {
                        $shipment->storeDevolutionExpenseIfExists();
                    }

                } catch (\Exception $e) {
                    throw new Exception($trackingCode . ': '. $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $errors[@$trackingCode] = $e->getMessage();
            throw new \Exception($e->getMessage());
        }

        if(!empty($errors)) {
            return $errors;
        }

        return true;
    }


    public function storeShipmentHistory($shipment, $histories) {

        if($histories) {

            aasort($histories, 'date');

            foreach ($histories as $item) {

                $item['created_at'] = $item['date'];
                $item['filepath'] = $item['attachment'];

                if($item['status_id'] == '1') {
                    $item['status_id'] = '16'; //entrada em rede;
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'created_at'  => $item['created_at'],
                    'status_id'   => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

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
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = []) {
    }

    /**
     * Auth user on API
     *
     * @param type $data
     * @return type
     */
    public function auth()
    {
        $url = $this->url . '/oauth/token';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache"
        ];

        $data = [
            "grant_type"    => "password",
            "client_id"     => $this->clientId,
            "client_secret" => $this->secret,
            "username"      => $this->user,
            "password"      => $this->password,
        ];

        $response = $this->execute($url, 'POST', $headers, $data);

        return $response['access_token'];
    }

    /**
     * Get API TOKEN from storage or from API
     *
     * @param type $data
     * @return type
     */
    public function getToken() {

        $token = null;

        //current time
        $now = Date::now();

        //check stored session
        $folder = storage_path() . '/webservices/';
        $filename = $folder . 'sessions.json';

        if(!File::exists($folder)){
            File::makeDirectory($folder);
        }

        if(!File::exists($filename)){
            File::put($filename, ''); //cria ficheiro em vazio
        } else {
            $content = json_decode(File::get($filename), true);

            $content = @$content['enovo-tms']['id-' . $this->secret];

            if(!empty($content)) {
                $expireTime = new Date(@$content['time']);
                $expireTime = $expireTime->subMinutes(10);

                if($now->lte($expireTime)) {
                    $token = @$content['token'];
                }
            }
        }

        //if dont has stored version, create new session
        if(empty($token)) {

            $token   = $this->auth();
            $content = json_decode(File::get($filename), true);

            if(empty($content)) {
                $content = [];
            }

            $content['enovo-tms'] = [
                'id-' . $this->secret => [
                    'token'     => $token,
                    'client_id' => $this->clientId,
                    'secret'    => $this->secret,
                    'time'      => $now->addHour(1)->format('Y-m-d H:i:s'),
                ]
            ];

            File::put($filename, json_encode($content));
        }

        return $token;
    }

    /**
     * Call API
     *
     * @param $url
     * @param null $headers
     * @param null $data
     * @return mixed
     */
    public function execute($url, $method, $headers = null, $data = [])
    {
        $data = http_build_query($data);

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

        $response = json_decode($response, true);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception($err);
        }

        if((isset($response[0]) && !empty(@$response[0]['error'])) || !empty(@$response['error'])) {

            $response = isset($response[0]) ? $response[0] : $response;

            if(is_array(@$response['message'])) {
                $message = @$response['message'] ? @$response['message'] : (@$response['feedback'] ? @$response['feedback'] : 'Erro desconhecido.');

                foreach ($message as $item) {
                    throw new \Exception(@$item[0]);
                }
            } else {
                $message = @$response['message'] ? @$response['message'] : (@$response['feedback'] ? @$response['feedback'] : 'Erro desconhecido.');
                throw new \Exception($message);
            }
        }

        return $response;
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
            if($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if(!$providerService) {
                /*$mapping = config('shipments_export_mapping.enovo_tms-services.' . $this->targetPartner);
                $providerService = $mapping[@$shipment->service->code];*/

                $providerService = @$shipment->service->code;
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço do fornecedor.');
        }

        return $providerService;
    }
}