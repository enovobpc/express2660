<?php
/**
 * ResolveIPNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 16:43
 */

namespace Webit\SoapApi\Features\Ip2Geo\Normaliser;

use Webit\SoapApi\Features\Ip2Geo\Ip;
use Webit\SoapApi\Input\Exception\NormalisationException;
use Webit\SoapApi\Input\InputNormaliser;

class ResolveIPNormaliser implements InputNormaliser
{

    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        if (! ($arguments instanceof Ip)) {
            throw new \InvalidArgumentException(__CLASS__ . ' requires arguments to be an instance of IP class.');
        }

        return array(
            'ipAddress' => (string) $arguments,
            'licenseKey' => ''
        );
    }
}
