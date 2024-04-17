<?php
/**
 * AbstractTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 14, 2015, 16:31
 */

namespace Webit\SoapApi\Tests;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\SoapClient\SoapClientFactory
     */
    protected function mockSoapClientFactory()
    {
        return \Mockery::mock('Webit\SoapApi\SoapClient\SoapClientFactory');
    }

    /**
     * @return \Mockery\MockInterface|\SoapClient
     */
    protected function mockSoapClient()
    {
        return \Mockery::mock('\SoapClient');
    }

    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\Executor\SoapApiExecutor
     */
    protected function mockApiExecutor()
    {
        return \Mockery::mock('Webit\SoapApi\Executor\SoapApiExecutor');
    }

    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\Input\InputNormaliser
     */
    protected function mockInputNormaliser()
    {
        return \Mockery::mock('Webit\SoapApi\Input\InputNormaliser');
    }

    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\Hydrator\Hydrator
     */
    protected function mockHydrator()
    {
        return \Mockery::mock('\Webit\SoapApi\Hydrator\Hydrator');
    }

    /**
     * @return \Mockery\MockInterface|\JMS\Serializer\Serializer
     */
    protected function mockJmsSerializer()
    {
       return \Mockery::mock('JMS\Serializer\Serializer');
    }

    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\Hydrator\Serializer\ResultTypeMap
     */
    protected function mockResultTypeMap()
    {
        return \Mockery::mock('Webit\SoapApi\Hydrator\Serializer\ResultTypeMap');
    }

    /**
     * @return \Mockery\MockInterface|\Webit\SoapApi\Input\Serializer\SerializationContextFactory
     */
    protected function mockSerializationContextFactory()
    {
        return \Mockery::mock('\Webit\SoapApi\Input\Serializer\SerializationContextFactory');
    }
}
