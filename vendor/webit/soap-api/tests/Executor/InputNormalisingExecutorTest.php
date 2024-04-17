<?php
/**
 * InputNormalisingExecutorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 14, 2015, 17:03
 */

namespace Webit\SoapApi\Tests\Executor;

use Webit\SoapApi\Executor\InputNormalisingExecutor;
use Webit\SoapApi\Tests\AbstractTest;

class InputNormalisingExecutorTest extends AbstractTest
{
    /**
     * @var \Mockery\MockInterface|\Webit\SoapApi\Executor\SoapApiExecutor
     */
    private $innerExecutor;

    /**
     * @var \Mockery\MockInterface|\Webit\SoapApi\Input\InputNormaliser
     */
    private $inputNormaliser;

    /**
     * @var InputNormalisingExecutor
     */
    private $executor;

    protected function setUp()
    {
        $this->innerExecutor = $this->mockApiExecutor();
        $this->inputNormaliser = $this->mockInputNormaliser();
        $this->executor = new InputNormalisingExecutor(
            $this->inputNormaliser,
            $this->innerExecutor
        );
    }

    /**
     * @test
     */
    public function shouldNormaliseInputBeforeExecution()
    {
        $function = 'function';
        $input = array('rawinput' => true);
        $normalisedInput = array('normalizedinput' => true);
        $result = new \stdClass();

        $this->inputNormaliser->shouldReceive('normaliseInput')->with($function, $input)->andReturn($normalisedInput)->once();
        $this->innerExecutor->shouldReceive('executeSoapFunction')->with($function, $normalisedInput)->andReturn($result)->once();
        $this->assertEquals(
            $result,
            $this->executor->executeSoapFunction($function, $input)
        );
    }

    /**
     * @test
     * @expectedException \Webit\SoapApi\Executor\Exception\NormalisationException
     */
    public function shouldWrapNormalisationException()
    {
        $function = 'function';
        $input = array('rawinput' => true);

        $this->inputNormaliser->shouldReceive('normaliseInput')->with($function, $input)->andThrow('\Exception')->once();

        $this->executor->executeSoapFunction($function, $input);
    }
}
