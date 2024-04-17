<?php

namespace App\Models\Sms;


class VodafoneApi extends \App\Models\Sms\Sms {

    /**
     * @var string
     */
    public $authUrl = 'https://smsws.vodafone.pt/SmsBroadcastWs/service.web?wsdl';
    public $url     = 'https://smsws.vodafone.pt/SmsBroadcastWs/service.web?wsdl';

    /**
     * Send SMS Text
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function sendSMS($params)
    {

        $params['from'] = env('SMS_FROM_NAME') ? env('SMS_FROM_NAME') : 'ENOVO';

        $params = [
            'authentication' => [
                'msisdn'   => $this->user,
                'password' => $this->password,
            ],
            'responseReception' => false,
            'destination' => @$params['to'],
            'text'        => @$params['message'],
            //'messageName' => @$params['from']
        ];

        $soap = new \SoapClient($this->url);
        $response = $soap->sendShortMessage($params);

        $response = json_encode($response);
        $response = json_decode($response, true);
        $response = @$response['return'];

        if(@$response['resultCode'] > 500) {
            throw new \Exception($response['resultCode'] . ' - ' .$response['resultMessage'], $response['resultCode']);
        }

        $response = [
            'gateway'       => 'SmsApi',
            'from'          => @$params['from'] ? $params['from'] : env('SMS_FROM_NAME'),
            'to'            => @$params['to'],
            'message'       => @$params['message'],
            'status_code'   => empty(@$response['resultCode']) ? 'FAILED' : 'SENT',
            'status'        => empty(@$response['resultCode']) ? @$response['resultMessage'] : 'Mensagem enviada com sucesso.',
            'success'       => 1,
            'sms_id'        => @$response['id'],
            'sms_parts'     => '',
            'token'         => $this->token
        ];

        return $response;
    }
}
