<?php
namespace App\Models\InvoiceGateway\SageX3;

use App\Models\Agency;
use App\Models\Traits\Xml2Array;
use Date, Response, Setting, File;

class Base {

    /**
     * @var string
     */
    public $url = 'http://93.108.235.141:8124/soap-generic/syracuse/collaboration/syracuse/CAdxWebServiceXmlCC?wsdl';

    /**
     * @var null
     */
    public $session_id = null;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $poolAlias;

    /**
     * @var string
     */
    public $poolId;

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        //YWSBPC -> Ler e Escrever => Clientes
        //YWSITM -> Ler => Artigos
        //YWSBPP -> Ler e Escrever => Prospects
        //YWSSIH -> Ler e Escrever => Faturas de Venda
        //YWSPIH -> Ler => Faturas de Compra
        //YWSECH -> Ler => Conta Corrente | PENDENTE DE PARAMETRIZAÇÃO


        ini_set('default_socket_timeout', 1200);

        //$this->poolAlias = 'WSDEMO';
        $this->poolAlias = 'WSPROTO';
        $this->poolId    = 'WSPROTO';
        $this->username  = 'WS';
        $this->password  = 'ws';
    }


    /**
     * Execute save method
     *
     * @param $data
     * @return bool
     */
    public function save($method, $xml) {

        $action   = 'save';

        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wss="http://www.adonix.com/WSS" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">
           <soapenv:Header/>
           <soapenv:Body>
              <wss:'.$action.' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                 <callContext xsi:type="wss:CAdxCallContext">
                    <codeLang xsi:type="xsd:string">POR</codeLang>
                    <poolAlias xsi:type="xsd:string">'.$this->poolAlias.'</poolAlias>
                    <poolId xsi:type="xsd:string">'.$this->poolId.'</poolId>
                    <requestConfig xsi:type="xsd:string"></requestConfig>
                 </callContext>
                 <publicName xsi:type="xsd:string">'.$method.'</publicName>
                 <objectXml xsi:type="xsd:string">'.$xml.'</objectXml>
              </wss:'.$action.'>
           </soapenv:Body>
        </soapenv:Envelope>';


        $headers = Array(
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Authorization: Basic ' . base64_encode($this->username.':'.$this->password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = Xml2Array::createArray($result);

        $error = @$result['soapenv:Envelope']['soapenv:Body']['multiRef'];
        if($error) {

            if(!@$error['type']) {
                $error = $error[0];
            }
            throw new \Exception($error['type']. ' - ' .$error['message'], $error['type']);
        }

        $xml = @$result['soapenv:Envelope']['soapenv:Body']['wss:saveResponse']['saveReturn']['resultXml']['@cdata'];

        $config = [
            'attributesKey' => 'attributes',
            'valueKey'      => 'value',
        ];

        $data = Xml2Array::createArray($xml, $config);

        $data   = $data['RESULT']['GRP'][0]['FLD'];
        $result = @$data[2];

        /*
        $result = $this->mappingResult($data, $method);


        $result = @$result['soapenv:Envelope']['soapenv:Body']['wss:saveResponse']['saveReturn']['status']['@value'];*/

        return $result;
    }

    /**
     * Execute run method
     *
     * @param $data
     * @return bool
     */
    public function get($method, $params = []) {

        $listSize = 5000;
        $action   = 'query';

        $objKeys = '';
        if (!empty($params)) {
            $objKeys = '<objectKeys xsi:type="wss:ArrayOfCAdxParamKeyValue" soapenc:arrayType="wss:CAdxParamKeyValue[]">
                     <keys>';

            foreach ($params as $key => $value) {
                $objKeys .= '<key>' . $key . '</key>
                       <value>' . $value . '</value>';
            }

            $objKeys .= '</keys>
                </objectKeys>';
        }


        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wss="http://www.adonix.com/WSS" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">
           <soapenv:Header/>
           <soapenv:Body>
              <wss:'.$action.' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                 <callContext xsi:type="wss:CAdxCallContext">
                    <codeLang xsi:type="xsd:string">POR</codeLang>
                    <poolAlias xsi:type="xsd:string">'.$this->poolAlias.'</poolAlias>
                    <poolId xsi:type="xsd:string">'.$this->poolId.'</poolId>
                    <requestConfig xsi:type="xsd:string"></requestConfig>
                 </callContext>
                 <publicName xsi:type="xsd:string">'.$method.'</publicName>
                    '.$objKeys.'
                 <listSize xsi:type="xsd:int">'.$listSize.'</listSize>
              </wss:'.$action.'>
           </soapenv:Body>
        </soapenv:Envelope>';

        $headers = Array(
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Authorization: Basic ' . base64_encode($this->username.':'.$this->password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = Xml2Array::createArray($result);

        $error = @$result['soapenv:Envelope']['soapenv:Body']['multiRef'];
        if($error) {
            throw new \Exception($error['type']. ' - ' .$error['message'], $error['type']);
        }

        $xml = @$result['soapenv:Envelope']['soapenv:Body']['wss:queryResponse']['queryReturn']['resultXml']['@cdata'];

        $config = [
            'attributesKey' => 'attributes',
            'valueKey'      => 'value',
        ];

        $data = Xml2Array::createArray($xml, $config);

        if(@$data['RESULT']['LIN']['FLD']) { //multiplas linhas
            $data = $data['RESULT'];

            $result = $this->mappingResult($data, $method);
        } else {

            $result = [];
            foreach ($data as $lin) {
                $data = $lin['LIN'];
                $result = $this->mappingResult($data, $method);
            }
        }

        return $result;
    }

    /**
     * Execute modify method
     *
     * @param $data
     * @return bool
     */
    public function run($method, $xml) {

        $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wss="http://www.adonix.com/WSS">
           <soapenv:Header/>
           <soapenv:Body>
              <wss:run soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                 <callContext xsi:type="wss:CAdxCallContext">
                    <codeLang xsi:type="xsd:string">POR</codeLang>
                    <poolAlias xsi:type="xsd:string">'.$this->poolAlias.'</poolAlias>
                    <poolId xsi:type="xsd:string">'.$this->poolId.'</poolId>
                    <requestConfig xsi:type="xsd:string"></requestConfig>
                 </callContext>
                 <publicName xsi:type="xsd:string">'.$method.'</publicName>
                 <inputXml xsi:type="xsd:string">'.$xml.'</inputXml>
              </wss:run>
           </soapenv:Body>
        </soapenv:Envelope>';

        $headers = Array(
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Authorization: Basic ' . base64_encode($this->username.':'.$this->password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = Xml2Array::createArray($result);

        $error = @$result['soapenv:Envelope']['soapenv:Body']['multiRef'];
        if($error) {

            if(!@$error['type']) {
                $error = $error[0];
            }
            throw new \Exception($error['type']. ' - ' .$error['message'], $error['type']);
        }

        $xml = @$result['soapenv:Envelope']['soapenv:Body']['wss:runResponse']['runReturn']['resultXml']['@cdata'];

        $config = [
            'attributesKey' => 'attributes',
            'valueKey'      => 'value',
        ];

        $data = Xml2Array::createArray($xml, $config);

        return $data;
    }

    /**
     * Execute save method
     *
     * @param $data
     * @return bool
     */
    public function delete($method, $params) {
        return $this->execute('delete', $method, $params);
    }

    /**
     * Execute webservice call
     *
     * @param $method
     * @param $data
     * @return bool
     */
    public function execute($action, $method, $params = [], $listSize = 1) {
    }

    /**
     * Cria a string xml para passar no xmlinput
     * @param  array $params <br>
     * ex: array('npag' => 1, 'ref' => 2)
     * @return string xml para passar na chamada do webservice
     */
    public static function xmlParams($params) {

        if(empty($params)) {
            return '';
        }

        $xmlInput = '<PARAM>';

        foreach ($params as $key => $value) {
            $xmlInput .= "<FLD NAME=\"". strtoupper($key)."\">". $value ."</FLD>";
        }

        $xmlInput .= '</PARAM>';

        return $xmlInput;
    }


    /**
     * Mapping
     * @param $data
     * @return array
     */
    public function mappingResult($data, $method) {

        $mappingFields = config('webservices_mapping.sageX3.' . $method);

        $result = [];

        unset($data['attributes']);

        foreach ($data as $lin) {

            $lineData = [];
            unset($lin['attributes']);

            foreach ($lin['FLD'] as $fld) {

                $key   = @$fld['attributes']['NAME'];
                $key   = @$mappingFields[$key] ? @$mappingFields[$key] : $key;
                $value = @$fld['value'];

                $lineData[$key] = $value;
            }
            $result[] = $lineData;
        }

        return $result;
    }
}