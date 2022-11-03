<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller as BaseController;

class OpenWeatherMapController extends BaseController
{
	public static function getTemperatureByCityName(Request $request) {

		if (!is_null($request->city)){

		   	$key = config('services.openweathermap.key');
		   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=".$request->city."&lang=es"."&appid=".$key)->json();

		    if($response['cod'] == "200") {

			    $temperature = $response['main']['temp'] - 273;
			    return $temperature;
			    
		    }

		}
		return null;
	}

	public static function getTemperatureByLatitudeLongitude(Request $request) {

		if (!is_null($request->latitude) && !is_null($request->longitude)){

		   	$key = config('services.openweathermap.key');
		   	$response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat=".$request->latitude."&lon=".$request->longitude."&appid=".$key)->json();

		    if($response['cod'] == "200") {

			    $temperature = $response['main']['temp'] - 273;
			    return $temperature;

		    }

		}
		return null;
	}		
}