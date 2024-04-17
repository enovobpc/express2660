<?php

namespace App\Models\GatewayPayment;

use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class IfThenPay extends \App\Models\GatewayPayment\Base {

    /**
     * @var string
     */
    public $mbUrl = 'https://ifthenpay.com/api/multibanco/reference/init';

    /**
     * @var string
     * Used for MBWay
     */
    public $mbWayUrl = "https://mbway.ifthenpay.com/ifthenpaymbw.asmx/";

    /**
     * @var null
     * Chaves IfThenPay
     */
    public $mbWayKey = null;
    public $mbKey = null;

    /**
     * @var int
     * MBWay limit time in minutes
     */
    public $mbway_time = 5;

    /**
     * IFTHENPAY Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        if (env('APP_ENV') == 'local') {
            $this->mbUrl = 'https://ifthenpay.com/api/multibanco/reference/sandbox';
        }

        $this->mbWayKey = env('IFTHENPAY_API_MBWAY_KEY');
        $this->mbKey    = env('IFTHENPAY_API_MB_KEY');

        if($apiKey) {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * Create a MB reference
     *
     * @return array
     */
    public function mbCreate($data)
    {
        $data['limit'] = $data['limit'] ? $data['limit'] : 3;

        $today = Carbon::today();
        $limit = $today->addDays($data['limit']);

        $data = [
            'mbKey'         => $this->mbKey,
            'orderId'       => $data['reference'],
            'amount'        => $data['value'],
            'description'   => @$data['description'] ?:$data['reference'],
            'expiryDays'    => $data['limit']
        ];
        
        // IFTHENPAY RESPONSE
        $response = $this->execute($this->mbUrl, $data);

        $result = [
            'result'        => $response['Status'] == '0' ? true : false,
            'reference'     => $response['OrderId'],
            'method'        => 'mb',
            'value'         => $response['Amount'],
            'currency'      => 'EUR',
            'mb_reference'  => $response['Reference'],
            'mb_entity'     => $response['Entity'],
            'expires_at'    => $response['ExpiryDate'],
            'status'        => Base::STATUS_PENDING
        ];

        return $result;
    }

    /**
     * Get MB info by reference
     *
     * @return bool
     */
    public function mbInfo($reference, $entity = null)
    {
        $method = "/multibanco/info";

        $data = [
            'referencia' => $reference,
            //'entidade'   => $entity,
        ];

        return $this->execute($method, $data);
    }

    /**
     * Creates a MBWay payment
     *
     * @return array
     */
    public function mbwayCreate($data)
    {
        $url = $this->mbWayUrl . "SetPedido";

        $phone = $data['phone'];
        $now   = new Date();

        $data = [
            'MbWayKey'      => $this->mbWayKey,
            'canal'         => '03',
            'referencia'    => $data['reference'],
            'valor'         => $data['value'],
            'nrtlm'         => $phone,
            'email'         => '',
            'descricao'     => $data['description'],
        ];

        // IFTHENPAY RESPONSE
        $response = $this->execute($url, $data);
        $response = $response['d'];

        $result = [
            'result'        => $response['Estado'] == '000' ? true : false,
            'reference'     => $response['IdPedido'],
            'method'        => 'mbway',
            'value'         => $response['Valor'],
            'currency'      => 'EUR',
            'mbway_phone'   => $phone,
            'description'   => $response['MsgDescricao'],
            'status'        => Base::STATUS_PENDING,
            'expires_at'    => $now->addMinutes($this->mbway_time)->format('Y-m-d H:i:s')
        ];

        return $result;
    }

    /**
     * Generate a session id
     *
     * @return bool
     */
    public function visaCreate($data)
    {
        $method = "cc/purchase_tds";

        $year  = $data['card_year'];
        $month = $data['card_month'];

        $data = [
            'chave'         => $this->apiKey,
            'valor'         => $data['value'],
            'id'            => $data['reference'], //referencia
            'numero'        => $data['card_number'],
            'cripto'        => $data['card_cvc'],
            'validade'      => str_pad($month, 2, '0', STR_PAD_LEFT).'/'.$year,
            'url_retorno'   => @$data['return_url'] . '?identificador=' . $data['reference'].'&',
        ];

        $response = $this->execute($method, $data);

        $result = [
            'result'        => @$response['sucesso'] ? true : false,
            'reference'     => $data['id'],
            'method'        => 'mb',
            'value'         => $data['valor'],
            'currency'      => 'EUR',
            'cc_number'     => @$data['numero'],
            'cc_cvc'        => @$data['cripto'],
            'cc_year'       => $year,
            'cc_month'      => $month,
            'status'        => @$response['sucesso'] ? 'success' : 'rejected',
            'conclude_url'  => @$response['tds_url'],
            'feedback'      => $response['resposta']
        ];

        return $result;
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($url, $data, $headers = null)
    {
        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        }

        $result = json_decode($response, true);
        return $result;
    }
}