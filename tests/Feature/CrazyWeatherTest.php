<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CrazyWeatherTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_suggest_playlist_by_city()
    {
        $this->withoutMiddleware();
        $response = $this->postJson('/getSuggestPlaylistByCity', ['city' => 'guadalajara']);
 
        $response
            ->assertStatus(200);
            //->assertJsonPath('success', 'true');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_suggest_playlist_by_latitude_longitude()
    {
        $this->withoutMiddleware();
        $response = $this->postJson('/getSuggestPlaylistByLatitudeLongitude', ['latitude' => '20.66682', 'longitude' => '-103.39182']);
 
        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }    
}
