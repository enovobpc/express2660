<?php
/**
 * ArrayHydratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 15, 2015, 11:43
 *
 */

namespace Webit\SoapApi\Tests\Hydrator;

use Webit\SoapApi\Hydrator\ArrayHydrator;
use Webit\SoapApi\Hydrator\Hydrator;
use Webit\SoapApi\Tests\AbstractTest;
use Webit\SoapApi\Util\StdClassToArray;

class ArrayHydratorTest extends AbstractTest
{
    /**
     * @var ArrayHydrator
     */
    private $hydrator;

    /**
     * @var Hydrator|\Mockery\MockInterface
     */
    private $innerHydrator;

    protected function setUp()
    {
        $this->innerHydrator = $this->mockHydrator();

        $this->hydrator = new ArrayHydrator(
            new StdClassToArray(),
            $this->innerHydrator
        );
    }

    /**
     * @test
     * @dataProvider getHydrationData
     * @param $input
     * @param $expectedResult
     */
    public function shouldHydrateNonScalarResultToArray($input, $expectedResult)
    {
        $this->innerHydrator->shouldReceive('hydrateResult')->andReturnUsing(function ($input) {
            return $input;
        });

        $this->assertEquals(
            $expectedResult,
            $this->hydrator->hydrateResult($input, 'any')
        );
    }

    public function getHydrationData()
    {
        $input = new \stdClass();
        $input->a = "abc";
        $input->b = new \stdClass();
        $input->b->a = new \stdClass();
        $input->b->b = array_fill(0, 10, new \stdClass());
        $input->c = new \stdClass();
        $input->c->a = 12.5;
        $input->c->b = true;

        $expectedResult = array(
            'a' => 'abc',
            'b' => array(
                'a' => array(),
                'b' => array_fill(0, 10, array())
            ),
            'c' => array(
                'a' => 12.5,
                'b' => true
            )
        );

        return array(
            array($input, $expectedResult),
            array(
                array($input, $input),
                array($expectedResult, $expectedResult)
            ),
            array(true, true),
            array("string", "string")
        );
    }
}
