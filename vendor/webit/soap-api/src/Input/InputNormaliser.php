<?php
/**
 * InputNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 15:58
 */

namespace Webit\SoapApi\Input;

use Webit\SoapApi\Input\Exception\NormalisationException;

/**
 * Class InputNormaliser
 * @package Webit\SoapApi\Input
 */
interface InputNormaliser
{
    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments);
}
