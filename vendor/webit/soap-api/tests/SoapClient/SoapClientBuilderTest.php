<?php
/**
 * SoapClientBuilderTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 14:57
 */

namespace Webit\SoapApi\Tests\SoapClient;

use Webit\SoapApi\SoapClient\SoapClientBuilder;
use Webit\SoapApi\Tests\AbstractTest;

class SoapClientBuilderTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldBuildSoapClient()
    {
        $builder = SoapClientBuilder::create();
        $builder->setWsdl(__DIR__.'/../Resources/test-wsdl.xml');
        $builder->setOptions(array('exceptions' => true));
        $builder->setOption('cache_wsdl', WSDL_CACHE_NONE);

        $client = $builder->build();
        $this->assertInstanceOf('\SoapClient', $client);
    }
}
