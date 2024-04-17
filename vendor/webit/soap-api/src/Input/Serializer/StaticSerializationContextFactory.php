<?php
/**
 * SerializationContextPrototype.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 14:58
 */

namespace Webit\SoapApi\Input\Serializer;

use JMS\Serializer\SerializationContext;

class StaticSerializationContextFactory implements SerializationContextFactory
{
    /**
     * @var bool
     */
    private $serializeNull = true;

    /**
     * @var string[]
     */
    private $groups;

    /**
     * @var null
     */
    private $version;

    /**
     * @var array
     */
    private $attributes;

    /**
     * StaticSerializationContextFactory constructor.
     * @param array $groups
     * @param bool $serializeNull
     * @param null $version
     * @param array $attributes
     */
    public function __construct(
        array $groups = array(),
        $serializeNull = false,
        $version = null,
        array $attributes = array()
    ) {
        $this->groups = $groups;
        $this->serializeNull = $serializeNull;
        $this->version = $version;
        $this->attributes = $attributes;
    }

    /**
     * @param string $soapFunction
     * @return SerializationContext
     */
    public function createContext($soapFunction)
    {
        $context = SerializationContext::create();

        $context->setSerializeNull($this->serializeNull);
        if ($this->groups) {
            $context->setGroups($this->groups);
        }

        if ($this->version) {
            $context->setVersion($this->version);
        }

        foreach ($this->attributes as $key => $value) {
            $context->setAttribute($key, $value);
        }

        return $context;
    }
}
