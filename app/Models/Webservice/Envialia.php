<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\WebserviceLog;
use Date, File, Imagick, View, Setting;
use Exception;
use Mpdf\Mpdf;
use App\Models\ShipmentHistory;

class Envialia extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url = 'http://ws.envialia-urgente.com/soap';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/envialia/';

    /**
     * @var string
     */
    private $agencia;

    /**
     * @var string
     */
    private $cliente;

    /**
     * @var string
     */
    private $password;

    /**
     * @var null
     */
    private $session_id = null;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * Envialia constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($agencia, $cliente, $password, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        $this->agencia  = $agencia;
        $this->cliente  = $cliente;
        $this->password = $password;
        $this->debug    = $debug;
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
        $parameters = [];
        $parameters['strCodAgeCargo'] = $codAgeCargo;
        $parameters['strCodAgeOri']   = $codAgeOri;
        $parameters['strAlbaran']     = $trakingCode;

        if ($result = $this->call('WebServService', 'ConsEnvEstados', $parameters)) {
            $status = $this->mappingResult($result['ENV_ESTADOS'], 'status');

            foreach($status as $key => $item) {

                $providerAgency = $item['provider_agency_code'];
                $providerUser   = $item['provider_user_id'];

                if(empty($providerAgency) && !empty($providerUser)) {
                    if(str_contains($providerUser, '#')) {
                        $item['provider_agency_code'] = str_replace('#', '00', $providerUser);
                    } elseif(str_contains($providerUser, 'E')) {
                        $item['provider_agency_code'] = str_replace('E', '00', $providerUser);
                    }
                }

                $status[$key] = $item + ['status_name' => $this->getCode($item['status'])];
            }

            return $status;
        }
        return $result;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode)
    {
        $data['strCodRec'] = $trakingCode;

        if ($result = $this->call('WebServService', 'ConsRecEstados', $data)) {
            $status = $this->mappingResult($result['REC_ESTADOS'], 'status');

            foreach($status as $key => $item) {
                $status[$key] = $item + ['status_name' => $this->getCode($item['status'])];
            }
            return $status;
        }
        return $result;
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
        $searchTrackings = explode(',', $trks);

        $trks = [];
        foreach ($searchTrackings as $trk) {
            $trks[] = ['Cod_Objecto'=> $trk];
        }

        $data['strCodRec'] = $trakingCode;

        if ($result = $this->call('WebServService', 'ConsEstadoEnvioMasivo', $data)) {
            $status = $this->mappingResult($result['REC_ESTADOS'], 'status');

            foreach($status as $key => $item) {
                $status[$key] = $item + ['status_name' => $this->getCode($item['status'])];
            }
            return $status;
        }
        return $result;
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date)
    {
        $parameters = [];
        $parameters['dtFecha'] = $date;

        if ($result = $this->call('WebServService', 'ConsEnvEstadosFecha', $parameters)) {
            return $this->mappingResult($result['ENV_ESTADOS'], 'status');
        }
        return $result;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
        $parameters = [];
        $parameters['strRef'] = $referencia;

        if ($result = $this->call('WebServService', 'ConsEnvEstadosRef', $parameters)) {
            return $this->parseEnvios($result);
        }

        return $result;
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date)
    {
        $parameters = [];
        $parameters['dtFecha'] = $date;
        if ($result = $this->call('WebServService', 'ConsEnvIncidenciasFecha', $parameters)) {
            return $this->mappingResult($result['ENV_INCIDENCIAS'], 'incidencias');
        }
        return $result;
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        $parameters = [];
        $parameters['strCodAgeCargo'] = $codAgeCargo;
        $parameters['strCodAgeOri']   = $codAgeOri;
        $parameters['strAlbaran']     = $trakingCode;

    /*    $parameters['strCodAgeCargo'] = '005246';
        $parameters['strCodAgeOri']   = '005246';
        $parameters['strAlbaran']     = '0109391409';*/

        if ($result = $this->call('WebServService', 'ConsEnvIncidencias', $parameters)) {
            return $this->mappingResult($result['ENV_INCIDENCIAS'], 'incidencias');
        }
        return $result;
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
        $parameters = [];
        $parameters['dtFecha'] = $date;

        $result = $this->call('WebServService', 'InfEnvios', $parameters);
        if ($result) {
            $result = $this->mappingResult($result['INF_ENVIOS'], 'shipment');

            return $result;
        }

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
        $parameters = [];
        $parameters['strCodAgeCargo'] = $codAgeCargo;
        $parameters['strCodAgeOri']   = $codAgeOri;
        $parameters['strAlbaran']     = $trakingCode;

        if ($result = $this->call('WebServService', 'ConsEnvio', $parameters)) {
            $shipment = $this->mappingResult($result['ENVIOS'], 'shipment');
            $shipment = $shipment[0];

            $shipment['volumetric_weight'] = ($shipment['height'] * $shipment['width'] * $shipment['length']) / 1000000;
            return $shipment;
        }
        return $result;
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
        $parameters = [];
        $parameters['strCod'] = $trakingCode;

        if ($result = $this->call('WebServService', 'ConsRecogida', $parameters)) {
            $shipment = $this->mappingResult($result['RECOGIDAS'], 'collection');
            $shipment = $shipment[0];
            $shipment['is_collection'] = 1;
            $shipment['date'] = Date::createFromFormat('m/d/Y H:i:s', $shipment['date'])->toDateTimeString();
            return $shipment;
        }
        return $result;
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
        if ($result = $this->execute('WebServService', 'GrabaRecogida2', $data, true)) {
            if(isset($result['strCodOut'])) {
                $result = $result['strCodOut'];
            }
        }
        return $result;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $exists = !empty(@$data['strAlbaran']);

        //elimina etiqueta se edição do envio e se a etiqueta estiver arquivada
        try {
            if ($exists) {
                $filepath = public_path() . $this->upload_directory . @$data['strAlbaran'] . '_labels.pdf';
                File::delete($filepath);
            }
        } catch (\Exception $e) {}

        $result = $this->execute('WebServService', 'GrabaEnvio7', $data);

        $trk = false;
        if ($result) {
            if(isset($result['strAlbaranOut'])) {

                $senderAgency = $result['strCodAgeOriOut'];
                $trk = $result['strAlbaranOut'];

                //DOWNLOAD DA ETIQUETA
                /*try {
                    if(!$exists && $data['intBul'] <= 20) {
                        $this->downloadLabel($senderAgency, $trk);
                    }
                } catch (\Exception $e) {}*/
            }
        }

        return $trk;
    }


    /**
     * Permite gravar uma incidencia
     * @param $data
     * @return type
     * @throws \Exception
     */
    public function storeIncidenciaResolution($data, $isCollection)
    {

        if($isCollection) {
            $method = 'GrabaRecIncActuacionLibre';
        } else {
            $method = 'GrabaEnvIncActuacionLibre';
            if(@$data['strCodTipoActu'] != '9999') {
                $method = 'GrabaEnvIncActuacion';
            }
        }

        try {
            return $this->execute('WebServService', $method, $data, false);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
    public function getEtiqueta($senderAgency, $trackingCode, $outputFormat = 'I')
    {
        $filepath = public_path() . $this->upload_directory . $trackingCode . '_labels.pdf';

        if(File::exists($filepath)) {
            return base64_encode(file_get_contents($filepath));
        } else {
            return $this->downloadLabel($senderAgency, $trackingCode);
        }

        return $result;
    }

    /**
     * Download and store envialia label
     *
     * @param $senderAgency
     * @param $trackingCode
     * @return bool
     * @throws \Exception
     */
    public function downloadLabel($senderAgency, $trackingCode, $save = true) {

        $folder = public_path() . $this->upload_directory;
        if(!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $data = [
            'strCodAgeOri' => $senderAgency,
            'strAlbaran'   => $trackingCode,
            'boPaginaA4'   => 0,
            'intNumEtiqImpresasA4' => 1
        ];

        $result = $this->execute('WebServService', 'ConsEtiquetaEnvio5', $data);
        $label  = @$result['strEtiqueta'];

        if ($label) {

            $label = $this->cropLabel($trackingCode, $label);

            if($save) {
                $result = File::put($folder . $trackingCode . '_labels.pdf', $label);

                if ($result === false) {
                    throw new \Exception('Não foi possível gravar a etiqueta.');
                }
            }

            return base64_encode($label);
        }

        return false;
    }

    /**
     * Crop envialia Label
     *
     * @param $label
     * @return string
     * @throws \ImagickException
     * @throws \Mpdf\MpdfException
     */
    public function cropLabel($trk, $label) {

        $customLogo = env('ENVIALIA_CUSTOM_LABEL', false);

        if (!class_exists('Imagick')) {
            throw new Exception('A aplicação requer o módulo imagick ativo.');
        }

        $im = new Imagick();
        $im->setResolution(300, 300);
        $im->readImageBlob(base64_decode($label));

        $tempImages = [];
        foreach ($im as $key => $imagick) {
            $imagick->setImageFormat('png');

            if ($customLogo) {
                $imagick->cropImage(5000, 1030, 0, 90);
            } else {
                $imagick->cropImage(5000, 1210, 0, 50);
            }

            $filebase64 = base64_encode($imagick->getImageBlob());
            $tempImages[] = $filebase64;
        }

        $im->clear();
        $im->destroy();

        /**
         * Create PDF
         */
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100, 145],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;

        $url = env('APP_URL');
        $url = str_replace('https://', 'www.', $url);

        $data['view']         = 'admin.shipments.pdf.label';
        $data['trackingCode'] = $trk;
        $data['customLogo']   = $customLogo;
        $data['url']          = $url;

        foreach ($tempImages as $path) {
            $data['path'] = $path;
            $mpdf->WriteHTML(View::make('admin.printer.shipments.layouts.adhesive_labels_envialia', $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        $b64Doc = $mpdf->Output('Etiquetas.pdf', 'S'); //return pdf base64 string
        return $b64Doc;
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
        $parameters = [];
        $parameters['strCodAgeCargo'] = $codAgeCargo;
        $parameters['strCodAgeOri']   = $codAgeOri;
        $parameters['strAlbaran']     = $trakingCode;

        try {
            if ($result = $this->execute('WebServService', 'ConsEnvPODDig2', $parameters)) {
                $url = $result['strURLEnvPODDig'];

                if ($url) {
                    $url = str_replace('amp;', '', $url);
                }

                return $url;
            }

            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null)
    {
        $parameters = [];
        $parameters['dtFecha'] = $date;

        if ($result = $this->call('WebServService', 'InfEnvEstPOD', $parameters)) {
            $result = $result['ENVIO'];

            if(!isset($result[0])) {
                $result = [0 => $result];
            }

            $data = [];
            foreach($result as $item) {

                if(is_null($tracking)) {
                    $data[] = [
                        'ENV' => $this->mappingResult($item['ENV'], 'shipment')[0],
                        'POD' => $this->mappingResult($item['PODENV'], 'pod')[0]
                    ];
                } else {

                    if(isset($item['ENV']['@attributes']['V_ALBARAN']) && $item['ENV']['@attributes']['V_ALBARAN'] == $tracking) {
                        $data = [
                            'ENV' => $this->mappingResult($item['ENV'], 'shipment')[0],
                            'POD' => $this->mappingResult($item['PODENV'], 'pod')[0]
                        ];
                    }
                }
            }
            return $data;
        }

        return $result;
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * @param $results
     * @return array
     */
    private function parseEnvios($results)
    {
        $sends = [];
        foreach ($results['ENV_ESTADOS_REF'] as $result) {
            $sends[] = [
                'tracking'  => $result['@attributes']['V_SERVICIO'],
                'date'      => $result['@attributes']['D_FEC_HORA_ALTA'],
                'code_type' => $result['@attributes']['V_COD_TIPO_EST'],
                'code'      => $this->getCode($result['@attributes']['V_COD_TIPO_EST'])
            ];
        }

        usort($sends, function ($a, $b) {
            return $a['date'] >= $b['date'];
        });
        return $sends;
    }

    /**
     * @param $service
     * @param $method
     * @param $parameters
     * @return mixed
     */
    private function call($service, $method, $parameters)
    {
        $request = $this->buildRequest($service, $method, $parameters);

        if ($service == 'WebServService' && !$this->session_id) {
            $this->login();
        }

        $xml = $this->buildXml($request);
        $url = $this->url;
        $result = $this->request($url, $xml);

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', $xml);
            file_put_contents (public_path().'/dumper/response.txt', $result);
        }

        if ($service == 'WebServService') {
            $result = str_replace('&lt;', '<', $result);
            $result = str_replace('&gt;', '>', $result);
            $re = '@(<CONSULTA>.*</CONSULTA>)@ms';
            preg_match($re, $result, $matches);

            if (!isset($matches[0]) || empty($matches[0])) {
                throw new Exception('Não foram devolvidos resultados do webservice pelo método ' . $method);
            } else {
                $xml = simplexml_load_string($matches[0], "SimpleXMLElement", LIBXML_NOCDATA);
                $xml = json_encode($xml);
                $result = json_decode($xml, true);
            }
        }

        return $result;
    }


    /**
     * Execute webservice
     *
     * @param type $service
     * @param type $method
     * @param type $parameters
     * @return type
     */
    private function execute($service, $method, $parameters, $isCollection = false)
    {
        $request = $this->buildRequest($service, $method, $parameters);

        if ($service == 'WebServService' && !$this->session_id) {
            $this->login();
        }

        $xml = $this->buildXml($request);
        $url = $this->url;

        $response = $this->request($url, $xml);

        if($this->debug) {
            if(!File::exists(public_path().'/dumper/')){
                File::makeDirectory(public_path().'/dumper/');
            }

            file_put_contents (public_path().'/dumper/request.txt', $xml);
            file_put_contents (public_path().'/dumper/response.txt', $response);
        }

        if ($service == 'WebServService') {
            $result = true;
            $response = str_replace('&lt;', '<', $response);
            $response = str_replace('&gt;', '>', $response);

            $re = '@<v1:(\w+)>([^<]+)<@ms';
            preg_match_all($re, $response, $matches);
            if (empty($matches[0])) {
                $result = false;
                $re = '@<fault(\w+)>([^>]+)<@ms';
                preg_match_all($re, $response, $matches);
            }

            $response = [];
            foreach ($matches[1] as $n => $match) {
                $response[$match] = $matches[2][$n];
            }

            if (!$result) {
                $error = @$response['string'];
                $errorMsg = $error;

                if (is_string(@$response['string'])) {
                    $errorParts = explode(':', $error);
                    $errorCode  = @$errorParts[0];
                    $errorMsg   = trim(@$errorParts[1]);
                    if($errorCode) {
                        if($method == 'GrabaEnvIncActuacion' || $method == 'GrabaEnvIncActuacionLibre') {
                            $errorMsg = $this->getError($errorCode, 'incidence', $errorMsg);
                            $errorMsg = empty($errorMsg) ? $error : $errorMsg;
                        } else {
                            $errorMsg   = $this->getError($errorCode, ($isCollection ? 'collection' : 'shipment'), $errorMsg);
                            $errorMsg = empty($errorMsg) ? $error : $errorMsg;
                        }

                    } else {
                        $errorCode = '9999';
                        $errorMsg  = $error;
                    }
                }
                else if(empty($errorCode) && $method == 'GrabaEnvIncActuacion' || $method == 'GrabaEnvIncActuacionLibre') {
                    return true;
                }

                throw new \Exception($errorMsg);
            }

            return $response;
        }

        return $response;
    }


    /**
     * @param $url
     * @param $xml
     * @return mixed
     */
    private function request($url, $xml)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($xml));
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * @param $request
     * @return string
     */
    private function buildXml($request)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                ' . $this->buildXmlHeader() . '
                <soap:Body>
                ' . $request . '
                </soap:Body>
        </soap:Envelope>';
        return $xml;
    }

    /**
     * @return string
     */
    private function buildXmlHeader()
    {
        $header = '';
        if (!empty($this->session_id)) {
            $header = '<soap:Header>
                            <ROClientIDHeader xmlns="http://tempuri.org/">
                                <ID>' . $this->session_id . '</ID>
                            </ROClientIDHeader>
                        </soap:Header>';
        }
        return $header;
    }

    /**
     * @param $service
     * @param $method
     * @param $parameters
     * @return string
     */
    public static function buildRequest($service, $method, $parameters)
    {
        $res = '<' . $service . '___' . $method . '>';
        foreach ($parameters as $key => $value) {
            $res .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $res .= '</' . $service . '___' . $method . '>';
        return $res;
    }

    /**
     * @param $response
     * @return array
     * @throws \Exception
     */
    public function setLogin($response)
    {
        $login = true;
        $re = '@<v1:(\w+)>([^<]+)<@ms';
        preg_match_all($re, $response, $matches);
        if (empty($matches[0])) {
            $login = false;
            $re = '@<fault(\w+)>([^>]+)<@ms';
            preg_match_all($re, $response, $matches);
        }
        $response = [];
        foreach ($matches[1] as $n => $match) {
            $response[$match] = $matches[2][$n];
        }

        if (!$login) {
            $error = @$response['string'];
            if (is_string(@$response['string'])) {
                $errorParts = explode(':', $error);
                $errorCode  = @$errorParts[0];

                if($errorCode) {
                    $errorMsg   = $this->getError($errorCode, 'login');
                    $errorMsg = empty($errorMsg) ? $error : $errorMsg;
                } else {
                    $errorCode = '9999';
                    $errorMsg  = $error;
                }
            }

            WebserviceLog::insert([
                'source'     => config('app.source'),
                'webservice' => 'Envialia',
                'method'     => 'Login',
                'response'   => 'User: '. $this->cliente. ' | Password: '.$this->password.' ['.$errorMsg.']',
                'status'     => 'error',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            throw new \Exception($errorMsg);
        } else {
            $this->session_id = $response['strSesion'];
        }

        //dd($response);
        return $response;
    }

    /**
     * @return array
     */
    public function login()
    {
        $parameters = [];
        $parameters['strCodAge'] = $this->agencia;
        $parameters['strCod']    = $this->cliente;
        $parameters['strPass']   = $this->password;

        $result = $this->call('LoginWSservice', 'LoginCli', $parameters);
        return $this->setLogin($result);
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
            if(isset($row['@attributes'])) {
                $row = $row['@attributes'];
            }
            $arr[] = mapArrayKeys($row, config('webservices_mapping.envialia.'.$mappingArray));
        }

        return $arr;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        try {
            if ($shipment->is_collection) {
                $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code);
            } else {
                $data = self::getEstadoEnvioByTrk(
                    $shipment->provider_cargo_agency,
                    $shipment->provider_sender_agency,
                    $shipment->provider_tracking_code);
            }

            /*$shipmentLinked = null;
            if ($shipment->linked_tracking_code) {
                $shipmentLinked = Shipment::where('tracking_code', $shipment->linked_tracking_code)->first();
            }*/

            if ($data) {
                $numeroIncidencia = 0;

                $oldStatus = null;
                $oldDate = null;

                //sort data array (em vez de usar o metodo aasort para nao fazer 2 ciclos foreach porque é preciso corrigir a data para ordenar corretamente)
                $sortData = array();
                foreach ($data as $key => $row) {
                    $data[$key]['created_at'] = Date::createFromFormat('m/d/Y H:i:s', $row['created_at'])->toDateTimeString();
                    $sortData[$key] = $data[$key]['created_at'];
                }
                array_multisort($sortData, SORT_ASC, $data);

                $deliveredTrks = [];
                foreach ($data as $item) {

                    $envialiaStatus = config('shipments_import_mapping.envialia-status');
                    $item['status_id'] = $envialiaStatus[$item['status']];

                    if ($item['status_id'] == '9') {

                        $envialiaIncidences = config('shipments_import_mapping.envialia-incidences');

                        try {
                            $incidenceData = self::getIncidenciasByTrk(
                                $shipment->provider_cargo_agency,
                                $shipment->provider_sender_agency,
                                $shipment->provider_tracking_code);

                            $incidenceCode = @$incidenceData[$numeroIncidencia]['incidence'];
                            $incidenceId   = @$incidenceData[$numeroIncidencia]['id'];
                        } catch (\Exception $e) {
                            $incidenceCode = null;
                            $incidenceId = $numeroIncidencia + 1;
                        }

                        $item['obs'] = @$incidenceData[$numeroIncidencia]['obs'];
                        $item['incidence_id']  = @$envialiaIncidences[$incidenceCode];
                        $item['provider_code'] = $incidenceId;
                        $numeroIncidencia++;
                    }

                    if ($item['status_id'] == '5') { //entregue
                        try {
                            $pod = self::InfEnvEstPOD($shipment->date, $shipment->provider_tracking_code);
                            $pod = @$pod['POD'];
                        } catch(\Exception $e) {}

                        $item['receiver'] = @$pod['pod_name'];
                        $item['obs']      = @$pod['pod_obs'];

                        $deliveredTrks[$shipment->provider_tracking_code] = $shipment->provider_tracking_code;
                    }


                    //em vez de registar como falhada, marca como incidencia e depois é que se coloca recolha falhada
                    if(config('app.source') == 'asfaltolargo' && $item['status_id'] == ShippingStatus::PICKUP_FAILED_ID) {
                        $item['status_id'] = 9;
                        $item['obs'] = 'Recolha falhada';
                    }

                    $history = ShipmentHistory::firstOrNew([
                        'shipment_id' => $shipment->id,
                        'created_at'  => $item['created_at'],
                        'status_id'   => $item['status_id']
                    ]);

                    $history->fill($item);
                    $history->shipment_id = $shipment->id;
                    $history->save();

                    if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                        $price = $shipment->addPickupFailedExpense();
                        $shipment->walletPayment(null, null, $price); //discount payment
                    }

                    //update shipment linked
                    /*if ($shipmentLinked) {
                        $history = ShipmentHistory::firstOrNew([
                            'shipment_id' => $shipmentLinked->id,
                            'created_at' => $item['created_at'],
                            'status_id' => $item['status_id']
                        ]);

                        $history->fill($item);
                        $history->shipment_id = $shipmentLinked->id;
                        $history->save();

                        $history->shipment = $shipment;
                    }*/
                }

                try {
                    $automatic = true;
                    if(in_array($shipment->customer_id, [10965,10994])) { //corrida do tempo
                        $automatic = false; //se automatic estiver a true, o sistema nao vai notificar o destinatario
                    }

                    //if($shipment->date >= '2020-06-19') {
                        $history->sendEmail(false, false, $automatic);
                    //}

                } catch (\Exception $e) {}

                $shipment->status_id   = $history->status_id;
                $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');
                $shipment->save();

                /*if ($shipmentLinked) {
                    $shipmentLinked->status_id = $history->status_id;
                    $shipmentLinked->save();
                }*/

                //DELETE STORED LABELS
                if($deliveredTrks) {
                    foreach ($deliveredTrks as $trackingCode) {
                        $filepath = public_path() . $this->upload_directory . $trackingCode . '_labels.pdf';
                        if (File::exists($filepath)) {
                            File::delete($filepath);
                        }
                    }
                }

                return $history->status_id ? $history->status_id : true;
            }
        } catch (\Exception $e) {
            throw new Exception($shipment->tracking_code . ': '. $e->getMessage());
        }
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false, $webserviceLogin = null, $forceCurDate = false) {

        $service = $this->getProviderService($shipment);

        if(config('app.source') == 'volumedourado') {
            $service = '24';
        }

        $reference =  $shipment->reference ? ' - '.$shipment->reference : '';

        $senderZipCode = $shipment->sender_zip_code;
        if($shipment->sender_country == 'pt') {
            $senderZipCode = explode('-', $senderZipCode);
            $senderZipCode = $senderZipCode[0];
            $senderZipCode = str_replace('-', '', $senderZipCode);
        }

        $recipientZipCode = $shipment->recipient_zip_code;
        if($shipment->recipient_country == 'pt') {
            $recipientZipCode = explode('-', $recipientZipCode);
            $recipientZipCode = $recipientZipCode[0];
            $recipientZipCode = str_replace('-', '', $recipientZipCode);
        }

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $returnPack = 0;
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 1;
        }

        $expenses = [];
        if (!empty($shipment->optional_fields)) {
            $opt = $shipment->optional_fields;
            $opt = array_keys(json_decode($opt, true));

            $expenses = ShippingExpense::whereIn('id', $opt)
                ->pluck('type', 'id')
                ->toArray();
        }
        
        //return guide
        $returnGuide = 0;
        if (in_array('rguide', $expenses)) {
            $returnGuide = 1;
        }
        
        //return check
        $returnCheck = 0;
        if (in_array('rcheck', $expenses)) {
            $returnCheck = 1;
        }

        $fragil = 0;
        if (in_array('fragile', $expenses)) {
            $fragil = 1;
        } 

        $sabado = 0;
        if (in_array('sabado', $expenses)) {
            $sabado = 1;
        }


        $zone = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country);

        $contactAttn = utf8_decode(str_replace('&', 'e', trim($shipment->recipient_attn)));
        if($isCollection) {
            $contactAttn = $shipment->sender_attn ? utf8_decode(str_replace('&', 'e', trim($shipment->sender_attn))) : $contactAttn;
        }

        $data = [
            'strCodAgeCargo' => $shipment->provider_sender_agency,
            'strCodTipoServ' => $service,
            'strCodCli'      => @$shipment->customer->code,
            'strNomOri'      => utf8_decode(str_replace('&', 'e', trim($shipment->sender_name))),
            'strDirOri'      => utf8_decode(str_replace('&', 'e', trim($shipment->sender_address))),
            'strPobOri'      => utf8_decode(str_replace('&', 'e', trim($shipment->sender_city))),
            'strCPOri'       => $senderZipCode,
            'strTlfOri'      => $shipment->sender_phone,
            'strPersContacto'=> utf8_decode(str_replace('&', 'e', trim($contactAttn))),
            'strNomDes'      => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_name))),
            'strDirDes'      => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_address))),
            'strPobDes'      => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_city))),
            'strCPDes'       => $recipientZipCode,
            'strTlfDes'      => $shipment->recipient_phone,
            'intPaq'         => $shipment->volumes,
            'dPesoOri'       => $shipment->weight,
            'dReembolso'     => $shipment->charge_price ? $shipment->charge_price : 0,
            'boRetorno'      => $returnPack, //com retorno?
            'strObs'         => utf8_decode(str_replace('&', 'e', trim($shipment->obs))),
            'strRef'         => 'TRK'.$shipment->tracking_code. $reference,
            'boAcuse'        => $returnGuide, //com guia assinada?
            'boSabado'       => $sabado,
            'strCodPais'     => in_array($shipment->recipient_country, ['pt', 'es']) ? '' : strtoupper($shipment->recipient_country),
            'dBaseImp'       => $shipment->cod == 'D' ? $shipment->billing_subtotal : 0,
            'dImpuesto'      => $shipment->cod == 'D' ? $shipment->billing_vat : 0,
            //'boPorteDebCli'  => '',
            //'boGestOri'      => '',
            //'boGestDes'      => '',
        ];

        if($shipment->insurance_price) {
            $data['dValor'] = $shipment->insurance_price;
        }

        if($shipment->total_price_when_collecting) {
            $data['dAnticipo'] = $shipment->total_price_when_collecting;
        }

        //dd($data);
        if(empty($shipment->provider_tracking_code)) {
            $isUpdate = false;
            $data['boInsert'] = 1;
            $data['dtFecha']  = $forceCurDate ? date('Y-m-d') : $shipment->date;
        } else {
            $isUpdate = true;
            //$data['dtFecha']    = $forceCurDate ? date('Y-m-d') : $shipment->date;
            $data['dtFecha']    = date('Y-m-d');
            $data['boInsert']   = 0;

            if($isCollection) {
                $data['strCod'] = $shipment->provider_tracking_code;
            } else {
                $data['strAlbaran'] = $shipment->provider_tracking_code;
            }
        }

        if($shipment->lenght && $shipment->height && $shipment->width) {
            $data['dAltoOri']  = $shipment->height;
            $data['dAnchoOri'] = $shipment->width;
            $data['dLargoOri'] = $shipment->lenght;
        }

        if($isCollection) {

            $data['dtFecRec']   = $shipment->date;
            $data['strObsDes']  = $shipment->obs_delivery;

            if(!empty($shipment->volumes)) {
                $data['intBul'] = (int) $shipment->volumes;
            }

            if(!empty($shipment->weight)) {
                $data['dPeso'] = (float) $shipment->weight;
            }

            if(!empty($shipment->start_hour)) {
                $startHourInteger = intval(str_replace(':', '', $shipment->start_hour));

                $startHour = $shipment->date.' ' . $shipment->start_hour.':00';
                $endHour   = $shipment->date.' ' . ($shipment->end_hour ? $shipment->end_hour : '00:00').':00';

                if($startHourInteger > 1300) {
                    $data['dtHoraRecIniTarde'] = $startHour;
                    $data['dtHoraRecFinTarde'] = $endHour;
                } else {
                    $data['dtHoraRecIni'] = $startHour;
                    $data['dtHoraRecFin'] = $endHour;
                }
            }

        } else {
            $data['strCodAgeOri'] = $shipment->provider_sender_agency;
            //$data['strCodAgeDes'] = $shipment->provider_recipient_agency;
        }

        //force sender data to hide on labels
        if((Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels')) && !($isCollection || $shipment->is_collection)) {
            $zipCode = trim($shipment->agency->zip_code);
            $zipCode = substr($zipCode, 0, 4);

            if(Setting::get('hidden_recipient_on_labels')) {
                $data['strNomOri'] = utf8_decode(str_replace('&', 'e', trim($shipment->agency->company)));
            }

            $data['strDirOri'] = utf8_decode(str_replace('&', 'e', trim($shipment->agency->address)));
            $data['strPobOri'] = utf8_decode(str_replace('&', 'e', trim($shipment->agency->city)));
            $data['strCPOri']  = $zipCode;
            $data['strTlfOri'] = $shipment->agency->phone;
        }


        if($isCollection) {
            return $this->storeRecolha($data, $isUpdate);
        } else {
            return $this->storeEnvio($data, $isUpdate);
        }
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {

        $parameters = [];
        $parameters['strCodAgeCargo'] = $shipment->provider_cargo_agency;
        $parameters['strCodAgeOri']   = $shipment->provider_sender_agency;
        $parameters['strAlbaran']     = $shipment->provider_tracking_code;

        $result = $this->execute('WebServService', 'BorraEnvio', $parameters);

        if (!empty($result['intCodeError'])) {
            return $this->getError('destroy', $result['intCodeError']);
        }

        return true;
    }

    /**
     * Grava uma resolução a um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveIncidenceResolution($incidenceResolution, $isCollection = false) {

        $mapping = config('shipments_export_mapping.envialia-incidences');
        $code = @$mapping[$incidenceResolution->resolution->code];

        if($isCollection) {
            $data = [];
            $data['strRecogida']         = @$incidenceResolution->shipment->provider_tracking_code;
            $data['strCodTipoActu']      = $code;
            $data['strDesTipoActuLibre'] = utf8_decode(str_replace('&', 'e', trim($incidenceResolution->obs)));
        } else {
            $data = [];
            $data['strCodAgeCargo']      = @$incidenceResolution->shipment->provider_cargo_agency;
            $data['strAlbaran']          = @$incidenceResolution->shipment->provider_tracking_code;
            $data['intIdEnvInc']         = @$incidenceResolution->history->provider_code;
            $data['strCodTipoActu']      = $code;
            $data['strDesTipoActuLibre'] = utf8_decode(str_replace('&', 'e', trim($incidenceResolution->obs)));
            $data['strDesTipoActu']      = utf8_decode(str_replace('&', 'e', trim($incidenceResolution->obs)));
        }


        return $this->storeIncidenciaResolution($data, $isCollection);
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
     * @param $code
     * @return mixed|string
     */
    private function getCode($code)
    {
        $messages_by_code = [
            '0' => 'Documentado', //aceite
            '1' => 'Em transito', //em transito
            '2' => 'Em distribuição', //em distribuicao
            '3' => 'Incidência', //incidencia
            '4' => 'Entregue', //entregue;
            '5' => 'Devolvido', //devolvido
            '6' => 'Recanalizado', //recanalizado
            '7' => 'Incidência', //incidencia
            '8' => 'Anulado', //anulado
            '10' => 'Pendente de novo envio', //pendente e novo envio -> atribuido
            '11' => 'Em delegação de destino',
            '12' => 'Recolhido na delegação', //recolhido na delegação -> em transito
            '13' => 'Anulado', //anulado
            '14' => 'Entrega Parcial', //entrega parcial
            '15' => 'Transito 72H', //transito 72H -> em transito
            '16' => 'Pendente de Emissão', //pendente de emissão => aceite
            'R0' => 'Solicitada',
            'R1' => 'Lida pela Agência',
            'R2' => 'Atribuida',
            'R3' => 'Incidência',
            'R4' => 'Realizada',
            'R5' => 'Pendente de Atribuição',
            'R6' => 'Recolha Falhada',
            'R7' => 'Finalizada',
            'R8' => 'Anulada',
            'R9' => 'Leitura Repartidor'
        ];

        return (!empty($messages_by_code[$code])) ? $messages_by_code[$code] : 'Indeterminado';
    }

    /**
     * @param $code
     * @return mixed|string
     */
    private function getError($code, $method, $defaultMsg = null) {

        $code = trim($code);

        $errorsLogin = [
            '1' => 'Os dados de ligação configurados na ficha de cliente estão incorretos. Verifique se o código de cliente e a password correspondem aos configurados no programa Enviália.',
            '2' => 'Falta permissão no programa da Enviália para o cliente poder usar os webservices. Ative a opção "Acesso webservice" na ficha do cliente no programa da Enviália.',
            '3' => 'O cliente não está associado a nenhuma agência Enviália.',
            '4' => 'O cliente está inativo no programa da Enviália.'
        ];

        $errorsShipment = [
            '1' => 'O cliente não existe.',
            '2' => 'O cliente não tem permissões para edição de envios no programa da enviália.',
            '3' => 'O cliente não tem permissões suficientes no programa da Enviália.',
            '4' => 'A data do envio está incorreta.',
            '5' => 'A data do envio é anterior à data de hoje.',
            '6' => 'O tipo de serviço escolhido não existe ou está inativo.',
            '7' => 'O código postal do remetente está incorreto ou não pertence a nenhuma agência.',
            '8' => 'O código postal do remetente está incorreto ou não pertence a nenhuma agência.',
            '9' => 'O código postal do destinatário está incorreto ou não pertence a nenhuma agência.',
            '10' => 'O código do envio deve ter 10 dígitos.',
            '11' => 'El primer dígito en este no de albarán está reservado para envíos sin conexión.',
            '12' => 'El no de albarán indicado ya se encuentra en uso.',
            '13' => 'O cliente não pertence à agência que pagadora.',
            '14' => 'O departamento não pertenece ao cliente.',
            '15' => 'O cliente não permite envios a seu encargo. Corrija as permissões no programa da enviália',
            '16' => 'La salida de ruta no existe.',
            '17' => 'Erro na submissão do envio. Não foi possível gravar na Enviália.',
            '18' => 'No está permitido dar de alta envíos sin conexión.',
            '19' => 'O código postal do destinatário está vazio ou não é válido. Corrija o código postal.',
            '20' => 'A data de entrega não é válida.',
            '21' => 'El usuario sólo puede importar envíos cuya agencia de cargo u origen sea la suya.',
            '22' => 'O envio deve ter pelo menos um volume.',
            '23' => 'O país de destino não existe ou não está disponível para o serviço escolhido.',
            '24' => 'El albarán ya se encuentra en uso.',
            '25' => 'A data de saída deve ser sexta-feira para entregas ao sábado.',
            '26' => 'O tipo de serviço escolhido não permite indicar margem entre horas.',
            '27' => 'O tipo de serviço não permite indicar horário de recolha.',
            '28' => 'El horario concertado no alcanza el tiempo mínimo concertado.',
            '29' => 'El cliente no permite indicar franja horaria.',
            '30' => 'El cliente no permite indicar horario concertado.',
            '31' => 'El horario concertado no se encuentra dentro del rango permitido.',
            '32' => 'O cliente selecionado obriga a que o campo referência seja preenchido.',
            '33' => 'Não são permitidos valores numéricos negativos.',
        ];

        $errorsCollection = [
            '-1' => 'Error al intentar realizar la canalización en destino.',
            '-2' => 'Error al intentar realizar la canalización en origen.',
            '1'  => 'O cliente não existe.',
            '2'  => 'El usuario no tiene suficientes permisos para modificar esta recogida.',
            '3'  => 'O cliente não tem permissões suficientes para realizar esta ação.',
            '4'  => 'A data não é inválida.',
            '5'  => 'A data da recolha é anterior à data de hoje ou já não é possível efetuar a recolha hoje.',
            '6'  => 'O tipo de serviço escolhido não existe ou está inativo.',
            '7'  => 'O código postal da agência de origem está incorreto ou não pertence a nenhuma agência.',
            '8'  => 'O código postal da agência que solicitou o serviço está incorreto ou não pertence a nenhuma agência.',
            '9'  => 'O código postal do local de descarga está incorreto ou não pertence a nenhuma agência.',
            '10'  => 'O código postal do cliente está incorreto.',
            '11'  => 'O cliente não pertence à agência que solicita o serviço.',
            '12'  => 'O cliente não autoriza recolhas a seu encargo.',
            '13'  => 'O código do envio associado à recolha não é válido.',
            '14'  => 'O departamento não pertenece ao cliente.',
            '15'  => 'Erro na admissão da recolha. Contacte a Enviália.',
            '16'  => 'O código postal do local de carga é inválido.',
            '17'  => 'O código postal do local de descarga é inválido',
            '18'  => 'O país da recolha não existe ou não está disponível para o serviço escolhido.',
            '19'  => 'O número de envios deve ser numérico',
            '20'  => 'O cliente selecionado obriga a que o campo referência seja preenchido.',
            '21'  => 'O cliente não tem permissão para eliminar recolhas.',
            '33'  => 'Não é possível efetuar a recolha na data marcada. Altere a data do pedido.',
            '34'  => 'Não é possível efetuar a recolha na hora marcada. Altere a hora do pedido.'
        ];
        
        $errorDelete = [
            '1' => 'O envio não existe.',
            '2' => 'O cliente não possui permissão para anular este envio.',
            '3' => 'A data do envio ou o seu estado atual não o permite anular.',
        ];

        $errorsIncidence = [
            '-1' => 'El usuario debe pertenecer a la agencia de cargo del envío.',
            '1'  => 'O envio não existe.',
            '2'  => 'A incidência não existe.',
            '3'  => 'Ação selecionada não existe na enviália',
            '4'  => 'Não se podem inserir atualizações a incidências resolvidas.',
            '5'  => 'Não se podem inserir atualizações a incidências resolvidas.'
        ];

        if($method == 'login') {
            $error = @$errorsLogin[$code];
        } elseif($method == 'shipment') {
            $error = @$errorsShipment[$code];
        } elseif($method == 'collection') {
            $error = @$errorsCollection[$code];
        } elseif($method == 'destroy') {
            $error = @$errorDelete[$code];
        } elseif($method == 'incidence') {
            $error = @$errorsIncidence[$code];
        }

        if(!empty($error)) {
            return $error;
        } elseif ($defaultMsg) {
            return $defaultMsg;
        }

        return 'Código de erro ' . $code . ' não reconhecido.';
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
                $mapping = config('shipments_export_mapping.envialia-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Enviália.');
        }

        return $providerService;
    }
}