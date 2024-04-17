<?php
/**
 * FrontHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:40
 */

namespace Webit\SoapApi\Hydrator;

use Doctrine\Common\Collections\ArrayCollection;

class FrontHydrator implements Hydrator
{
    /**
     * @var Hydrator[]|ArrayCollection
     */
    private $hydrators;

    /**
     * @var Hydrator
     */
    private $fallbackHydrator;

    /**
     * FrontHydrator constructor.
     * @param Hydrator[] $hydrators
     * @param Hydrator $fallbackHydrator
     */
    public function __construct(array $hydrators, Hydrator $fallbackHydrator = null)
    {
        $this->hydrators = new ArrayCollection($hydrators);
        $this->fallbackHydrator = $fallbackHydrator ?: new VoidHydrator();
    }

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        /** @var Hydrator $hydrator */
        $hydrator = $this->hydrators->get($soapFunction) ?: $this->fallbackHydrator;

        return $hydrator->hydrateResult($result, $soapFunction);
    }
}
