<?php
/**
 * SoapApi.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 09:20
 */

namespace Webit\SoapApi\Features\GlobalWeather;

use Webit\SoapApi\Executor\SoapApiExecutor;

class GlobalWeatherClient
{
    /**
     * @var SoapApiExecutor
     */
    private $executor;

    /**
     * SoapApi constructor.
     * @param SoapApiExecutor $executor
     */
    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Country $country
     * @return City[] cities list
     */
    public function getCities(Country $country)
    {
        $cities = $this->executor->executeSoapFunction(
            'GetCitiesByCountry',
            $country
        );

        return $cities;
    }

    /**
     * @param City $city
     * @return Weather
     */
    public function getWeather(City $city)
    {
        $weather = $this->executor->executeSoapFunction(
            'GetWeather',
            $city
        );

        return $weather;
    }
}
