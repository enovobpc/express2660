<?php
/**
 * FrontInputNormaliser.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:43
 */

namespace Webit\SoapApi\Input;

use Doctrine\Common\Collections\ArrayCollection;
use Webit\SoapApi\Input\Exception\NormalisationException;

class FrontInputNormaliser implements InputNormaliser
{
    /**
     * @var InputNormaliser[]|ArrayCollection
     */
    private $normalisers;

    /**
     * @var InputNormaliser
     */
    private $fallbackNormaliser;

    /**
     * FrontInputNormalizer constructor.
     * @param InputNormaliser[] $normalisers
     * @param InputNormaliser $fallbackNormaliser
     */
    public function __construct(array $normalisers, InputNormaliser $fallbackNormaliser = null)
    {
        $this->normalisers = new ArrayCollection($normalisers);
        $this->fallbackNormaliser = $fallbackNormaliser ?: new VoidInputNormaliser();
    }


    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        $normaliser = $this->normalisers->get($soapFunction) ?: $this->fallbackNormaliser;

        return $normaliser->normaliseInput($soapFunction, $arguments);
    }
}
