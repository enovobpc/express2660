<?php
/**
 * GetWeatherHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:58
 */

namespace Webit\SoapApi\Features\GlobalWeather\Hydrator;

use JMS\Serializer\SerializerInterface;
use Webit\SoapApi\Hydrator\Hydrator;

class GetWeatherHydrator implements Hydrator
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * GetWeatherHydrator constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        $xml = $result->GetWeatherResult;
        list($header, $xml) = explode("\n", $xml, 2); // removes xml header with wrong encoding (don't be bothered about this)

        return $this->serializer->deserialize($xml, 'Webit\SoapApi\Features\GlobalWeather\Weather', 'xml');
    }
}
