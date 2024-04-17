<?php
/**
 * GetCitiesByCountryHydrator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:35
 */

namespace Webit\SoapApi\Features\GlobalWeather\Hydrator;

use Doctrine\Common\Cache\ArrayCache;
use Webit\SoapApi\Features\GlobalWeather\City;
use Webit\SoapApi\Features\GlobalWeather\Country;
use Webit\SoapApi\Hydrator\Hydrator;

class GetCitiesByCountryHydrator implements Hydrator
{
    /**
     * @var ArrayCache
     */
    private $countryCache;

    public function __construct()
    {
        $this->countryCache = new ArrayCache();
    }

    /**
     * @param \stdClass|array $result
     * @param string $soapFunction
     * @return mixed
     */
    public function hydrateResult($result, $soapFunction)
    {
        $xml = $result->GetCitiesByCountryResult;
        $simpleXml = new \SimpleXMLElement($xml);

        $cities = array();
        /** @var \SimpleXMLElement $table */
        foreach ($simpleXml->children() as $table) {

            $cities[] = new City(
                $this->hydrateCountry($table),
                (string) $table->children()->City
            );
        }

        return $cities;
    }

    /**
     * @param \SimpleXMLElement $table
     * @return Country
     */
    private function hydrateCountry(\SimpleXMLElement $table)
    {
        $countryName = (string) $table->children()->Country;
        if (! $this->countryCache->contains($countryName)) {
            $country = new Country($countryName);
            $this->countryCache->save($countryName, $country);
            return $country;
        }

        return $this->countryCache->fetch($countryName);
    }
}
