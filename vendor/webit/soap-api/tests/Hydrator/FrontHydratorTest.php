<?php
/**
 * FrontHydratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:42
 *
 */

namespace Webit\SoapApi\Tests\Hydrator;

use Webit\SoapApi\Hydrator\FrontHydrator;
use Webit\SoapApi\Tests\AbstractTest;

class FrontHydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldHydrateBySoapFunctionName()
    {
        /** @var \Mockery\MockInterface[] $hydrators */
        $hydrators = array(
            'fnc1' => $this->mockHydrator(),
            'fnc2' => $this->mockHydrator()
        );

        $result = 'result';
        $hydratedResult = 'hydrated';

        $hydrator = new FrontHydrator($hydrators);
        $hydrators['fnc1']->shouldReceive('hydrateResult')->with($result, 'fnc1')->andReturn($hydratedResult)->once();

        $this->assertEquals($hydratedResult, $hydrator->hydrateResult($result, 'fnc1'));
    }

    /**
     * @test
     */
    public function shouldUseFallbackHydratorIfSpecificNotFound()
    {
        $hydrators = array();
        $fallbackHydrator = $this->mockHydrator();

        $result = 'result';
        $hydratedResult = 'hydrated';

        $hydrator = new FrontHydrator($hydrators, $fallbackHydrator);
        $fallbackHydrator->shouldReceive('hydrateResult')->with($result, 'fnc1')->andReturn($hydratedResult)->once();

        $this->assertEquals($hydratedResult, $hydrator->hydrateResult($result, 'fnc1'));
    }
}
