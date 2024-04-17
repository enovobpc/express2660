<?php

namespace App\Models\Cpanel;

class Quota extends \App\Models\Cpanel\Base {

    /**
     * Return disk quota information
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_local_quota_info/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getServerQuota()
    {
        $response = $this->execute('Quota', 'get_quota_info');
        return $response;
    }

    /**
     * Return disk quota details
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_stats/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getServerQuotaDetails()
    {
        $params = [
            'display' => 'diskusage|fileusage|bandwidthusage|cachedlistdiskusage|cachedmysqldiskusage'
        ];

        $response = $this->execute('StatsBar', 'get_stats', $params);
        return $response;
    }
}