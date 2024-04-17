<?php
/**
 * VoidInputNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:43
 */

namespace Webit\SoapApi\Input;

use Webit\SoapApi\Input\Exception\NormalisationException;

class VoidInputNormaliser implements InputNormaliser
{

    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        return $arguments;
    }
}
