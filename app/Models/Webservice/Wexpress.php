<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;

class Wexpress extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    //https://app.swaggerhub.com/apis/W-Express/shipping/1.1#/
    private $urlTest = 'https://sandbox.wexpress.me/';
    private $url     = 'https://api.wexpress.me/';

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

        $shippingId = $this->getShippingId($trackingCode);

        return 'https://label.wexpress.me/correios/?shipping_id=' . $shippingId;

        /*$url = 'https://label.wexpress.me/correios/download/'.$shippingId.'-label-printer.pdf';

        $file = file_get_contents($url);
        $file = base64_encode($file);
        return $file;
        */
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

        $response = $this->execute('shipping', $data);

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', print_r($data, true));
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }

        if(!empty(@$response['message']) && @$response['shipping_status'] == 'CANCELED') {
            throw new \Exception($response['message']);
        } else {
            if(@$response['courier_tracking_number']) {
                return $response['courier_tracking_number'].','.$response['shipping_id'];
            }
            return $response['shipping_id'];
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
                $shipment->status_id   = $history->status_id;
                $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
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


        $serviceCode = 'wexpress_correios_std';

        if($shipment->service->display_code == 'EXPR') {
            $serviceCode = 'wprime_correios_exp';
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

        $packages = [];
        $items    = [];
        if($shipment->pack_dimensions->isEmpty()) {
            $kgPerVol = $shipment->weight/$shipment->volumes;
            for ($i = 0; $i<$shipment->volumes; $i++) {
                $packages[] = [
                    "weight" => $kgPerVol < 1 ? 1 : $kgPerVol,
                    "width"  => 15,
                    "length" => 15,
                    "height" => 15
                ];
            }

            $items[]= [
                    "description"   => "Bens diversos",
                    "quantity"      => $shipment->volumes,
                    "unit_value"    => 0
                ];
        } else {
            foreach ($shipment->pack_dimensions as $dimension) {
                $packages[] = [
                    "weight" => $dimension->weight,
                    "width"  => $dimension->width,
                    "length" => $dimension->length,
                    "height" => $dimension->height
                ];

                $items[]= [
                    "description"   => $dimension->description ? $dimension->description : "Bens diversos",
                    "quantity"      => $dimension->qty,
                    "unit_value"    => $dimension->price
                ];
            }
        }



        $senderName = explode(' ', $shipment->sender_name);
        $senderLastname  = array_pop($senderName);
        $senderFirstName = trim(str_replace($senderLastname, '', $shipment->sender_name));

        $recipientName = explode(' ', $shipment->recipient_name);
        $recipientLastname  = array_pop($recipientName);
        $recipientFirstName = trim(str_replace($recipientLastname, '', $shipment->recipient_name));

        $data = [
            "external_shipping_id"        => "TRK".$shipment->tracking_code,
            "external_shipping_reference" => $shipment->reference,
            "service_code"      => $serviceCode,
            "incoterms"         => "DDU",
            "dimensions_unit"   => "in",
            "weight_unit"       => "kg",
            "currency"          => "USD",
            "freight_value"     => $shipment->total_price > 0.00 ? $shipment->total_price : 0.01,
            "insurance_value"   => $shipment->insurance_price,
            "packages" => $packages,
            "sender" => [
                "first_name" => $senderFirstName,
                "last_name"  => $senderLastname,
                "website"    => "",
                "address"    => [
                    "address_line_1" => $shipment->sender_address,
                    "address_line_2" => "",
                    "postal_code"    => $shipment->sender_zip_code,
                    "city"           => $shipment->sender_city,
                    "state"          => $shipment->sender_state ? $shipment->sender_state : "FL",
                    "country"        => strtoupper($shipment->sender_country)
                ]
            ],
            "recipient" => [
                "first_name"    => $recipientFirstName,
                "last_name"     => $recipientLastname,
                "tax_id_type"   => "CPF",
                "tax_id"        => $shipment->recipient_vat ? $shipment->recipient_vat : "12345678909",
                "email"         => $shipment->recipient_email ? $shipment->recipient_email : env('MAIL_FROM'),
                "phone"         => $shipment->recipient_phone,
                "address"       => [
                    "address_number" => "1",
                    "address_line_1" => $shipment->recipient_address,
                    "address_line_2" => "",
                    "postal_code"    => str_replace('-','', $shipment->recipient_zip_code),
                    "city"           => $shipment->recipient_city,
                    "state"          => $shipment->recipient_state ? $shipment->recipient_state : "SP",
                ]
            ],
            "items" => $items
        ];

        if($shipment->provider_tracking_code) {
            $data['shipping_id'] = $this->getShippingId($shipment->provider_tracking_code);;
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
                'x-api-key: ' . $this->apiKey,
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
}