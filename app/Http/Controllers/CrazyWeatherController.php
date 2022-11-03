<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Helpers\SpotifyController;
use App\Helpers\OpenWeatherMapController;
use App\Models\RequestsStats;
use OpenApi\Annotations as OA;

/** 
 *
 * Class CrazyWeatherController.
 *
 * @author  Pablo Blanco <pa_blanco@hotmail.com>
 * 
 * @OA\Info(title="Crazy Weather API", version="1.0.0")
 *
 * @OA\Post(
 *     path="/getSuggestPlaylistByCity",
 *     tags={"getSuggestPlaylistByCity"},
 *     summary="Muestra una lista de reproduccion",   
 *     description="Devuelve una lista de reproduccion en funcion de la temperatura de la ciudad.",
 *     security={
 *         {"Bearer Token": {"Token:token"}}
 *     },
 *     @OA\RequestBody(
 *         description="Input city name",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="city",
 *                     description="The city name that you required the play list",
 *                     type="string",
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Response OK",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                       property="success",
 *                       description="Status of the result of the request",
 *                       type="string",
 *                 ),
 *                 @OA\Property(
 *                       property="playlist",
 *                       description="The city name that you required the play list",
 *                       type="object",
 *                       @OA\Schema(
 *                           type="object",
 *                           @OA\Property(
 *                               property="track {nro}",
 *                               description="Show the track nema suggested",
 *                               type="string",
 *                           ),
 *                       ),
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     description="Message showing the result of the request",
 *                     type="string",
 *                 )
 *             )
 *         )
 *     )
 * ) 
 *
 * @OA\Post(
 *     path="/getSuggestPlaylistByLatitudeLongitude",
 *     tags={"getSuggestPlaylistByLatitudeLongitude"},
 *     summary="Muestra una lista de reproduccion",   
 *     description="Devuelve una lista de reproduccion en funcion de la temperatura de la latitud y longitud dada.",
 *     security={
 *         {"Bearer Token": {"Token:token"}}
 *     },
 *     @OA\RequestBody(
 *         description="Input latitude longitude",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="latitude",
 *                     description="The latitude that you required the play list",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="longitude",
 *                     description="The longitude that you required the play list",
 *                     type="string",
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Response OK",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                       property="success",
 *                       description="Status of the result of the request",
 *                       type="string",
 *                 ),
 *                 @OA\Property(
 *                       property="playlist",
 *                       description="The city name that you required the play list",
 *                       type="object",
 *                       @OA\Schema(
 *                           type="object",
 *                           @OA\Property(
 *                               property="track {nro}",
 *                               description="Show the track nema suggested",
 *                               type="string",
 *                           ),
 *                       ),
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     description="Message showing the result of the request",
 *                     type="string",
 *                 )
 *             )
 *         )
 *     )
 * ) 
 *
 */
class CrazyWeatherController extends Controller
{

    public function getSuggestPlaylistByCity(Request $request){

        if (!empty($request->city)) {

            $temperature = OpenWeatherMapController::getTemperatureByCityName($request);
            $response = self::getSuggestPlaylistByTemperature($temperature);

            if (!is_null($response)) {

                $log = RequestsStats::insert([
                    'success'       => true,
                    'city'       => $request->city,
                    'response'      => json_encode($response),
                    'ip'            => $request->ip(),
                    'temperature'   => $temperature,
                    'playlist'      => Arr::get($response, 'playlist'),
                    'created_at'    => date('y-m-d h:m:s'),
                ]); 
                //dd($log);
                return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');

            } else {

                $response = ['success' => false, 'playlist' => null, 'message' => 'El metodo getSuggestPlaylistByTemperature no devolvio informacion'];
                return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8'); 

            }

        } else {

            $response = ['success' => false, 'playlist' => null, 'message' => 'Se requiere del campo ciudad'];
            return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');   

        }
    }

    public function getSuggestPlaylistByLatitudeLongitude(Request $request){

        if (isset($request->latitude) && isset($request->longitude)) {

            $temperature = OpenWeatherMapController::getTemperatureByLatitudeLongitude($request);
            $response = self::getSuggestPlaylistByTemperature($temperature);

            if (!is_null($response)) {

                return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');

            } else {

                $response = ['success' => false, 'playlist' => null, 'message' => 'El metodo getSuggestPlaylistByTemperature no devolvio informacion'];
                return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8'); 

            }

        } else {

            $response = ['success' => false, 'playlist' => null, 'message' => 'Se requiere de los campos latitud y longitud'];
            return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');   

        }
    }   

    public function getSuggestPlaylistByTemperature($temperature = null){

        if (!is_null($temperature)) {

            if ($temperature > 30) {
                $genre = 'party';
            }

            if ($temperature >= 15 && $temperature <= 30) {
                $genre = 'pop';
            }

            if ($temperature >= 10 && $temperature <= 14) {
                $genre = 'rock';
            }

            if ($temperature >= 10 && $temperature <= 14) {
                $genre = 'classic';
            }

            $suggestedTrackNameList = Collect([]);
            $trackListInfo = SpotifyController::suggestedPlaylistByGenre($genre);
            $totalTracks = count($trackListInfo);
            
            for ($i = 0; $i < $totalTracks; $i++) {
                $suggestedTrackNameList->put('Track '.$i+1, Arr::get($trackListInfo, $i.'.album.name'));
            }

            $response = [
                'success' => true, 
                'playlist' => $suggestedTrackNameList, 
                'message' => 'Consulta exitosa'
            ];

            return $response;

        }else{

            return null;   

        }
    }
}