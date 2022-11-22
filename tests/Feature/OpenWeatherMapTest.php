<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class OpenWeatherMapTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_temperature_by_city_name()
    {
        $this->withoutMiddleware();
        $temperature = $this->postJson('/getTemperatureByCityName', ['city' => 'guadalajara']);
        $this->assertNotEmpty($temperature);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_temperature_by_latitude_longitude()
    {
        $this->withoutMiddleware();
        $temperature = $this->postJson('/getTemperatureByCityName', ['latitude' => '20.66682', 'longitude' => '-103.39182']);
        $this->assertNotEmpty($temperature);
    }

}
