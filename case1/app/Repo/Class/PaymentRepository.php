<?php

namespace App\Repo\Class;

use App\Models\Payment;
use App\Repo\Interfaces\IPaymentRepository;

class PaymentRepository implements IPaymentRepository
{
    public function createPayment(array $data): Payment
    {
        return Payment::create($data);
    }

    public function getPaymentById(int $id): Payment
    {
        return Payment::with('booking')->findOrFail($id);
    }

    public function updatePayment(int $id, array $data): Payment
    {
        $payment = Payment::findOrFail($id);

        $payment->update($data);

        return $payment->fresh('booking');
    }
}