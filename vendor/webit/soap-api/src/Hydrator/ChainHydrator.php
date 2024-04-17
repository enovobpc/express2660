<?php
/**
 * ChainHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 12 21, 2015, 16:41
 * Copyright (C) 8x8
 */

namespace Webit\SoapApi\Hydrator;

class ChainHydrator implements Hydrator
{
    /**
     * @var Hydrator[]
     */
    private $hydrators;

    /**
     * ChainHydrator constructor.
     * @param \Webit\SoapApi\Hydrator\Hydrator[] $hydrators
     */
    public function __construct(array $hydrators)
    {
        $this->hydrators = $hydrators;
    }

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        foreach ($this->hydrators as $hydrator) {
            $result = $hydrator->hydrateResult($result, $soapFunction);
        }

        return $result;
    }
}
