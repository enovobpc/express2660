<?php
/**
 * ChainHydratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 21, 2015, 16:41
 */

namespace Webit\SoapApi\Tests\Hydrator;

use Webit\SoapApi\Hydrator\ChainHydrator;
use Webit\SoapApi\Tests\AbstractTest;

class ChainHydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldGoThroughHydrators()
    {
        $hydrators = array(
            $this->mockHydrator(),
            $this->mockHydrator(),
        );

        $input = new \stdClass();
        $soapFunction = 'any';

        $result1 = new \stdClass();
        $result2 = new \stdClass();

        $chainHydrator = new ChainHydrator($hydrators);

        $hydrators[0]->shouldReceive('hydrateResult')->with($input, $soapFunction)->andReturn($result1)->once();
        $hydrators[1]->shouldReceive('hydrateResult')->with($result1, $soapFunction)->andReturn($result2)->once();

        $this->assertSame($result2, $chainHydrator->hydrateResult($input, $soapFunction));
    }
}
