<?php
/**
 * Weather.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:20
 */

namespace Webit\SoapApi\Features\GlobalWeather;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Weather
 * @JMS\XmlRoot("CurrentWeather")
 */
class Weather
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Location")
     */
    private $location;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Time")
     */
    private $time;

    /**
     * @var string
     * @JMS\Type("array<string>")
     * @JMS\XmlList(entry="Wind")
     */
    private $wind;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Visibility")
     */
    private $visibility;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("SkyConditions")
     */
    private $skyConditions;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Temperature")
     */
    private $temperature;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("DewPoint")
     */
    private $dewPoint;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("RelativeHumidity")
     */
    private $relativeHumidity;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Pressure")
     */
    private $pressure;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("Status")
     */
    private $status;

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getWind()
    {
        return isset($this->wind[0]) ? $this->wind[0] : null;
    }

    /**
     * @return string
     */
    public function getWindChill()
    {
        return isset($this->wind[1]) ? $this->wind[1] : null;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @return string
     */
    public function getSkyConditions()
    {
        return $this->skyConditions;
    }

    /**
     * @return string
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @return string
     */
    public function getDewPoint()
    {
        return $this->dewPoint;
    }

    /**
     * @return string
     */
    public function getRelativeHumidity()
    {
        return $this->relativeHumidity;
    }

    /**
     * @return string
     */
    public function getPressure()
    {
        return $this->pressure;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
