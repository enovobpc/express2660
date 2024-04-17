<?php

namespace App\Models\Cpanel;

class Subdomain extends \App\Models\Cpanel\Base {

    /**
     * @var string
     */
    private $module = 'SubDomain';

    /**
     * Create Subdomain
     * https://api.docs.cpanel.net/openapi/cpanel/operation/addsubdomain/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function createMysqlDB($domain, $rootdomain, $dir = null)
    {
        $params = [
            'domain' => $domain,
            'rootdomain' => $rootdomain,
        ];

        if($dir) {
            $params['domain'] = $dir;
        }

        $response = $this->execute($this->module, 'create_database', $params);
        return $response;
    }
}