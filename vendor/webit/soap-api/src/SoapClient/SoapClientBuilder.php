<?php
/**
 * SoapClientBuilder.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 14, 2015, 15:27
 */

namespace Webit\SoapApi\SoapClient;

class SoapClientBuilder
{
    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @return SoapClientBuilder
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param string $wsdl
     * @return SoapClientBuilder
     */
    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;

        return $this;
    }

    /**
     * @param array $options
     * @return SoapClientBuilder
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return SoapClientBuilder
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * @return \SoapClient
     */
    public function build()
    {
        return new \SoapClient($this->wsdl, $this->options);
    }
}
