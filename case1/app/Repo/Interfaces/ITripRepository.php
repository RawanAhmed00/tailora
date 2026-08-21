<?php

namespace App\Repo\Interfaces;

use App\Models\Trip;

interface ITripRepository
{
    public function create(array $data): Trip;

    public function findById(
        int $tripId,
        int $userId
    ): ?Trip;

    public function getUserTrips(
        int $userId,
        int $perPage = 10
    );

    public function delete(
        Trip $trip
    ): bool;

     public function getTripForCitySelection(
        int $tripId,
        int $userId
    ): ?Trip;

    public function getFullTrip(
    int $tripId,
    int $userId
): ?Trip;
}