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
use Mpdf\Mpdf;

class Delnext extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $url = 'https://www.delnext.com/'; //PROD


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/delnext/';

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
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        if (config('app.env') == 'local') {
            $this->session_id  =  'ax]?-2[U]3T(_ZG{M<)Dqu&S^4<7WDd4TW]/sV{Lm9YBc`^Jb%';
        } else {
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
        $url = $this->url . 'EN/TrackingAPI';

        $trackingCode = explode(',', $trackingCode);
        $trackingCode = @$trackingCode[1];

        $data = [
            'tracking_number' => $trackingCode
        ];

        $response = $this->callApi($url, $data);

        if (@$response['ParcelInfo']['History']) {
            return $this->mappingResult(@$response['ParcelInfo']['History'], 'status');
        }

        return null;
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
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {

        $url = $this->url . 'EN/OrderDetailsAPI';

        $orderId = explode(',', $trackingCode);
        $orderId = @$orderId[0];

        $data = [
            'hash_key' => $this->session_id,
            'order_id' => $orderId
        ];

        $response = $this->callApi($url, $data);

        if ($response['success']) {
            return $response['parcels'];
        }

        throw new \Exception($response['message']);
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        //$url = $this->url . 'create_orders_api.php';
        $url = $this->url . 'EN/CreateOrderAPI';

        $response = $this->callApi($url, $data);

        if (@$response['success'] == '0') {
            throw new \Exception(@$response['message'] ? @$response['message'] : 'Erro desconhecido ao documentar.');
        }
        $orderId = $response['orders_id'];
        $trk     = @$response['tracking_code'];

        return $orderId . ',' . $trk;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($codAgeCargo, $trackingCode, $outputFormat = null, $shipmentTRK = null, $totalVolumes = null)
    {

        $url = $this->url . 'reference_api.php';

        $data = [
            'reference_number' => 'TRK' . $shipmentTRK,
            'hash_key'         => $this->session_id
        ];

        $response = $this->callApi($url, $data);

        if ($response['success'] == '1') {
            $label = @$response[0]['pdf'];
            return $label;
        }

        return false;
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
    public function callApi($url, $data = null)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        }

        return json_decode($response, true);
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

                if (empty($item['status_id'])) {
                    $item['status_id'] = '41'; //sem info
                }

                if(config('app.source') == '2660express' && str_contains($item['obs'], 'absent')) {
                    $item['status_id'] = '47'; //AUSENTE
                }

                $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $item['created_at']);
                $history = ShipmentHistory::whereBetween('created_at', [$createdAt->copy()->subSecond(10), $createdAt->addSecond(10)])
                    ->firstOrNew([
                        'shipment_id'  => $shipment->id,
                        'obs'          => $item['obs'],
                        'status_id'    => $item['status_id']
                    ]);

                $history->fill($data);
                $history->shipment_id = $shipment->id;
                $history->created_at  = $item['created_at'];
                $history->save();

                $history->shipment = $shipment;

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $shipment->addPickupFailedExpense();
                }
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            $lastHistory = $this->getMostRecentHistory($shipment->id);
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
    public function saveShipment($shipment, $isCollection = false)
    {

        $service = $this->getProviderService($shipment);

        $data = [
            'hash_key'              => $this->session_id,
            'sender_first_name'     => str_replace("'", "", $shipment->sender_name),
            'sender_last_name'      => ' ',
            'sender_street_address' => str_replace("'", "", $shipment->sender_address),
            'sender_post_code'      => $shipment->sender_zip_code,
            'sender_city'           => str_replace("'", "", $shipment->sender_city),
            'sender_country'        => $shipment->sender_country,
            'sender_telephone'      => $shipment->sender_phone ? $shipment->sender_phone : 'N/A',
            'sender_email'          => ' ',
            'sender_company'        => str_replace("'", "", $shipment->sender_name),
        ];


        $parcels = [];
        for ($i = 0; $i < $shipment->volumes; $i++) {
            $parcels[$i] = [
                'receiver_first_name'   => $shipment->recipient_name,
                'receiver_last_name'    => ' ',
                'receiver_street_address' => str_replace("'", "", $shipment->recipient_address),
                'receiver_post_code'    => $shipment->recipient_zip_code,
                'receiver_city'         => str_replace("'", "", $shipment->recipient_city),
                'receiver_country'      => strtoupper($shipment->recipient_country),
                'receiver_telephone'    => $shipment->recipient_phone ? $shipment->recipient_phone : 'N/A',
                'receiver_email'        => $shipment->recipient_email,
                'receiver_company'      => str_replace("'", "", $shipment->recipient_name),
                'parcel_weight'         => $shipment->weight,
                'parcel_length'         => '0.00',
                'parcel_width'          => '0.00',
                'parcel_height'         => '0.00',
                'parcel_reference'      => 'TRK' . $shipment->tracking_code,
                'parcel_contents'       => 'N/A',
                'parcel_content_value'  => $shipment->charge_price ? $shipment->charge_price : '0.00',
                'parcel_service_type'   => $service,
                'parcel_insured'        => $shipment->is_insured,
                'cod'                   => $shipment->charge_price > 0.00 ? '1' : '0'
            ];
        }

        $data['parcel'] = $parcels;
        $data['products_commen'] = $shipment->obs;

        if (empty($shipment->provider_tracking_code)) {
            return $this->storeEnvio($data);
        }

        return $shipment->provider_tracking_code;
    }

    /**
     * get devoluted_labels
     */
    public static function getDevolutionLabels($delnext_body)
    {
        //ir buscar todos os ids da tabela trace_ability
        // if (config('app.env') == 'local') {
        //     return "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1mtYdNXKhRmIC/Exy8YQpfvHdFXbUKmIEdsarl34XuelnYE8KP9QvIjFrho+YBeX2flsTM40kVGgt8Eic5N7eOfD2hb+JfpSSuS2jN+HPjPOBBOcydFRXGtiokof2uBcO4P3nYV3Ntx23xsoHNz4amU3UIMeg5OEyGQAH9bNn8iLK0VYrPvKN23uC0yNa6NODRUvqIsLQxgb8Gdl+9dgmdyWzTivOEBDoO/qNmDslQ9tt7abcbBJiIgKXO0tcbsbmfshZMoQLbHxlLBkN4dqP4dMY6sNCBzTRA1sI5P1daVP1UVToqOMC9+6ZjY2P6eS0PQppR+tqd5tFNawxzGDGypK/sZtavdnu4UPi2YcrjlJo8JbF6Craw+Zr+wXwHk7w77h1AXgcpooihU7UN0Q+KkuCMxdqMq1a1p3gpOOlP9WWNk0kcRRiN3hWqSnilGYrzu7ubOnZzZ31clhZ5/h4ZqoqPFv8wztkCkmJEtxeUzjog0Gd7jG48b8Zb08xomaRlH0v9qXH0rcVm0Prnoshk/DU0p07MKFDesu2Bcj9X4tzzftC4EMbzQ+PJIo0Dw7X3z953Jvw91FwyF5zmxbDrzPtm774MDi+ugLb/5zTe8dt9/ay0HLE7BDjieQ32s0nWQqiYp70cEmSrM8hvqbR4oQXHPj68IFV4H1DdzZjcdvUNZFmePmbPDAaXoj+rv9wbDg8sMN2PNf2y8ugMci7koPNR7hAddltfIbdEXhoXJN5Q8Em5jO4S7DG4qKiesGef4H6Z4jmAplbmRzdHJlYW0KZW5kb2JqCjExIDAgb2JqCjw8L1R5cGUgL1BhZ2UKL1BhcmVudCA5IDAgUgovTWVkaWFCb3ggWzAgMCAyODMuNDYgNDgxLjg5XQovUmVzb3VyY2VzIDggMCBSCi9Db250ZW50cyAxMCAwIFI+PgplbmRvYmoKOSAwIG9iago8PC9UeXBlIC9QYWdlcwovS2lkcyBbMTEgMCBSXQovQ291bnQgMT4+CmVuZG9iagoxMiAwIG9iago8PC9UeXBlIC9DYWxhbG9nCi9QYWdlcyA5IDAgUj4+CmVuZG9iagp4cmVmCjAgMTMKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDA4NTUgMDAwMDAgbiAKMDAwMDAwMDkzOCAwMDAwMCBuIAowMDAwMDAzMjQ1IDAwMDAwIG4gCjAwMDAwMDM1MzcgMDAwMDAgbiAKMDAwMDAwMzgyOCAwMDAwMCBuIAowMDAwMDAzOTI4IDAwMDAwIG4gCjAwMDAwMDQwMjMgMDAwMDAgbiAKMDAwMDAwNTIxMSAwMDAwMCBuIAowMDAwMDA0MTY3IDAwMDAwIG4gCjAwMDAwMDUxMDEgMDAwMDAgbiAKMDAwMDAwNTI2NyAwMDAwMCBuIAp0cmFpbGVyCjw8L1NpemUgMTMKL1Jvb3QgMTIgMCBSPj4Kc3RhcnR4cmVmCjUzMTUKJSVFT0YK";
        // }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.delnext.com/return_label_quick.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('reference_number' => $delnext_body, 'hash_key' => 'somos_os_maiores_enovo_teste'),
            CURLOPT_HTTPHEADER => array(
                'Cookie: COOKIES_OF_COUNTRY_CODE=PT; COOKIES_OF_COUNTRY_DEFAULT_LANGUAGE=PT; COOKIES_OF_COUNTRY_ID=171; COOKIES_OF_IP_CLIENT=188.82.17.22; first_time_visit=1; zenid=02d86a3b20dfd22389cf3ddffb7e450a'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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

        $seconds = 59; //para manter os registos ordenados
        foreach ($data as $row) {

            if (!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.delnext.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $row['created_at'] = $row['date'] . ' ' . @$row['time'];
                $row['created_at'] = Carbon::createFromFormat('d/m/Y H:i:s', @$row['created_at'] . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT));
                $row['created_at']  = $row['created_at']->format('Y-m-d H:i:s');

                $row['obs'] = utf8_decode($row['obs']) . ' ' . @$row['status'];

                $status = config('shipments_import_mapping.delnext-status');
                if (str_contains($row['status_id'], 'Parcel transferred:')) {
                    $row['status_id'] = 16;
                } else {
                    $row['status_id'] = @$status[trim($row['status_id'])];
                }


                if (isset($row)) {
                    $arr[] = $row;
                }

                $seconds--;
            } else {
                $arr = $row;
            }
        }

        return $arr;
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
     * Get most recent history
     *
     * @param $shipmentId
     * @return mixed
     */
    public function getMostRecentHistory($shipmentId)
    {
        $history = \DB::select("SELECT status_id, created_at FROM shipments_history WHERE shipment_id = '" . $shipmentId . "' order by created_at desc limit 0,1");
        return @$history[0];
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

                $providerService = 3; //para PT e ES
                if (!in_array($shipment->recipient_country, ['pt', 'es'])) {
                    $providerService = 1; //Internacional
                }

                if (@$shipment->service->code == 'AI' && $shipment->recipient_country == 'pt') {
                    $providerService = 2;
                } elseif (@$shipment->service->code == 'MI' && $shipment->recipient_country == 'pt') {
                    $providerService = 1;
                }
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Delnext.');
        }

        return $providerService;
    }
}
