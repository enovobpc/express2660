<?php
/**
 * SoapApiExecutorBuilderTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 15:04
 */

namespace Webit\SoapApi\Tests\Executor;

use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\Tests\AbstractTest;

class ExecutorBuilderTest extends AbstractTest
{
    /**
     * @var SoapApiExecutorBuilder
     */
    private $builder;

    protected function setUp()
    {
        $wsdl = __DIR__.'/../Resources/test-wsdl.xml';
        $this->builder = SoapApiExecutorBuilder::create();
        $this->builder->setWsdl(
            $wsdl
        );
    }

    /**
     * @test
     */
    public function shouldBuildRawExecutor()
    {
        $this->assertInstanceOf(
            'Webit\SoapApi\Executor\RawExecutor',
            $this->builder->build()
        );
    }

    /**
     * @test
     */
    public function shouldBuildInputNormalisingExecutor()
    {
        $this->builder->setInputNormaliser($this->mockInputNormaliser());

        $this->assertInstanceOf(
            'Webit\SoapApi\Executor\InputNormalisingExecutor',
            $this->builder->build()
        );
    }

    /**
     * @test
     */
    public function shouldBuildHydratingExecutor()
    {
        $this->builder->setHydrator($this->mockHydrator());

        $this->assertInstanceOf(
            'Webit\SoapApi\Executor\ResultHydratingExecutor',
            $this->builder->build()
        );
    }

    /**
     * @test
     */
    public function shouldAllowSetSoapClient()
    {
        $this->builder->setSoapClient($this->mockSoapClient());
    }
}
