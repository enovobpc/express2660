<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mpdf\Mpdf;
use DB;

class Integra2 extends \App\Models\Webservice\Base {

    /**
     * @var null
     */
    private $customerCode;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * @var null
     */
    private $ftpHost = 'mp.ftp.logista.com';

    /**
     * @var null
     */
    private $ftpUser = null;

    /**
     * @var null
     */
    private $ftpPass = null;

    /**
     * @var null
     */
    private $ftpPort = 21;

    /**
     * @var null
     */
    private $agencia = null;

    /**
     * Correos Express constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        if(config('app.env') == 'local') {
            //$this->customerCode = '001168'; //log-in
            $this->customerCode = '005169'; //novoexpresso
            $this->ftpUser = 'mp0277'; //log-in
            $this->ftpPass = '7Afpw9cXwA';
            $this->agencia = '408';
        } else {
            $this->customerCode = $agencia;
            $this->ftpUser      = $user;
            $this->ftpPass      = $password;
            $this->agencia      = $sessionId;
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
    public function getEstadoEnvioByTrk($shipment)
    {
        require_once base_path() . '/resources/helpers/DOMhtml.php';

        $trk = $shipment->provider_tracking_code;
        $recipientZipCode = substr($shipment->recipient_zip_code, 0, 3);

        $url = 'https://www.integra2.es/bin/integra2/tracking?ref='.$trk.'&cp='.$recipientZipCode.'&language=ptPT';
        //$url = 'https://www.integra2.es/bin/integra2/tracking?ref=134495981&cp=115&language=ptPT';

        $content = json_decode(file_get_contents($url));

        if(@$content->error) {
            return false;
        }

        $proof      = @$content->docs->docsItem->urlDoc;
        $weight     = @$content->datosPrincipales->kilos;
        $reference  = @$content->datosPrincipales->refPral;
        $expedition = @$content->datosPrincipales->expedicion;


        $lastStatus = [
            'status' => @$content->tracking->trackingItem->descTracking,
            'date'   => @$content->tracking->trackingItem->fecha,
            'pobDest'=> @$content->datosDestino->pobDest
        ];


        $url = 'https://clientes.integra2.es/integra2/seguimiento?EXPEDICION='.$expedition.'&REFERENCIA='.$reference;

        $html = file_get_html($url);

        $tableTr = @$html->find('.celdaTab tr td');

        $history = [];
        foreach ($tableTr as $key => $tr) {
            $parts = $tr->plaintext;
            $parts = nl2br($parts);
            $parts = explode('<br />', $parts);

            try {

                if(!empty(@$parts[0])) {
                    $status     = trim(str_replace('&nbsp;', ' ', @$parts[0]));
                    $location   = trim(str_replace('&nbsp;', ' ', @$parts[1]));
                    $location   = explode(' ', $location);
                    $location   = @$location[1];


                    $date = trim(str_replace('&nbsp;', ' ', @$parts[2]));
                    $date = Carbon::createFromFormat('d/m/Y H:i', $date);
                    $date = $date->format('Y-m-d H:i:s');


                    $statusArr = config('shipments_import_mapping.integra2-status');
                    $statusId = @$statusArr[$status];

                    $data = [
                        'status_id'  => $statusId,
                        'obs'        => $location,
                        'city'       => $location,
                        'created_at' => @$date,
                        'weight'     => $weight
                    ];


                    if($statusId == 5 && !empty($proof)) {
                        $data['obs'].= '<br/><a href="'.$proof.'" target="_blank">Prova Entrega</a>';
                    }

                    $history[] = $data;
                }
            } catch(\Exception $e) {
                //dd($e->getMessage());
            }
        }

        /*if($lastStatus['status'] == 'ENTREGADO') {

            $date = $lastStatus['date'];
            $date = Carbon::createFromFormat('d/m/Y H:i', $date);
            $date = $date->format('Y-m-d H:i:s');

            $history[] = [
                'status_id'  => 5,
                'obs'        => '',
                'city'       => $location,
                'created_at' => @$date,
                'weight'     => $weight
            ];
        }*/

