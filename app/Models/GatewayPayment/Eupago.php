<?php

namespace App\Models\GatewayPayment;

use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;

class Eupago extends \App\Models\GatewayPayment\Base {

    /**
     * @var string
     */
    //public $url = '​https://replica.eupago.pt/clientes/rest_api';
    public $url = "https://clientes.eupago.pt/clientes/rest_api/";

    /**
     * @var null
     */
    public $apiKey = null;

    /**
     * @var int
     * MBWay limit time in minutes
     */
    public $mbway_time = 5;

    /**
     * KeyInvoice Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = env('EUPAGO_API_KEY');

        if($apiKey) {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * Generate a session id
     *
     * @return array
     */
    public function mbCreate($data)
    {
        $method = "multibanco/create";

        $data['limit'] = $data['limit'] ? $data['limit'] : 3;

        $today = Carbon::today();
        $limit = $today->addDays($data['limit']);

        $data = [
            'chave'         => $this->apiKey,
            'valor'         => $data['value'],
            'id'            => $data['reference'],
            'data_inicio'   => $today->format('Y-m-d'),
            'data_fim'      => $limit->format('Y-m-d'),
            'per_dump'      => 0,
        ];

        // EUPAGO RESPONSE
        $response = $this->execute($method, $data);

        $result = [
            'result'        => @$response['sucesso'] ? true : false,
            'reference'     => $data['id'],
            'method'        => 'mb',
            'mb_reference'  => @$response['referencia'],
            'mb_entity'     => @$response['entidade'],
            'value'         => $data['valor'],
            'currency'      => 'EUR',
            'expires_at'    => @$response['data_fim'],
            'status'        => Base::STATUS_PENDING
        ];

        return $result;
    }

    /**
     * Generate a session id
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
     * Generate a session id
     *
     * @return array
     */
    public function mbwayCreate($data)
    {
        $method = "mbway/create";

        $phone = $data['phone'];
        $now   = new Date();

        $data = [
            'chave'         => $this->apiKey,
            'valor'         => $data['value'],
            'id'            => $data['reference'],
            'alias'         => $phone,
            'descricao'     => $data['description'],
        ];

        // EUPAGO RESPONSE
        $response = $this->execute($method, $data);

        $result = [
            'result'        => $response['sucesso'] ? true : false,
            'reference'     => $data['id'],
            'method'        => 'mbway',
            'value'         => $data['valor'],
            'currency'      => 'EUR',
            'mbway_phone'   => $data['alias'],
            'description'   => $data['descricao'],
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
    public function execute($method, $data, $headers = null)
    {
        $url = $this->url . $method;

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