<?php
/**
 * VoidHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:49
 */

namespace Webit\SoapApi\Hydrator;

class VoidHydrator implements Hydrator
{

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        return $result;
    }
}
