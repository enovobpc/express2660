<?php
/**
 * https://api.docs.cpanel.net/cpanel/introduction
 */

namespace App\Models\Cpanel;

class Base extends \App\Models\BaseModel {

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $token;

    /**
     * Cpanel constructor
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct()
    {
        $this->user  = env('CPANEL_USER');
        $this->token = env('CPANEL_TOKEN');
    }

    /**
     * Call API
     *
     * @param $url
     * @param null $headers
     * @param null $data
     * @return mixed
     */
    public function execute($module, $function = null, $params = null, $method = 'GET')
    {
        $url = $this->getUrl($module, $function, $params);

        $headers = [
            'Authorization: cpanel '.$this->user.':'.$this->token
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_HTTPHEADER      => $headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if(!$response['status']) {
            throw new \Exception(@$response['errors'][0]);
        }

        $response = $response['data'];

        return $response;
    }

    /**
     * Get cpanel URL
     *
     * @param $module
     * @param $function
     * @param $params
     */
    public function getUrl($module, $function = null, $params = null) {

        $host = request()->getHttpHost();

        if(env('APP_ENV') == 'local') {
            $host = 'quickbox.pt';
        }

        $url = 'https://'. $host .':2083/execute/'.$module;

        if($function) {
            $url.= '/'.$function;
        }

        if($params) {
            $params = http_build_query($params);
            $url.= '?'.$params;
        }

        return $url;
    }
}