<?php

namespace App\Models\Sms;


class SmsApi extends \App\Models\Sms\Sms {

    /**
     * @var string
     */
    public $authUrl = 'https://api.smsapi.com/';
    public $url     = 'https://api.smsapi.com/sms.do';

    /**
     * Send SMS Text
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function sendSMS($params)
    {

        $params['format'] = 'json';

        if(config('app.env') == 'local') {
            //$params['test'] = 1;
        }

        $params['from'] = env('SMS_FROM_NAME') ? env('SMS_FROM_NAME') : 'ENOVO';

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $params);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $this->token"
        ));

        $content = curl_exec($c);
        $content = json_decode($content, true);
        //$http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        if(@$content['error']) {
            throw new \Exception($this->mapError($content['error'], $content['message']));
        }

        $response = [
            'gateway'       => 'SmsApi',
            'from'          => @$params['from'] ? $params['from'] : env('SMS_FROM_NAME'),
            'to'            => @$params['to'],
            'message'       => @$params['message'],
            'status_code'   => @$content['list'][0]['status'],
            'status'        => $this->mapStatus(@$content['list'][0]['status']),
            'success'       => $this->mapSuccess(@$content['list'][0]['status']),
            'sms_id'        => @$content['list'][0]['id'],
            'sms_parts'     => @$content['list'][0]['parts'],
            'token'         => $this->token
        ];

        return $response;
    }

    /**
     * Map erros by code of error
     *
     * @param $errorNo
     * @return mixed
     */
    public function mapError($errorNo, $defaultMsg = null) {

        $errors = [
            '8' => 'Erro desconhecido no pedido',
            '11' => 'The message is too long or there is no message or parameter: nounicode is set and special characters (including Polish characters) are used',
            '12' => 'The message has more parts than defined in &max_parts parameter.',
            '13' => 'Número de telemóvel inválido. Verifique se o número(s) indicado(s) são telemóveis e se indicou os indicativos.',
            '14' => 'Nome do remetente da SMS não autorizado.',
            '17' => 'FLASH message cannot contain special characters',
            '18' => 'Número de parametros inválido.',
            '19' => 'Demasiadas mensagens para um único pedido.'
        ];

        return isset($errors[$errorNo]) ? $errorNo. ' - ' . $errors[$errorNo] : $errorNo . ' - ' . $defaultMsg;
    }

    /**
     * Map status by code of status
     *
     * @param $errorNo
     * @return mixed
     */
    public function mapStatus($statusNo, $defaultMsg = null) {

        $status = [
            'NOT_FOUND'     => 'Wrong ID or report has expired',
            'EXPIRED'       => 'Mensagens expiradas.',
            'SENT'          => 'Mensagem enviada com sucesso.',
            'DELIVERED'     => 'Mensagem entregue ao destinatário.',
            'UNDELIVERED'   => 'Mensagem não entregue (número inválido, erro de roaming ou outro)',
            'FAILED'        => 'Envio falhado.',
            'REJECTED'      => 'Mensagem rejeitada (número inválido, erro de roaming ou outro)',
            'UNKNOWN'       => 'Sem informação de estado',
            'QUEUE'         => 'Enviado',
            'ACCEPTED'      => 'Mensagem entregue ao operador.',
            'STOP'          => 'Envio de mensagem parada pelo utilizador.',
        ];
    
        return isset($status[$statusNo]) ? $status[$statusNo] : $defaultMsg;
    }

    /**
     * Map success of sending by code of status
     *
     * @param $errorNo
     * @return mixed
     */
    public function mapSuccess($statusNo) {

        $status = [
            'NOT_FOUND'     => false,
            'EXPIRED'       => false,
            'SENT'          => true,
            'DELIVERED'     => true,
            'UNDELIVERED'   => false,
            'FAILED'        => false,
            'REJECTED'      => false,
            'UNKNOWN'       => true,
            'QUEUE'         => true,
            'ACCEPTED'      => true,
            'STOP'          => false,
        ];

        return isset($status[$statusNo]) ? true : false;
    }
}
