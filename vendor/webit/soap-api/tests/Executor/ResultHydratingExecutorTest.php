<?php
/**
 * ResultHydratingExecutorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 14, 2015, 17:13
 *
 */

namespace Webit\SoapApi\Tests\Executor;

use Webit\SoapApi\Executor\ResultHydratingExecutor;
use Webit\SoapApi\Tests\AbstractTest;

class ResultHydratingExecutorTest extends AbstractTest
{
    /**
     * @var \Mockery\MockInterface|\Webit\SoapApi\Hydrator\Hydrator
     */
    private $hydrator;

    /**
     * @var \Mockery\MockInterface|\Webit\SoapApi\Executor\SoapApiExecutor
     */
    private $innerExecutor;

    /**
     * @var ResultHydratingExecutor
     */
    private $executor;

    protected function setUp()
    {
        $this->hydrator = $this->mockHydrator();
        $this->innerExecutor = $this->mockApiExecutor();

        $this->executor = new ResultHydratingExecutor($this->hydrator, $this->innerExecutor);
    }

    /**
     * @test
     */
    public function shouldHydrateResult()
    {
        $function = 'function';
        $input = array('input');
        $rawResult = array();
        $hydratedResult = new \stdClass();

        $this->innerExecutor->shouldReceive('executeSoapFunction')->with($function, $input)->andReturn($rawResult)->once();
        $this->hydrator->shouldReceive('hydrateResult')->with($rawResult, $function)->andReturn($hydratedResult)->once();

        $this->assertEquals(
            $hydratedResult,
            $this->executor->executeSoapFunction($function, $input)
        );
    }

    /**
     * @test
     * @expectedException \Webit\SoapApi\Executor\Exception\HydrationException
     */
    public function shouldWrapHydrationException()
    {
        $function = 'function';
        $input = array('input');
        $rawResult = array();

        $this->innerExecutor->shouldReceive('executeSoapFunction')->andReturn($rawResult);
        $this->hydrator->shouldReceive('hydrateResult')->andThrow('\Exception');

        $this->executor->executeSoapFunction($function, $input);
    }
}
