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

class Vasp extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    //DOCS https://hackmd.io/@dsi-vasp/APIVaspExpressoParte2
    private $url     = 'https://vaspapirest.vaspexpresso.pt/'; //PROD
    private $testUrl = 'https://vaspapirest-qua.vaspexpresso.pt/'; //DEV


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/vasp/';

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
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null,  $department = null, $endpoint = null, $debug = false, $webserviceId = null)
    {
        if (config('app.env') == 'local') {
            $this->user      =  'paulo.costa@enovo.pt';
            $this->password  =  'LLg27Rsev0bLCom4S8oSHDSGGJUuiv';
        } else {
            $this->user       = $user;
            $this->password   = $password;
            $this->session_id = $sessionId;
            $this->webservice_id = $webserviceId;
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
        $url = $this->getUrl() . 'api/V2/TrackAndTrace/' . $trackingCode;

        $headers = [
            'cache-control' => 'no-cache',
            'content-type'  => 'application/json'
        ];

        $data = [
            'ClientBarCodeOrReference' => $trackingCode,
        ];

        $response = $this->callApi($url, 'GET', $headers, $data);

        if (empty(@$response->response)) {
            return [];
        }

        $response = $response->response;

        $movements = json_encode($response->movements);
        $movements = json_decode($movements, true);
        $data = $this->mappingResult($movements, 'status');


        $data['weight']   = $response->weight;
        //$data['fator_m3'] = (float) str_replace(',','.', $shipment[0]->vol);

        if (Setting::get('shipments_round_up_weight')) {
            $data['weight'] = roundUp(@$data['weight']);
        }

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
        $url = $this->getUrl() . 'api/V2/TrackAndTrace/' . $trackingCode;

        $headers = [
            'cache-control' => 'no-cache',
            'content-type'  => 'application/json'
        ];

        $data = [
            'ClientBarCodeOrReference' => $trackingCode,
        ];

        $response = $this->callApi($url, 'GET', $headers, $data);

        if (empty(@$response->response)) {
            return [];
        }

        return $response->response;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $method = 'POST';
        if (@$data['tracking_code']) {
            $method = 'PUT';
            unset($data['tracking_code']);
        }

        $url = $this->getUrl() . 'api/V2/Shipment/Service/Label';

        $headers = [
            'cache-control: no-cache',
            'authorization: Bearer ' . $this->auth(),
            'content-type: application/json',
        ];

        $dumperData = print_r($data, true);
        $data = json_encode($data);

        $response = $this->callApi($url, $method, $headers, $data, true);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            $request = $dumperData;
            file_put_contents(public_path() . '/dumper/request.txt', $request);
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }

        if (!empty($response->exceptions)) {
            throw new \Exception(@$response->exceptions[0]->exceptionCode . ' - ' . @$response->exceptions[0]->exceptionMessage);
        }

        $trk = @$response->service->clientBarCode;
        $labels = @$response->labels;

        if (!File::exists(public_path() . $this->upload_directory)) {
            File::makeDirectory(public_path() . $this->upload_directory);
        }

        foreach ($labels as $volume => $label) {
            $result = File::put(public_path() . $this->upload_directory . $trk . '_labels_vol' . $volume . '.txt', $label);
            if ($result === false) {
                throw new \Exception('Não foi possível gravar a etiqueta.');
            }
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
    public function getEtiqueta($senderAgency, $trackingCode)
    {

        //$this->getCargoManifest($trackingCode);

        $labels = [];
        for ($i = 0; $i <= 50; $i++) {
            $labelPath = public_path() . $this->upload_directory . $trackingCode . '_labels_vol' . $i . '.txt';

            if (File::exists($labelPath)) {
                $label = File::get($labelPath);

                $labelParts = str_replace("\r\n", '#', $label);
                $labelParts = explode('#', $labelParts);

                //dd($labelParts);
                $label = [
                    'trk'           => $trackingCode . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'servico'       => $this->getDataFromLabel($labelParts[16]),
                    'codigoRota'    => $this->getDataFromLabel($labelParts[17]),
                    'nomeRota'      => $this->getDataFromLabel($labelParts[18]),
                    'dataRecolha'   => $this->getDataFromLabel($labelParts[19]),
                    'dataEntrega'   => $this->getDataFromLabel($labelParts[26]),

                    'sender_name'   => $this->getDataFromLabel($labelParts[37]),
                    'sender_address'   => $this->getDataFromLabel($labelParts[38]),
                    'sender_zip_code'   => $this->getDataFromLabel($labelParts[41]),
                    'sender_city'   => $this->getDataFromLabel($labelParts[40]),

                    'recipient_name'   => $this->getDataFromLabel($labelParts[28]),
                    'recipient_address'   => $this->getDataFromLabel($labelParts[29]),
                    'recipient_zip_code'   => $this->getDataFromLabel($labelParts[32]),
                    'recipient_city'   => $this->getDataFromLabel($labelParts[31]),
                    'recipient_attn'   => $this->getDataFromLabel($labelParts[34]),
                    'recipient_phone'   => $this->getDataFromLabel($labelParts[35]),

                    'weight' => $this->getDataFromLabel($labelParts[21]),
                    'obs' => $this->getDataFromLabel($labelParts[44]),
                    'charge_price' => $this->getDataFromLabel($labelParts[42]),
                    'volume' => $i + 1,
                ];


                //                dd($label);

                $labels[] = $label;
            } else {
                $i = 51; //stop loop
            }
        }

        $totalVolumes = count($labels);
        try {

            $mpdf = new Mpdf(getLabelFormat('15x10'));
            $mpdf->showImageErrors = true;
            $mpdf->shrink_tables_to_fit = 0;


            foreach ($labels as $label) {

                $barcode = $label['trk']; //
                $barcode = $this->rotateBarcode($label['trk']);

                $data = [
                    'tracking' => $trackingCode,
                    'label'    => $label,
                    'barcode'  => $barcode,
                    'totalVolumes' => $totalVolumes,
                    'source' => 'admin',
                    'view' => 'admin.printer.shipments.labels.label_vasp',
                ];



                $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels_vasp', $data)->render()); //write
            }

            $mpdf->debug = true;
            $labels = base64_encode($mpdf->Output('Etiquetas.pdf', 'S'));

            return $labels;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Print guides
     * @param $trackingCode
     * @return mixed
     * @throws \Exception
     */
    public function getGuides($trackingCode)
    {

        $label = public_path() . $this->upload_directory . $trackingCode . '_guides.txt';

        if (File::exists($label)) {
            $file = File::get(public_path() . $this->upload_directory . $trackingCode . '_guides.txt');
        } else {
            $url = $this->getUrl() . 'api/V2/ShippingGuides/' . $trackingCode;

            $headers = [
                'cache-control: no-cache',
                'authorization: Bearer ' . $this->auth(),
                'content-type: application/json',
                'Accept: application/json, text/json, text/x-json, text/javascript, application/xml, text/xml'
            ];

            $response = $this->callApi($url, 'GET', $headers);

            if (!empty($response->exceptions)) {
                throw new \Exception(@$response->exceptions[0]->exceptionCode . ' - ' . @$response->exceptions[0]->exceptionMessage);
            }

            $file = @$response->response->document;
            File::put(public_path() . $this->upload_directory . $trackingCode . '_guides.txt', $file);
        }

        return $file;
    }

    /**
     * Print guides
     * @param $trackingCode
     * @return mixed
     * @throws \Exception
     */
    public function getCargoManifest($trackingCode)
    {

        $label = public_path() . $this->upload_directory . $trackingCode . '_manifest.txt';

        //if(File::exists($label)) {
        $file = File::get(public_path() . $this->upload_directory . $trackingCode . '_manifest.txt');
        //} else {
        $url = $this->getUrl() . 'api/V2/CargoManifest/Volumes';

        $headers = [
            'cache-control: no-cache',
            'authorization: Bearer ' . $this->auth(),
            'content-type: application/json'
        ];

        $volumes = [];
        for ($i = 0; $i <= 50; $i++) {
            $labelPath = public_path() . $this->upload_directory . $trackingCode . '_labels_vol' . $i . '.txt';

            if (File::exists($labelPath)) {
                $volumes[] = $trackingCode . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $i = 51;
            }
        }

        $data = json_encode(["volumes" => $volumes]);

        $response = $this->callApi($url, 'POST', $headers, $data, true);

        if (!empty($response->exceptions)) {
            throw new \Exception(@$response->exceptions[0]->exceptionCode . ' - ' . @$response->exceptions[0]->exceptionMessage);
        }

        /*$file = @$response->response->document;

        header('Content-Type: application/pdf');

        echo base64_decode(json_encode($file));
        exit;

        dd(1);*/
        File::put(public_path() . $this->upload_directory . $trackingCode . '_manifest.txt', $file);
        //}

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

        $url = $this->getUrl() . 'api/V2/Shipment';

        $headers = [
            'cache-control: no-cache',
            'authorization: Bearer ' . $this->auth(),
            'content-type: application/json',
        ];

        $data = [
            'clientBarCode' => $trackingCode
        ];

        $response = $this->callApi($url, 'DELETE', $headers, $data);

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
    public function auth()
    {
        $newToken = true;

        if ($this->webservice_id && empty($this->session_id)) {

            $newToken = false;

            $weberviceData = CustomerWebservice::whereId($this->webservice_id)
                ->select(['session_id', 'session_validity'])
                ->first();

            if ($weberviceData) {
                $now = Date::now()->subMinutes(60); //margem 60 minutos
                if (empty($weberviceData->session_id) || $weberviceData->session_validity->gte($now)) {
                    $newToken = true;
                }
            } else {
                $newToken = true;
            }
        }

        if ($newToken) {
            $url = $this->getUrl() . '/token';

            $headers = [
                'cache-control' => 'no-cache',
                'content-type' => 'application/x-www-form-urlencoded'
            ];

            $data = [
                'grant_type' => 'password',
                'username' => $this->user,
                'password' => $this->password,
            ];

            $response = $this->callApi($url, 'POST', $headers, $data);

            if (!empty(@$response->error)) {
                if (@$response->error_description) {
                    throw new \Exception(@$response->error_description);
                } else {
                    throw new \Exception(@$response->error);
                }
            }

            $this->session_id = @$response->access_token;
            $this->validity   = @$response->access_token;

            CustomerWebservice::whereId($this->webservice_id)
                ->update([
                    'session_id' => $this->session_id,
                    'session_validity' => $this->validity
                ]);

            return $this->session_id;
        }

        return $this->session_id;
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

        $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);

        if ($data) {

            $webserviceFatorM3 = null;
            $webserviceWeight = $data['weight'];
            unset($data['weight']);

            /*$shipmentLinked = false;
            if ($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }*/

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

                if($shipment->is_collection) {
                    if($item['status_id'] == 9){
                        $item['status_id'] == ShippingStatus::PICKUP_FAILED_ID;
                    } elseif($item['status_id'] == 17){ // CONFIRMED
                        $item['status_id'] == ShippingStatus::PICKUP_CONCLUDED_ID;
                    }
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

            //update shipment price
            if ($webserviceWeight > $shipment->weight || $webserviceFatorM3) {
                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                $tmpShipment = $shipment;
                $tmpShipment->fator_m3 = $webserviceFatorM3;
                $prices = Shipment::calcPrices($tmpShipment);

                $oldPrice = $shipment->total_price;

                $shipment->fator_m3 = $webserviceFatorM3;
                $shipment->volumetric_weight  = @$prices['volumetricWeight'];
                $shipment->cost_price         = @$prices['cost'];

                if (!$shipment->price_fixed) {
                    $shipment->total_price  = @$prices['total'];
                    $shipment->fuel_tax     = @$prices['fuelTax'];
                    $shipment->extra_weight = @$prices['extraKg'];
                }

                //DISCOUNT FROM WALLET THE DIFERENCE OF PRICE
                if (hasModule('account_wallet') && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                    $diffPrice = $shipment->total_price - $oldPrice;
                    if ($diffPrice > 0.00) {
                        try {
                            \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                            $shipment->customer->subWallet($diffPrice);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
            $shipment->save();

            if($shipment->status_id == ShippingStatus::PICKUP_CONCLUDED_ID) {
                $this->storeEnvioByTrk($shipment->provider_tracking_code, $shipment);
            }

            return true;
        }
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

        $newShipment = $originalShipment->replicate();

        $newShipment->date                = date('Y-m-d');
        $newShipment->recipient_email     = $originalShipment->recipient_email;
        $newShipment->recipient_phone     = $originalShipment->recipient_phone;
        $newShipment->charge_price        = $originalShipment->charge_price;
        $newShipment->customer_id         = $originalShipment->customer_id;
        $newShipment->agency_id           = $originalShipment->agency_id;
        $newShipment->sender_agency_id    = $originalShipment->sender_agency_id;
        $newShipment->sender_phone        = $originalShipment->sender_phone;
        $newShipment->recipient_agency_id = $originalShipment->recipient_agency_id;
        $newShipment->provider_id         = $originalShipment->provider_id;
        $newShipment->service_id          = @$originalShipment->service_id;
        $newShipment->status_id           = 15; //aguarda sync
        $newShipment->webservice_method   = $originalShipment->webservice_method;
        $newShipment->submited_at         = Date::now();

        $newShipment->end_hour   = null;
        $newShipment->start_hour = null;

        $newShipment->type = 'P';
        $newShipment->parent_tracking_code = $originalShipment->tracking_code;
        
        $newShipment->setTrackingCode();
        
        if ($originalShipment->total_price_after_pickup > 0.00) {
            $newShipment->shipping_price = $originalShipment->total_price_after_pickup;
            $newShipment->price_fixed    = true;
        }
        
        //adiciona taxa de recolha
        $newShipment->insertOrUpdadePickupExpense($originalShipment); //add expense
        $originalShipment->update([
            'children_tracking_code' => $newShipment->tracking_code,
            'children_type' => Shipment::TYPE_PICKUP,
            'status_id'     => ShippingStatus::PICKUP_CONCLUDED_ID
        ]);

        //calcula preços
        $prices = Shipment::calcPrices($newShipment);
        if(@$prices['fillable']) {
            $newShipment->fill($prices['fillable']);
            $newShipment->storeExpenses($prices);
        }
        
        $newShipment->save();
        
            //desconta preço do envio da wallet
        if (hasModule('account_wallet') && !@$originalShipment->customer->is_mensal) {
            $price = $newShipment->billing_total;

            if ($price > 0.00) {
                try {
                    $newShipment->walletPayment();
                } catch (\Exception $e) {
                }
            }
        }
        
        return $newShipment;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false)
    {

        $reference =  $shipment->reference ? ' - ' . $shipment->reference : '';

        /*$zipCode4 = explode('-', $shipment->recipient_zip_code);
        $zipCode4 = @$zipCode4[0];*/

        $service = $this->getProviderService($shipment);

        $islandTransport = false;
        if (in_array($service, ['99', '100', '101', '102', '106', '107', '108', '109'])) {
            $islandTransport = true;
        }

        //$service = empty($service) ? 'VASP24' : $service;
        $service = empty($service) ? 93 : $service;

        $data = [
            'service' => [
                //"ServiceType"               => $service,
                "ServiceFlow"               => $shipment->is_collection ? 2 : 0, //2 = recolha, 0 = envio
                "ServiceTypeID"             => $service,
                "IslandTransport"           => $islandTransport,
                "ServiceMustDeliverOnRemote" => false,
                "ServiceDeliveryPointCode"  => "",
                "ServiceClientReference"    => $reference,
                // "ServiceClientBarCode"      => $shipment->tracking_code,
                "ServicePinCode"            => "",
                "ServiceNumberOfVolumes"    => $shipment->volumes,
                "ServiceTotalWeightOfVolumes" => $shipment->weight,
                "ServiceAmount"             => $shipment->charge_price,
                "ServicePoD"                => false, //retorno de guia assinada
                "ServiceSms"                => "",
                "ServiceInstructions"       => $shipment->obs,
                "ServiceEstimatedDateOfConclusion" => "",
                "ServicePreferentialPeriod" => "",
                "SenderClientCode"          => "",
                "SenderName"                => $shipment->sender_name,
                "SenderContactName"         => $shipment->sender_attn ? $shipment->sender_attn : $shipment->sender_name,
                "SenderContactPhoneNumber"  => $shipment->sender_phone,
                "SenderContactEmail"        => null,
                "SenderAddressStreet"       => $shipment->sender_address,
                "SenderAddressDoorNumber"   => null,
                "SenderAddressFloor"        => null,
                "SenderAddressPlace"        => $shipment->sender_city,
                "SenderAddressZipCode"      => $shipment->sender_zip_code,
                "SenderAddressZipCodePlace" => $shipment->sender_city,
                "SenderAddressDistrict"     => null,
                "SenderAddressCountryCode"  => strtoupper($shipment->sender_country),
                "ReceiverClientCode"        => "",
                "ReceiverName"              => $shipment->recipient_name,
                "ReceiverContactName"       => $shipment->recipient_attn ? $shipment->recipient_attn : $shipment->recipient_name,
                "ReceiverContactPhoneNumber" => $shipment->recipient_phone,
                "ReceiverContactEmail"      => null,
                "ReceiverAddressStreet"     => $shipment->recipient_address,
                "ReceiverAddressDoorNumber" => null,
                "ReceiverAddressFloor"      => null,
                "ReceiverAddressPlace"      => $shipment->recipient_city,
                "ReceiverAddressZipCode"    => $shipment->recipient_zip_code,
                "ReceiverAddressZipCodePlace" => $shipment->recipient_city,
                "ReceiverAddressDistrict"   => null,
                "ReceiverAddressCountryCode" => strtoupper($shipment->recipient_country),
                "ReceiverFixedInstructions" => "",
                "PrinterModel"              => "GK420d"
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

            $row = mapArrayKeys($row, config('webservices_mapping.vasp.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $row['created_at'] = new Carbon($row['date'] . ' ' . $row['time']);

                $status = config('shipments_import_mapping.vasp-status');
                $row['status_id'] = @$status[$row['status']];

                if ($row['status_id'] == '9') { //incidencia

                    /*$incidences = config('shipments_import_mapping.ctt-incidences');
                    $row['incidence_id'] = @$incidences[$row['incidence_id']];*/
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
     * Return barcode image
     * @param $trk
     * @return string
     */
    public function rotateBarcode($trk)
    {
        $filepath = 'https://bwipjs-api.metafloor.com/?bcid=code128&text=' . $trk . '&scaleX=3&scaleY=2&rotate=L';
        $filepath = file_get_contents($filepath);

        $filepath = base64_encode($filepath);
        $filepath = 'data:image/png;base64,' . $filepath;

        return $filepath;
    }

    /**
     * Return content from label data
     * @param $trk
     * @return string
     */
    public function getDataFromLabel($str)
    {

        if ($str) {
            $str = explode('"', $str);
            return trim($str[1]);
            //dd($str);
            /*$str = end($str);
            $str = str_replace('"', '', $str);*/
        }

        return $str;
    }

    /**
     * Return url to correct context
     * @param $trk
     * @return string
     */
    public function getUrl()
    {

        if (config('app.env') == 'local') {
            return $this->testUrl;
        }

        return $this->url;
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

        $zipCode4 = explode('-', $shipment->recipient_zip_code);
        $zipCode4 = @$zipCode4[0];

        try {

            $serviceKey = $shipment->recipient_country;
            if ($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            if ($providerService == 'AI') {

                //portosanto
                if (in_array($zipCode4, ["9400"])) {
                    //$service = 'VPSAAR';
                    $providerService = 100;
                }

                //madeira
                else if (in_array($zipCode4, ["9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225", "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385"])) {
                    //$service = 'VMADAR';
                    $providerService = 99;
                }

                //acores sao miguel
                else if (in_array($zipCode4, ["9500", "9504", "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684"])) {
                    //$service = 'VACIAR';
                    $providerService = 101;
                }

                //acores (restantes ilhas)

                else if (in_array($zipCode4, ["9700","9701","9760","9580","9880","9800","9900","9804","9850","9875","9930","9934","9940","9944","9950","9960","9970","9980"])) {
                    //$service = 'VACIIAR';
                    $providerService = 102;
                }
            } elseif ($providerService == 'MI') {

                //portosanto
                if (in_array($zipCode4, ["9400"])) {
                    //$service = 'VPSMAR';
                    $providerService = 107;
                }

                //madeira
                else if (in_array($zipCode4, ["9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225", "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385"])) {
                    //$service = 'VMADMAR';
                    $providerService = 106;
                }

                //acores sao miguel
                else if (in_array($zipCode4, ["9500", "9504", "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684"])) {
                    //$service = 'VACIMAR';
                    $providerService = 109;
                }

                //acores (restantes ilhas)
                else if(in_array($zipCode4, ["9700","9701","9760","9580","9880","9800","9804","9850","9875","9900","9930","9934","9940","9944","9950","9960","9970","9980"])) {
                    //$service = 'VACIIMAR';
                    $providerService = 108;
                }
            }


            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {
                $serviceCode = @$shipment->service->code;
                $serviceCode = $serviceCode ? $serviceCode : '24H';

                if ($serviceCode == 'AI') {

                    //portosanto
                    if (in_array($zipCode4, ["9400"])) {
                        //$service = 'VPSAAR';
                        $providerService = 100;
                    }

                    //madeira
                    else if (in_array($zipCode4, ["9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225", "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385"])) {
                        //$service = 'VMADAR';
                        $providerService = 99;
                    }

                    //acores sao miguel
                    else if (in_array($zipCode4, ["9500", "9504", "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684"])) {
                        //$service = 'VACIAR';
                        $providerService = 101;
                    }

                    //acores (restantes ilhas)
                    else if (in_array($zipCode4, ["9700", "9701", "9760", "9580", "9880", "9800", "9804", "9850", "9875", "9930", "9934", "9940", "9944", "9950", "9960", "9970", "9980"])) {
                        //$service = 'VACIIAR';
                        $providerService = 102;
                    }
                } elseif ($serviceCode == 'MI') {

                    //portosanto
                    if (in_array($zipCode4, ["9400"])) {
                        //$service = 'VPSMAR';
                        $providerService = 107;
                    }

                    //madeira
                    else if (in_array($zipCode4, ["9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225", "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385"])) {
                        //$service = 'VMADMAR';
                        $providerService = 106;
                    }

                    //acores sao miguel
                    else if (in_array($zipCode4, ["9500", "9504", "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684"])) {
                        //$service = 'VACIMAR';
                        $providerService = 109;
                    }

                    //acores (restantes ilhas)
                    else if (in_array($zipCode4, ["9700", "9701", "9760", "9580", "9880", "9800", "9804", "9850", "9875", "9930", "9934", "9940", "9944", "9950", "9960", "9970", "9980"])) {
                        //$service = 'VACIIMAR';
                        $providerService = 108;
                    }
                } else {
                    $services = config('shipments_export_mapping.vasp-services');
                    $providerService = @$services[$serviceCode];
                }
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço VASP.');
        }

        return $providerService;
    }
}
