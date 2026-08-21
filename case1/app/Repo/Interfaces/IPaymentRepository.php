<?php

namespace App\Repo\Interfaces;

use App\Models\Payment;

interface IPaymentRepository
{
    public function createPayment(array $data): Payment;

    public function getPaymentById(int $id): Payment;

    public function updatePayment(int $id, array $data): Payment;
}