<?php
/**
 * City.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:26
 */

namespace Webit\SoapApi\Features\GlobalWeather;

use JMS\Serializer\Annotation as JMS;

class City
{
    /**
     * @var Country
     * @JMS\SerializedName("CountryName")
     */
    private $country;

    /**
     * @var string
     * @JMS\SerializedName("CityName")
     */
    private $name;

    /**
     * City constructor.
     * @param Country $country
     * @param string $name
     */
    public function __construct(Country $country, $name)
    {
        $this->country = $country;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
