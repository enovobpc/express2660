<?php
/**
 * GeoLocation.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 17:12
 */

namespace Webit\SoapApi\Features\Ip2Geo;

class GeoLocation
{
    private $city;

    private $state;

    private $country;

    private $organization;

    private $latitute;

    private $longitude;

    private $areaCode;

    private $timeZone;

    private $hasDaylightSavings;

    private $certainty;

    private $regionName;

    private $countryCode;

    /**
     * GeoLocation constructor.
     * @param $city
     * @param $state
     * @param $country
     * @param $organization
     * @param $latitute
     * @param $longitude
     * @param $areaCode
     * @param $timeZone
     * @param $hasDaylightSavings
     * @param $certainty
     * @param $regionName
     * @param $countryCode
     */
    public function __construct(
        $city = null,
        $state = null,
        $country = null,
        $organization = null,
        $latitute = null,
        $longitude = null,
        $areaCode = null,
        $timeZone = null,
        $hasDaylightSavings = null,
        $certainty = null,
        $regionName = null,
        $countryCode = null
    ) {
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->organization = $organization;
        $this->latitute = $latitute;
        $this->longitude = $longitude;
        $this->areaCode = $areaCode;
        $this->timeZone = $timeZone;
        $this->hasDaylightSavings = $hasDaylightSavings;
        $this->certainty = $certainty;
        $this->regionName = $regionName;
        $this->countryCode = $countryCode;
    }

    /**
     * @return null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return null
     */
    public function getLatitute()
    {
        return $this->latitute;
    }

    /**
     * @return null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return null
     */
    public function getAreaCode()
    {
        return $this->areaCode;
    }

    /**
     * @return null
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @return null
     */
    public function getHasDaylightSavings()
    {
        return $this->hasDaylightSavings;
    }

    /**
     * @return null
     */
    public function getCertainty()
    {
        return $this->certainty;
    }

    /**
     * @return null
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @return null
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
