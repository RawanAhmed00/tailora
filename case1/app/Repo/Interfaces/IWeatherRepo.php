<?php
namespace App\Repo\Interfaces;

interface IWeatherRepo
{
    public function getWeatherByTripId(int $tripId);

    public function createWeather(array $data);

}