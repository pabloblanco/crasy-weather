<?php

namespace App\Exceptions;

use Exception;

class OpenWeatherMapException extends Exception
{
    protected $apiResponse;

    public function __construct($message = null, $url = null, $apiResponse = null, Exception $previous = null)
    {
        parent::__construct($message, $url, $previous);

        $this->apiResponse = $apiResponse;
    }

    /**
     * Get the API Response.
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }
}