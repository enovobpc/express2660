<?php

namespace App\Models\Cpanel;

class ServerInfo extends \App\Models\Cpanel\Base {

    /**
     * Return service and device status
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_information/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getServerStatus()
    {
        $response = $this->execute('ServerInformation', 'get_information');
        return $response;
    }

    /**
     * Return service and device status
     * https://api.docs.cpanel.net/openapi/cpanel/operation/Variables-get_server_information/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getServerInfo()
    {
        $response = $this->execute('Variables', 'get_server_information');
        return $response;
    }
}