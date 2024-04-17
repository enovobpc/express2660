<?php
/**
 * StdClassToArray.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 15, 2015, 11:32
 */

namespace Webit\SoapApi\Util;

class StdClassToArray
{
    /**
     * @param \stdClass $stdClass
     * @return array
     */
    public function toArray(\stdClass $stdClass)
    {
        return $this->castToArray($stdClass);
    }

    /**
     * @param mixed $input
     * @return array|mixed
     */
    private function castToArray($input)
    {
        if (is_scalar($input) || is_null($input)) {
            return $input;
        }

        if ($input instanceof \stdClass) {
            $input = (array) $input;
        }

        foreach ($input as &$value) {
            $value = $this->castToArray($value);
        }

        return $input;
    }
}
