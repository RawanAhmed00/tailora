<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\IPaymobService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected IPaymentService $paymentService;

    public function __construct(
        IPaymentService $paymentService
    ) {
        $this->paymentService = $paymentService;
    }

    /**
     * Create payment for booking.
     */
    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        $payment = $this->paymentService->createPayment(
            auth()->id(),
            $request->booking_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment,
        ], 201);
    }

    /**
     * Get payment by ID.
     */
    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully.',
            'data' => $payment,
        ]);
    }

    /**
     * Create Paymob checkout.
     */
    public function checkout(
        int $id,
        IPaymobService $paymobService
    ): JsonResponse {
        $payment = Payment::with('booking.user')
            ->whereHas('booking', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        $result = $paymobService->createIntention($payment);

        return response()->json([
            'success' => true,
            'message' => 'Paymob checkout created successfully.',
            'data' => $result,
        ]);
    }
}