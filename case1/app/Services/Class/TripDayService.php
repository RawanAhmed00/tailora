<?php

namespace App\Services\Class;

use App\Models\City;
use App\Models\TripDay;
use App\Repo\Interfaces\ITripDayRepository;
use App\Services\Interfaces\ITripDayService;
use Illuminate\Validation\ValidationException;

class TripDayService implements ITripDayService
{
    protected ITripDayRepository $tripDayRepository;

    public function __construct(
        ITripDayRepository $tripDayRepository
    ) {
        $this->tripDayRepository = $tripDayRepository;
    }

    public function updateTripDay(
        int $tripDayId,
        int $userId,
        array $data
    ): ?TripDay {

        $tripDay = $this->tripDayRepository->findById($tripDayId);

        /*
        |--------------------------------------------------------------------------
        | Trip Day not found
        |--------------------------------------------------------------------------
        */

        if (!$tripDay) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Check ownership
        |--------------------------------------------------------------------------
        */

        if ($tripDay->trip->user_id !== $userId) {
            throw ValidationException::withMessages([
                'trip_day' => [
                    'You are not authorized to modify this trip day.'
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check City
        |--------------------------------------------------------------------------
        */

        if (isset($data['city_id'])) {

            $city = City::find($data['city_id']);

            if (!$city) {
                throw ValidationException::withMessages([
                    'city_id' => [
                        'The selected city does not exist.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | City must belong to Trip Country
            |--------------------------------------------------------------------------
            */

            if ($city->country_id !== $tripDay->trip->country_id) {

                throw ValidationException::withMessages([
                    'city_id' => [
                        'The selected city does not belong to the trip country.'
                    ],
                ]);
            }
        }

        return $this->tripDayRepository->update(
            $tripDay,
            $data
        );
    }

public function addAttraction(
    int $tripDayId,
    int $userId,
    int $attractionId
): ?TripDay {

    $tripDay = $this->tripDayRepository->findById($tripDayId);

    if (!$tripDay) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Check ownership
    |--------------------------------------------------------------------------
    */

    if ($tripDay->trip->user_id !== $userId) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Find attraction
    |--------------------------------------------------------------------------
    */

    $attraction = \App\Models\Attraction::find($attractionId);

    if (!$attraction) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'attraction_id' => [
                'Attraction not found.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Attraction must belong to same city
    |--------------------------------------------------------------------------
    */

    if ($attraction->city_id !== $tripDay->city_id) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'attraction_id' => [
                'This attraction does not belong to the TripDay city.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Check if already exists
    |--------------------------------------------------------------------------
    */

    $alreadyExists = $tripDay->attractions()
        ->where('attractions.id', $attractionId)
        ->exists();

    if ($alreadyExists) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'attraction_id' => [
                'This attraction is already added to this TripDay.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get next order
    |--------------------------------------------------------------------------
    */

    $nextOrder = (
        $tripDay->attractions()
            ->max('trip_day_attraction.order') ?? 0
    ) + 1;

    /*
    |--------------------------------------------------------------------------
    | Add attraction
    |--------------------------------------------------------------------------
    */

    $tripDay->attractions()->attach(
        $attractionId,
        [
            'order' => $nextOrder,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Return updated TripDay
    |--------------------------------------------------------------------------
    */

    return $tripDay->fresh([
        'trip',
        'city',
        'attractions.categories',
    ]);
}


public function removeAttraction(
    int $tripDayId,
    int $userId,
    int $attractionId
): ?TripDay {

    $tripDay = $this->tripDayRepository->findById($tripDayId);

    if (!$tripDay) {
        return null;
    }

    if ($tripDay->trip->user_id !== $userId) {
        return null;
    }

    $tripDay->attractions()->detach($attractionId);

    return $tripDay->fresh([
        'trip',
        'city',
        'attractions',
    ]);
}


public function replaceAttraction(
    int $tripDayId,
    int $userId,
    int $oldAttractionId,
    int $newAttractionId
): ?TripDay {

    $tripDay = $this->tripDayRepository->findById($tripDayId);

    if (!$tripDay) {
        return null;
    }

    if ($tripDay->trip->user_id !== $userId) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | New attraction
    |--------------------------------------------------------------------------
    */

    $newAttraction = \App\Models\Attraction::find(
        $newAttractionId
    );

    if (!$newAttraction) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'new_attraction_id' => [
                'Attraction not found.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Same city validation
    |--------------------------------------------------------------------------
    */

    if ($newAttraction->city_id !== $tripDay->city_id) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'new_attraction_id' => [
                'This attraction does not belong to the TripDay city.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get old attraction order
    |--------------------------------------------------------------------------
    */

    $oldAttraction = $tripDay->attractions()
        ->where('attractions.id', $oldAttractionId)
        ->first();

    if (!$oldAttraction) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'old_attraction_id' => [
                'The old attraction is not assigned to this TripDay.'
            ],
        ]);
    }

    $order = $oldAttraction->pivot->order;

    /*
    |--------------------------------------------------------------------------
    | Replace
    |--------------------------------------------------------------------------
    */

    $tripDay->attractions()->detach($oldAttractionId);

    $tripDay->attractions()->attach(
        $newAttractionId,
        [
            'order' => $order,
        ]
    );

    return $tripDay->fresh([
        'trip',
        'city',
        'attractions',
    ]);
}


public function reorderAttractions(
    int $tripDayId,
    int $userId,
    array $attractions
): ?TripDay {

    $tripDay = $this->tripDayRepository->findById($tripDayId);

    if (!$tripDay) {
        return null;
    }

    if ($tripDay->trip->user_id !== $userId) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Make sure all attractions belong to this TripDay
    |--------------------------------------------------------------------------
    */

    $existingIds = $tripDay->attractions()
        ->pluck('attractions.id')
        ->toArray();

    $requestedIds = collect($attractions)
        ->pluck('id')
        ->toArray();

    sort($existingIds);
    sort($requestedIds);

    if ($existingIds !== $requestedIds) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'attractions' => [
                'You must provide all attractions currently assigned to this TripDay.'
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update order
    |--------------------------------------------------------------------------
    */

    foreach ($attractions as $item) {

        $tripDay->attractions()->updateExistingPivot(
            $item['id'],
            [
                'order' => $item['order'],
            ]
        );
    }

    return $tripDay->fresh([
        'trip',
        'city',
        'attractions',
    ]);
}

}