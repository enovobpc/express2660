<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Date, File, Log, View, Setting;
use App\Models\ShipmentHistory;
use App\Models\WebserviceLog;
use App\Models\ZipCode;
use Carbon\Carbon;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use thm\tnt_ec\service\ShippingService\Activity;
use thm\tnt_ec\service\ShippingService\ShippingService;
use thm\tnt_ec\service\TrackingService\TrackingService;
use Mockery\Exception;

class TntExpress extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $labelUrl = 'https://express.tnt.com/expresslabel/documentation/getlabel';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/tnt/';

    /**
     * @var string
     */
    private $account;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($account, $user, $password, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        $this->account    = $account;
        $this->user       = $user;
        $this->password   = $password;

        /*$this->account    = '000110901';
        $this->user       = 'VELOZ_T';
        $this->password   = 'tnt12345';*/
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($codAgeCargo = null, $codAgeOri = null, $trakingCode) {

        $ts = new TrackingService($this->user, $this->password);

        // this will return more detailed response
        // for more details please read TNT documentation (link below)
        $ts->setLevelOfDetails()->setComplete()->setDestinationAddress()
            ->setOriginAddress()
            ->setPackage()
            ->setPod()
            ->setShipment();

        $ts->setMarketTypeInternational(); //para envios internacionais
        $ts->setLocale('PT');

        $response = $ts->searchByConsignment(array($trakingCode));

        $error = false;
        if($response->hasError() === true) {
            foreach($response->getErrors() as $error) {
                WebserviceLog::set('TNT', 'syncShipments', $error.' | Envio TNT '. $trakingCode, 'error');
            }
        }

        $statusHistory = [];
        try {
            foreach ($response->getConsignments() as $consignment) {

                // you can output entire Consignment object for testing purpose
                foreach ($consignment->getStatuses() as $status) {
                    $code = (array) $status->getStatusCode();

                    $date = (array) $status->getLocalEventDate();
                    $date = $date[0];

                    $hour = (array) $status->getLocalEventTime();
                    $hour = $hour[0];

                    $date = new \Jenssegers\Date\Date($date . ' ' . $hour.'00');
                    $date = $date->format('Y-m-d H:i:s');

                    $statusHistory[] = [
                        'status_code' => @$code[0],
                        'description' => $status->getStatusDescription(),
                        'created_at'  => $date,
                        'depot_code'  => $status->getDepotCode().' - '.$status->getDepotName(),
                    ];
                }
            }
        } catch (\Exception $e) {

        }

        return $statusHistory;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode) {
        return $this->getEstadoEnvioByTrk(null, null, $trakingCode);
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date) {}


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia) {}

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date) {}

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode) {}


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date) {

    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode) {}

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode) {}

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data) {
        throw new Exception('Não é possível criar recolhas via TNT.');
    }

    /**
     * Insere um envio
     *
     * @param array $data
     * @return string
     */
    public function storeEnvio($data)
    {

        $totalVolume = 0.001; // cubic meters
        if (!empty($data['dimensions'])) {
            $totalVolume = 0;
            foreach ($data['dimensions'] as $dimension) {
                $totalVolume += (($dimension['width'] * $dimension['height'] * $dimension['length']) * $dimension['qty']) / 1000000;
            }
        }

        $shipping = new ShippingService($this->user, $this->password);

        $shipping->setAccountNumber($this->account)
            ->autoActivity() // this will generate <ACTIVITY> element autmatically.
            ->setSender()
                ->setCompanyName($data['sender_name'])
                ->setAddressLine(substr($data['sender_address'], 0, 30))
                ->setAddressLine(substr($data['sender_address'], 30, 30))
                ->setAddressLine(substr($data['sender_address'], 60, 30))
                ->setCity($data['sender_city'])
                //->setProvince($data['sender_city'])
                ->setPostcode($data['sender_zip_code'])
                ->setCountry($data['sender_country'])
                //->setVat('')
                ->setContactName($data['sender_name'])
                ->setContactDialCode($data['sender_dialcode'])
                ->setContactPhone($data['sender_phone'])
                ->setContactEmail('geral@enovo.pt');

        $shipping->setCollection()
            ->useSenderAddress() // use same addres as is for sender
            ->setShipDate($data['date'])
            ->setPrefCollectTime('09:00', '20:00')
            //->setAltCollectionTime('12:00', '20:00')
            ->setCollectInstruction('');

        $c1 = $shipping->addConsignment()
            ->setConReference($data['trk'])
            ->setCustomerRef($data['reference'])
            ->setContype('N')
            ->setPaymentind('S')
            ->setItems($data['volumes'])
            ->setTotalWeight($data['weight'])
            ->setTotalVolume($totalVolume)
            ->setCurrency('EUR')
            ->setGoodsValue(1.00)
            //->setInsuranceValue(1.00)
            //->setInsuranceCurrency('EUR')
            ->setService($data['service'])
            //->addOption('PR')
            ->setDescription('')
            ->setDeliveryInstructions($data['obs']);

        $c1->setReceiver()
            ->setCompanyName($data['recipient_name'])
            ->setAddressLine(substr($data['recipient_address'], 0, 30))
            ->setAddressLine(substr($data['recipient_address'], 30, 30))
            ->setAddressLine(substr($data['recipient_address'], 60, 30))
            ->setCity($data['recipient_city'])
            //->setProvince($data['recipient_city'])
            ->setPostcode($data['recipient_zip_code'])
            ->setCountry($data['recipient_country'])
            ->setVat('999999999')
            ->setContactName($data['recipient_attn'])
            ->setContactDialCode($data['recipient_dialcode'])
            ->setContactPhone($data['recipient_phone'])
            ->setContactEmail('');

        $c1->setReceiverAsDelivery(); // make delivery address same as receiver

        if (empty($data['dimensions'])) {
            $c1->addPackage()
                ->setItems($data['volumes'])
                ->setDescription('Mercadoria diversa')
                ->setLength(0.10)
                ->setHeight(0.10)
                ->setWidth(0.10)
                ->setWeight($data['weight']);
        } else {
            foreach ($data['dimensions'] as $dimension) {
                $c1->addPackage()
                    ->setItems($dimension['qty'])
                    ->setDescription($dimension['description'])
                    ->setLength($dimension['length'] * 0.01)
                    ->setHeight($dimension['height'] * 0.01)
                    ->setWidth($dimension['width'] * 0.01)
                    ->setWeight($dimension['weight']);
            }
        }

        //$shipping->disableSSLVerify();

        //dd($shipping->getXmlContent());

        $response = $shipping->send();

        if($response->hasError() === true) {
            $errors = $response->getErrors();
            $message = $this->getError($errors);
            throw new Exception($message);

        } else {

            $result = $response->getResults();
            $secoundPos = array_slice($result, 1, 1);
            $trk = @$secoundPos[$data['trk']]['NUMBER'];

            // Get express label
            $matches = [];
            // Number part of tracking returned by Express Connect
            preg_match("/\d+/", $trk, $matches);

            $label = $this->getExpressLabel($data, $matches[0]);
            if (!empty($label)) {
                $folder = public_path() . '/uploads/labels/tnt/';
                if(!File::exists($folder)) {
                    File::makeDirectory($folder, 0755, true);
                }

                $html = $this->showExpressLabelAsHtml($label);
                if (empty($html)) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }

                $result = File::put(public_path() . $this->upload_directory . $trk . '_labels.txt', $html);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }
            }

            // get old label
            $label = $response->getLabel();
            if(!empty($label)) {

                $folder = public_path() . '/uploads/labels/tnt/';
                if(!File::exists($folder)) {
                    File::makeDirectory($folder, 0755, true);
                }

                $html = $this->showXMLasHTML($label);
                $result = File::put(public_path() . $this->upload_directory . $trk . '_labels_old.txt', $html);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }
            }

            // get manifest
            $manifest = $response->getManifest();
            if(!empty($manifest)) {
                $html = $this->showXMLasHTML($manifest);
                $result = File::put(public_path() . $this->upload_directory . $trk . '_manifest.txt', $html);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }
            }

            // get consignment note
            $note = $response->getConsignmentNote();
            if(!empty($note)) {
                $html = $this->showXMLasHTML($note);
                $result = File::put(public_path() . $this->upload_directory . $trk . '_consignment_note.txt', $html);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
                }
            }

            // get invoice
            $invoice = $response->getInvoice();
            if(!empty($invoice)) {
                $html = $this->showXMLasHTML($invoice);
                $result = File::put(public_path().$this->upload_directory . $trk.'_invoice.txt', $html);
                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a capa do envio.');
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
     * @return void|string
     */
    public function getEtiqueta($senderAgency, $trackingCode, $outputFormat = null, $shipmentTRK = null, $totalVolumes = null) {

        $labelsExtension = "_labels.txt";

        $request = request();
        if ($request && $request->get('old', false)) {
            $labelsExtension = "_labels_old.txt";
        }

        $file = File::get(public_path(). $this->upload_directory . $trackingCode. $labelsExtension);
        $file = str_replace('�', ' ', $file);

        if (request()->get('old')) {
            echo $file;
            exit;
        }

        $mpdf = new Mpdf([
            'orientation'   => 'P',
            'format'        => [87, 135],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->WriteHTML(File::get(public_path('/assets/admin/css/tnt/label-rendering-style.css')), HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(File::get(public_path('/assets/admin/css/tnt/label-rendering-style-fr.css')), HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(File::get(public_path('/assets/admin/css/tnt/label-rendering-style-it.css')), HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(File::get(public_path('/assets/admin/css/tnt/label-rendering-exception-style.css')), HTMLParserMode::HEADER_CSS);
        $file = str_replace('<div xmlns="" id="box">', '<div xmlns="" id="box"></div>', $file);
        $mpdf->WriteHTML($file, HTMLParserMode::HTML_BODY);

        $pdf = $mpdf->Output('Etiquetas.pdf', 'S');
        $pdf = base64_encode($pdf);
        return $pdf; //string
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
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null) {}

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

        if($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code);
        } else {
            $data = self::getEstadoEnvioByTrk(
                $shipment->provider_cargo_agency,
                $shipment->provider_sender_agency,
                $shipment->provider_tracking_code);
        }

        if($data) {

            $data = array_reverse($data);

            foreach ($data as $item) {

                $fedexStatus = config('shipments_import_mapping.tnt-express-status');
                $item['status_id'] = @$fedexStatus[$item['status_code']];
                $item['created_at'] = new Date($item['created_at']);

                if(empty($item['status_id'])) {
                    throw new \Exception('Estado com o código '. $item['status_code'] . ' sem mapeamento.');
                }

                if($item['status_id'] == '9') {

                    $fedexIncidences = config('shipments_import_mapping.tnt-express-incidences');

                    $incidenceId = @$fedexIncidences[$item['status_id']];
                    if($incidenceId) {
                        $item['incidence_id'] = $incidenceId;
                    }
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'created_at'  => $item['created_at'],
                    'status_id'   => $item['status_id']
                ]);

                $history->fill($item);
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

        $service = $this->getProviderService($shipment);

        $reference =  $shipment->reference ? ' - '.$shipment->reference : '';

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        $senderZipCode    = (strlen($shipment->sender_zip_code) == 4 && strtoupper($shipment->recipient_country) == 'PT') ? $shipment->sender_zip_code.'-000' : $shipment->sender_zip_code;
        $recipientZipCode = (strlen($shipment->recipient_zip_code) == 4 && strtoupper($shipment->recipient_country) == 'PT') ? $shipment->recipient_zip_code.'-000' : $shipment->recipient_zip_code;

        $date = new \Jenssegers\Date\Date($shipment->date);

        $shipmentWeight = $shipment->weight;
        if(config('app.source') == 'ship2u') {
               $shipmentWeight = '0.1';
        }

        $senderDialCode     = substr(ZipCode::getDialCode($shipment->sender_country), 1);
        $recipientDialCode  = substr(ZipCode::getDialCode($shipment->recipient_country), 1);

        $data = [
            "trk"                => 'TRK' . $shipment->tracking_code,
            "date"               => $date->format('d/m/Y'),
            "service"            => $service,
            "volumes"            => $shipment->volumes,
            "weight"             => $shipmentWeight,
            "charge_price"       => $shipment->charge_price ? forceDecimal($shipment->charge_price) : 0,
            "sender_name"        => $shipment->sender_name,
            "sender_address"     => $shipment->sender_address,
            "sender_city"        => removeAccents($shipment->sender_city),
            "sender_country"     => strtoupper($shipment->sender_country),
            "sender_zip_code"    => $senderZipCode,
            "sender_phone"       => $shipment->sender_phone ? $shipment->sender_phone : '910000000',
            "sender_dialcode"    => $senderDialCode,
            "recipient_attn"     => $shipment->recipient_attn ? $shipment->recipient_attn : '--',
            "recipient_name"     => $shipment->recipient_name,
            "recipient_address"  => $shipment->recipient_address,
            "recipient_city"     => removeAccents($shipment->recipient_city),
            "recipient_zip_code" => $recipientZipCode,
            "recipient_country"  => strtoupper($shipment->recipient_country),
            "recipient_dialcode" => $recipientDialCode,
            "recipient_phone"    => $shipment->recipient_phone ? $shipment->recipient_phone : '910000000',
            "obs"                => $shipment->obs,
            "reference"          => 'TRK'.$shipment->tracking_code . $reference,
            "dimensions"         => @$shipment->pack_dimensions->toArray() ?? []
        ];

        return $this->storeEnvio($data);
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {
        throw new \Exception('Método indisponível');
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
     * Return Error message
     * @param $errors
     */
    public function getError($errors) {

        $errorCode = @$errors[0];

        $errorsList = [
            '229' => 'A data do envio não pode ser anterior à data de hoje.',
            '527' => 'Não foi encontrado nenhuma correspondencia entre o código postal e a localidade indicada no remetente.',
            '528' => 'Existem vários resultados para o Código Postal e Localidade indicados no remetente. Experimente colocar o código postal no formato XXXX-XXX',
            '534' => 'A combinação do código postal e da cidade nos dados do remetente é inválida',
            '546' => 'O código postal do remetente é inválido ou não foi encontrado na base de dados da TNT',
            '692' => 'Se a data de recolha é hoje, o horário de fim da recolha deve ser maior que agora (pelo menos + 1 hora)',
            '247' => 'Já não é possível fazer hoje a recolha. Altere a data do envio para o dia útil seguinte.',
            '555' => 'A localidade de destino não foi encontrada. Verifique a ortografia.'
        ];


        if(isset($errorsList[$errorCode])) {
            $message =  $errorCode . ' - ' . $errorsList[$errorCode];
        } else {
            $message = @$errors[0] . ' - ' . @$errors[1];
        }

        if(@$errors[2]) {
            $message.= ' - ' . $errors[2];
        }

        return $message;
    }

    public function httpPost($strRequest, $userId, $password) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->labelUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $userPass = "";
        if ((trim($userId)!="") && (trim($password)!="")) {
            $userPass = $userId.":".$password;
            curl_setopt($ch, CURLOPT_USERPWD, $userPass);
        }

        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequest);

        $isSecure = strpos($this->labelUrl, "https://");

        if ($isSecure===0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $result = curl_exec($ch);

        $errorCode = curl_errno($ch);
        $errorMessage = curl_error($ch);

        if($errorCode || $errorMessage) {
            throw new \Exception($errorCode . ' - ' . $errorMessage);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * Convert XML to HTML
     *
     * @param $xmlReceived
     * @return string
     */
    public function showXMLasHTML($xmlReceived){

        $dom = new \DOMDocument();
        $dom->loadXML($xmlReceived);
        $xpath = new \DOMXPath($dom);
        $styleLocation = $xpath->evaluate('string(//processing-instruction()[name() = "xml-stylesheet"])');
        $last = explode("\"", $styleLocation, 3);
        $xslLocation = $last[1];

        $xsl_received = utf8_encode(file_get_contents($xslLocation));

        $xslDoc = new \DOMDocument();
        $xslDoc->loadXML($xsl_received);

        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML(utf8_encode($xmlReceived));

        $proc = new \XSLTProcessor();
        $proc->importStylesheet($xslDoc);
        $resultHtml = $proc->transformToXML($xmlDoc);

        $resultHtml = str_replace('%5C', '/', $resultHtml);
        $html = '<div style="font-family: Arial, Helvetica, sans-serif;">' . utf8_encode($resultHtml) . '</div>';

        return $html;
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment) {

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
            if($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if(!$providerService) {
                $mapping = config('shipments_export_mapping.tnt-express-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->display_code . ' não tem correspondência com nenhum serviço TNT Express.');
        }

        return $providerService;
    }

    /**
     * Get label using Express Label
     * 
     * @param array $data
     * @param string $conNumber
     * @return string
     */
    public function getExpressLabel($data, $conNumber) {
        $date = Carbon::createFromFormat('d/m/Y', $data['date']);
        $formattedDate = $date->format('Y-m-d');
        $time = !empty($data['start_hour']) ? ($data['start_hour'] . ':00') : '09:00:00';

        $serviceCode = $this->convertLegacyServiceCode($data['service']);
        if (!$serviceCode) {
            return '';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <labelRequest>
            <consignment key="CON1">
                <consignmentIdentity>
                    <consignmentNumber><![CDATA['. $conNumber .']]></consignmentNumber>
                    <customerReference><![CDATA['. $data['trk'] .']]></customerReference>
                </consignmentIdentity>
                <collectionDateTime><![CDATA['. $formattedDate . 'T' . $time .']]></collectionDateTime>
                <sender>
                    <name><![CDATA['. substr($data['sender_name'], 0, 40) .']]></name>
                    <addressLine1><![CDATA['. substr($data['sender_address'], 0, 30) .']]></addressLine1>
                    <addressLine2><![CDATA['. substr($data['sender_address'], 30, 30) .']]></addressLine2>
                    <addressLine3><![CDATA['. substr($data['sender_address'], 60, 30) .']]></addressLine3>
                    <town><![CDATA['. substr($data['sender_city'], 40) .']]></town>
                    <postcode><![CDATA['. $data['sender_zip_code'] .']]></postcode>
                    <country><![CDATA['. $data['sender_country'] .']]></country>
                </sender>
                <delivery>
                    <name><![CDATA['. substr($data['recipient_name'], 0, 40) .']]></name>
                    <addressLine1><![CDATA['. substr($data['recipient_address'], 0, 30) .']]></addressLine1>
                    <addressLine2><![CDATA['. substr($data['recipient_address'], 30, 30) .']]></addressLine2>
                    <addressLine3><![CDATA['. substr($data['recipient_address'], 60, 30) .']]></addressLine3>
                    <town><![CDATA['. substr($data['recipient_city'], 0, 40) .']]></town>
                    <postcode><![CDATA['. $data['recipient_zip_code'] .']]></postcode>
                    <country><![CDATA['. $data['recipient_country'] .']]></country>
                </delivery>';

        $xml .= '<product>
                    <lineOfBusiness><![CDATA['. $serviceCode['line'] .']]></lineOfBusiness>
                    <groupId><![CDATA[0]]></groupId>
                    <subGroupId><![CDATA[0]]></subGroupId>
                    <id><![CDATA['. $serviceCode['service'] .']]></id>
                    <type><![CDATA['. $serviceCode['type'] .']]></type>';

        foreach ($serviceCode['options'] as $option) {
            $xml .= '<option>'. $option .'</option>';
        }

        $xml .= '</product>
                <account>
                    <accountNumber><![CDATA['. $this->account .']]></accountNumber>
                    <accountCountry><![CDATA['. strtoupper(Setting::get('app_country') ?? 'PT') .']]></accountCountry>
                </account>
                <specialInstructions><![CDATA['. $data['obs'] .']]></specialInstructions>
                <cashAmount><![CDATA['. $data['charge_price'] .']]></cashAmount>
                <totalNumberOfPieces><![CDATA['. $data['volumes'] .']]></totalNumberOfPieces>';

        if (empty($data['dimensions'])) {
            $xml .= '<pieceLine>
                        <identifier><![CDATA[1]]></identifier>
                        <goodsDescription><![CDATA[Mercadoria diversa]]></goodsDescription>
                        <pieceMeasurements>
                            <length><![CDATA[0.10]]></length>
                            <width><![CDATA[0.10]]></width>
                            <height><![CDATA[0.10]]></height>
                            <weight><![CDATA['. $data['weight'] .']]></weight>
                        </pieceMeasurements>';

            for ($i = 1; $i <= $data['volumes']; $i++) {
                $xml .= '<pieces>
                    <sequenceNumbers><![CDATA['. $i .']]></sequenceNumbers>
                    <pieceReference><![CDATA[]]></pieceReference>
                </pieces>';
            }

            $xml .= '</pieceLine>';
        } else {
            $id = $sequenceNumber = 1;
            foreach ($data['dimensions'] as $dimension) {
                $xml .= '<pieceLine>
                        <identifier><![CDATA['. $id .']]></identifier>
                        <goodsDescription><![CDATA['. $dimension['description'] .']]></goodsDescription>
                        <pieceMeasurements>
                            <length><![CDATA['. $dimension['length'] * 0.01 .']]></length>
                            <width><![CDATA['. $dimension['width'] * 0.01 .']]></width>
                            <height><![CDATA['. $dimension['height'] * 0.01 .']]></height>
                            <weight><![CDATA['. $dimension['weight'] .']]></weight>
                        </pieceMeasurements>';

                for ($i = 0; $i < $dimension['qty']; $i++) {
                    $xml .= '<pieces>
                        <sequenceNumbers><![CDATA['. $sequenceNumber .']]></sequenceNumbers>
                        <pieceReference><![CDATA[]]></pieceReference>
                    </pieces>';

                    $sequenceNumber++;
                }

                $xml .= '</pieceLine>';

                $id++;
            }
        }

        $xml .= '</consignment>
            </labelRequest>';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://express.tnt.com/expresslabel/documentation/getlabel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic '. base64_encode($this->user . ':' . $this->password)
            ],
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);
        $response   = curl_exec($curl);
        $error      = curl_error($curl);
        curl_close($curl);

        return $response;
    }

    public function showExpressLabelAsHtml($xmlReceived) {
        try {
            $xsl_received = utf8_encode(file_get_contents(public_path('assets/admin/xsl/tnt/HTMLRoutingLabelRenderer.xsl')));

            $xslDoc = new \DOMDocument();
            $xslDoc->loadXML($xsl_received);
    
            $xmlDoc = new \DOMDocument();
            $xmlDoc->loadXML(utf8_encode($xmlReceived));
    
            $proc = new \XSLTProcessor();
            $proc->importStylesheet($xslDoc);
            $resultHtml = $proc->transformToXML($xmlDoc);
    
            $resultHtml = str_replace('%5C', '/', $resultHtml);
            $html = utf8_encode($resultHtml);
    
            return $html;
        } catch (Exception $e) {
            Log::error($e);
            dd($xmlReceived);
        }

        return null;
    }

    /**
     * Retorna tempos transito
     */
    public function calcTransitTime($params = null) {

        $service = $this->convertLegacyServiceCode($params['service']);

        $curl = curl_init();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <priceRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <appId>PC</appId>
                <appVersion>3.2</appVersion>
                <priceCheck>
                    <rateId>rate1</rateId>
                    <sender>
                        <country>'. strtoupper($params['sender_country']) .'</country>
                        <town>'. $params['sender_city'] .'</town>
                        <postcode>'.$params['sender_zip_code'].'</postcode>
                    </sender>
                    <delivery>
                        <country>'. strtoupper($params['recipient_country']) .'</country>
                        <town>'. $params['recipient_city'] .'</town>
                        <postcode>'.$params['recipient_zip_code'].'</postcode>
                    </delivery>
                    <collectionDateTime>'.$params['pickup_date'].'T'. date('H:i') .':00</collectionDateTime>
                    <product>
                        <id>'. $params['service'] .'</id>
                        <type>'. (str_contains($params['service'], 'D') ? 'D' : 'N') .'</type>
                    </product>
                    <currency>EUR</currency>
                    <priceBreakDown>true</priceBreakDown>
                    <consignmentDetails>
                        <totalWeight>1</totalWeight>
                        <totalVolume>1</totalVolume>
                        <totalNumberOfPieces>1</totalNumberOfPieces>
                    </consignmentDetails>
                    <pieceLine>
                        <numberOfPieces>1</numberOfPieces>
                        <pieceMeasurements>
                            <length>1</length>
                            <width>1</width>
                            <height>1</height>
                            <weight>1</weight>
                        </pieceMeasurements>
                        <pallet>1</pallet>
                    </pieceLine>
                </priceCheck>
            </priceRequest>';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://express.tnt.com/expressconnect/pricing/getprice',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.base64_encode($this->user.':'.$this->password),
                'Content-Type: text/plain'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);

        curl_close($curl);

        $data = [];

        $xml = simplexml_load_string($response);

        if(@$xml->errors && !str_contains(@$xml->errors->brokenRule->description, 'Standard Rates')) {
            throw new \Exception(@$xml->errors->brokenRule->description);
        } else {
            foreach ($xml->priceResponse->ratedServices as $value) {
                $data["TotalPrice"] = (string)$value->ratedService->totalPrice;
                $data["ArrivalDateTime"] = (string)$value->ratedService->estimatedTimeOfArrival;
                $data["PickupStartDateTime"] = (string)$value->ratedService->serviceCallCutOffTimes->icaStartDateAndTime;
                $data["PickupEndDateTime"] = (string)$value->ratedService->serviceCallCutOffTimes->icaEndDateAndTime;
            }

            $date = new Date($data['ArrivalDateTime']);
            $date = $date->format('Y-m-d');
        }

        return [
            'delivery_date' => @$date,
            'transit_days'  => 0
        ];

    }

    /**
     * Convert legacy TNT code to new used in express label
     * 
     * @param string $code
     * @return array|null
     */
    protected function convertLegacyServiceCode($code) {
        $expressLabelCodes = [
            '09D' => [
                'line'      => 2,
                'service'   => 'EX09',
                'type'      => 'D',
                'options'   => []
            ],
            '09N' => [
                'line'      => 2,
                'service'   => 'EX09',
                'type'      => 'N',
                'options'   => []
            ],
            '10D' => [
                'line'      => 2,
                'service'   => 'EX10',
                'type'      => 'D',
                'options'   => []
            ],
            '10N' => [
                'line'      => 2,
                'service'   => 'EX10',
                'type'      => 'N',
                'options'   => []
            ],
            '12D' => [
                'line'      => 2,
                'service'   => 'EX12',
                'type'      => 'D',
                'options'   => []
            ],
            '12N' => [
                'line'      => 2,
                'service'   => 'EX12',
                'type'      => 'N',
                'options'   => []
            ],
            '15D' => [
                'line'      => 2,
                'service'   => 'EX',
                'type'      => 'D',
                'options'   => []
            ],
            '15N' => [
                'line'      => 2,
                'service'   => 'EX',
                'type'      => 'N',
                'options'   => []
            ],
            '412' => [
                'line'      => 2,
                'service'   => 'EC12',
                'type'      => 'N',
                'options'   => []
            ],
            '48N' => [
                'line'      => 2,
                'service'   => 'EC',
                'type'      => 'N',
                'options'   => []
            ],
            '09' => [
                'line'      => 1,
                'service'   => 'EX09',
                'type'      => 'N',
                'options'   => []
            ],
            '10' => [
                'line'      => 1,
                'service'   => 'EX10',
                'type'      => 'N',
                'options'   => []
            ],
            '12' => [
                'line'      => 1,
                'service'   => 'EX12',
                'type'      => 'N',
                'options'   => []
            ],
            '15' => [
                'line'      => 1,
                'service'   => 'EX',
                'type'      => 'N',
                'options'   => []
            ],
        ];

        return $expressLabelCodes[$code] ?? null;
    }
}