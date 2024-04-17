<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\CustomerWebservice;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\WebserviceLog;
use Date, Response, File, Imagick, Setting, View, Session;
use App\Models\ShipmentHistory;
use Mockery\Exception;
use Mpdf\Mpdf;

class Tipsa {

    /**
     * @var string
     */
    private $url = 'http://webservices.tipsa-dinapaq.com:8099/SOAP?service=';

    //http://webservices.tipsa-dinapaq.com/SOAP?service=LoginWSService
//    private $url = 'http://213.236.3.130:8100/SOAP?service=';
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
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($agencia, $cliente, $password, $sessionId = null,  $department=null, $endpoint=null, $debug=false, $customerWebserviceId = null)
    {
        $this->agencia = $agencia;
        $this->cliente = $cliente;
        $this->password = $password;
        $this->session_id  = $sessionId;
        $this->webservice_id = $customerWebserviceId;
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
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        $parameters = [];
        $parameters['strCodAgeCargo'] = $codAgeCargo;
        $parameters['strCodAgeOri']   = $codAgeOri;
        $parameters['strAlbaran']     = $trakingCode;

        try {
            if ($result = $this->call('WebServService', 'ConsEnvEstados', $parameters)) {
                $status = $this->mappingResult($result['ENV_ESTADOS'], 'status');

                //dd($status);
                foreach($status as $key => $item) {

                    $providerAgency = $item['provider_agency_code'];
                    $providerUser   = $item['provider_user_id'];

                    if(empty($providerAgency)) {
                        $item['provider_agency_code'] = self::getAgencyFromUser($providerUser);
                    }

                    $status[$key] = $item + ['status_name' => $this->getCode($item['status'])];
                }

                return $status;
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

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

        try {
            if ($result = $this->call('WebServService', 'ConsRecEstados', $data)) {
                $status = $this->mappingResult($result['REC_ESTADOS'], 'status');

                foreach($status as $key => $item) {
                    $status[$key] = $item + ['status_name' => $this->getCode($item['status'])];
                }

                return $status;
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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

        try {
            $result = $this->call('WebServService', 'InfEnvios', $parameters);

            if ($result) {
                $result = $this->mappingResult($result['INF_ENVIOS'], 'shipment');

                $shipments = [];
                $quickboxTrackingCodes = [];
                foreach ($result as $shipment) {

                    $data = $this->getEnvioByTrk($shipment['provider_cargo_agency'], $shipment['provider_sender_agency'], $shipment['provider_tracking_code']);

                    if(!empty($data['provider_return_type'])) {
                        $quickboxTrackingCodes[] = $data['trk'];
                    }

                    $shipments[] = $data;
                }
            }

            if(!empty($shipments)) {
                //associa os envios ao cliente correto
                $customerIds = Shipment::whereIn('tracking_code', $quickboxTrackingCodes)
                    ->pluck('customer_id', 'tracking_code')
                    ->toArray();

                $shipmentsArr = [];
                foreach ($shipments as $shipment) {
                    $trk = @$shipment['trk'];

                    if(@$customerIds[$trk]) {
                        $shipment['customer_id'] = $customerIds[$trk];
                    }

                    $shipmentsArr[] = $shipment;
                }

                $shipments = $shipmentsArr;
            }

            return $shipments;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
            $shipment['fator_m3'] = (@$shipment['width'] * @$shipment['height'] * @$shipment['length']) / 1000000;

            $retType = @$shipment['provider_return_type'];
            $quickboxTrk = $this->getTrkFromRef($shipment['reference']);

            $shipment['trk'] = $quickboxTrk;

            if(!empty($retType)) {
                if($retType == 'RET') {
                    $shipment['parent_tracking_code'] = $quickboxTrk;
                    $shipment['type'] = Shipment::TYPE_RETURN;
                } elseif($retType == 'DEV') {
                    $shipment['service'] = 'DEV'; //serviço = dev
                    $shipment['parent_tracking_code'] = @$quickboxTrk;
                    $shipment['type'] = Shipment::TYPE_DEVOLUTION;
                } elseif($retType == 'REC') {
                    $shipment['service'] = '48'; //CONVERTE RECANALIZADO EM SERVIÇO 48
                    $shipment['parent_tracking_code'] = @$quickboxTrk;
                    $shipment['type'] = Shipment::TYPE_RECANALIZED;
                } elseif($retType == 'ACU') {
                    $shipment['service'] = 'RCS'; //serviço = retorno guia
                }
            } elseif(@$shipment['provider_collection_tracking_code']) {
                $shipment['provider_return_type'] = 'PICKUP';
                $shipment['parent_tracking_code'] = @$quickboxTrk;
                $shipment['type'] = Shipment::TYPE_PICKUP;
            }

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
        if ($result = $this->execute('WebServService', 'GrabaRecogida2', $data)) {
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
        if ($result = $this->execute('WebServService', 'GrabaEnvio17', $data)) {
            if(isset($result['strAlbaranOut'])) {
                $result = $result['strAlbaranOut'];
            }
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
    private function call($service, $method, $parameters, $repartidor = false)
    {
        try {
            $request = $this->buildRequest($service, $method, $parameters);

            if (!$this->session_id && $service == 'WebServService') {
                $this->session_id = $this->login();
            }

            $xml = $this->buildXml($request);

            $url = $this->url . $service;

            $response = $this->request($url, $xml);

            if($this->debug) {
                if(!File::exists(public_path().'/dumper/')){
                    File::makeDirectory(public_path().'/dumper/');
                }

                file_put_contents (public_path().'/dumper/request.txt', $xml);
                file_put_contents (public_path().'/dumper/response.txt', $response);
            }

            if ($service == 'WebServService') {
                $response = str_replace('&lt;', '<', $response);
                $response = str_replace('&gt;', '>', $response);
                $re = '@(<CONSULTA>.*</CONSULTA>)@ms';
                preg_match($re, $response, $matches);

                if (empty($matches[0])) {
                    $response = str_replace('&lt;', '<', $response);
                    $response = str_replace('&gt;', '>', $response);
                    $re = '@<fault(\w+)>([^>]+)<@ms';
                    preg_match_all($re, $response, $matches);

                    $response = [];
                    foreach ($matches[1] as $n => $match) {
                        $response[$match] = $matches[2][$n];
                    }

                    if (in_array(@$response['code'], ['EROSessionNotFound', 'EConvertError'])) {
                        $this->session_id = $this->login();
                        return $this->call($service, $method, $parameters, $repartidor);
                    } else {
                        $msg = 'O webservice devolveu uma resposta vazia. Verifique os dados que estão a ser submetidos ao pedido.';
                        if(@$response['string']) {
                            $msg = $response['string'];
                        }
                        throw new \Exception($msg);
                    }

                } else {
                    $response = @$matches[0];
                    $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
                    $xml = json_encode($xml);
                    $response = json_decode($xml, true);
                }
            }

            return $response;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Execute webservice
     *
     * @param type $service
     * @param type $method
     * @param type $parameters
     * @return type
     */
    private function execute($service, $method, $parameters, $isCollection = false, $repartidor = false)
    {

        $request = $this->buildRequest($service, $method, $parameters);

        if ($service == 'WebServService' && !$this->session_id) {
            if($repartidor) {
                $this->loginRepartidor();
            } else {
                $this->login();
            }
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
            $error    = false;
            $response = str_replace('&lt;', '<', $response);
            $response = str_replace('&gt;', '>', $response);

            $re = '@<v1:(\w+)>([^<]+)<@ms';
            preg_match_all($re, $response, $matches);

            if (empty($matches[0])) {
                $error = true;
                $re = '@<fault(\w+)>([^>]+)<@ms';
                preg_match_all($re, $response, $matches);
            }

            $response = [];
            foreach ($matches[1] as $n => $match) {
                $response[$match] = $matches[2][$n];
            }

            if($error) {
                if (in_array(@$response['code'], ['EROSessionNotFound', 'EConvertError'])) {
                    $this->session_id = null;
                    $this->session_id = $this->login();
                    return $this->execute($service, $method, $parameters, $repartidor);
                } else {
                    $error    = @$response['string'];
                    $errorMsg = $error;
                    if (is_string(@$response['string'])) {
                        $errorParts = explode(':', $error);
                        $errorCode  = @$errorParts[0];

                        if($errorCode) {
                            if($method == 'GrabaEnvIncActuacion' || $method == 'GrabaEnvIncActuacionLibre') {
                                $errorMsg = $this->getError($errorCode, 'incidence', $error);
                                $errorMsg = empty($errorMsg) ? $error : $errorMsg;
                            } else {
                                $errorMsg = $this->getError($errorCode, ($isCollection ? 'collection' : 'shipment'), $error);
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

                    if($errorMsg) {
                        throw new \Exception($errorMsg);
                    }
                }
            } else {
                return $response;
            }
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
    private function buildRequest($service, $method, $parameters)
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
                    $errorMsg   = $this->getError($errorCode, 'login', $error);
                    $errorMsg = empty($errorMsg) ? $error : $errorMsg;
                } else {
                    $errorCode = '9999';
                    $errorMsg  = $error;
                }
            }

            WebserviceLog::insert([
                'source'     => config('app.source'),
                'webservice' => 'Tipsa',
                'method'     => 'Login',
                'response'   => 'User: '. $this->cliente. ' | Password: '.$this->password.' ['.$errorMsg.']',
                'status'     => 'error',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            throw new \Exception($errorMsg);
        } else {
            $response = $response['strSesion'];
        }

        return $response;
    }

    /**
     * @return array
     */
    private function login()
    {
        $parameters = [];
        $parameters['strCodAge'] = $this->agencia;
        $parameters['strCod']    = $this->cliente;
        $parameters['strPass']   = $this->password;

        try {
            $result = $this->call('LoginWSservice', 'LoginCli', $parameters);
            $login = $this->setLogin($result);
            $this->session_id = $login;
            /*
                        CustomerWebservice::flushCache(CustomerWebservice::CACHE_TAG);

                        if(1 || config('app.source') == 'asfaltolargo') {
                            CustomerWebservice::where('method', 'tipsa')
                                ->where('agency', $this->agencia)
                                ->where('user', $this->cliente)
                                ->where('password', $this->password)
                                ->update(['session_id' => $login]);
                        } else {
                            CustomerWebservice::whereId($this->webservice_id)->update(['session_id' => $login]);
                        }*/

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $login;
    }

    /**
     * @return array
     */
    /*    private function loginRepartidor()
        {
            $parameters = [];
            $parameters['strCodAge'] = $this->agencia;
            $parameters['strCod']    = $this->cliente;
            $parameters['strPass']   = $this->password;

            $result = $this->call('LoginWSService', 'Login', $parameters);
            return $this->setLogin($result);
        }*/

    /**
     * @param $code
     * @return mixed|string
     */
    private function getCode($code)
    {
        $messages_by_code = [
            '1'  => 'Tránsito',
            '2'  => 'Reparto',
            '3'  => 'Entregado',
            '4'  => 'Incidencia',
            '5'  => 'Devuelto',
            '6'  => 'Falta de expedición',
            '7'  => 'Recanalizado',
            '9'  => 'Falta de expedición administrativa',
            '10' => 'Destruído',
            '14' => 'Disponible',
            '15' => 'Entrega parcial'
        ];
        return (!empty($messages_by_code[$code])) ? $messages_by_code[$code] : 'Indeterminado';
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
            $arr[] = mapArrayKeys($row, config('webservices_mapping.tipsa.'.$mappingArray));
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
                $webserviceShipment = self::getRecolhaByTrk($shipment->provider_tracking_code);

            } else {
                $data = self::getEstadoEnvioByTrk(
                    $shipment->provider_cargo_agency,
                    $shipment->provider_sender_agency,
                    $shipment->provider_tracking_code);

                $webserviceShipment = self::getEnvioByTrk(
                    $shipment->provider_cargo_agency,
                    $shipment->provider_sender_agency,
                    $shipment->provider_tracking_code);
            }

            $webserviceWeight = (float)@$webserviceShipment['weight'];
            $webserviceVolumetricWeight = (float)@$webserviceShipment['volumetric_weight'];

            if (empty($data)) {
                return true;
            } else {
                $numeroIncidencia = 0;

                foreach ($data as $item) {

                    $tipsaStatus = config('shipments_import_mapping.tipsa-status');
                    if ($shipment->is_collection) {
                        $tipsaStatus = config('shipments_import_mapping.tipsa-status-collection');
                    }

                    $item['status_id'] = $tipsaStatus[$item['status']];
                    $item['created_at'] = Date::createFromFormat('m/d/Y H:i:s', $item['created_at'])->toDateTimeString();

                    if ($item['status_id'] == '9' && !$shipment->is_collection) {

                        try {
                            $tipsaIncidences = config('shipments_import_mapping.tipsa-incidences');
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
                        $item['incidence_id']  = @$tipsaIncidences[$incidenceCode];
                        $item['provider_code'] = $incidenceId;
                        $numeroIncidencia++;

                    } elseif ($item['status_id'] == '5' && !$shipment->is_collection) {
                        $podUrl = self::ConsEnvPODDig($shipment->provider_cargo_agency, $shipment->provider_sender_agency, $shipment->provider_tracking_code);

                        if ($podUrl) {
                            $item['obs'] = '<a href="' . $podUrl . '" target="_blank"><i class="fas fa-file"></i> Ver comprovativo de Entrega</a>';
                        }

                    } else {
                        $item['status_id'] == '18'; //converte incidencia em recolha falhada
                    }

                    $history = ShipmentHistory::firstOrNew([
                        'shipment_id' => $shipment->id,
                        'created_at'  => $item['created_at'],
                        'status_id'   => $item['status_id']
                    ]);

                    $exists = $history->exists;
                    $history->fill($item);
                    $history->shipment_id = $shipment->id;
                    $history->save();

                    $history->shipment = $shipment;

                    if(!$exists && $history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                        $price = $shipment->addPickupFailedExpense();
                        $shipment->walletPayment(null, null, $price); //desconta o pagamento apenas a 1ª vez
                    }
                }

                try {
                    $history->sendEmail(false,false,true);
                } catch (\Exception $e) {}

                $weight = ($webserviceWeight > $shipment->weight) ? $webserviceWeight : $shipment->weight;

                if ($weight > $shipment->weight || $webserviceVolumetricWeight > $shipment->weight) {
                    $shipment->weight = $weight;

                    $tmpShipment = $shipment;
                    $tmpShipment->weight = ($webserviceVolumetricWeight > $weight) ? $webserviceVolumetricWeight : $weight;
                    $prices = Shipment::calcPrices($tmpShipment);

                    $shipment->volumetric_weight = $webserviceVolumetricWeight;
                    $shipment->total_price       = @$prices['total'];
                    $shipment->cost_price        = @$prices['cost'];
                    $shipment->fuel_tax          = @$prices['fuelTax'];
                    $shipment->extra_weight      = @$prices['extraKg'];
                }

                $shipment->status_id   = $history->status_id;
                $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
                $shipment->save();
            }

            return $history->status_id ? $history->status_id : true;

        } catch (\Exception $e) {

        }
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false, $cancelShipment = false) {

        $service = $this->getProviderService($shipment);

        $reference =  $shipment->reference ? ' - '.$shipment->reference : '';

        $zone = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country);

        $senderZipCode    = $shipment->sender_zip_code;
        $recipientZipCode = $shipment->recipient_zip_code;

        $senderZipCode = explode('-', $senderZipCode);
        $senderZipCode = $senderZipCode[0];
        $senderZipCode = str_replace('-', '', $senderZipCode);

        $recipientZipCode = explode('-', $recipientZipCode);
        $recipientZipCode = $recipientZipCode[0];
        $recipientZipCode = str_replace('-', '', $recipientZipCode);


        if(strlen($senderZipCode) == 4) {
            $senderZipCode = '6'. $senderZipCode;
        }

        if(strlen($recipientZipCode) == 4) {
            $recipientZipCode = '6'. $recipientZipCode;
        }

        $shipment->has_return = empty($shipment->has_return) ? [] : $shipment->has_return;

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

        $data = [
            'strCodAgeCargo' => $shipment->provider_cargo_agency,
            //'strCodAgeOri'   => $shipment->provider_sender_agency,
            //'strCodAgeDes'   => $shipment->provider_recipient_agency,
            'strCodTipoServ' => $service,
            'strCodCli'      => $this->cliente,//utf8_decode(str_replace('&', 'e', $shipment->customer->code)),
            'strNomOri'      => utf8_decode(str_replace('&', 'e', $shipment->sender_name)),
            'strDirOri'      => utf8_decode(str_replace('&', 'e', $shipment->sender_address)),
            'strPobOri'      => utf8_decode(str_replace('&', 'e', $shipment->sender_city)),
            'strCPOri'       => $senderZipCode,
            'strTlfOri'      => $shipment->sender_phone,
            'strPersContacto'=> utf8_decode(str_replace('&', 'e', $isCollection ? $shipment->sender_attn : $shipment->recipient_attn)),
            'strNomDes'      => utf8_decode(str_replace('&', 'e', $shipment->recipient_name)),
            'strDirDes'      => utf8_decode(str_replace('&', 'e', $shipment->recipient_address)),
            'strPobDes'      => utf8_decode(str_replace('&', 'e', $shipment->recipient_city)),
            'strCPDes'       => $recipientZipCode,
            'strTlfDes'      => $shipment->recipient_phone,
            'intPaq'         => $shipment->volumes,
            'dPesoOri'       => $shipment->weight,
            'dReembolso'     => $shipment->charge_price ? $shipment->charge_price : 0,
            'boRetorno'      => $returnPack, //com retorno?
            'strObs'         => utf8_decode(str_replace('&', 'e', $shipment->obs)),
            'strRef'         => 'TRK'.$shipment->tracking_code. $reference,
            'boAcuse'        => $returnGuide, //com guia assinada?
            'boSabado'       => $sabado,
            'strCodPais'     => in_array($shipment->recipient_country, ['pt', 'es']) ? '' : strtoupper($shipment->recipient_country),
            'dBaseImp'       => $shipment->cod == 'D' ? $shipment->billing_subtotal : 0,
            'dImpuesto'      => $shipment->cod == 'D' ? $shipment->billing_vat : 0,
        ];

        if($shipment->insurance_price) {
            $data['dValor'] = $shipment->insurance_price;
        }

        if($shipment->total_price_when_collecting) {
            $data['dAnticipo'] = $shipment->total_price_when_collecting;
        }

        //FORÇA AGENCIAS DE DESTINO VOLUMEDOURADO
        /*if(config('app.source') == 'volumedourado') {

            if(!$shipment->is_collection) {
                $data['strCodAgeOri'] = '053002';

                $zipCodes = [
                    '2745', '2735', '2725', '2715', '2605', '2705', '2710',
                    '2645', '2775', '2750', '2765', '2785', '2740', '2770',
                    '2730', '2780', '2795', '2790', '2760', '2625', '2615',
                    '2600', '2690', '2695', '2685', '2660', '2670', '2680'
                ];
                if(in_array($shipment->recipient_zip_code, $zipCodes)) {
                    $data['strCodAgeDes'] = '053006';
                }
            }
        }*/

        /*if($cancelShipment) {
            $data['boAnulado'] = 1;
        }*/

        if(empty($shipment->provider_tracking_code)) {
            $data['boInsert'] = 1;
            $data['dtFecha']  = $shipment->date;
        } else {
            $data['dtFecha']    = $shipment->date;
            $data['boInsert']   = '0';
            $data['Stralbaran'] = $shipment->provider_tracking_code;
        }

        if($shipment->lenght && $shipment->height && $shipment->width) {
            $data['dAltoOri']  = $shipment->height;
            $data['dAnchoOri'] = $shipment->width;
            $data['dLargoOri'] = $shipment->lenght;
        }

        if($isCollection) {
            $data['dtFecRec'] = $shipment->date;
            $data['strCodAgeSol'] = $shipment->provider_sender_agency;
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
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
        }
    }


    /**
     * Permite gravar uma incidencia
     * @param $data
     * @return type
     * @throws \Exception
     */
    public function storeIncidenciaResolution($data, $isCollection = false)
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
        $data = [
            'strCodAgeOri' => $senderAgency,
            'strAlbaran'   => $trackingCode,
            'intIdRepDet'  => 233, //0 = original, 233 = nova, ainda possível: 225, 226, 227, 228, 130,
            'strFormato'   => 'PDF', //PDF ou TXT
        ];

        $result = $this->execute('WebServService', 'ConsEtiquetaEnvio4', $data);

        if(@$result['strEtiqueta']) {
            return @$result['strEtiqueta'];
        }

        throw new \Exception('Falha ao obter etiqueta');
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

        if ($result = $this->execute('WebServService', 'ConsURLDocAgrupados', $parameters)) {
            $url = $result['strURL'];

            if($url) {
                $url = str_replace('amp;', '', $url);
            }
            return $url;
        }
        return $result;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {

        $parameters = [];
        $parameters['strCodAgeCar'] = $shipment->provider_cargo_agency;
        $parameters['strCodAgeOri'] = $shipment->provider_sender_agency;
        $parameters['strAlbaran']   = $shipment->provider_tracking_code;

        $result = $this->execute('WebServService', 'BorraEnvio', $parameters);

        if (!empty($result['intCodErrorOut'])) {
            throw new Exception($this->getError($result['intCodErrorOut'], 'destroy'));
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

        $mapping = config('shipments_export_mapping.tipsa-incidences');
        $code = @$mapping[$incidenceResolution->resolution->code];

        if($isCollection) {
            $data = [];
            $data['strCodRec']   = @$incidenceResolution->shipment->provider_tracking_code;
            $data['intIdRecInc'] = $code;
            $data['strTexto']    = utf8_decode(str_replace('&', 'e', trim($incidenceResolution->obs)));;
        } else {
            $data = [];
            $data['strCodAgeCargo']      = @$incidenceResolution->shipment->provider_cargo_agency;
            $data['strCodAgeOri']        = @$incidenceResolution->shipment->provider_sender_agency;
            $data['strAlbaran']          = @$incidenceResolution->shipment->provider_tracking_code;
            $data['intIdEnvInc']         = @$incidenceResolution->history->provider_code;
            $data['strCodTipoActu']      = $code;
            $data['strTexto']            = utf8_decode(str_replace('&', 'e', trim($incidenceResolution->obs)));;
            $data['strTipo']             = 'AGE';
        }

        return $this->storeIncidenciaResolution($data, $isCollection);
    }

    /**
     * @return type
     */
    public function saveEstadoEnvio($trackingCode, $statusCode)
    {

        $data = [
            'codigoRec' => $trackingCode,
            'estado'    => $statusCode,
            'fechaHora' => date('d/m/Y H:i:s')
        ];

        $result = $this->execute('WebServService', 'GrabaEnvEstadosMasivo', $data, true);

        if ($result) {
        }
        return $result;
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
     * get quickbox TRK from reference
     */
    public function getTrkFromRef($ref) {
        if(strtolower(substr($ref, 0, 3)) == 'trk') {
            return substr($ref, 3, 12);
        }
        return false;
    }

    /**
     * @param $code
     * @return mixed|string
     */
    private function getError($code, $method, $defaultMsg = null) {

        $code = trim($code);

        $errorsLogin = [
            '1' => 'Os dados de ligação configurados na ficha de cliente estão incorretos. Verifique se o código de cliente e a password correspondem aos configurados no Dinapaq.',
            '2' => 'Falta permissão no Dinapaq para o cliente poder usar os webservices. Ative a opção "Acesso webservice" na ficha do cliente no programa da Dinapaq.',
            '3' => 'IP Bloqueado pela TIPSA. Tempo mínimo de espera 10 minutos. Tente novamente dentro de alguns minutos.',
            '4' => 'O cliente está inativo no Dinapaq.'
        ];

        $errorsShipment = [
            '1' => 'O cliente não existe.',
            '2' => 'O cliente não tem permissões para edição de envios no Dinapaq.',
            '3' => 'O cliente não tem permissões suficientes no Dinapaq.',
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
            '15' => 'O cliente não permite envios a seu encargo. Corrija as permissões no Dinapaq',
            '16' => 'La salida de ruta no existe.',
            '17' => 'Erro na submissão do envio. Não foi possível gravar na Tipsa.',
            '18' => 'No está permitido dar de alta envíos sin conexión.',
            '19' => 'O código postal do destinatário está vazio ou não é válido.',
            '20' => 'A data de entrega não é válida.',
            '21' => 'El usuario sólo puede importar envíos cuya agencia de cargo u origen sea la suya.',
            '22' => 'O envio deve ter pelo menos um volume.',
            '23' => 'No se pueden introducir valores numéricos negativos.',
            '24' => 'La agencia no admite valor asegurado en envíos locales.',
            '25' => 'Há campos obrigatórios que estão a ser comunicados vazios.',
            '26' => 'El tipo de servicio no es admisible para el cliente seleccionado.',
            '27' => 'El no de albarán no puede terminar en 000.',
            '28' => 'El tipo de servicio no es admisible para la agencia de destino seleccionada.',
            '29' => 'Agencia de origen no permitida',
            '30' => 'La agencia de cargo ha llegado al límite de envíos mensuales permitidos',
            '31' => 'Ya existe un envío con la misma agencia de cargo, origen y el número de albarán especificado en la configuración de números de albarán para clientes finales. Debe modificar dicha configuración para poder grabar envíos',
            '32' => 'No tiene permisos para grabar envíos con el código de cliente indicado',
            '33' => 'O país de destino não existe ou não está disponível para o serviço escolhido.',
            '34' => 'O envio deve ter encargos alfandegários.',
            '35' => 'La fecha de entrega debe ser mayor a la fecha de salida.',
            '36' => 'El tipo de servicio requiere dirección agrupada.',
            '37' => 'El cliente no tiene permiso para grabar el envío con dirección agrupada.',
            '38' => 'El cliente requiere departamento.',
            '39' => 'El envío pendiente ya existe como envío.',
            '40' => 'El envío ya existe como pendiente.',
            '41' => 'El envío no supera el importe mínimo requerido con FRQ.'
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
            '15'  => 'Erro na admissão da recolha. Contacte a Tipsa.',
            '16'  => 'O código postal do local de carga é inválido.',
            '17'  => 'O código postal do local de descarga é inválido',
            '18'  => 'O país da recolha não existe ou não está disponível para o serviço escolhido.',
            '19'  => 'O número de envios deve ser numérico',
            '20'  => 'O cliente selecionado obriga a que o campo referência seja preenchido.',
            '21'  => 'O cliente não tem permissão para eliminar recolhas.',
        ];

        $errorDelete = [
            '1' => 'O envio não existe',
            '2' => 'O cliente não possui permissão para anular este envio.',
            '3' => 'A data do envio não o permite anular.',
        ];

        $errorIncidence = [
            '1' => 'El código de actuación no existe o está inactivo',
            '2' => 'El tipo de la actuación no es válido.',
            '3' => 'El usuario no tiene permiso para meter actuaciones.',
            '4' => 'El envío no existe o el usuario no tiene permiso para modificarlo.',
            '5' => 'El usuario debe pertenecer a la agencia de cargo.',
            '6' => 'No se puede introducir una actuación sin incidencia si el envío ya tiene incidencias.',
            '7' => 'La incidencia que se genera con la actuación no está configurada.',
            '8' => 'El usuario debe pertenecer a la agencia de cargo o a la agencia de destino.',
            '9' => 'La incidencia indicada no existe.',
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
            $error = @$errorIncidence[$code];
        }

        return !empty(@$error) ? $error : ($defaultMsg ? $defaultMsg : 'Código de erro ' . $code . ' não reconhecido.');
    }

    /**
     * @param $code
     * @return mixed|string
     */
    private function getAgencyFromUser($userCode) {

        $arr = [
            '53009' => '053009', //COIMBRA

            '53015' => '053002', //OPORTO
            '53022' => '053002',
            '53085' => '053002',

            '53087' => '053003', //OPORTO DISTRIBUICION

            '53081' => '053008', //LISBOA
            '53068' => '053008',
            '53100' => '053008',

            '53092' => '053007', //LISBOA DISTRIBUICION

            '53131' => '053020', //POMBAL
            '53117' => '053020',

            '53073' => '053021', //EVORA
        ];

        return @$arr[$userCode];
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
                $mapping = config('shipments_export_mapping.tipsa-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Tipsa.');
        }

        return $providerService;
    }
}