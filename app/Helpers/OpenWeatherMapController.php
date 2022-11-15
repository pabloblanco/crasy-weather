<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\OpenWeatherMapApiException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Routing\Controller as BaseController;

class OpenWeatherMapController extends BaseController
{
	public static function getTemperatureByCityName(Request $request) {

		if (!is_null($request->city)){

			try {

			   	$key = config('services.openweathermap.key');
			   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=".$request->city."&lang=es"."&appid=".$key);

			    if ($response->status() == 200) {

			    	if  (isset($response['main']['temp'])) {

					    $temperature = $response['main']['temp'] - 273;
					    return $temperature;

					} else {

			    		return null;

			    	}
				    
			    } else {

			    	return null;

			    }

			} catch(RequestException $e) {

            	$url = $e->getRequest()->getUri();
            	$apiResponse = $e->getRequest()->getRequestTarget();
            	$message = $e->getMessage();

            	// Se puede incluir en OpenWeatherMapException que reporte el error al log o al Slack
            	throw new OpenWeatherMapApiException($message, $url, $apiResponse, $e);

        	}

		}

		return null;
		
	}

	public static function getTemperatureByLatitudeLongitude(Request $request) {

		if (!is_null($request->latitude) && !is_null($request->longitude)){

			try {

			   	$key = config('services.openweathermap.key');
			   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat=".$request->latitude."&lon=".$request->longitude."&appid=".$key);

			    if ($response->status() == 200) {

			    	if  (isset($response['main']['temp'])) {

					    $temperature = $response['main']['temp'] - 273;
					    return $temperature;

					} else {

			    		return null;

			    	}
				    
			    } else {

			    	return null;

			    }

			} catch(RequestException $e) {

		    	$errorResponse = json_decode($e->getResponse()->getBody()->getContents());
            	$status = $errorResponse->error->status;
            	$message = $errorResponse->error->message;

            	// Se puede incluir en OpenWeatherMapException que reporte el error al log o al Slack
            	throw new OpenWeatherMapApiException($message, $status, $errorResponse);

        	}

		}

		return null;

	}		
}