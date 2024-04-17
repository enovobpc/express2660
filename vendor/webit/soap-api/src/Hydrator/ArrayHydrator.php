<?php
/**
 * ArrayHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 15, 2015, 11:35
 */

namespace Webit\SoapApi\Hydrator;

use Webit\SoapApi\Util\StdClassToArray;

class ArrayHydrator implements Hydrator
{
    /**
     * @var StdClassToArray
     */
    private $stcClassToArray;

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * ArrayHydrator constructor.
     * @param StdClassToArray $stcClassToArray
     */
    public function __construct(
        StdClassToArray $stcClassToArray,
        Hydrator $hydrator = null
    ) {
        $this->stcClassToArray = $stcClassToArray;
        $this->hydrator = $hydrator ?: new VoidHydrator();
    }

    /**
     * Converts stdClass or stdClass[] to array recursively
     *
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        $result = $this->hydrator->hydrateResult($result, $soapFunction);
        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $hydrated[$key] = $this->hydrateResult($value, $soapFunction);
            }

            return $hydrated;
        }

        if ($result instanceof \stdClass) {
            return $this->stcClassToArray->toArray($result);
        }

        return $result;
    }
}
