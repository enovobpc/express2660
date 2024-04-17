<?php
/**
 * Country.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:27
 */

namespace Webit\SoapApi\Features\GlobalWeather;

use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\Annotation as JMS;

class Country
{
    /**
     * @var string
     */
    private $name;

    /**
     * Country constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @JMS\HandlerCallback("json", direction = "serialization")
     * @param GenericSerializationVisitor $visitor
     * @return mixed|string
     */
    public function serializationHandler(GenericSerializationVisitor $visitor, $type, $context)
    {
        return $visitor->visitString($this, array('string'), $context);
    }
}
