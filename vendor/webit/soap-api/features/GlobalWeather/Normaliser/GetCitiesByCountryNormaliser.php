<?php
/**
 * GetCitiesByCountryNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:28
 */

namespace Webit\SoapApi\Features\GlobalWeather\Normaliser;

use Webit\SoapApi\Features\GlobalWeather\Country;
use Webit\SoapApi\Input\Exception\NormalisationException;
use Webit\SoapApi\Input\InputNormaliser;

class GetCitiesByCountryNormaliser implements InputNormaliser
{

    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        if (! ($arguments instanceof Country)) {
            throw new NormalisationException('Unsupported input type. Instance of "Country" expected.');
        }

        return array(
            'CountryName' => (string) $arguments
        );
    }
}
