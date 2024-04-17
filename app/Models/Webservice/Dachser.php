<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;
use Mpdf\Mpdf;

class Dachser extends \App\Models\Webservice\Base {

    /**
     * @var null
     */
    private $icc;

    /**
     * @var null
     */
    private $password;

    /**
     * @var null
     */
    private $conta;

    /**
     * @var null
     */
    private $debug = false;

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
            $this->icc        = '4345'; //
            $this->password   = '';
            $this->conta      = '';
        } else {
            $this->icc        = $user; //icc = numero remetente dachser
            $this->password   = $password;
            $this->conta      = $agencia;
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
    public function getEstadoEnvioByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode)
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
    public function storeEnvio($data, $isCollection)
    {

        $icc = $this->icc;
        $trk = str_pad($data['id'], 9, '0', STR_PAD_LEFT);
        $trk = $icc . $trk;

        $fileRow = 'TRS002';//RECTYPE
        $fileRow.= 'SCN_02'; //ESCENARIO
        $fileRow.= str_pad($trk, 25, ' '); //TRSID
        $fileRow.= $this->fileRow(@$data['date'], 8); //FECHA
        $fileRow.= $this->fileRow(@$data['reference'], 6); //EXPEDICION
        $fileRow.= $this->fileRow(@$data['reference'], 13); //NUMERO UNICO
        $fileRow.= $this->fileRow(@$data['sender_name'], 9); //RMT_CODIGO
        $fileRow.= $this->fileRow(@$data['sender_name'], 32);
        $fileRow.= $this->fileRow(@$data['sender_address'], 40);
        $fileRow.= $this->fileRow(@$data['sender_country'], 2);
        $fileRow.= $this->fileRow(@$data['sender_zip_code'], 12);
        $fileRow.= $this->fileRow(@$data['sender_vat'], 12);
        $fileRow.= $this->fileRow(@$data['sender_email'], 80);
        $fileRow.= $this->fileRow('', 15); //FAX
        $fileRow.= $this->fileRow(@$data['sender_phone'], 15);
        $fileRow.= $this->fileRow(@$data['sender_attn'], 40);
        $fileRow.= $this->fileRow(@$data['obs'], 40);

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

        $shipment = Shipment::where('webservice_method', 'dachser')
            ->where('provider_tracking_code', $trackingCode)
            ->first();

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

            $serviceName  = $this->getServiceName(@$shipment->service->code);
            $sourceAgency = $this->getAgency($shipment->sender_zip_code, $shipment->sender_country);
            $destAgency   = $this->getAgency($shipment->recipient_zip_code, $shipment->recipient_country);

            if(in_array($shipment->recipient_country, ['pt','es'])) {
                $zipCode      = substr($shipment->recipient_zip_code, -3);
                $volume       = str_pad($vol, 3, '0', STR_PAD_LEFT);
                $barcode      = $destAgency['code'] . $zipCode . $trackingCode . $volume;
                $barcodeLabel = $destAgency['code'] . '-' . $zipCode . '-' . $trackingCode .'-' . $volume;
            } else {

                $countryCode = $this->getBarcodeCountryCode($shipment->recipient_country);
                $volume      = str_pad($vol, 3, '0', STR_PAD_LEFT);

                $barcode      = $destAgency['code'] . '1' . $countryCode . $trackingCode . $volume;
                $barcodeLabel = $destAgency['code'] . '-1' . $countryCode . '-' . $trackingCode .'-' . $volume;


                $iln = $this->getIln($shipment->recipient_country);
                $trk = substr($shipment->id, 1, 6);
                $controlDigit = 2;

                $codigoSSCC      = '00'.substr($this->icc, 0, 1).$iln.substr($this->icc, 1, 4).$trk.$controlDigit;
                $codigoSSCCLabel = '(00) '. substr($this->icc, 0, 1). ' ' . $iln.' ' .substr($this->icc, 1, 4).' '.$trk. ' '.$controlDigit;


                $barcodeInternacional  = $codigoSSCC;
                $barcodeIntLabel       = $codigoSSCCLabel;
            }

            dd($barcodeInternacional);

            $data = [
                'shipment'        => $shipment,
                'volume'          => $vol,
                'trackingCode'    => $trackingCode,
                'barcode'         => @$barcode,
                'barcodeLabel'    => @$barcodeLabel,
                'barcodeInt'      => @$barcodeInternacional,
                'barcodeIntLabel' => @$barcodeIntLabel,
                'sourceAgencyCode'=> @$sourceAgency['code'],
                'sourceAgencyName'=> @$sourceAgency['name'],
                'destAgencyCode'  => @$destAgency['code'],
                'destAgencyName'  => @$destAgency['name'],
                'serviceName'     => @$serviceName,
                'view'            => 'admin.printer.shipments.labels.label_dachser'
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
        $localFile  = public_path().'/dumper/dachser_'.$trk.'.csv';
        $remoteFile = '/tlog/'.$trk.'.csv';

        file_put_contents ($localFile, $fileRow);

        /*// FTP access parameters
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

            if(!$login) {
                throw new \Exception('Cannot login via FTP');
            }
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }

        // perform file upload
        $upload = ftp_put($connectionId, $remoteFile, $localFile, FTP_BINARY);

        // check upload status:
        if(!$upload) {
            return false;
        }

        // close the FTP stream
        ftp_close($connectionId);

        File::delete($localFile);*/

        return $trk;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        if($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk(null, null, $shipment->provider_tracking_code);
        } else {
            $data = self::getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        }

        if($data) {

            $webserviceFatorM3 = $data['fator_m3'];
            $webserviceWeight  = $data['weight'];
            unset($data['weight'], $data['fator_m3']);

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                //$date = new Carbon($item['created_at']);

                if(empty($item['status_id'])) {
                    $item['status_id'] = 31; //aguarda expedição
                }
                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => @$item['obs'],
                    'incidence_id' => @$item['incidence_id'],
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
                $shipment->volumetric_weight  = $prices['volumetricWeight'];
                $shipment->cost_price  = $prices['cost'];

                if(!$shipment->price_fixed) {
                    $shipment->total_price  = $prices['total'];
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

                //update linked shipment
                /*if($shipmentLinked) {
                    $shipmentLinked->weight = $webserviceWeight > $shipmentLinked->weight ? $webserviceWeight : $shipmentLinked->weight;

                    $tmpShipment = $shipment;
                    $tmpShipment->fator_m3 = $webserviceFatorM3;
                    $prices = Shipment::calcPrices($tmpShipment);

                    $shipmentLinked->fator_m3 = $webserviceFatorM3;
                    $shipmentLinked->volumetric_weight  = $prices['volumetricWeight'];
                    $shipmentLinked->cost_price  = $prices['cost'];

                    if(!$shipmentLinked->price_fixed) {
                        $shipmentLinked->total_price  = @$prices['total'];
                        $shipmentLinked->fuel_tax     = @$prices['fuelTax'];
                        $shipmentLinked->extra_weight = @$prices['extraKg'];
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

        $reference = 'TRK'.$shipment->tracking_code;
        $reference.=  $shipment->reference ? ' - '.$shipment->reference : '';

        try {
            $services = config('shipments_export_mapping.dachser-services');
            $code = @$shipment->service->code;
            $code = '24H';
            $service = $services[$code];
        } catch (\Exception $e) {
            throw new Exception('O serviço '. $code .' não tem correspondência com nenhum serviço Correos Express.');
        }

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

        //return guide
        $returnGuide = 'N';
        if($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 'S';
        }


        $recRemota = 'N';
        if($shipment->sender_country != 'pt') {
            $recRemota = 'S';
        }

        //complementar services
        $systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $sabado = $returnGuide = 'N';
        if(!empty($shipmentComplementarServices)) {
            //check service sabado
            if(in_array('sabado', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)) {
                $sabado = 'S';
            }

            //return guide
            if(in_array('rguide', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['rguide'], $shipmentComplementarServices)) {
                $returnGuide = 'S';
            }
        }

        $date = explode('-', $shipment->date);
        $date = $date[0].$date[1].$date[2];


        $data = [
            "id"                 => $shipment->id,
            "sender_name"        => $shipment->sender_name,
            "sender_zip_code"    => $shipment->sender_zip_code,
            "sender_city"        => $shipment->sender_city,
            "sender_address"     => $shipment->sender_address,
            "sender_attn"        => $shipment->sender_attn,
            "sender_phone"       => $shipment->sender_phone,
            "sender_country"     => strtoupper($shipment->sender_country),

            "recipient_name"     => $shipment->recipient_name,
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_city"     => $shipment->recipient_city,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_attn"     => $shipment->recipient_attn,
            "recipient_phone"    => $shipment->recipient_phone,
            "recipient_country"  => strtoupper($shipment->recipient_country),

            "cod"           => $shipment->payment_at_recipient ? "D" : "P",
            "service_code"  => $service,
            "rguide"        => $returnGuide ? 'true' : 'false',
            "date"          => $date, //format ddmmyyyy
            "start_hour"    => $shipment->start_hour ? $shipment->start_hour : '08:00',
            "end_hour"      => $shipment->end_hour ? $shipment->end_hour : '19:00',
            "volumes"       => $shipment->volumes ? $shipment->volumes : '0',
            "weight"        => $shipment->weight ? $shipment->weight : '0',
            "charge_price"  => $shipment->charge_price ? $shipment->charge_price : '0',
            "reference"     => $reference,
            "obs"           => $shipment->obs,
            "obs2"          => $shipment->obs_delivery,
            "rec_remota"    => $recRemota,
            "sabado"        => $sabado,
        ];

        if(!$shipment->provider_tracking_code) {
            return $this->storeEnvio($data, $isCollection);
        }

        return $shipment->provider_tracking_code;
    }


    public function getAgency($zipCode, $country) {

        if(!in_array($country, ['pt', 'es'])) {
            return $this->getAgencyInternacional($zipCode, $country);
        }

        $plazasFile = storage_path('webservices/dachser/cp_nacional.txt');

        $file = fopen($plazasFile, "r");

        $destCountryAgencies = [];
        while(!feof($file)) {
            $line = trim(utf8_encode(fgets($file)));

            $countryFromFile = $this->getCountryCode(substr($line, 0, 2));

            if($countryFromFile == $country) {
                $destCountryAgencies[] = [
                    'country'       => $countryFromFile,
                    'from_zip'      => substr($line, 2, 5),
                    'to_zip'        => substr($line, 7, 5),
                    'agency_code'   => substr($line, 12, 3),
                    'agency_name'   => substr($line, 15),
                ];
            }
        }


        if($country == 'pt') {
            $zipCode = explode('-', $zipCode);
            $zipCode = '0'.$zipCode[0];
        }

        foreach ($destCountryAgencies as $row) {
            if(valueBetween($zipCode, $row['from_zip'], $row['to_zip'])) {
                return [
                    'code' => $row['agency_code'],
                    'name' => $row['agency_name'],
                ];
            }
        }

        return [
            'code' => '',
            'name' => ''
        ];
    }

    public function getAgencyInternacional($zipCode, $country) {
        $plazasFile = storage_path('webservices/dachser/cp_int.txt');

        $file = fopen($plazasFile, "r");


        $plataformaInt = 'P.I.COI';
        if($country == 'tr') {
            $plataformaInt = 'P.I.ABI';
        }


        $countryCode = $this->getCountryCode($country);

        dd($countryCode);
        $destCountryAgencies = [];
        while(!feof($file)) {
            $line = trim(utf8_encode(fgets($file)));
            $line = explode(';', $line);

            $countryFromFile = $this->getCountryCode($line[0]);

            if($countryFromFile == $country) {

                $destCountryAgencies[] = [
                    'country_code'  => $line[0],
                    'country'       => $countryFromFile,
                    'from_zip'      => $line[1],
                    'to_zip'        => $line[2],
                    'agency_code'   => $line[3],
                    'agency_name'   => $plataformaInt . ' - '.strtoupper($country),
                ];
            }
        }


        if($country == 'pt') {
            $zipCode = explode('-', $zipCode);
            $zipCode = '0'.$zipCode[0];
        }

        foreach ($destCountryAgencies as $row) {
            if(valueBetween($zipCode, $row['from_zip'], $row['to_zip'])) {
                return [
                    'code' => $row['agency_code'],
                    'name' => $row['agency_name'],
                    'country' => $row['country_code'],
                ];
            }
        }

        return [
            'code' => '',
            'name' => $plataformaInt,
            'country' => $countryCode
        ];
    }

    public function getCountryCode($code) {

        $codes = [
            '00' => 'es',
            '01' => 'pt',
            '02' => 'fr',
            '03' => 'ad',
            '04' => 'ma',
            '05' => 'is',
            '11' => 'de',
            '12' => 'at',
            '13' => 'be',
            '14' => 'dk',
            '15' => 'fi',
            '17' => 'nl',
            '18' => 'hu',
            '19' => 'ie',
            '20' => 'it',
            '21' => 'lu',
            '22' => 'no',
            '23' => 'gb',
            '24' => 'cz',
            '25' => 'se',
            '26' => 'ch',
            '27' => 'pl',
            '28' => 'sk',
            '29' => 'si',
            '30' => 'hr',
            '31' => 'ee',
            '32' => 'lv',
            '33' => 'li',
            '34' => 'ru',
            '35' => 'lt',
            '36' => 'us',
            '37' => 'gr',
            '38' => 'mc',
            '39' => 'ro',
            '40' => 'bg',
            '41' => 'tr',
            '47' => 'vn',
            '49' => 'rs',
            '52' => 'ba',
            '53' => 'mk',
        ];

        return @$codes[$code];
    }

    /**
     * Devolve o Código do país a usar no código de barras
     * @param $code
     * @return mixed
     */
    public function getBarcodeCountryCode($code) {

        $codes = [
            'es' => '50',
            'pt' => '51',
            'fr' => '06',
            'ad' => '52',
            'ma' => '53',
            'is' => '54',
            'de' => '01',
            'at' => '02',
            'be' => '03',
            'dk' => '04',
            'fu' => '05',
            'nl' => '07',
            'hu' => '08',
            'ie' => '09',
            'it' => '10',
            'lu' => '11',
            'no' => '12',
            'gb' => '13',
            'cz' => '14',
            'se' => '15',
            'ch' => '16',
            'pl' => '17',
            'sk' => '18',
            'si' => '19',
            'hr' => '20',
            'ee' => '21',
            'lv' => '22',
            'li' => '23',
            'ru' => '24',
            'lt' => '25',
            'us' => '26',
            'gr' => '27',
            'mc' => '28',
            'ro' => '29',
            'bg' => '30',
            'tr' => '31',
            'vn' => '37',
            'rs' => '39',
            'ba' => '42',
            'mk' => '43',
        ];

        return @$codes[$code];
    }

    public function getIln($code) {
        return '4048712';
    }

    public function getServiceName($code) {

        $config = config('shipments_export_mapping.dachser');
        $code   = @$config[$code];

        $codes = [
            '3'  => 'EuroEXPRESS',
            '5'  => 'NIGHT',
            '8'  => 'EuroPREMIUM',
            '13' => 'FIX',
            '14' => '10',
            '15' => '13',
            '34' => 'Targoflex',
            '35' => 'Targospeed',
            '36' => 'TFix',
        ];

        return @$codes[$code] ? @$codes[$code] : 'EXPRESS';
    }

    public function fileRow($value, $size) {
        return str_pad(substr($value, 0, $size), $size, ' ');
    }
}