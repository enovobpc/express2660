<?php
/**
 * AbstractApiTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 15:45
 */

namespace Webit\SoapApi\Tests;

use Webit\SoapApi\AbstractApi;
use Webit\SoapApi\Executor\SoapApiExecutor;

class AbstractApiTest extends AbstractTest
{
    /**
     * @var \Mockery\MockInterface|SoapApiExecutor
     */
    private $executor;

    protected function setUp()
    {
        $this->executor = $this->mockApiExecutor();
    }

    /**
     * @test
     */
    public function shouldDelegateToExecutor()
    {
        $api = new TestApi($this->executor);

        $this->executor->shouldReceive('executeSoapFunction')->with('function', array('x'))->once();

        $api->test();
    }
}

class TestApi extends AbstractApi
{
    public function test()
    {
        return $this->doRequest('function', array('x'));
    }
}
