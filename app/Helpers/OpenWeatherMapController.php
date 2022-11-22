<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\OpenWeatherMap\ApiException as OpenWeatherMapApiException;
use App\Exceptions\Temperature\CannotGetException as TemperatureCannotGetException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Routing\Controller as BaseController;

class OpenWeatherMapController extends BaseController
{
	public static function getTemperatureByCityName(Request $request) {

		try {

		   	$key = config('services.openweathermap.key');
		   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=".$request->city."&lang=es"."&appid=".$key);

		    throw_if(($response->status() != 200), TemperatureCannotGetException::class, 'Fallo la consulta a la API de OpenWeathermap', $response->status());

	    	if  (!empty($response['main']['temp'])) {

			    $temperature = $response['main']['temp'] - 273;

			    return $temperature;

			} else {

	    		throw new TemperatureCannotGetException('Fallo la consulta a la API de OpenWeathermap, esta no devolvio la temperatura', 404);

	    	}

		} catch(RequestException $e) {

    		$message = $e->getMessage();
    		throw new OpenWeatherMapApiException($message, 400);

    	} catch(ConnectionException $e) {

    		$message = $e->getMessage();
    		throw new OpenWeatherMapApiException($message, 400);

    	}
		
	}

	public static function getTemperatureByLatitudeLongitude(Request $request) {

		try {

		   	$key = config('services.openweathermap.key');
		   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat=".$request->latitude."&lon=".$request->longitude."&appid=".$key);

		   	throw_if(($response->status() != 200), OpenWeatherMapApiException::class, 'Fallo la consulta a la API de OpenWeathermap', $response->status()); 

	    	if  (isset($response['main']['temp'])) {

			    $temperature = $response['main']['temp'] - 273;
		    
			    return $temperature;

			} else {

				throw new TemperatureCannotGetException('Fallo la consulta a la API de OpenWeathermap, esta no devolvio la temperatura', 404);	

		    }
			    
		} catch(RequestException $e) {

    		$message = $e->getMessage();
    		throw new OpenWeatherMapApiException($message, 400);

    	} catch(ConnectionException $e) {

    		$message = $e->getMessage();
    		throw new OpenWeatherMapApiException($message, 400);
    		
    	}

	}		

}