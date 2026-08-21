<?php

namespace App\Services\Class;

use App\Models\Payment;
use App\Services\Interfaces\IPaymobService;
use Illuminate\Support\Facades\Http;

class PaymobService implements IPaymobService
{
    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config(
            'services.paymob.base_url',
            'https://accept.paymob.com'
        );

        $this->secretKey = config('services.paymob.secret_key');
    }

    public function createIntention(Payment $payment): array
    {
        $payment->load('booking.user');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(
            $this->baseUrl . '/v1/intention/',
            [
                'amount' => (int) ($payment->amount * 100),

                'currency' => $payment->currency,

                'payment_methods' => [
                    (int) config('services.paymob.integration_id'),
                ],

                'items' => [
                    [
                        'name' => 'Travel Booking #' . $payment->booking_id,
                        'amount' => (int) ($payment->amount * 100),
                        'description' => 'Travel booking payment',
                        'quantity' => 1,
                    ],
                ],

                'billing_data' => [
                    'first_name' =>
                        $payment->booking->user->name ?? 'Customer',

                    'last_name' => 'Customer',

                    'email' =>
                        $payment->booking->user->email
                        ?? 'customer@example.com',

                    'phone_number' =>
                        $payment->booking->user->phone
                        ?? '01000000000',

                    'apartment' => 'NA',
                    'floor' => 'NA',
                    'street' => 'NA',
                    'building' => 'NA',
                    'shipping_method' => 'NA',
                    'postal_code' => 'NA',
                    'city' => 'Cairo',
                    'state' => 'Cairo',
                    'country' => 'EG',
                ],
            ]
        );

       $response->throw();

$result = $response->json();

$payment->update([
    'paymob_intention_id' =>
        $result['intention_order_id'] ?? null,

    'paymob_client_secret' =>
        $result['client_secret'] ?? null,

    'paymob_order_id' =>
        $result['intention_order_id'] ?? null,
]);

return $result;
    }
public function getCheckoutData(Payment $payment): array
{
    $payment->load('booking.user');

    if (!$payment->paymob_intention_id) {
        throw new \Exception('Paymob intention has not been created yet.');
    }

    $intention = $this->createIntention($payment);

    return [
        'public_key' => config('services.paymob.public_key'),

        'client_secret' => $intention['client_secret'] ?? null,

        'intention_id' => $intention['id'] ?? null,

        'order_id' => $intention['intention_order_id'] ?? null,

        'amount' => $intention['intention_detail']['amount'] ?? null,

        'currency' => $intention['intention_detail']['currency'] ?? null,

        'payment_methods' => $intention['payment_methods'] ?? [],
    ];
}
}