<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use JMS\Serializer\SerializerBuilder;
use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\Features\GlobalWeather\City;
use Webit\SoapApi\Features\GlobalWeather\Country;
use Webit\SoapApi\Features\GlobalWeather\Hydrator\GetCitiesByCountryHydrator;
use Webit\SoapApi\Features\GlobalWeather\Hydrator\GetWeatherHydrator;
use Webit\SoapApi\Features\GlobalWeather\GlobalWeatherClient;
use Webit\SoapApi\Features\GlobalWeather\Normaliser\GetCitiesByCountryNormaliser;
use Webit\SoapApi\Features\GlobalWeather\Normaliser\GetWeatherNormaliser;
use Webit\SoapApi\Hydrator\FrontHydrator;
use Webit\SoapApi\Input\FrontInputNormaliser;

/**
 * Defines application features from the specific context.
 */
class GlobalWeatherContext implements Context, SnippetAcceptingContext
{
    /**
     * @var bool
     */
    private $mockSoapClient;

    /**
     * @var \Webit\SoapApi\Features\GlobalWeather\GlobalWeatherClient
     */
    private $soapApiClient;

    /**
     * @var mixed
     */
    private $result;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($mockSoapClient = true)
    {
        $this->mockSoapClient = (bool) $mockSoapClient;

        $loader = require __DIR__ .'/../../vendor/autoload.php';
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
    }

    /**
     * @Given Global Weather Client API
     */
    public function globalWeatherClientApi()
    {
        $jmsSerializer = SerializerBuilder::create()->build();

        $builder = SoapApiExecutorBuilder::create();

        // 1. Set SoapClient Factory
        $builder->setSoapClient(
            $this->mockSoapClient ? $this->createMockedClient() : $this->createRealClient()
        );

        // 2. Configure input normaliser
        $normaliser = new FrontInputNormaliser(array(
            'GetCitiesByCountry' => new GetCitiesByCountryNormaliser(), // simple
            'GetWeather' => new GetWeatherNormaliser($jmsSerializer) // based on JMSSerializer
        ));
        $builder->setInputNormaliser($normaliser);

        // 3. Configure hydrators
        $hydrator = new FrontHydrator(array(
            'GetCitiesByCountry' => new GetCitiesByCountryHydrator(), // based on SimpleXML
            'GetWeather' => new GetWeatherHydrator($jmsSerializer) // based on JMSSerializer
        ));
        $builder->setHydrator($hydrator);

        $executor = $builder->build();
        $this->soapApiClient = new GlobalWeatherClient(
            $executor
        );
    }

    /**
     * @When I ask for list of cities in :arg1
     */
    public function iAskForListOfCitiesIn($country)
    {
        $this->result = $this->soapApiClient->getCities(new Country($country));
    }

    /**
     * @Then List of the cities should be returned
     */
    public function listOfTheCitiesShouldBeReturned()
    {
        \PHPUnit_Framework_Assert::assertInternalType('array', $this->result);
        foreach ($this->result as $city) {
            \PHPUnit_Framework_Assert::assertInstanceOf('Webit\SoapApi\Features\GlobalWeather\City', $city);
        }
    }

    /**
     * @When I ask for weather for :city in :country
     */
    public function iAskForWeatherForIn($cityName, $country)
    {
        $city = new City(new Country($country), $cityName);
        $this->result = $this->soapApiClient->getWeather($city);
    }

    /**
     * @Then Current weather should be returned
     */
    public function currentWeatherShouldBeReturned()
    {
        \PHPUnit_Framework_Assert::assertInstanceOf('\Webit\SoapApi\Features\GlobalWeather\Weather', $this->result);
    }

    /**
     * @return \SoapClient
     */
    private function createRealClient()
    {
        return \Webit\SoapApi\SoapClient\SoapClientBuilder::create()
            ->setWsdl("http://www.webservicex.net/globalweather.asmx?WSDL")
            ->setOptions(array(
                "exceptions" => true
            ))
        ->build();
    }

    /**
     * @return \Mockery\MockInterface|\SoapClient
     */
    private function createMockedClient()
    {
        $client = \Mockery::mock('\SoapClient');

        $client->shouldReceive('__soapCall')->with('GetCitiesByCountry', \Mockery::any())->andReturn(
            $this->createCitiesResult()
        );

        $client->shouldReceive('__soapCall')->with('GetWeather',  \Mockery::any())->andReturn(
            $this->createWeatherResult()
        );

        return $client;
    }

    private function createCitiesResult()
    {
        $result = new \stdClass();
        $result->GetCitiesByCountryResult = file_get_contents(__DIR__.'/../GlobalWeather/Fixtures/get-cities.xml');

        return $result;
    }

    private function createWeatherResult()
    {
        $result = new \stdClass();
        $result->GetWeatherResult = file_get_contents(__DIR__.'/../GlobalWeather/Fixtures/get-weather.xml');

        return $result;
    }
}
