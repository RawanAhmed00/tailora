<?php

namespace App\Services\Interfaces;

interface IBookingService
{
    public function getUserBookings(int $userId);

    public function getUserBookingById(int $userId, int $id);

    public function createBooking(int $userId, array $data);

    public function getAllBookings();

    public function getBookingById(int $id);

    public function updateBooking(int $id, array $data);

    public function deleteBooking(int $id);

    public function assignTourGuide(int $bookingId, int $tourGuideId);

    public function getAvailableTourGuides(?int $bookingId = null, ?string $requestDate = null, ?string $requestTime = null);
}