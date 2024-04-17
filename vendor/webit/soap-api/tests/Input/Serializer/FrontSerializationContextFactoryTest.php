<?php
/**
 * FrontSerializationContextFactoryTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 15:39
 */

namespace Webit\SoapApi\Tests\Input\Serializer;

use JMS\Serializer\SerializationContext;
use Webit\SoapApi\Input\Serializer\FrontSerializationContextFactory;
use Webit\SoapApi\Tests\AbstractTest;

class FrontSerializationContextFactoryTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldCreateContextBySoapFunction()
    {
        $factories = array(
            'func1' => $this->mockSerializationContextFactory(),
            'func2' => $this->mockSerializationContextFactory()
        );
        $context = SerializationContext::create();

        $factory = new FrontSerializationContextFactory($factories);

        $factories['func2']->shouldReceive('createContext')->with('func2')->once()->andReturn($context);

        $this->assertSame(
            $context,
            $factory->createContext('func2')
        );
    }

    /**
     * @test
     */
    public function shouldUseFallbackFactoryWhenSpecificNotFound()
    {
        $factories = array();
        $fallbackFactory = $this->mockSerializationContextFactory();
        $context = SerializationContext::create();

        $factory = new FrontSerializationContextFactory($factories, $fallbackFactory);

        $fallbackFactory->shouldReceive('createContext')->with('func2')->once()->andReturn($context);

        $this->assertSame(
            $context,
            $factory->createContext('func2')
        );
    }
}
