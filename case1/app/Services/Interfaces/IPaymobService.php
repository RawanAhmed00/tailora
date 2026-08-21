<?php

namespace App\Services\Interfaces;

use App\Models\Payment;

interface IPaymobService
{
    public function createIntention(Payment $payment): array;

    public function getCheckoutData(Payment $payment): array;
}