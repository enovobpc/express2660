<?php
/**
 * FrontInputNormaliserTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:53
 */

namespace Webit\SoapApi\Tests\Input;

use Webit\SoapApi\Input\FrontInputNormaliser;
use Webit\SoapApi\Tests\AbstractTest;

class FrontInputNormaliserTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldNormaliseBySoapFunctionName()
    {
        $normalisers = array(
            'fnc1' => $this->mockInputNormaliser(),
            'fnc2' => $this->mockInputNormaliser()
        );

        $func = 'fnc1';
        $input = 'input';
        $normalisedInput = 'normalised';

        $normaliser = new FrontInputNormaliser($normalisers);
        $normalisers[$func]->shouldReceive('normaliseInput')->with($func, $input)->once()->andReturn($normalisedInput);

        $this->assertEquals($normalisedInput, $normaliser->normaliseInput($func, $input));
    }

    /**
     * @test
     */
    public function shouldUseFallbackNormaliserIfSpecificNotFound()
    {
        $normalisers = array();
        $fallbackNormaliser = $this->mockInputNormaliser();

        $func = 'fnc1';
        $input = 'input';
        $normalisedInput = 'normalised';

        $normaliser = new FrontInputNormaliser($normalisers, $fallbackNormaliser);
        $fallbackNormaliser->shouldReceive('normaliseInput')->with($func, $input)->once()->andReturn($normalisedInput);

        $this->assertEquals($normalisedInput, $normaliser->normaliseInput($func, $input));
    }
}
