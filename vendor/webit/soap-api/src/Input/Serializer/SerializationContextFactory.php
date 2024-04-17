<?php
/**
 * SerializationContextFactory.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 15:12
 */

namespace Webit\SoapApi\Input\Serializer;

use JMS\Serializer\SerializationContext;

interface SerializationContextFactory
{
    /**
     * @param string $soapFunction
     * @return SerializationContext
     */
    public function createContext($soapFunction);
}
