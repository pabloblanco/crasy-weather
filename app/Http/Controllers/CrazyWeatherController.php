<?php

namespace App\Http\Controllers;

use App\Exceptions\Temperature\CannotGetException as TemperatureCannotGetException;
use App\Exceptions\Tracklist\CannotGetException as TracklistCannotGetException;
use App\Exceptions\Stats\QueryException as StatsQueryException;
use Illuminate\Database\QueryException;
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

        throw_if(empty($request->city), TemperatureCannotGetException::class, 'Se requiere del campo ciudad', 400); 

        $temperature = OpenWeatherMapController::getTemperatureByCityName($request);

        $response = self::getSuggestPlaylistByTemperature($temperature);

        try {

            $statsRecord = RequestsStats::insert([
                'success'       => true,
                'city'          => $request->city,
                'response'      => json_encode($response),
                'ip'            => $request->ip(),
                'temperature'   => $temperature,
                'playlist'      => Arr::get($response, 'playlist'),
                'created_at'    => date('y-m-d h:m:s'),
            ]); 

        } catch(QueryException $e) {

            $message = $e->getMessage();

            throw new StatsQueryException($message, 422);

        }

        return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');

    }

    public function getSuggestPlaylistByLatitudeLongitude(Request $request){

        throw_if(empty($request->latitude) || empty($request->longitude), TemperatureCannotGetException::class, 'Se requiere de los campos latitud y longitud', 400); 

        $temperature = OpenWeatherMapController::getTemperatureByLatitudeLongitude($request);

        $response = self::getSuggestPlaylistByTemperature($temperature);

        try {

            $statsRecord = RequestsStats::insert([
                'success'       => true,
                'city'          => $request->latitude.', '.$request->longitude,
                'response'      => json_encode($response),
                'ip'            => $request->ip(),
                'temperature'   => $temperature,
                'playlist'      => Arr::get($response, 'playlist'),
                'created_at'    => date('y-m-d h:m:s'),
            ]);

        } catch(QueryException $e) {

            $message = $e->getMessage();

            throw new StatsQueryException($message, 422);

        }

        return response($response, 200)->header('Content-Type', 'application/json; charset=utf-8');

    }   

    public function getSuggestPlaylistByTemperature($temperature = null){

        $genre = '';

        if ($temperature > 30) {
            $genre = 'party';
        }

        if ($temperature >= 15 && $temperature <= 30) {
            $genre = 'pop';
        }

        if ($temperature >= 10 && $temperature < 15) {
            $genre = 'rock';
        }

        if ($temperature < 10) {
            $genre = 'classical';
        }

        $suggestedTrackNameList = Collect([]);
        $trackListInfo = SpotifyController::getSuggestedPlaylistByGenre($genre);

        throw_if(empty($trackListInfo), TemperatureCannotGetException::class, 'El metodo getSuggestedPlaylistByGenre no devolvio informacion', 404); 

        $totalTracks = count($trackListInfo);
        
        for ($i = 0; $i < $totalTracks; $i++) {

            $track = 'Track '.(string) ($i + 1);
            $suggestedTrackNameList->put($track, Arr::get($trackListInfo, $i.'.album.name'));

        }

        $response = [
            'success' => true, 
            'playlist' => $suggestedTrackNameList, 
            'message' => 'Consulta exitosa'
        ];

        return $response;

    }

}