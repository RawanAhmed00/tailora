<?php

namespace App\Services\Class;

use App\Models\Attraction;
use App\Models\City;
use App\Models\Trip;
use App\Models\TripDay;
use App\Repo\Interfaces\ITripRepository;
use App\Services\Interfaces\ITripService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TripService implements ITripService
{
    protected ITripRepository $tripRepository;

    public function __construct(
        ITripRepository $tripRepository
    ) {
        $this->tripRepository = $tripRepository;
    }

    public function createTrip(
        int $userId,
        array $data
    ): Trip {

        $data['user_id'] = $userId;

        $data['source'] = 'system';

        $data['status'] = 'planned';

        return $this->tripRepository->create($data);
    }

    public function getTrip(
        int $tripId,
        int $userId
    ): ?Trip {

        return $this->tripRepository->findById(
            $tripId,
            $userId
        );
    }

    public function getUserTrips(
        int $userId,
        int $perPage = 10
    ) {

        return $this->tripRepository->getUserTrips(
            $userId,
            $perPage
        );
    }

    public function deleteTrip(
        int $tripId,
        int $userId
    ): bool {

        $trip = $this->tripRepository->findById(
            $tripId,
            $userId
        );

        if (!$trip) {
            return false;
        }

        return $this->tripRepository->delete($trip);
    }
public function selectCities(
    int $tripId,
    int $userId,
    array $days
): ?Trip {

    return DB::transaction(function () use (
        $tripId,
        $userId,
        $days
    ) {

        $trip = $this->tripRepository
            ->getTripForCitySelection(
                $tripId,
                $userId
            );

        if (!$trip) {
            return null;
        }

        $startDate = Carbon::parse($trip->start_date);
        $endDate = Carbon::parse($trip->end_date);

       $totalDays = (int) $startDate->diffInDays($endDate) + 1;
  

        /*
         * Number of selected days must match
         * the trip duration.
         */
        if (count($days) !== $totalDays) {
            throw new \InvalidArgumentException(
                "You must provide exactly {$totalDays} days."
            );
        }

        /*
         * Get day numbers.
         */
        $dayNumbers = array_column($days, 'day_number');

        /*
         * No duplicate days.
         */
        if (
            count($dayNumbers) !==
            count(array_unique($dayNumbers))
        ) {
            throw new \InvalidArgumentException(
                'Duplicate day numbers are not allowed.'
            );
        }

        /*
         * Make sure all days exist:
         * 1, 2, 3 ... totalDays
         */
        sort($dayNumbers);

        $expectedDays = range(1, $totalDays);

        if ($dayNumbers !== $expectedDays) {
            throw new \InvalidArgumentException(
                'You must provide all trip days.'
            );
        }

        /*
         * Get selected city IDs.
         */
        $cityIds = array_unique(
            array_column($days, 'city_id')
        );

        /*
         * Make sure cities belong
         * to the trip country.
         */
        $validCities = City::whereIn('id', $cityIds)
            ->where('country_id', $trip->country_id)
            ->pluck('id')
            ->toArray();

        if (count($validCities) !== count($cityIds)) {
            throw new \InvalidArgumentException(
                'One or more selected cities do not belong to the trip country.'
            );
        }

        /*
         * Remove previous itinerary.
         */
        $trip->tripDays()->delete();

        /*
         * Create TripDays.
         */
        foreach ($days as $day) {

            TripDay::create([
                'trip_id' => $trip->id,
                'city_id' => $day['city_id'],
                'day_number' => $day['day_number'],

                'date' => $startDate
                    ->copy()
                    ->addDays($day['day_number'] - 1)
                    ->toDateString(),

                'estimated_expenses' => 0,
            ]);
        }

        return $trip->load([
            'country',
            'tripDays.city',
        ]);
    });
}
public function updateTripDayCity(
    int $tripDayId,
    int $userId,
    int $cityId
): ?Trip {

    return DB::transaction(function () use (
        $tripDayId,
        $userId,
        $cityId
    ) {

        $tripDay = TripDay::with('trip')
            ->where('id', $tripDayId)
            ->whereHas('trip', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        if (!$tripDay) {
            return null;
        }

        /*
         * Make sure the city belongs
         * to the same country as the trip.
         */
        $city = City::where('id', $cityId)
            ->where(
                'country_id',
                $tripDay->trip->country_id
            )
            ->first();

        if (!$city) {
            throw new \InvalidArgumentException(
                'Selected city does not belong to the trip country.'
            );
        }

        $tripDay->update([
            'city_id' => $city->id,
        ]);

        return $tripDay->trip->load([
            'country',
            'tripDays.city',
        ]);
    });
}
public function getTripDayAttractions(
    int $tripDayId,
    int $userId
) {
    $tripDay = TripDay::with('trip')
        ->where('id', $tripDayId)
        ->whereHas('trip', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first();

    if (!$tripDay) {
        return null;
    }

    if (!$tripDay->city_id) {
        throw new \InvalidArgumentException(
            'You must select a city for this day first.'
        );
    }

    return Attraction::where(
        'city_id',
        $tripDay->city_id
    )
        ->with('categories')
        ->get();
}
public function selectTripDayAttractions(
    int $tripDayId,
    int $userId,
    array $attractionIds
) {
    return DB::transaction(function () use (
        $tripDayId,
        $userId,
        $attractionIds
    ) {

        $tripDay = TripDay::with('trip')
            ->where('id', $tripDayId)
            ->whereHas('trip', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        if (!$tripDay) {
            return null;
        }

        if (!$tripDay->city_id) {
            throw new \InvalidArgumentException(
                'You must select a city for this day first.'
            );
        }

        /*
         * Remove duplicates.
         */
        $attractionIds = array_values(
            array_unique($attractionIds)
        );

        /*
         * Make sure all attractions
         * belong to the TripDay city.
         */
        $validAttractionIds = Attraction::whereIn(
                'id',
                $attractionIds
            )
            ->where(
                'city_id',
                $tripDay->city_id
            )
            ->pluck('id')
            ->toArray();

        if (
            count($validAttractionIds) !==
            count($attractionIds)
        ) {
            throw new \InvalidArgumentException(
                'One or more attractions do not belong to this day city.'
            );
        }

        /*
         * Clear previous selections.
         */
        $tripDay->attractions()->detach();

        /*
         * Attach attractions with order.
         */
        $attachData = [];

        foreach ($attractionIds as $index => $attractionId) {
            $attachData[$attractionId] = [
                'order' => $index + 1,
            ];
        }

        $tripDay->attractions()->attach($attachData);

        return $tripDay->load([
            'trip',
            'city',
            'attractions.categories',
        ]);
    });
}
public function getFullTrip(
    int $tripId,
    int $userId
): ?Trip {

    return $this->tripRepository->getFullTrip(
        $tripId,
        $userId
    );
}
}