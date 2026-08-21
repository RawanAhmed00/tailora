<?php

namespace App\Services\Interfaces;

use App\Models\Trip;

interface ITripService
{
    public function createTrip(
        int $userId,
        array $data
    ): Trip;

    public function getTrip(
        int $tripId,
        int $userId
    ): ?Trip;

    public function getUserTrips(
        int $userId,
        int $perPage = 10
    );

    public function deleteTrip(
        int $tripId,
        int $userId
    ): bool;

     public function selectCities(
    int $tripId,
    int $userId,
    array $days
): ?Trip;
    public function updateTripDayCity(
    int $tripDayId,
    int $userId,
    int $cityId
): ?Trip;
public function getTripDayAttractions(
    int $tripDayId,
    int $userId
);
public function selectTripDayAttractions(
    int $tripDayId,
    int $userId,
    array $attractionIds
);
public function getFullTrip(
    int $tripId,
    int $userId
): ?Trip;
}