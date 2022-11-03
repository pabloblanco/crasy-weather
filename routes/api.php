<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrazyWeatherController;
use App\Helpers\OpenWeatherMapController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$router->group(['middleware' => ['AuthorizedIp', 'AuthorizedToken']], function ($app) {

    $app->post('getSuggestPlaylistByLatitudeLongitude', [CrazyWeatherController::class, "getSuggestPlaylistByLatitudeLongitude"]);

    $app->post('getSuggestPlaylistByCity', [CrazyWeatherController::class, "getSuggestPlaylistByCity"]);

});


if (env('APP_ENV') == 'local' || env('APP_ENV') == 'test'){

    Route::post('getTemperatureByLatitudeLongitude', [OpenWeatherMapController::class, "getTemperatureByLatitudeLongitude"]);

    Route::post('getTemperatureByCityName', [OpenWeatherMapController::class, "getTemperatureByCityName"]);

    Route::get('/', function () {
    return view('welcome');
});

}
