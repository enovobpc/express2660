<?php

namespace App\Models\Sms;


class UsenditApi extends \App\Models\Sms\Sms
{

    public $url     = 'https://usendit.pt/v2/remoteusendit.asmx/SendMessage';
    public $urlTest = 'https://apitest/usendit.pt/v2/remoteusendit.asmx/SendMessage';

    /**
     * Send SMS Text
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function sendSMS($params)
    {
        $username   = env('SMS_USERNAME');
        $password   = env('SMS_PASSWORD');
        $sender     = env('SMS_FROM_NAME');

        @$params['to'] = trim(@$params['to'], '+');

        if (is_array(@$params['to'])) {
            @$params['to'] = implode(';', @$params['to']);
        }

        $data = [
            'username'              => $username,
            'password'              => $password,
            'partnerEventId'        => '',
            'timezone'              => '',
            'partnerMsgId'          => '',
            'sender'                => $sender,
            'msisdn'                => @$params['to'],
            'mobileOperator'        => '-1',
            'priority'              => 0,
            'expirationDatetime'    => '',
            'messageText'           => @$params['message'],
            'scheduleDatetime'      => '',
            'beginTime'             => '',
            'endTime'               => '',
            'workingDays'           => false,
            'isFlash'       => false,

        ];

        $soap = new \SoapClient($this->url);

        $response = $soap->sendSMS($data);

        $response = json_encode($response);
        $response = json_decode($response, true);

        $responseCode = @$response['ReturnCode'];
        $errorMsg = $this->getErrorMsg($responseCode);

        if (@$responseCode > 0) {
            throw new \Exception($responseCode . ' - ' . $errorMsg, $responseCode);
        }

        $response = [
            'gateway'       => get_class($this),
            'from'          => @$params['from'] ? $params['from'] : env('SMS_FROM_NAME'),
            'to'            => @$params['to'],
            'message'       => @$params['message'],
            'status_code'   => empty($responseCode) ? 'SENT' : 'FAILED',
            'status'        => empty($responseCode) ? 'Enviado' : $errorMsg,
            'success'       => 1,
            'sms_id'        => @$response['MsgId'],
            'sms_parts'     => Sms::countSmsParts(@$params['message']),
            'token'         => $this->token
        ];

        return $response;
    }

    /**
     * @param $error
     */
    function getErrorMsg($errorCode)
    {

        $errors = [
            '0' => 'Sucesso',
            '1' => 'Agendamento realizado mas com contactos inválidos',
            '2' => 'Username/password inválidos',
            '3' => 'Registo de utilizador pendente de código de confirmação enviado por SMS',
            '4' => 'Utilizador não tem permissões para enviar SMS',
            '5' => 'Utilizador não possui créditos suficientes para o envio',
            '6' => 'Lista de contactos null ou vazia',
            '7' => 'Lista de contactos excede a dimensão máxima',
            '8' => 'Todos os contactos da lista são inválidos',
            '9' => 'Erro interno de configuração',
            '10' => 'Erro de processamento da lista de envio',
            '11' => 'Erro de agendamento'
        ];

        return @$errors[$errorCode] ? $errors[$errorCode] : '';
    }
}
