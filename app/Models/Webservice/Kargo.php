<?php

namespace App\Models\Webservice;

use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Date, File, View, Setting;
use Mockery\Exception;

class Kargo extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url;

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
    private $upload_directory = '/uploads/labels/kargo/';

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($clientId = null, $user = null, $password = null, $secret = null, $department=null, $endpoint=null, $debug=false)
    {
        $this->url      = 'https://api.kargodotcom.com/';
        $this->user     = $user;
        $this->password = $password;

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
        $url = $this->url . $this->version . '/shipments/history/massive?trackings=' . $trackingCode;

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache",
            "Authorization: Bearer " . $this->getToken(),
        ];

        $response = $this->execute($url, 'GET', $headers);
        return $response;
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
        $file = File::get(public_path().'/uploads/labels/kargo/'.$trackingCode.'_label.txt');
        return $file;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment) {

        /*
        $serviceCode = @$shipment->service->code;

        try {
            $arrayMapping = config('shipments_export_mapping.kargo-services');
            $serviceCode = @$arrayMapping[$serviceCode] ? $arrayMapping[$serviceCode] : $serviceCode;
        } catch (\Exception $e) {}

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
        }*/


        $reference = 'TRK'.$shipment->tracking_code . ($shipment->reference ? ' - '.$shipment->reference : '');

        $data = [
            "name"      => $shipment->recipient_name,
            "address"   => $shipment->recipient_address,
            "phone"     => $shipment->recipient_phone,
            "zip_code"  => $shipment->recipient_zip_code,
            "location"  => $shipment->recipient_city,
            "country"   => strtoupper($shipment->recipient_country),
            "email"     => $shipment->recipient_email,
            "merchandise" => [
                "volumes"   => intval($shipment->volumes),
                "product"   => "n/a",
                "weight"    => $shipment->weight,
                "refund"    => forceDecimal($shipment->charge_price),
                "description"    => "n/a",
                "declared_value" => forceDecimal($shipment->goods_price ? $shipment->goods_price : 1),
                "internal_reference" => $reference,
            ]
        ];

        if($shipment->provider_tracking_code) {
            return $shipment->provider_tracking_code;
        } else {
            return $this->storeEnvio($data);
        }

    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {
        $url = $this->url . 'tracking/';

        $headers = [
            "Content-Type: application/json",
            "cache-control: no-cache",
            "x-kargo-token: " . $this->getToken(),
        ];

        $response = $this->execute($url, 'POST', $headers, $data);


        if(@$response['status']) {
            $trk   = @$response['tracking_data'][0]['_id'];
            $label = @$response['tracking_data'][0]['pdf_encoded'];


            $folder = public_path().$this->upload_directory;
            if(!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            //store label
            if($label) {
                $result = File::put(public_path().$this->upload_directory . $trk . '_label.txt', $label);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta.');
                }
            }

            return $trk;
        } else {
            throw new \Exception(@$response['message']);
        }
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
            //throw new \Exception($e->getMessage());
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
            $shipment->status_date = $history->created_at;
            $shipment->save();

            return $history->status_id ? $history->status_id : true;
        }

        return false;
    }

    /**
     * Auth user on API
     *
     * @param type $data
     * @return type
     */
    public function auth()
    {
        $url = $this->url . '/login/';

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
            "cache-control: no-cache"
        ];

        $data = [
            "user"     => $this->user,
            "password" => $this->password,
        ];

        $response = $this->execute($url, 'POST', $headers, $data);

        return @$response['jwt'];
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

            $content = @$content['kargo']['id-' . $this->user];

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

            $content['kargo'] = [
                'id-' . $this->user => [
                    'token'     => $token,
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

        if($method == 'POST') {
            $data = json_encode($data);
        } else {
            $data = http_build_query($data);
        }

        if(!$headers) {
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json'
            );
        }

        $curl = curl_init();

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
            CURLOPT_HTTPHEADER      => $headers,
        ));

        $response = curl_exec($curl);

        $response = json_decode($response, true);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception($err);
        }

        if(!empty(@$response['error'])) {
            $error = @$response['error_description'];

            if(@$response['errors'][0]) {
                $error = @$response['errors'][0];
            }

            throw new \Exception($error);
        }

        return $response;
    }
}