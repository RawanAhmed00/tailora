<?php

namespace App\Repo\Class;

use App\Models\Booking;
use App\Repo\Interfaces\IBookingRepository;

class BookingRepository implements IBookingRepository
{
    public function getUserBookings(int $userId)
    {
        return Booking::with([
            'hotel',
            'flight',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getUserBookingById(int $userId, int $id)
    {
        return Booking::with([
            'hotel',
            'flight',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ])
            ->where('user_id', $userId)
            ->findOrFail($id);
    }

    public function createBooking(array $data)
    {
        return Booking::create($data);
    }

    public function getAllBookings()
    {
        return Booking::with([
            'user',
            'hotel',
            'flight',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ])
            ->latest()
            ->paginate(10);
    }

    public function getBookingById(int $id)
    {
        return Booking::with([
            'user',
            'hotel',
            'flight',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ])->findOrFail($id);
    }

    public function updateBooking(int $id, array $data)
    {
        $booking = Booking::findOrFail($id);

        $booking->update($data);

        return $booking->fresh([
            'user',
            'hotel',
            'flight',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ]);
    }

    public function deleteBooking(int $id)
    {
        $booking = Booking::findOrFail($id);

        return $booking->delete();
    }
}