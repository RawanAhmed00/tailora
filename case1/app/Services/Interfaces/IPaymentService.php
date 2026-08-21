<?php

namespace App\Services\Interfaces;

use App\Models\Payment;

interface IPaymentService
{
    public function createPayment(int $userId, int $bookingId): Payment;

    public function getPaymentById(int $id): Payment;

    public function updatePayment(int $id, array $data): Payment;
}