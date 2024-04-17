<?php
/**
 * HydratorSerializerTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 16:43
 */

namespace Webit\SoapApi\Tests\Hydrator;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Webit\SoapApi\Hydrator\HydratorSerializerBased;
use Webit\SoapApi\Hydrator\Serializer\ResultTypeMap;
use Webit\SoapApi\Tests\AbstractTest;

/**
 * Class HydratorSerializerTest
 * @package Webit\SoapApi\Tests\Hydrator
 */
class HydratorSerializerBasedTest extends AbstractTest
{
    /**
     * @var Serializer|\Mockery\MockInterface
     */
    private $serializer;

    /**
     * @var ResultTypeMap|\Mockery\MockInterface
     */
    private $resultTypeMap;

    /**
     * @var HydratorSerializerBased
     */
    private $hydrator;

    protected function setUp()
    {
        $this->serializer = $this->mockJmsSerializer();
        $this->resultTypeMap = $this->mockResultTypeMap();

        $this->hydrator = new HydratorSerializerBased(
            $this->serializer,
            $this->resultTypeMap
        );
    }

    /**
     * @test
     */
    public function shouldHydrateWithSerializer()
    {
        $resolvedType = 'type';

        $func = 'func';
        $result = array('result');
        $hydrated = 'hydrated';

        $this->resultTypeMap->shouldReceive('getResultType')->with($func)->andReturn($resolvedType)->once();
        $this->serializer->shouldReceive('fromArray')->with($result, $resolvedType)->once()->andReturn($hydrated);

        $this->assertEquals(
            $hydrated,
            $this->hydrator->hydrateResult($result, $func)
        );
    }

    /**
     * @test
     */
    public function shouldApplyArrayCollectionFix()
    {
        $resolvedType = 'ArrayCollection<type>';

        $func = 'func';
        $result = array('result');
        $hydrated = array(
            'result' => 'x'
        );

        $this->resultTypeMap->shouldReceive('getResultType')->andReturn($resolvedType);
        $this->serializer->shouldReceive('fromArray')->andReturn($hydrated);

        $this->assertEquals(
            new ArrayCollection($hydrated),
            $this->hydrator->hydrateResult($result, $func)
        );
    }

    /**
     * @test
     * @expectedException \Webit\SoapApi\Hydrator\Exception\HydrationException
     */
    public function shouldWrapException()
    {
        $resolvedType = 'ArrayCollection<type>';

        $func = 'func';
        $result = array('result');

        $this->resultTypeMap->shouldReceive('getResultType')->andReturn($resolvedType);
        $this->serializer->shouldReceive('fromArray')->andThrow('\Exception');

        $this->hydrator->hydrateResult($result, $func);
    }

    /**
     * @test
     * @expectedException \Webit\SoapApi\Hydrator\Exception\HydrationException
     */
    public function shouldAcceptOnlyArrayResult()
    {
        $this->hydrator->hydrateResult('string', 'func');
    }
}
