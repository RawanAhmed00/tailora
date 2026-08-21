<?php

namespace App\Repo\Class;

use App\Models\Trip;
use App\Repo\Interfaces\ITripRepository;

class TripRepository implements ITripRepository
{
    public function create(array $data): Trip
    {
        return Trip::create($data);
    }

    public function findById(
        int $tripId,
        int $userId
    ): ?Trip {

        return Trip::with([
            'country',
            'flight',
            'tripDays.city',
            'tourGuideRequests.tourGuide',
            'bookings.tourGuide',
            'bookings.tourGuideRequests.tourGuide',
        ])
            ->where('id', $tripId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getUserTrips(
        int $userId,
        int $perPage = 10
    ) {

        return Trip::with([
            'country',
            'flight',
            'tripDays.city',
            'tourGuideRequests.tourGuide',
            'bookings.tourGuide',
            'bookings.tourGuideRequests.tourGuide',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function delete(Trip $trip): bool
    {
        return $trip->delete();
    }
    public function getTripForCitySelection(
        int $tripId,
        int $userId
    ): ?Trip {
        return Trip::where('id', $tripId)
            ->where('user_id', $userId)
            ->with([
                'country',
                'tripDays.city',
            ])
            ->first();
    }
    public function getFullTrip(
    int $tripId,
    int $userId
): ?Trip {

    return Trip::with([
        'country',
        'flight',
        'tourGuideRequests.tourGuide',
        'bookings.tourGuide',
        'bookings.tourGuideRequests.tourGuide',

        'tripDays' => function ($query) {
            $query->orderBy('day_number');
        },

        'tripDays.city',

        'tripDays.attractions' => function ($query) {
            $query->orderBy(
                'trip_day_attraction.order'
            );
        },

        'tripDays.attractions.categories',
    ])
    ->where('id', $tripId)
    ->where('user_id', $userId)
    ->first();
}
}