<?php

namespace App\Services\Interfaces;

use App\Models\TripDay;

interface ITripDayService
{
    public function updateTripDay(
        int $tripDayId,
        int $userId,
        array $data
    ): ?TripDay;

    public function addAttraction(
        int $tripDayId,
        int $userId,
        int $attractionId
    ): ?TripDay;

    public function removeAttraction(
        int $tripDayId,
        int $userId,
        int $attractionId
    ): ?TripDay;

    public function replaceAttraction(
        int $tripDayId,
        int $userId,
        int $oldAttractionId,
        int $newAttractionId
    ): ?TripDay;

    public function reorderAttractions(
        int $tripDayId,
        int $userId,
        array $attractions
    ): ?TripDay;
}