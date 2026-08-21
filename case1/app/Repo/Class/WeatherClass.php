<?php

namespace App\Repo\Class;

use App\Models\Weather;
use App\Repo\Interfaces\IWeatherRepo;

class WeatherClass implements IWeatherRepo
{
    public function getWeatherByTripId(int $tripId)
    {
        return Weather::where('trip_id', $tripId)->get();
    }
    public function createWeather(array $data)
     {
        return weather::create($data);
     }
   
}