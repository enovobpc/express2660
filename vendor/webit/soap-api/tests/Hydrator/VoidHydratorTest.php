<?php
/**
 * VoidHydratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:51
 *
 */

namespace Webit\SoapApi\Tests\Hydrator;

use Webit\SoapApi\Hydrator\VoidHydrator;
use Webit\SoapApi\Tests\AbstractTest;

class VoidHydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldReturnSameInputResult()
    {
        $function = 'fnc';
        $result = 'result';
        $hydrator = new VoidHydrator();

        $this->assertEquals($result, $hydrator->hydrateResult($result, $function));
    }
}
