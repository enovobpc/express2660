<?php
/**
 * File: AuthException.php
 * Created at: 2014-11-25 06:26
 */
 
namespace Webit\GlsTracking\Api\Exception;

use Webit\GlsTracking\Model\Message\AbstractResponse;

/**
 * Class AuthException
 * @author Daniel Bojdo <daniel.bojdo@web-it.eu>
 */
class AuthException extends \RuntimeException implements GlsTrackingApiException
{
    /**
     * @var AbstractResponse
     */
    private $apiResponse;

    public function __construct($message, $code, AbstractResponse $apiResponse, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->apiResponse = $apiResponse;
    }

    /**
     * @return AbstractResponse
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }
}
 