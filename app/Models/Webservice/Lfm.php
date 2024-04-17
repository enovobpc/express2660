<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;

class Lfm extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url     = 'https://lfmexport.com/api_v1/invoices/';

    /**
     * @var null
     */
    private $debug = false;

    /**
     * @var null
     */
    private $apiKey = null;

    /**
     * Ctt constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $cliente = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug = false)
    {
        $this->apiKey = $sessionId;
        $this->debug  = $debug;

        //$this->apiKey = 'BYrUhGoz2tRRTfMu5JYONw==';
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

    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode, $shipment)
    {
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
        return false;
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
        return false;
    }

    /**
     * Permite obter um envio da base de dados  um envio pelo seu trk caso exista envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeEnvioByTrk($trakingCode, $originalShipment)
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
    public function getRecolhaByTrk($trakingCode)
    {
        return false;
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
        return false;
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
        return '';
    }


    /**
     * Submit a shipment
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function storeEnvio($data)
    {

        $response = $this->execute('insert.php', $data);

        if(empty($response)) {
            throw new \Exception('ERRO API LFM - Não retornou nenhuma resposta.');
        }

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', print_r($data, true));
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }

        if(@$response['status'] == 'ERROR' || empty(@$response['status'])) {
            throw new \Exception($this->getError($response));
        } else {

            $parcelNo = str_replace('PARCEL NUMBER = ', '', @$response['error_desc']);
            $trk = $parcelNo.','.@$response['invoice_id'];

            return $trk;

        }

        return null;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        return true;

        if($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code, $shipment);
        } else {
            $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        }


        if($data) {

            $weightChanged = true;
            $deliveredTrks = [];
            foreach ($data as $key => $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::DELIVERED_ID) {
                    $deliveredTrks[$shipment->provider_tracking_code] = $shipment->provider_tracking_code;
                }

            }

            try {
                if($history) {
                    $history->sendEmail(false, false, true);
                }
            } catch (\Exception $e) {}


            if($history) {
                $shipment->status_id = $history->status_id;
            }

            /**
             * Calcula o preço e custo do envio
             */
            if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id
                    && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id
                    && $shipment->agency_id && $shipment->customer_id && $weightChanged)) {

                $serviceId = $shipment->service_id;
                if($shipment->is_collection) {
                    $serviceId = @$shipment->service->assigned_service_id;
                }

                $tmpShipment = $shipment;
                $tmpShipment->service_id = $serviceId;
                $prices = Shipment::calcPrices($tmpShipment);

                $oldPrice = $shipment->total_price;
                $shipment->cost_price = @$prices['cost'];

                if (!empty($data['total_price_for_recipient'])) {
                    $shipment->total_price = 0;
                    $shipment->payment_at_recipient = 1;
                    $shipment->total_price_for_recipient = $data['total_price_for_recipient'];
                } else {
                    $shipment->payment_at_recipient = 0;
                    $shipment->total_price_for_recipient = 0;

                    if (!$shipment->price_fixed) {
                        $shipment->total_price  = @$prices['total'];
                        $shipment->fuel_tax     = @$prices['fuelTax'];
                        $shipment->extra_weight = @$prices['extraKg'];
                    }

                    //DISCOUNT FROM WALLET THE DIFERENCE OF PRICE
                    if(hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                        $diffPrice = $shipment->total_price - $oldPrice;
                        if($diffPrice > 0.00) {
                            try {
                                \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                                $shipment->customer->subWallet($diffPrice);
                            } catch (\Exception $e) {}
                        }
                    }
                }
            }

            $shipment->save();

            if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                $price = $shipment->addPickupFailedExpense();
                $shipment->walletPayment(null, null, $price); //discount payment
            }

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
    public function saveShipment($shipment, $isCollection = false, $webserviceLogin = null) {

        /**
         * MIA_EXPRESS = Partindo Miami Express – Destino Brazil
         * MIA_DDU     = Partindo Miami – Destino Brazil
         */

        $serviceCode = 'MIA_EXPRESS';
        if($shipment->service->code == 'STD') {
            $serviceCode = 'MIA_DDU';
        } elseif($shipment->service->code == 'TRIBUT') {
            $serviceCode = 'MIA_EXPRESS';
        }

        /*try {
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


        $senderName = explode(' ', $shipment->sender_name);
        $senderLastname  = array_pop($senderName);
        $senderFirstName = trim(str_replace($senderLastname, '', $shipment->sender_name));

        $recipientName = explode(' ', $shipment->recipient_name);
        $recipientLastname  = array_pop($recipientName);
        $recipientFirstName = trim(str_replace($recipientLastname, '', $shipment->recipient_name));

        $items = [];
        if($shipment->pack_dimensions->isEmpty()) {

            $packages = [
                "weight" => $shipment->weight < 1 ? 1 : $shipment->weight,
                "width"  => 6.3,
                "length" => 6.3,
                "height" => 6.3,
                "freight_value"   => $shipment->total_price > 0.00 ? $shipment->total_price : 0.01,
                "insurance_value" => 0
            ];

            $items[]= [
                "ncm_code"              => "39264000",
                "sku_code"              => "N/A",
                "description_official"  => "Items diversos",
                "description_user"      => $shipment->obs,
                "quantity"              => $shipment->volumes,
                "value"                 => $shipment->goods_price ? $shipment->goods_price : 1,
                "contains_battery"      => false,
                "contains_perfume"      => false
            ];
        } else {

            $packages = [
                'weight' => $shipment->pack_dimensions->first()->weight < 1 ? 1 : $shipment->pack_dimensions->first()->weight,
                'width'  => $shipment->pack_dimensions->first()->width,
                'length' => $shipment->pack_dimensions->first()->length,
                'height' => $shipment->pack_dimensions->first()->height,
                'freight_value'   => $shipment->cost_price > 0.00 ? $shipment->cost_price : ($shipment->total_price > 0.00 ? $shipment->total_price : 0.01),
                'insurance_value' => 0
            ];

            foreach ($shipment->pack_dimensions as $dimension) {
                /*$packages[] = [
                    "weight" => $dimension->weight,
                    "width"  => $dimension->width,
                    "length" => $dimension->length,
                    "height" => $dimension->height,
                    "freight_value"   => $shipment->total_price > 0.00 ? $shipment->total_price : 0.01,
                    "insurance_value" => $shipment->insurance_price
                ];*/

                $items[]= [
                    "ncm_code"              => "39264000",
                    "sku_code"              => $dimension->description ? $dimension->description : 0,
                    "description_official"  => $dimension->description ? $dimension->description : "Bens diversos",
                    "description_user"      => $shipment->obs,
                    "quantity"              => $dimension->qty,
                    "value"                 => $dimension->price > 0.00 ? $dimension->price : $shipment->goods_price,
                    "contains_battery"      => false,
                    "contains_perfume"      => false
                ];
            }
        }

        $data = [
            "token"       => $this->apiKey ,
            "service_id"  => $serviceCode,
            "external_customer_id" => str_pad($shipment->id, 8, '0', STR_PAD_LEFT),
            "sender" => [
                "first_name"=> $senderFirstName,
                "last_name" => $senderLastname,
                "email"     => @$shipment->customer->contact_email,
                "ddi"       => "+1",
                "phone"     => $shipment->sender_phone,
                "address"   => [
                    "number"        => "0",
                    "address_line_1"=> $shipment->sender_address,
                    "address_line_2"=> "",
                    "state"         => $shipment->sender_state,
                    "city"          => $shipment->sender_city,
                    "postal_code"   => $shipment->sender_zip_code
                ]
            ],
            "recipient" => [
                "type"         => "i",
                "tax_id"       => $shipment->recipient_vat ? $shipment->recipient_vat : "12345678909",
                "first_name"   => $recipientFirstName,
                "last_name"    => $recipientLastname,
                "business_name"=> "",
                "email"        => $shipment->recipient_email ? $shipment->recipient_email : 'trk@trk.com',
                "ddi"          => "+55",
                "phone"        => $shipment->recipient_phone,
                "address"      => [
                    "po_box"        => "",
                    "number"        => 0,
                    "address_line_1"=> $shipment->recipient_address,
                    "address_line_2"=> "",
                    "state"         => $shipment->recipient_state,
                    "city"          => $shipment->recipient_city,
                    "postal_code"   => $shipment->recipient_zip_code,
                    "country"       => strtoupper($shipment->recipient_country)
                ]
            ],
            "parcel_details" => $packages,
            "items" => $items
        ];


        if($shipment->provider_tracking_code) {
            try {
                $this->destroyShipment($shipment);
            } catch(\Exception $e) {}
        }


        $trk = $this->storeEnvio($data);

        return $trk;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {

        $invoiceId = explode(',', $shipment->provider_tracking_code);
        $invoiceId = $invoiceId[1];

        $data = [
            'token' => $this->apiKey,
            'invoice_id' => $invoiceId
        ];

        $response = $this->execute('delete.php', $data);

        if(@$response['status'] != 'SUCESS') {
            throw new \Exception(@$response['error_desc']);
        }
        return true;
    }

    /**
     * @param $method
     * @param $data
     * @return mixed
     */
    public function execute($method, $data) {
        $curl = curl_init();

        $url = $this->url . $method;

        $data = json_encode($data);

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
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }


    /**
     * @param $trackingCode
     * @return mixed
     */
    public function getShippingId($trackingCode) {
        $parts = explode(',', $trackingCode);

        $shippingId = @$parts[1];
        if(empty($shippingId)) {
            $shippingId = @$parts[0];
        }

        return $shippingId;
    }

    /**
     * @param $response
     */
    public function getError($response) {

        $errorsList = [
            '-18' => 'Telefone de coleta ou entrega inválido.'
        ];

        if(@$errorsList[@$response['error_code']]) {
            return @$errorsList[@$response['error_code']];
        }


        return @$response['error_code'].' - '. @$response['error_desc'];
    }
}