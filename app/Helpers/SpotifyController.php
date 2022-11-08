<?php

namespace App\Helpers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spotify;
use SpotifySeed;

class SpotifyController extends BaseController
{
	public static function getSuggestedPlaylistByGenre($genre = null) {

		if (!empty($genre)) {

			$trackList = [];
			$count = 0;
			$seed = SpotifySeed::setGenres([$genre]);
			$recommendations = Spotify::recommendations($seed)->get();

			foreach ($recommendations as $recommendation){
				$trackList[$count] = $recommendation;
				$count = $count + 1;
			}
			
			return $trackList[0];
		   	
		} else {

			return null;

		}
	}
}