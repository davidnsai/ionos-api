<?php

namespace DNSApi;

use Exception;

/**
 * DNS API Exception
 *
 * Custom exception class for DNS API related errors.
 * Provides additional error information from API responses.
 */
class DNSApiException extends Exception
{
    /**
     * @var mixed Additional error data from API response
     */
    private $errors;

    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param mixed $errors Additional error data
     * @param Exception|null $previous Previous exception
     */
    public function __construct($message = "", $code = 0, $errors = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get additional error information
     *
     * @return mixed Error data from API response
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
