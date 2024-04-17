<?php

namespace App\Models\Webservice;

use App\Models\ShippingStatus;
use Carbon\Carbon;
use Date, File, Setting;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use LynX39\LaraPdfMerger\PdfManage;

class Dhl extends \App\Models\Webservice\Base
{

    /**
     * @var string
     * Docs: https://clientesparcel.dhl.es/ac.core/gateway/swagger/index.html
     */
    private $url = 'https://clientesparcel.dhl.es/ac.core/gateway/a/api/v1/';
    private $trackingUrl = 'https://clientesparcel.dhl.es/LiveTracking/api/expediciones?numeroExpedicion=';


    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/dhl/';

    /**
     * @var null
     */
    private $account;

    /**
     * @var null
     */
    private $userId;

    /**
     * @var null
     */
    private $key;

    /**
     * @var null
     */
    private $sessionId;

    /**
     * @var null
     */
    private $debug;

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
            /* $this->account = '710016759';
             $this->userId  = 'bbb18b5e-37e0-48c1-9db9-1a0e4d5e4a1a';
             $this->key     = 'bc3818d0-71e8-45b7-9a47-35b792993877';*/

            //sopostal - credenciais teste
            $this->account = '710014675';
            $this->userId  = 'f7919404-c202-4394-a7cb-d60c7a79135b';
            $this->key     = 'bd8e7547-e791-434d-9c21-7c768f02ac0e';
        } else {
            $this->account = $agencia;
            $this->userId  = $user;
            $this->key     = $password;
        }

        $this->sessionId = $this->login();
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
    public function getEstadoEnvioByTrk($trakingCode)
    {
        if (!$trakingCode) {
            return false;
        }

        $trakingCode = explode(',', $trakingCode);


        if (empty(@$trakingCode[1])) {
            $trakingCode = $trakingCode[0];
        } else {
            $trakingCode = @$trakingCode[1];
        }

        $url = $this->trackingUrl . $trakingCode;

        try {
            $result = file_get_contents($url);
            $result = json_decode($result, true);

            if (empty($result)) {
                throw new \Exception('Sem informação de estados.');
            } else {

                $history = @$result['Seguimiento'];

                $mappedStatus = $this->mappingResult($history, 'status');

                return [
                    'history' => $mappedStatus,
                    'weight'  => (float) $result['Kilos']
                ];
            }
        } catch (\Exception $e) {
            $result = file_get_contents($url);
        }

        return $result;
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
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
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

        $data = [
            "accountId"        => $this->account,
            "pickupDate"       => $data['date'],
            "description"      => "",
            "pickupLocation"   => "",
            "numberOfPackages" => $data['volumes'],
            "numberOfPallets"  => 0,
            "totalWeight"      => $data['weight'],
            "shipper" => [
                "email" => [
                    "address" => "trk@trk.pt"
                ],
                "phoneNumber" => $data['sender_phone'],
                "name" => [
                    "firstName"     => $data['sender_attn'],
                    "companyName"   => $data['sender_name'],
                ],
                "address" => [
                    "street"     => $data['sender_address'],
                    "city"       => $data['sender_city'],
                    "number"     => "",
                    "postalCode" => $data['sender_zip_code'],
                    "isBusiness" => true,
                    "addition"   => "A",
                    "countryCode" => $data['sender_country'],
                ]
            ],
            "receiver" => [
                "email" => [
                    "address" =>  $data['recipient_email'] ?  $data['recipient_email'] : "trk@trk.pt"
                ],
                "phoneNumber" => $data['recipient_phone'],
                "name" => [
                    "firstName"     => $data['recipient_attn'],
                    "companyName"   => $data['recipient_name'],
                    "lastName"      => ""
                ],
                "address" => [
                    "countryCode"   => $data['recipient_country'],
                    "number"        => "",
                    "isBusiness"    => true,
                    "street"        => $data['recipient_address'],
                    "city"          => $data['recipient_city'],
                    "postalCode"    => $data['recipient_zip_code']
                ]
            ],
            "timeSlot" => [
                "from" => $data['start_hour'] ? $data['start_hour'] : '08:00',
                "to"   => $data['end_hour'] ? $data['end_hour'] : '19:00',
            ],
            //"type"          => "",
            "provideLabels" => true,
            "requesterName" => $data['recipient_name']
        ];

        $response = $this->execute('pickup-requests', $data);

        //dd($response);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            file_put_contents(public_path() . '/dumper/request.txt', print_r($data, true));
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }

        if (!File::exists(public_path() . $this->upload_directory)) {
            File::makeDirectory(public_path() . $this->upload_directory, 0777, true, true);
        }

        try {

            if (@$response['message']) {
                throw new \Exception($response['message']);
            }

            $masterTrk = @$response['confirmationNumber'];
            $accountId = @$response['accountId'];
            return $masterTrk;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return null;
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {

        $shipmentMasterReference = 'TRK' . $data['tracking_code'] . '_' . time();

        $options = [
            ["key" => "DOOR"], //Entrega na porta
            /*[
                "key"       => "REFERENCE",
                "reference" => $shipmentMasterReference
            ],*/
            [
                "key"   => "REFERENCE",
                "input" => 'TRK' . $data['tracking_code']
            ]
        ];

        if(@$data['reference']) {
            $options[] = [
                "key" => "REFERENCE2",
                "input" => $data['reference']
            ];
        }

        if ($data['charge_price']) {
            $options[] = [
                "key"   => "COD_CASH",
                "input" => $data['charge_price']
            ];
        }


        $shipmentId = uuid();
        $shipmentId = substr($shipmentId, 0, -12) . $data['tracking_code'];

        $volumes = (int) $data['volumes'];
        $weight  = (float) $data['weight'];
        $weight  = floor($weight / $volumes);

        $data = [
            "key" => $this->sessionId,
            "accountId"  => $this->account,
            "shipmentId" => $shipmentId,
            "orderReference" => $data['reference'],
            "receiver" => [
                "email"       => "trk@trk.com",
                "phoneNumber" => $data['recipient_phone'],
                "name" => [
                    "firstName"     => $data['recipient_attn'],
                    "companyName"   => $data['recipient_name'],
                    "lastName"      => ""
                ],
                "address" => [
                    "countryCode"   => $data['recipient_country'],
                    "number"        => "",
                    "isBusiness"    => true,
                    "street"        => $data['recipient_address'],
                    "city"          => $data['recipient_city'],
                    "postalCode"    => $data['recipient_zip_code']
                ]
            ],
            "shipper" => [
                "email"       => "",
                "phoneNumber" => $data['sender_phone'],
                "name" => [
                    "firstName" => $data['sender_name'],
                    "lastName" => ""
                ],
                "address" => [
                    "street"     => $data['sender_address'],
                    "city"       => $data['sender_city'],
                    "number"     => "",
                    "postalCode" => $data['sender_zip_code'],
                    "isBusiness" => true,
                    "addition"   => "A",
                    "countryCode" => $data['sender_country'],
                ]
            ],
            "product"     => $data['service'],
            "options"     => $options,
            "returnLabel" => false,
            "pieces" => [
                [
                    "parcelType" => "SMALL",
                    "quantity"   => $volumes,
                    "weight"     => $weight,
                    /*"dimensions" => [
                        "length" => 20,
                        "width"  => 25,
                        "height" => 30
                    ]*/
                ]
            ]
        ];


        //dd($data);

        $response = $this->execute('shipments', $data);

        if ($this->debug) {
            if (!File::exists(public_path() . '/dumper/')) {
                File::makeDirectory(public_path() . '/dumper/');
            }

            file_put_contents(public_path() . '/dumper/request.txt', print_r($data, true));
            file_put_contents(public_path() . '/dumper/response.txt', $response);
        }

        if (!File::exists(public_path() . $this->upload_directory)) {
            File::makeDirectory(public_path() . $this->upload_directory, 0777, true, true);
        }

        try {

            if (@$response['pieces']) {

                $trks = [];
                foreach (@$response['pieces'] as $key => $piece) {
                    $trks[] = $piece['trackerCode'];
                }

                $masterTrk = @$response['shipmentTrackerCode'];
                $trk = implode(',', $trks);

                $this->storeLabels(@$response['pieces'], $masterTrk);

                //$masterTrk = @$response['shipmentTrackerCode'] . ',' . $trk;
                $masterTrk = $masterTrk . ',' . @$trks[0];
                return $masterTrk;
            } elseif(@$response['message']) {
                throw new \Exception('DHL informa: '.@$response['message']);
            }

            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return null;
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

        $file = File::get(public_path() . '/uploads/labels/dhl/' . $trackingCode . '_labels.pdf');
        $file = base64_encode($file);
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
    public function destroyShipment($trackingCode)
    {
        return true;
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

        if ($url == 'authenticate/api-key') {
            $header = [
                "Content-Type: application/json"
            ];
        } else {
            $header = [
                "Content-Type: application/json",
                'Authorization: Bearer ' . $this->sessionId
            ];
        }





        $url = $this->url . $url;


        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
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

        curl_close($curl);

        if (!$response) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        } else {

            /* if (!empty(@$response['key'])) {
                 throw new \Exception(@$response['key'] . ' - ' . $response['message'] . ' ' . (@$response['details'] ? json_encode(@$response['details']) : ''));
             }*/

            if (!empty(@$response['errors']['DomainValidations'])) {
                $error = @$response['errors']['DomainValidations'];

                throw new \Exception(@$response['errors']['DomainValidations'][0]);
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
            "userId" => $this->userId,
            "key" => $this->key,
            "accountNumbers" => [
                $this->account
            ]
        ];

        //dd($data);
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

        $url = 'capabilities/business?' . $queryString;

        $result = $this->execute($url, [], 'GET');
        return $result;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        $data = self::getEstadoEnvioByTrk($shipment->provider_tracking_code);

        $webserviceWeight  = null;
        $receiverSignature = null;

        if ($data) {

            if (isset($data['weight'])) {
                $webserviceWeight  = $data['weight'];
            }

            $data = @$data['history'];

            aasort($data, 'created_at');

            foreach ($data as $key => $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'incidence_id' => @$item['incidence_id'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($data);
                $history->shipment_id = $shipment->id;
                $history->save();

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            /**
             * Update shipment weight
             */
            if ($webserviceWeight > $shipment->weight) {
                $shipment->weight   = $webserviceWeight;

                $tmpShipment = $shipment;
                $prices = Shipment::calcPrices($tmpShipment);

                $shipment->volumetric_weight  = $prices['volumetricWeight'];
                $shipment->total_price  = $prices['total'];
                $shipment->cost_price   = $prices['cost'];
                $shipment->fuel_tax     = @$prices['fuelTax'];
                $shipment->extra_weight = @$prices['extraKg'];
            }

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
    public function saveShipment($shipment, $isCollection = false) {

        $data = $shipment->toArray();

        $isCollection = $isCollection ? $isCollection : $shipment->is_collection;

        $data['sender_country']    = strtoupper($data['sender_country']);
        $data['recipient_country'] = strtoupper($data['recipient_country']);

        if(config('app.source') == 'thyman'){
            $data['sender_zip_code'] = '2710-020';
            $data['sender_city'] = 'SINTRA';
        }

        if(in_array($data['recipient_country'], ['PT', 'ES'])) {
            $data['service'] = 'EPL-DOM-IBERIA';
        } else if(in_array($data['recipient_country'],
            ['FR','DE','IT','BE','BG','CZ','DK','EE','IE','EL','HR',
                'CY','LV','LT','LU','HU','MT','NL','AT','PL', 'RO', 'SI',
                'SK','FI','SE','UK','NO','CH','TR','RS','AL','MK','ME'])) { //Internacional Europa
            $data['service'] = 'EPL-INT-IBERIA-ESU';
        } else {
            $data['service'] = 'EPL-INT-IBERIA-ESI'; //fora europa
        }

        if($isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
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
    public function storeLabels($pieces, $shipmentTrk)
    {

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
            $outputFilepath = public_path() . $this->upload_directory . $shipmentTrk . '_' . $i . '_labels.pdf';
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
        $outputFilepath = public_path() . $this->upload_directory . $shipmentTrk . '_labels.pdf';
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
    private function mappingResult($data, $mappingArray)
    {

        $arr = [];

        foreach ($data as $row) {

            if (!is_array($row)) {
                $row = (array) $row;
            }

            $row = mapArrayKeys($row, config('webservices_mapping.dhl.' . $mappingArray));

            //mapping and process status
            if ($mappingArray == 'status' || $mappingArray == 'collection-status') {

                $row['date'] = explode('/', $row['date']);
                $row['date'] = @$row['date'][2] . '-' . @$row['date'][1] . '-' . @$row['date'][0];

                $row['created_at'] = new Carbon($row['date'] . ' ' . $row['hour'] . ':00');

                $description = trim($row['description']);
                $statusCode  = trim(@$row['code']);
                if (empty($statusCode))
                    continue;

                $status = config('shipments_import_mapping.dhl-status');
                $row['status_id'] = @$status[$statusCode];

                if ($row['status_id'] == '9') {
                    $incidences = config('shipments_import_mapping.dhl-incidences');
                    $row['incidence_id'] = @$incidences[$statusCode];
                }

                if ($row['status_id'] == 'ZZZ') {
                    $row['status_id'] = @$status[$description];
                }


                $row['obs'] = trim(@$row['city']);

                /*if($row['status_id'] == '9') { //incidencia

                    $incidences = config('shipments_import_mapping.dhl-incidences');
                    $row['incidence_id'] = @$incidences[$row['incidence_id']];

                    $row['obs'] = $row['obs'] . '<br/>' . $row['incidence_obs'];
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
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = [])
    {
    }
}