<?php
/**
 * File: SoapApiExecutorInterface.php
 * Created at: 2014-11-25 18:28
 */
 
namespace Webit\SoapApi\Executor;

/**
 * Interface SoapApiExecutorInterface
 * @author Daniel Bojdo <daniel@bojdo.eu>
 */
interface SoapApiExecutor
{
    /**
     * @param string $soapFunction
     * @param mixed $input
     * @throws \Webit\SoapApi\Exception\SoapApiException
     * @return mixed
     */
    public function executeSoapFunction($soapFunction, $input = null);
}
