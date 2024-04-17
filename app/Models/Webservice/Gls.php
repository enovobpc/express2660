<?php

namespace App\Models\Webservice;

use App\Models\ShippingStatus;
use Carbon\Carbon;
use Date, File, Setting;
use App\Models\Shipment;
use App\Models\ShipmentHistory;

class Gls extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url           = 'https://api.gls-group.eu/public/v1/shipments';
    private $trackTraceUrl = 'http://www.gls-group.eu/276-I-PORTAL-WEBSERVICE/services/Tracking/wsdl/Tracking.wsdl';
    //private $url           = 'https://api-qs.gls-group.eu/public/v1/shipments'; // test
    //private  $trackTraceUrl = 'http://test.your-gls.eu:80/276-I-PORTAL-WEBSERVICE/services/Tracking/wsdl/Tracking.wsdl'; //test

    /**
     * @var null
     */
    private $shipperId = null;

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
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        $this->shipperId = $sessionId;
        $this->user      = $user;
        $this->password  = $password;
        $this->debug     = $debug;
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
        $reference = array(
            'Credentials' => array('UserName' => $this->user, 'Password' => $this->password),
            'RefValue' => $trakingCode
        );

        $client = new \SoapClient($this->trackTraceUrl);
        $result = $client->GetTuDetail($reference);
        $result = (array) $result;

        if($result['ExitCode']->ErrorCode != 0) {
            throw new \Exception($result['ExitCode']->ErrorDscr);
        }

        $history = (array) $result['History'];
        $history = $this->mappingResult($history, 'status');

        $data['weight'] = $result['TuWeight'];
        $data['history'] = $history;
        $data['signature'] = @$result['Signature'];

        return $data;
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
        $reference = array(
            'Credentials' => array('UserName' => $this->user, 'Password' => $this->password),
            'RefValue' => $trakingCode
        );

        $client = new \SoapClient($this->trackTraceUrl);
        $result = $client->GetTuPOD($reference);
        $result = (array) $result;

        if($result['ExitCode']->ErrorCode != 0) {
            throw new \Exception($result['ExitCode']->ErrorDscr);
        }

        return base64_encode($result['PODFile']);
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
    public function storeRecolha($data)
    {
        return false;
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {
        if(empty($this->user)) {
            throw new \Exception('Cliente sem webservice ativo.');
        }

        $result = $this->request($this->url, $data);


        if(isset($result['errors'])) {
            $error = $result['errors'][0]->exitCode. ' - '.$result['errors'][0]->description;
            throw new \Exception($error);
        } else {

            if(isset($result['parcels'])) {
                $trk = [];
                foreach ($result['parcels'] as $key => $parcel) {
                    $trk[$key] = $parcel->parcelNumber;
                }

                $trk = implode(',', $trk);
                $trk = str_limit($trk, 200, '');
            } else {
                throw new \Exception('Não foi possível encontrar o código do envio.');
            }

            //save label
            $file = @$result['labels'][0];

            if($file) {
                $result = File::put(public_path().'/uploads/labels/gls/'.$trk.'.txt', $file);
                if ($result === false) {
                    throw new \Exception('Envio gravado com sucesso mas não foi possível gerar a etiqueta.');
                }
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
    private function request($url, $data)
    {
        $base64Auth = base64_encode($this->user . ':' . $this->password);

        $header = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Accept-Language: en',
            'Authorization: Basic ' . $base64Auth
        );

        $data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);
        $result = (array) $result;

        return $result;
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
    public function updateHistory($shipment) {

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

        $webserviceWeight  = null;
        $receiverSignature = null;

        if($data) {

            if(isset($data['weight'])) {
                $webserviceWeight  = $data['weight'];
            }

            if(isset($data['signature'])) {
                $receiverSignature  = $data['signature'];
            }

            $data = $data['history'];

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                $date = new Carbon($item['created_at']);

                if($key == 0 && $date->hour == 0 && $date->minute == 0) {
                    $item['status_id'] = 16; //documentado
                }

                if($item['status_id'] == 5) { //delivered
                    $item['obs'] = $receiverSignature;
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($data);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $shipment->addPickupFailedExpense();
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            /**
             * Update shipment weight
             */
            if($webserviceWeight > $shipment->weight) {
                $shipment->weight   = $webserviceWeight;

                //$agencyId, $serviceId, $customerId, $providerId, $weight, $volumes, $charge = null, $volumeM3 = 0, $fatorM3 = 0, $zone = 'pt'
                $prices = Shipment::getPrice(
                    $shipment->agency_id,
                    $shipment->service_id,
                    $shipment->customer_id,
                    $shipment->provider_id,
                    $shipment->weight,
                    $shipment->volumes,
                    $shipment->charge_price,
                    null,
                    $shipment->fator_m3,
                    Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country),
                    Shipment::getBillingZipCode($shipment->sender_zip_code, $shipment->recipient_zip_code, $shipment->is_collection),
                    $shipment->sender_zip_code,
                    $shipment->recipient_zip_code,
                    $shipment->sender_country,
                    $shipment->recipient_country,
                    $shipment->sender_agency_id,
                    $shipment->recipient_agency_id,
                    $shipment->kms
                );

                $shipment->volumetric_weight  = $prices['volumetricWeight'];
                $shipment->total_price = $prices['total'];
                $shipment->cost_price  = $prices['cost'];
            }

            $shipment->status_id = $history->status_id;
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

        $reference =  $shipment->reference ? ' - '.$shipment->customer->code.' '.$shipment->reference : '';

        $countryRecipient = $shipment->recipient_country;
        $countrySender = $shipment->sender_country;

        $services = [];

        $chargeService = [];
        if(!empty($shipment->charge_price) && $shipment->charge_price > 0.00) {
            $services[] = [
                "name" => "cod",
                "infos" => [[
                    "name"  => "amount",
                    "value" => $shipment->charge_price
                ]]
            ];
        }

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return guide
        if($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $services[] = ['name' => 'proofservice'];
        }

        //cash on delivery
        if($shipment->payment_at_recipient) {
            $services[] = [
                "name" => "cashondelivery",
                "infos"  => [[
                    "name"   => "amount",
                    "value"  => $shipment->total_price_for_recipient
                ]]
            ];
        }

        $shipment->volumes = empty($shipment->volumes) ? 1 : $shipment->volumes;

        $weight = 0;
        if($shipment->weight) {
            $weight = $shipment->weight / $shipment->volumes;
        }


        for($i = 0 ; $i < $shipment->volumes ; $i++){

            $parcelArr = [
                "weight"     => $weight,
                "comment"    => $shipment->obs,
            ];

            if(!empty($services) && $i == 0) { //só adiciona os serviços adicionais ao primeiro volume
                $parcelArr["services"] = $services;
            }

            $parcels[] = $parcelArr;

            //dd($parcels);
        }

        $data = [
            "shipperId"     => $this->user . " " . $this->shipperId,
            //"shipmentDate"  => $shipment->date,
            "references"    => ['TRK' . $shipment->tracking_code . $reference],
            ];

        if($isCollection) {
            $data["addresses"]["pickup"]  = [
                "name1"     => substr($shipment->sender_name, 0, 32),
                "street1"   => substr($shipment->sender_address, 0, 32),
                "country"   => $countrySender,
                "zipCode"   => $shipment->sender_zip_code,
                "city"      => $shipment->sender_city,
                "phone"     => $shipment->sender_phone,
            ];
        }

        $data["addresses"]["delivery"] = [
                "name1"   => substr($shipment->recipient_name, 0, 32),
                "street1" => substr($shipment->recipient_address, 0, 32),
                //"street2" => substr($shipment->recipient_address, 32, 32),
                "country" => $countryRecipient,
                "zipCode" => $shipment->recipient_zip_code,
                "city"    => $shipment->recipient_city,
                "contact" => $shipment->recipient_attn,
                "phone"   => $shipment->recipient_phone,
        ];

        $data["parcels"]  = $parcels;
        $data["incoterm"] = '20'; //ver lista possivel


        $trackings = [];
        //foreach ($shipment->volumes as $volume) {
            $trackings = $this->storeEnvio($data);
        //}

        return $trackings;
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

            if(isset($row['Date'])) {
                $date = @$row['Date'];
                $row['Date'] = $date->Year.'-'.str_pad($date->Month, 2, "0", STR_PAD_LEFT).'-'.str_pad($date->Day, 2, "0", STR_PAD_LEFT).' '.str_pad($date->Hour, 2, "0", STR_PAD_LEFT).':'.str_pad($date->Minut, 2, "0", STR_PAD_LEFT).':00';
            }

            $row = mapArrayKeys($row, config('webservices_mapping.gls.'.$mappingArray));

            //mapping and process status
            if($mappingArray == 'status') {
                $services = config('shipments_import_mapping.gls-webservice-status');
                $row['status_id'] = @$services[(int) $row['status_id']];

                if (isset($row)) {
                    $arr[] = $row;
                }
            }
        }

        return $arr;
    }
}