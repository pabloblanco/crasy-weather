<?php

namespace App\Exceptions\Stats;

use Illuminate\Http\JsonResponse;
use Exception;
use Log;

class QueryException extends Exception
{

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @return void
     */
    public function report()
    {
        Log::error($this->getMessage(), ['error_code' => $this->code]);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request)
    {

        return new JsonResponse([
            'success' => false, 
            'playlist' => null, 
            'message' => $this->getMessage(),
        ], $this->code);

    }

}