<?php

namespace App\Services\Interfaces;
use Illuminate\Http\Request;

interface IWeatherService
{
    public function getWeatherByTripId(int $tripId);

    public function getWeatherFromApi(string $city, int $tripId);

    public function getCondition(?int $code);
}