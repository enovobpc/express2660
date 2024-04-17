<?php
/**
 * GetWeatherNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:33
 */

namespace Webit\SoapApi\Features\GlobalWeather\Normaliser;

use JMS\Serializer\Serializer;
use Webit\SoapApi\Features\GlobalWeather\City;
use Webit\SoapApi\Input\Exception\NormalisationException;
use Webit\SoapApi\Input\InputNormaliser;

class GetWeatherNormaliser implements InputNormaliser
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * GetWeatherNormaliser constructor.
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        if (! ($arguments instanceof City)) {
            throw new NormalisationException('Unsupported input type. Instance of "City" expected.');
        }

        return $this->serializer->toArray($arguments);
    }
}
