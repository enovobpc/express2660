<?php
/**
 * SoapApiExecutorBuilder.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 15, 2015, 13:36
 */

namespace Webit\SoapApi\Executor;

use Webit\SoapApi\Executor\Exception\MissingSoapClientFactoryException;
use Webit\SoapApi\Hydrator\Hydrator;
use Webit\SoapApi\Input\InputNormaliser;
use Webit\SoapApi\SoapClient\SoapClientBuilder;
use Webit\SoapApi\SoapClient\SoapClientFactory;

class SoapApiExecutorBuilder
{
    /**
     * @var SoapClientBuilder
     */
    private $soapClientBuilder;

    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var InputNormaliser
     */
    private $inputNormaliser;

    /**
     * @var Hydrator
     */
    private $hydrator;

    public function __construct()
    {
        $this->soapClientBuilder = SoapClientBuilder::create();
    }

    /**
     * @return SoapApiExecutorBuilder
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param \SoapClient $soapClient
     */
    public function setSoapClient(\SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * @param InputNormaliser $inputNormaliser
     */
    public function setInputNormaliser($inputNormaliser)
    {
        $this->inputNormaliser = $inputNormaliser;
    }

    /**
     * @param Hydrator $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @return SoapApiExecutor
     */
    public function build()
    {
        $client = $this->soapClient ?: $this->soapClientBuilder->build();

        $executor = new RawExecutor($client);
        if ($this->inputNormaliser) {
            $executor = new InputNormalisingExecutor($this->inputNormaliser, $executor);
        }

        if ($this->hydrator) {
            $executor = new ResultHydratingExecutor($this->hydrator, $executor);
        }

        return $executor;
    }

    /**
     * @param string $wsdl
     */
    public function setWsdl($wsdl)
    {
        $this->soapClientBuilder->setWsdl($wsdl);
    }

    /**
     * @param array $options
     */
    public function setSoapClientOptions(array $options)
    {
        $this->soapClientBuilder->setOptions($options);
    }
}
