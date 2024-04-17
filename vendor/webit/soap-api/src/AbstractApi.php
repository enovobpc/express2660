<?php
/**
 * AbstractApi.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on Nov 25, 2014, 15:55
 */

namespace Webit\SoapApi;

use Webit\SoapApi\Executor\SoapApiExecutor;

/**
 * Class AbstractApi
 * @package Webit\SoapApi
 */
abstract class AbstractApi
{
    /**
     * @var SoapApiExecutor
     */
    private $executor;

    /**
     * @param SoapApiExecutor $soapApiExecutor
     */
    public function __construct(SoapApiExecutor $soapApiExecutor)
    {
        $this->executor = $soapApiExecutor;
    }

    /**
     * @param string $soapFunction
     * @param mixed $input
     * @return mixed
     */
    protected function doRequest($soapFunction, $input = null)
    {
        return $this->executor->executeSoapFunction($soapFunction, $input);
    }
}
