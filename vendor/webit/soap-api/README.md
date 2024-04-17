# SOAP API Library
This library provides set of tools to ease building clients / SDK's for web services based on SOAP.

## Installation

Composer: add the **webit/soap-api** into **composer.json**

```json
{
    "require": {
        "php": ">=5.4",
        "webit/soap-api": "~2.0"
    }
}
```

## Tests

```bash
./vendor/bin/phpunit
```

## Examples

Run Behat tests

```bash
./vendor/bin/behat
```

Then explore their implementation under ***features/***

## Usage

### Build your very first SDK

The heart of the library is ***SoapApiExecutor*** with its only method ***executeSoapFunction($soapFunction, $input)***.
It delegates to underlying ***\SoapClient***.

Let's provide SDK for IP2Geo Web Service (WSDL can be found [here](http://ws.cdyne.com/ip2geo/ip2geo.asmx?WSDL))

```php

<?php
namespace Webit\SoapApi\Features\Ip2Geo;

use Webit\SoapApi\Executor\SoapApiExecutor;

class Ip2GeoSimpleClient
{
    /** @var SoapApiExecutor */
    private $executor;

    /**
     * @param SoapApiExecutor $executor
     */
    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Ip $ip
     * @return GeoLocation
     */
    public function getGeoLocation(Ip $ip)
    {
        $result = $this->executor->executeSoapFunction(
            'ResolveIP',
            array(
                'ipAddress' => $ip,
                'licenseKey' => ''
            )
        );
        
        return $this->hydrateToGeoLocation($result);
    }
    
    /**
     * @param $result
     * @return null|GeoLocation
     */
    private function hydrateToGeoLocation($result)
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

// IP address wrapper
class Ip
{
    /** @var string */
    private $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function __toString()
    {
        return (string) $this->ip;
    }
}

```

To use our client we need to configure ***SoapApiExecutor*** then pass it into ***Ip2GeoSimpleClient*** constructor.

```php

<?php

use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\SoapClient\SoapClientSimpleFactory;

$builder = SoapApiExecutorBuilder::create();
$builder->setWsdl('http://ws.cdyne.com/ip2geo/ip2geo.asmx?WSDL');

$client = new \Webit\SoapApi\Features\Ip2Geo\Ip2GeoSimpleClient(
    $builder->build()
);

$result = $client->getGeoLocation(new Ip('8.8.8.8')); // returns GeoLocation instance

```

### Input Normalisation

Let's abstract transformation of our ***Ip*** to SOAP function arguments.

```php

<?php
namespace Webit\SoapApi\Features\Ip2Geo\Normaliser;

use Webit\SoapApi\Features\Ip2Geo\Ip;
use Webit\SoapApi\Input\Exception\NormalisationException;
use Webit\SoapApi\Input\InputNormaliser;

class ResolveIPNormaliser implements InputNormaliser
{

    /**
     * @param string $soapFunction
     * @param mixed $arguments
     * @throws NormalisationException
     * @return array
     */
    public function normaliseInput($soapFunction, $arguments)
    {
        if (! ($arguments instanceof Ip)) {
            throw new NormalisationException(__CLASS__ . ' requires arguments to be an instance of IP class.');
        }

        return array(
            'ipAddress' => (string) $arguments,
            'licenseKey' => ''
        );
    }
}

```

Then ***Ip2GeoInputNormalisingClient*** will look like:

```php

<?php
namespace Webit\SoapApi\Features\Ip2Geo;

use Webit\SoapApi\Executor\SoapApiExecutor;

class Ip2GeoInputNormalisingClient
{
    /** @var SoapApiExecutor */
    private $executor;

    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Ip $ip
     * @return GeoLocation
     */
    public function getGeoLocation(Ip $ip)
    {
        $result = $this->executor->executeSoapFunction('ResolveIP', $ip);

        return $this->hydrateToGeoLocation($result);
    }
    
    public function hydrateToGeoLocation($result)
    {
        // same as Ip2GeoSimpleClient
    }
}

```

Now we configure ***SoapApiExecutor*** to use ***ResolveIPNornaliser*** for given SOAP Function:

```php

<?php
use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\SoapClient\SoapClientSimpleFactory;

$builder = SoapApiExecutorBuilder::create();
$builder->setWsdl('http://ws.cdyne.com/ip2geo/ip2geo.asmx?WSDL');

$builder->setInputNormaliser(
    new \Webit\SoapApi\Input\FrontInputNormaliser(
        array(
            'ResolveIP' => new ResolveIPNormaliser()
        )
    )
);

$client = new \Webit\SoapApi\Features\Ip2Geo\Ip2GeoInputNormalisingClient(
    $builder->build()
);

$result = $client->getGeoLocation(new Ip('8.8.8.8')); // returns GeoLocation instance

```

### Result Hydration

Our Client looks pretty good but still we have some ugly "hydration" part in. Let's abstract it as well.

```php

<?php
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

```

Then ***Ip2GeoResultHydratingClient*** looks like:
 
```php

<?php
namespace Webit\SoapApi\Features\Ip2Geo;

use Webit\SoapApi\Executor\SoapApiExecutor;

class Ip2GeoResultHydratingClient
{
    /** @var SoapApiExecutor */
    private $executor;

    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Ip $ip
     * @return GeoLocation
     */
    public function getGeoLocation(Ip $ip)
    {
        return $this->executor->executeSoapFunction('ResolveIP', $ip);
    }
}

```

Now we configure ***SoapApiExecutor*** to use both ***ResolveIPHydrator*** and ***ResolveIPNornaliser*** as for given SOAP Function:

```php

<?php
use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\SoapClient\SoapClientSimpleFactory;

$builder = SoapApiExecutorBuilder::create();
$builder->setWsdl('http://ws.cdyne.com/ip2geo/ip2geo.asmx?WSDL');

$builder->setInputNormaliser(
    new \Webit\SoapApi\Input\FrontInputNormaliser(
        array(
            'ResolveIP' => new ResolveIPNormaliser()
        )
    )
);

$builder->setHydrator(
    new \Webit\SoapApi\Hydrator\FrontHydrator(
        array(
            'ResolveIP' => new ResolveIPHydrator()
        )
    )
);

$client = new \Webit\SoapApi\Features\Ip2Geo\Ip2GeoResultHydratingClient(
    $builder->build()
);

$result = $client->getGeoLocation(new Ip('8.8.8.8')); // returns GeoLocation instance

```
