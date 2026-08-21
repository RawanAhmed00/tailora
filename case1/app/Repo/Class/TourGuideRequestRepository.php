<?php

namespace App\Repo\Class;

use App\Models\TourGuideRequest;
use App\Repo\Interfaces\ITourGuideRequestRepository;

class TourGuideRequestRepository implements ITourGuideRequestRepository
{
    public function getTourGuideRequests(int $tourGuideId)
    {
        return TourGuideRequest::with([
            'trip.user',
            'trip.country',
            'booking.user',
            'booking.flight',
            'booking.hotel',
            'booking.trip',
            'tourGuide',
        ])
            ->where('tour_guide_id', $tourGuideId)
            ->latest()
            ->paginate(10);
    }

    public function getTourGuideRequestById(
        int $tourGuideId,
        int $requestId
    ) {
        return TourGuideRequest::with([
            'trip.user',
            'trip.country',
            'booking.user',
            'booking.flight',
            'booking.hotel',
            'booking.trip',
            'tourGuide',
        ])
            ->where('tour_guide_id', $tourGuideId)
            ->findOrFail($requestId);
    }

    public function acceptRequest(
        int $tourGuideId,
        int $requestId
    ) {
        $request = TourGuideRequest::where(
            'tour_guide_id',
            $tourGuideId
        )
            ->where('status', 'pending')
            ->findOrFail($requestId);

        /*
        |--------------------------------------------------------------------------
        | Make sure another tour guide did not already accept
        |--------------------------------------------------------------------------
        */

        $alreadyAccepted = TourGuideRequest::where(
            'booking_id',
            $request->booking_id
        )
            ->where('status', 'accepted')
            ->exists();

        if ($alreadyAccepted) {
            throw new \Exception(
                'This booking has already been accepted by another tour guide.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Accept current request
        |--------------------------------------------------------------------------
        */

        $request->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'rejected_at' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Reject all other pending requests
        |--------------------------------------------------------------------------
        */

        TourGuideRequest::where(
            'booking_id',
            $request->booking_id
        )
            ->where('id', '!=', $request->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'accepted_at' => null,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Associate booking with accepted tour guide
        |--------------------------------------------------------------------------
        */

        $booking = $request->booking;

        if ($booking) {
            $booking->update([
                'tour_guide_id' => $tourGuideId,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Return updated request
        |--------------------------------------------------------------------------
        */

        return $request->fresh([
            'booking.user',
            'booking.flight',
            'booking.hotel',
            'booking.trip',
            'tourGuide',
        ]);
    }

    public function rejectRequest(
        int $tourGuideId,
        int $requestId
    ) {
        $request = TourGuideRequest::where(
            'tour_guide_id',
            $tourGuideId
        )
            ->where('status', 'pending')
            ->findOrFail($requestId);

        $request->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'accepted_at' => null,
        ]);

        $booking = $request->booking;
        if ($booking && $booking->tour_guide_id == $tourGuideId) {
            $booking->update([
                'tour_guide_id' => null,
            ]);
        }

        return $request->fresh([
            'booking.user',
            'booking.flight',
            'booking.hotel',
            'booking.trip',
            'tourGuide',
        ]);
    }
}