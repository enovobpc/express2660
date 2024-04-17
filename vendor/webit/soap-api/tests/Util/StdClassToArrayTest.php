<?php
/**
 * StdClassToArrayTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 15, 2015, 11:39
 *
 */

namespace Webit\SoapApi\Tests\Util;

use Webit\SoapApi\Tests\AbstractTest;
use Webit\SoapApi\Util\StdClassToArray;

class StdClassToArrayTest extends AbstractTest
{
    /**
     * @var StdClassToArray
     */
    private $stdClassToArray;

    protected function setUp()
    {
        $this->stdClassToArray = new StdClassToArray();
    }

    /**
     * @param \stdClass $input
     * @param $expectedResult
     * @test
     * @dataProvider getStdClassToArrayData
     */
    public function shouldConvertStdClassToArray(\stdClass $input, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stdClassToArray->toArray($input)
        );
    }

    public function getStdClassToArrayData()
    {
        $input = new \stdClass();
        $input->a = "abc";
        $input->b = new \stdClass();
        $input->b->a = new \stdClass();
        $input->b->b = array_fill(0, 10, new \stdClass());
        $input->c = new \stdClass();
        $input->c->a = 12.5;
        $input->c->b = true;

        $input->d = new \stdClass();
        $input->d->a = 12.5;
        $input->d->b = null;

        $expectedResult = array(
            'a' => 'abc',
            'b' => array(
                'a' => array(),
                'b' => array_fill(0, 10, array())
            ),
            'c' => array(
                'a' => 12.5,
                'b' => true
            ),
            'd' => array(
                'a' => 12.5,
                'b' => null
            )
        );

        return array(
            array($input, $expectedResult)
        );
    }
}
