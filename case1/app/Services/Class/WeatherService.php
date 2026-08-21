<?php

namespace App\Services\Class;

use App\Repo\Interfaces\IWeatherRepo;
use App\Services\Interfaces\IWeatherService;
use Illuminate\Support\Facades\Http;

class WeatherService implements IWeatherService
{
    protected $weatherRepo;

    public function __construct(IWeatherRepo $weatherRepo)
    {
        $this->weatherRepo = $weatherRepo;
    }

    public function getWeatherByTripId(int $tripId)
    {
        return $this->weatherRepo->getWeatherByTripId($tripId);
    }

    public function getWeatherFromApi(string $city, int $tripId)
    {
        // Get city location
        $location = Http::get(
            'https://geocoding-api.open-meteo.com/v1/search',
            [
                'name' => $city,
                'count' => 1,
                'language' => 'en',
                'format' => 'json',
            ]
        );

        if (
            !$location->successful() ||
            empty($location->json('results'))
        ) {
            return null;
        }

        $result = $location->json('results.0');

        $latitude = $result['latitude'];
        $longitude = $result['longitude'];
        $country = $result['country'] ?? null;

        // Get weather
        $weather = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone' => 'auto',
            ]
        );

        if (!$weather->successful()) {
            return null;
        }

        // Get current weather data
        $current = $weather->json('current');

        $data = [
            'trip_id' => $tripId,

            'city' => $result['name'],

            'country' => $country,

            'latitude' => $latitude,

            'longitude' => $longitude,

            'temperature' => $current['temperature_2m'] ?? null,

            'humidity' => $current['relative_humidity_2m'] ?? null,

            'wind_speed' => $current['wind_speed_10m'] ?? null,

            'condition' => $this->getCondition(
                $current['weather_code'] ?? null
            ),

            'forecast_date' => now()->toDateString(),
        ];

        return $this->weatherRepo->createWeather($data);
    }

    public function getCondition(?int $code): string
    {
        return match ($code) {

            0 => 'Clear sky',

            1, 2, 3 => 'Partly cloudy',

            45, 48 => 'Fog',

            51, 53, 55 => 'Drizzle',

            61, 63, 65 => 'Rain',

            71, 73, 75 => 'Snow',

            80, 81, 82 => 'Rain showers',

            95 => 'Thunderstorm',

            96, 99 => 'Thunderstorm with hail',

            default => 'Unknown',
        };
    }
}