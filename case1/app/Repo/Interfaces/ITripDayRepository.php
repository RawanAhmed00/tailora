<?php

namespace App\Repo\Interfaces;

use App\Models\TripDay;

interface ITripDayRepository
{
    public function findById(int $id): ?TripDay;

    public function update(TripDay $tripDay, array $data): TripDay;

    public function syncAttractions(
        TripDay $tripDay,
        array $attractions
    ): TripDay;

    public function removeAttraction(
        TripDay $tripDay,
        int $attractionId
    ): TripDay;
}