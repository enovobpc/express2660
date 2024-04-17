<?php
/**
 * InputNormalisingExecutor.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 14, 2015, 14:00
 */

namespace Webit\SoapApi\Executor;

use Webit\SoapApi\Executor\Exception\NormalisationException;
use Webit\SoapApi\Input\InputNormaliser;

class InputNormalisingExecutor implements SoapApiExecutor
{
    /**
     * @var InputNormaliser
     */
    private $inputNormaliser;

    /**
     * @var SoapApiExecutor
     */
    private $soapApiExecutor;

    /**
     * InputNormalisingExecutor constructor.
     * @param InputNormaliser $inputNormaliser
     * @param SoapApiExecutor $soapApiExecutor
     */
    public function __construct(InputNormaliser $inputNormaliser, SoapApiExecutor $soapApiExecutor)
    {
        $this->inputNormaliser = $inputNormaliser;
        $this->soapApiExecutor = $soapApiExecutor;
    }

    /**
     * @param string $soapFunction
     * @param mixed $input
     * @return mixed
     */
    public function executeSoapFunction(
        $soapFunction,
        $input = null
    ) {
        try {
            $normalisedInput = $this->inputNormaliser->normaliseInput($soapFunction, $input);
        } catch (\Exception $e) {
            throw new NormalisationException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->soapApiExecutor->executeSoapFunction($soapFunction, $normalisedInput);
    }
}
