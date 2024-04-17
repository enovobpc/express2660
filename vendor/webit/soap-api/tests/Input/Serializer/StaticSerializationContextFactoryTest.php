<?php
/**
 * StaticSerializationContextFactoryTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 15:30
 */

namespace Webit\SoapApi\Tests\Input\Serializer;

use JMS\Serializer\SerializationContext;
use Webit\SoapApi\Input\Serializer\StaticSerializationContextFactory;
use Webit\SoapApi\Tests\AbstractTest;

class StaticSerializationContextFactoryTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider getSerializationContextData
     */
    public function shouldCreateSerializationContext($groups, $serializeNull, $version, $attributes, $expectedContext)
    {
        $factory = new StaticSerializationContextFactory(
            $groups,
            $serializeNull,
            $version,
            $attributes
        );

        $this->assertEquals($expectedContext, $factory->createContext('func'));
    }

    public function getSerializationContextData()
    {
        $groups = array('a', 'b');
        $serializeNull = true;
        $version = '2.3';
        $attributes = array('a' => 'b');

        return array(
            array(
                array(),
                false,
                null,
                array(),
                SerializationContext::create()
            ),
            array(
                $groups,
                false,
                null,
                array(),
                SerializationContext::create()->setGroups($groups)
            ),
            array(
                $groups,
                $serializeNull,
                null,
                array(),
                SerializationContext::create()->setGroups($groups)->setSerializeNull($serializeNull)
            ),
            array(
                $groups,
                $serializeNull,
                $version,
                array(),
                SerializationContext::create()->setGroups($groups)->setSerializeNull($serializeNull)->setVersion($version)
            ),
            array(
                $groups,
                $serializeNull,
                $version,
                $attributes,
                SerializationContext::create()->setGroups($groups)->setSerializeNull($serializeNull)->setVersion($version)->setAttribute('a', 'b')
            ),
        );
    }
}
