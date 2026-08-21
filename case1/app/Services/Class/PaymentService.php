<?php

namespace App\Services\Class;

use App\Models\Booking;
use App\Models\Payment;
use App\Repo\Interfaces\IPaymentRepository;
use App\Services\Interfaces\IPaymentService;

class PaymentService implements IPaymentService
{
    public function __construct(
        protected IPaymentRepository $paymentRepository
    ) {
    }

    public function createPayment(
        int $userId,
        int $bookingId
    ): Payment {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            throw new \Exception(
                'Booking not found or does not belong to this user.'
            );
        }

        $payment = $this->paymentRepository->createPayment([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'currency' => 'EGP',
            'status' => 'pending',
            'payment_method' => null,
        ]);

        return $payment->load('booking');
    }

    public function getPaymentById(int $id): Payment
    {
        return $this->paymentRepository->getPaymentById($id);
    }

    public function updatePayment(
        int $id,
        array $data
    ): Payment {
        return $this->paymentRepository->updatePayment(
            $id,
            $data
        );
    }
}