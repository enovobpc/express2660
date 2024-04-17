<?php
/**
 * HydratorSerializerBased.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 16:19
 */

namespace Webit\SoapApi\Hydrator;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Webit\SoapApi\Hydrator\Exception\HydrationException;
use Webit\SoapApi\Hydrator\Serializer\ResultTypeMap;

/**
 * Class HydratorSerializerBased
 * @package Webit\SoapApi\Hydrator
 */
class HydratorSerializerBased implements Hydrator
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ResultTypeMap
     */
    private $resultTypeMap;

    public function __construct(
        Serializer $serializer,
        ResultTypeMap $resultTypeMap
    ) {
        $this->serializer = $serializer;
        $this->resultTypeMap = $resultTypeMap;
    }

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        if (! is_array($result)) {
            throw new HydrationException('HydratorSerializerBased expects result to be an array.');
        }

        try {
            $resultType = $this->resultTypeMap->getResultType($soapFunction);
            $hydrated = $this->serializer->fromArray(
                $result,
                $resultType
            );
        } catch (\Exception $e) {
            throw new HydrationException(
                sprintf('Error during result hydration to type "%s"', $resultType),
                null,
                $e
            );
        }

        /**
         * Workaround for JMS Serializer bug #9
         * @see https://github.com/schmittjoh/serializer/issues/9
         */
        if (substr($resultType, 0, 15) == 'ArrayCollection' && is_array($hydrated)) {
            $hydrated = new ArrayCollection($hydrated);
        }

        return $hydrated;
    }
}
