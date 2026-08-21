<?php

namespace App\Repo\Class;

use App\Models\TripDay;
use App\Repo\Interfaces\ITripDayRepository;

class TripDayRepository implements ITripDayRepository
{
    public function findById(int $id): ?TripDay
    {
        return TripDay::with([
            'trip',
            'city',
            'attractions',
        ])->find($id);
    }

    public function update(
        TripDay $tripDay,
        array $data
    ): TripDay {
        $tripDay->update($data);

        return $tripDay->fresh([
            'trip',
            'city',
            'attractions',
        ]);
    }

    public function syncAttractions(
        TripDay $tripDay,
        array $attractions
    ): TripDay {

        $tripDay->attractions()->sync($attractions);

        return $tripDay->fresh([
            'trip',
            'city',
            'attractions',
        ]);
    }

    public function removeAttraction(
        TripDay $tripDay,
        int $attractionId
    ): TripDay {

        $tripDay->attractions()->detach($attractionId);

        return $tripDay->fresh([
            'trip',
            'city',
            'attractions',
        ]);
    }
}
