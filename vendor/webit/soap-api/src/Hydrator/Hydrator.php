<?php
/**
 * HydratorInterface.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 15:57
 */

namespace Webit\SoapApi\Hydrator;

/**
 * Class HydratorInterface
 * @package Webit\SoapApi\Hydrator
 */
interface Hydrator
{
    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction);
}
