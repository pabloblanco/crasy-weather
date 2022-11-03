<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Helpers\OpenWeatherMapController;
use App\Http\Controllers\CrazyWeatherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//if (env('APP_ENV') == 'local' || env('APP_ENV') == 'test'){

Route::get('/', [HomeController::class, 'index'])->name('home');

$router->group(['middleware' => ['AuthorizedIp', 'AuthorizedToken']], function ($app) {

    $app->post('getTemperatureByCityName', [OpenWeatherMapController::class, "getTemperatureByCityName"]);

    $app->post('getTemperatureByLatitudeLongitude', [OpenWeatherMapController::class, "getTemperatureByLatitudeLongitude"]);

    $app->post('getSuggestPlaylistByLatitudeLongitude', [CrazyWeatherController::class, "getSuggestPlaylistByLatitudeLongitude"]);

    $app->post('getSuggestPlaylistByCity', [CrazyWeatherController::class, "getSuggestPlaylistByCity"]);

});

//}