        return $history;
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
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode)
    {
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
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode) {
        return false;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {

        $trk = str_pad($data['id'], 9, '0', STR_PAD_LEFT);
        $trk = $trk;

        $zipCode = explode('-', @$data['recipient_zip_code']);
        $zipCode4 = @$zipCode[0];
        $zipCode3 = @$zipCode[1];

        $fileRow = 'EXP;'; //Tipo de Línea
        $fileRow.= '01;';  //Código División
        $fileRow.= $this->fileRow($this->customerCode, 6, true);
        $fileRow.= $this->fileRow($trk, 9);
        $fileRow.= $this->fileRow(@$data['recipient_name'], 40);
        $fileRow.= $this->fileRow(@$data['recipient_address'], 40);
        $fileRow.= $this->fileRow(@$data['recipient_attn'], 40);
        $fileRow.= $this->fileRow(@$data['recipient_city'], 40);
        $fileRow.= $this->fileRow(@$data['recipient_country'], 2);
        $fileRow.= $this->fileRow($zipCode4, 5);
        $fileRow.= $this->fileRow($zipCode3, 3, true);
        $fileRow.= $this->fileRow(@$data['recipient_phone'], 15);
        $fileRow.= $this->fileRow(@$data['recipient_vat'], 15);
        $fileRow.= $this->fileRow('trk@trk.com', 100); //email
        $fileRow.= $this->fileRow(@$data['volumes'], 4, true);
        $fileRow.= $this->fileRow(@$data['volumes'], 4, true);
        $fileRow.= $this->fileRow(@$data['weight'], 8, true);
        $fileRow.= $this->fileRow(@$data['fator_m3'], 5, true);
        $fileRow.= $this->fileRow('', 8, true); //valor declarado seguro
        $fileRow.= $this->fileRow(@$data['charge_price'], 7, true);
        $fileRow.= $this->fileRow(@$data['obs'], 100);
        $fileRow.= 'N;';
        $fileRow.= $this->fileRow(@$data['cod'], 1);
        $fileRow.= $this->fileRow(@$data['date'], 15);
        $fileRow.= 'N;'; //plazo grantizado
        $fileRow.= $this->fileRow(@$data['recipient_code'], 15);
        $fileRow.= 'N;';
        $fileRow.= 'N;';

        $this->storeFTP($fileRow, $trk);

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            $request = $fileRow;
            file_put_contents (public_path().'/dumper/request.txt', $request);
            file_put_contents (public_path().'/dumper/response.txt', '');
        }

        return $trk;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeRecolha($data)
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

        $shipment = Shipment::where('webservice_method', 'integra2')
            ->where('provider_tracking_code', $trackingCode)
            ->first();


        $senderAgency = $this->getAgency('4000', $shipment->sender_country);

        if (empty($senderAgency)) {
            return false;
        }

        $recipientAgency = $this->getAgency($shipment->recipient_zip_code, $shipment->recipient_country);
        if (empty($recipientAgency)) {
            return false;
        }

        $mpdf = new Mpdf([
            'format'        => [100,150],
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_left'   => 0,
            'margin_right'  => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        for ($vol = 1; $vol <= $shipment->volumes ; $vol++) {

            $barcode = $this->getBarcode($shipment, $vol, @$senderAgency['agency_label_barcode']);

            $data = [
                'shipment'        => $shipment,
                'volume'          => $vol,
                'trackingCode'    => $trackingCode,
                'barcode'         => @$barcode,
                'senderAgency'    => $senderAgency,
                'recipientAgency' => $recipientAgency,
                'view'            => 'admin.printer.shipments.labels.label_integra2'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }


        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        return $mpdf->Output('Comprovativo de Envio.pdf', 'I'); //output to screen
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

        if(str_contains($trackingCode, ',') || strlen($trackingCode) == '9') { //so elimina recolhas
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

            if(@$response['codError']) {
                throw new \Exception($response['codError'] . ' - '. $response['mensError']);
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
    private function storeFTP($fileRow, $trk)
    {
        $filename = 'I2_CLIE'.$this->agencia.$this->customerCode.'FTP_'.$trk;
        $localFile  = public_path().'/dumper/'.$trk.'.csv';
        $remoteFile = '/tlog/'.$filename.'.csv';

        file_put_contents ($localFile, $fileRow);

        if(env('APP_ENV') != 'local') {
            // FTP access parameters
            $host = $this->ftpHost;
            $user = $this->ftpUser;
            $pass = $this->ftpPass;
            $port = $this->ftpPort;

            // connect to FTP server
            try {
                $connectionId = ftp_connect($host, $port);
            } catch (\Exception $e) {
                throw new \Exception('FTP ERROR: Cannot connect to host.');
            }

            // send access parameters
            try {
                $login = ftp_login($connectionId, $user, $pass);

                if (!$login) {
                    throw new \Exception('Cannot login via FTP');
                }
            } catch (\Exception $e) {
                throw new \Exception('FTP ERROR: Cannot login');
            }

            // perform file upload
            $upload = ftp_put($connectionId, $remoteFile, $localFile, FTP_BINARY);

            // check upload status:
            if (!$upload) {
                return false;
            }

            // close the FTP stream
            ftp_close($connectionId);

            File::delete($localFile);
        }

        return true;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        $data = self::getEstadoEnvioByTrk($shipment);

        if($data) {

            $webserviceFatorM3 = 0;
            $webserviceWeight  = 0;
            unset($data['weight'], $data['fator_m3']);

           /* $shipmentLinked = false;
            if($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }*/

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                if(empty($item['status_id'])) {
                    $item['status_id'] = 31; //aguarda expedição
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => @$item['obs'],
                    //'incidence_id' => @$item['incidence_id'],
                    'created_at'   => @$item['created_at'],
                    'status_id'    => @$item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            //update shipment price
            $weightChanged = ($webserviceWeight > $shipment->weight || $webserviceFatorM3);
            if((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || ($weightChanged && empty($shipment->invoice_id)
                    && empty($shipment->ignore_billing)
                    && empty($shipment->is_blocked))) {

                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                //$agencyId, $serviceId, $customerId, $providerId, $weight, $volumes, $charge = null, $volumeM3 = 0, $fatorM3 = 0, $zone = 'pt'
                $prices = Shipment::calcPrices($shipment);
                $oldPrice = $shipment->total_price;
                $shipment->fator_m3 = $webserviceFatorM3;
                $shipment->volumetric_weight  = @$prices['volumetricWeight'];
                $shipment->cost_price         = @$prices['cost'];
                $shipment->fuel_tax           = @$prices['fuelTax'];
                $shipment->extra_weight       = @$prices['extraKg'];

                if(!$shipment->price_fixed) {
                    $shipment->total_price = $prices['total'];
                    $shipment->fuel_tax    = $prices['fuel_tax'];
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

                //update linked shipment
                /*if($shipmentLinked) {
                    $shipmentLinked->weight = $webserviceWeight > $shipmentLinked->weight ? $webserviceWeight : $shipmentLinked->weight;

                    $tmpShipment = $shipmentLinked;
                    $tmpShipment->fator_m3 = $webserviceFatorM3;
                    $prices = Shipment::calcPrices($tmpShipment);

                    $shipmentLinked->fator_m3 = $webserviceFatorM3;
                    $shipmentLinked->volumetric_weight  = @$prices['volumetricWeight'];
                    $shipmentLinked->cost_price         = @$prices['cost'];
                    $shipmentLinked->fuel_tax           = @$prices['fuelTax'];
                    $shipmentLinked->extra_weight       = @$prices['extraKg'];

                    if(!$shipmentLinked->price_fixed) {
                        $shipmentLinked->total_price = @$prices['total'];
                        $shipmentLinked->fuel_tax    = @$prices['fuel_tax'];
                    }
                }*/
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
    public function saveShipment($shipment, $isCollection = false) {


        if(!in_array($shipment->recipient_country, ['pt', 'es'])) {
            throw new \Exception('Serviço indisponível para o país de destino.');
        }

        $date = str_replace('-', '', $shipment->date);

        $data = [
            "id"                 => $shipment->id,

            'sender_address' => $shipment->sender_address,
            'sender_zip_code' => $shipment->sender_zip_code,
            'sender_city' => $shipment->sender_city,
            'sender_country' => $shipment->sender_country,
            'sender_phone' => $shipment->sender_phone,

            "recipient_name"     => $shipment->recipient_name,
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_city"     => $shipment->recipient_city,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_attn"     => $shipment->recipient_attn,
            "recipient_phone"    => $shipment->recipient_phone,
            "recipient_country"  => strtoupper($shipment->recipient_country),

            "cod"           => $shipment->payment_at_recipient ? "D" : "P",
            "date"          => $date,
            "volumes"       => $shipment->volumes ? $shipment->volumes : '0',
            "weight"        => $shipment->weight ? str_replace('.', '', number($shipment->weight, 3)) : '0',
            "charge_price"  => $shipment->charge_price ? str_replace('.', '', number($shipment->charge_price, 2)): '0',
            "obs"           => $shipment->obs
        ];

        if(!$shipment->provider_tracking_code) {
            return $this->storeEnvio($data, $isCollection);
        }

        return $shipment->provider_tracking_code;
    }


    public function fileRow($value, $size, $numeric = false) {

        if($numeric) {
            return str_pad(substr($value, 0, $size), $size, '0', STR_PAD_LEFT).';';
        }

        return str_pad(substr($value, 0, $size), $size, ' ').';';
    }

    /**
     * Create barcode
     * @param $shipment
     * @param $volume
     * @return string
     */
    public function getBarcode($shipment, $volume, $senderAgencyCode) {

        //Código de Sector (a facilitar por Grupo Integra2)
        $barcode = '0';

        //Código de Producto / Línea de Negocio
        $barcode.= '1'; //AMBIPAQ

        //Código Postal (rellenado con ceros por la izquierda).
        $zipCode = explode('-', trim($shipment->recipient_zip_code));
        $zipCode = @$zipCode[0];
        $barcode.= str_pad($zipCode, 6, '0', STR_PAD_LEFT);

        //Código de Delegación de Origen
        $barcode.= $senderAgencyCode;

        //Código de Cliente
        $barcode.= str_pad($this->customerCode, 6, '0', STR_PAD_LEFT);

        //Referencia de Cliente (Nº de Albarán)
        $barcode.= str_pad($shipment->provider_tracking_code, 9, '0', STR_PAD_LEFT);

        //Nº de Orden del Bulto en la Expedición
        $barcode.= str_pad($volume, 3, '0', STR_PAD_LEFT);

        //Código de País de Destino
        $barcode.= $this->getCodigoPais($shipment->recipient_country);

        //Código de País de Origen
        $barcode.= $this->getCodigoPais($shipment->sender_country);

        //Código de País de Tránsito = Código de País de Destino
        $barcode.= $this->getCodigoPais($shipment->recipient_country);

        return $barcode;
    }

    public function getAgency($zipCode, $country) {

        $zipCode = explode('-', trim($zipCode));
        $zipCode = @$zipCode[0];

        try {
            $agency = DB::connection('mysql_core')
                ->table('integra2_agencies_zipcodes')
                ->join('integra2_agencies', 'integra2_agencies_zipcodes.agency', '=', 'integra2_agencies.agency_code')
                ->where('zip_code', $zipCode)
                ->where('country', $country)
                ->first();

            $agency = (array) $agency;

            if (empty($agency)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }


        return $agency;
    }

    public function getCodigoPais($recipientCountry) {

        if($recipientCountry == 'pt') {
            return '02';
        } elseif($recipientCountry == 'es') {
            return '01';
        } else {
            return '03';
        }
    }
}