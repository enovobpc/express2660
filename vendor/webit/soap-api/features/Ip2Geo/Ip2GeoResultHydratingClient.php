<?php
/**
 * Ip2GeoResultHydratingClient.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 17:26
 */

namespace Webit\SoapApi\Features\Ip2Geo;

use Webit\SoapApi\Executor\SoapApiExecutor;

class Ip2GeoResultHydratingClient
{
    /**
     * @var SoapApiExecutor
     */
    private $executor;

    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Ip $ip
     * @return GeoLocation
     */
    public function getGeoLocation(Ip $ip)
    {
        return $this->executor->executeSoapFunction('ResolveIP', $ip);
    }
}
