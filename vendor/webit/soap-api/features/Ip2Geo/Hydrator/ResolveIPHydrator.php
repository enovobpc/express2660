<?php
/**
 * ResolveIPHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 17:24
 */

namespace Webit\SoapApi\Features\Ip2Geo\Hydrator;

use Webit\SoapApi\Features\Ip2Geo\GeoLocation;
use Webit\SoapApi\Hydrator\Hydrator;

class ResolveIPHydrator implements Hydrator
{

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        $result = isset($result->ResolveIPResult) ? $result->ResolveIPResult : null;
        if (! $result) {
            return null;
        }

        return new GeoLocation(
            $result->City,
            $result->StateProvince,
            $result->Country,
            $result->Organization,
            $result->Latitude,
            $result->Longitude,
            $result->AreaCode,
            $result->TimeZone,
            $result->HasDaylightSavings,
            $result->Certainty,
            $result->RegionName,
            $result->CountryCode
        );
    }
}
