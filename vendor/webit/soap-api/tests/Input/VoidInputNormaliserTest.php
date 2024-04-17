<?php
/**
 * VoidInputNormaliserTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 16, 2015, 11:52
 *
 */

namespace Webit\SoapApi\Tests\Input;

use Webit\SoapApi\Input\VoidInputNormaliser;
use Webit\SoapApi\Tests\AbstractTest;

class VoidInputNormaliserTest extends AbstractTest
{
    /**
     * @test
     */
    public function shouldReturnRawInput()
    {
        $input = 'input';

        $normaliser = new VoidInputNormaliser();
        $this->assertEquals($input, $normaliser->normaliseInput('fnc', $input));
    }
}
