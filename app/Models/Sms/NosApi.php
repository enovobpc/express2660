<?php

namespace App\Models\Sms;


class NosApi extends \App\Models\Sms\Sms {

    /**
     * @var string
     */
    //https://other-static.nos.pt/smspro/UMNL_SMSPRO_web_services.pdf
    public $url     = 'https://smspro.nos.pt/smspro/smsprows.asmx?wsdl';

    /**
     * Send SMS Text
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function sendSMS($params)
    {

        $params['from'] = env('SMS_FROM_NAME') ? env('SMS_FROM_NAME') : 'ENOVO';

        if(is_array(@$params['to'])) {
            @$params['to'] = implode(';', @$params['to']);
        }

        $data = [
            'TenantName'  => env('SMS_TOKEN'),
            'strName'     => env('SMS_FROM_NAME'),
            'strUsername' => $this->user,
            'strPassword' => $this->password,
            'MsisdnList'  => @$params['to'],
            'strMessage'  => @$params['message'],
        ];

        $soap = new \SoapClient($this->url);

        $response = $soap->sendSMS($data);

        $response = json_encode($response);
        $response = json_decode($response, true);

        $responseCode = @$response['ReturnCode'];
        $errorMsg = $this->getErrorMsg($responseCode);

        if(@$responseCode > 0) {
            throw new \Exception($responseCode . ' - ' .$errorMsg, $responseCode);
        }

        $response = [
            'gateway'       => 'NosApi',
            'from'          => @$params['from'] ? $params['from'] : env('SMS_FROM_NAME'),
            'to'            => @$params['to'],
            'message'       => @$params['message'],
            'status_code'   => empty($responseCode) ? 'SENT' : 'FAILED',
            'status'        => empty($responseCode) ? 'Enviado' : $errorMsg,
            'success'       => 1,
            'sms_id'        => @$response['id'],
            'sms_parts'     => Sms::countSmsParts(@$params['message']),
            'token'         => $this->token
        ];

        return $response;
    }

    /**
     * @param $error
     */
    function getErrorMsg($errorCode) {

        $errors = [
            '3' => 'Utilizador ou password inválida.',
            '37' => 'Cliente inactivo.',
            '38' => 'Interface Web Services não disponível para este cliente.'
        ];

        return @$errors[$errorCode] ? $errors[$errorCode] : '';

    }
}
