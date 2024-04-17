<?php
/**
 * SerializationContextFactory.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 14:47
 */

namespace Webit\SoapApi\Input\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;

class FrontSerializationContextFactory
{
    /**
     * @var SerializationContextFactory[]|ArrayCollection
     */
    private $factories;

    /**
     * @var SerializationContextFactory
     */
    private $fallbackFactory;

    /**
     * FrontSerializationContextFactory constructor.
     * @param SerializationContextFactory[] $factories
     * @param SerializationContextFactory $fallbackFactory
     */
    public function __construct(array $factories, SerializationContextFactory $fallbackFactory = null)
    {
        $this->factories = new ArrayCollection($factories);
        $this->fallbackFactory = $fallbackFactory ?: new StaticSerializationContextFactory();
    }

    /**
     * @param string $soapFunction
     * @return SerializationContext
     */
    public function createContext($soapFunction)
    {
        $factory = $this->factories->get($soapFunction) ?: $this->fallbackFactory;

        return $factory->createContext($soapFunction);
    }
}
