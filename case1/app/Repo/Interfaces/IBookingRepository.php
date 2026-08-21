<?php

namespace App\Repo\Interfaces;

interface IBookingRepository
{
    public function getUserBookings(int $userId);

    public function getUserBookingById(int $userId, int $id);

    public function createBooking(array $data);

    public function getAllBookings();

    public function getBookingById(int $id);

    public function updateBooking(int $id, array $data);

    public function deleteBooking(int $id);
}