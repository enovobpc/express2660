<?php
/**
 * ResultTypeMap.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 16:37
 */

namespace Webit\SoapApi\Hydrator\Serializer;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ResultTypeMap
 * @package Webit\SoapApi\Hydrator
 */
class ResultTypeMap
{

    /**
     * @var ArrayCollection
     */
    private $types;

    /**
     * @var string
     */
    private $fallbackType;

    public function __construct(
        array $types,
        $fallbackType = 'array'
    ) {
        $this->fallbackType = $fallbackType;
        $this->types = new ArrayCollection($types);
    }

    /**
     * @param string $soapFunction
     * @return string
     */
    public function getResultType($soapFunction)
    {
        return $this->types->get($soapFunction) ?: $this->fallbackType;
    }
}
