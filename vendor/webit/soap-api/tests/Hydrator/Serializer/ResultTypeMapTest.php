<?php
/**
 * ResultMapTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 17:17
 */

namespace Webit\SoapApi\Tests\Hydrator\Serializer;

use Webit\SoapApi\Hydrator\Serializer\ResultTypeMap;
use Webit\SoapApi\Tests\AbstractTest;

/**
 * Class ResultMapTest
 * @package Webit\SoapApi\Tests\Hydrator\Result
 */
class ResultMapTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldGetTypeByFunction()
    {
        $map = new ResultTypeMap(
            array(
                'fnc1' => 'array',
                'fnc2' => 'string'
            )
        );

        $this->assertEquals('string', $map->getResultType('fnc2'));
    }

    /**
     * @test
     */
    public function shouldUseFallbackTypeIfSpecificNotFound()
    {
        $map = new ResultTypeMap(
            array(),
            'string'
        );

        $this->assertEquals('string', $map->getResultType('fnc2'));
    }

}
