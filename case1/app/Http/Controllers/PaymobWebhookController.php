<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymobWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Get webhook payload
        |--------------------------------------------------------------------------
        */

        $data = $request->all();

        $transaction = $data['obj'] ?? $data;

        /*
        |--------------------------------------------------------------------------
        | Verify HMAC
        |--------------------------------------------------------------------------
        */

        $receivedHmac = $request->query('hmac')
            ?? $request->input('hmac')
            ?? null;

        if (!$receivedHmac) {
            return response()->json([
                'success' => false,
                'message' => 'HMAC is missing.',
            ], 401);
        }

        $hmacSecret = config('services.paymob.hmac_secret');

        $hmacString =
            ($transaction['amount_cents'] ?? '') .
            ($transaction['created_at'] ?? '') .
            ($transaction['currency'] ?? '') .
            ($transaction['error_occured'] ?? '') .
            ($transaction['has_parent_transaction'] ?? '') .
            ($transaction['id'] ?? '') .
            ($transaction['integration_id'] ?? '') .
            ($transaction['is_3d_secure'] ?? '') .
            ($transaction['is_auth'] ?? '') .
            ($transaction['is_capture'] ?? '') .
            ($transaction['is_refunded'] ?? '') .
            ($transaction['is_standalone_payment'] ?? '') .
            ($transaction['is_voided'] ?? '') .
            ($transaction['order']['id'] ?? $transaction['order_id'] ?? '') .
            ($transaction['owner'] ?? '') .
            ($transaction['pending'] ?? '') .
            ($transaction['source_data']['pan'] ?? '') .
            ($transaction['source_data']['sub_type'] ?? '') .
            ($transaction['source_data']['type'] ?? '') .
            ($transaction['success'] ?? '');

        $calculatedHmac = hash_hmac(
            'sha512',
            $hmacString,
            $hmacSecret
        );
        \Log::info('PAYMOB HMAC DEBUG', [
    'received_hmac' => $receivedHmac,
    'calculated_hmac' => $calculatedHmac,
    'hmac_string' => $hmacString,
]);

        if (!hash_equals(
            strtolower($calculatedHmac),
            strtolower($receivedHmac)
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid HMAC.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Get transaction information
        |--------------------------------------------------------------------------
        */

        $transactionId = $transaction['id'] ?? null;

        $orderId = $transaction['order']['id']
            ?? $transaction['order_id']
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | Find payment
        |--------------------------------------------------------------------------
        */

        $payment = Payment::where(function ($query) use ($orderId) {
            $query
                ->where('paymob_order_id', $orderId)
                ->orWhere('paymob_intention_id', $orderId);
        })->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'order_id' => $orderId,
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Check transaction status
        |--------------------------------------------------------------------------
        */

        $success = $transaction['success'] ?? false;

        /*
        |--------------------------------------------------------------------------
        | Payment successful
        |--------------------------------------------------------------------------
        */

        if (
            $success === true ||
            $success === 'true' ||
            $success === 1 ||
            $success === '1'
        ) {
            $payment->update([
                'status' => 'paid',
                'payment_method' => 'paymob',
                'paymob_transaction_id' => $transactionId,
                'paid_at' => now(),
            ]);

            $payment->booking()->update([
                'status' => 'confirmed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Payment failed
        |--------------------------------------------------------------------------
        */

        $payment->update([
            'status' => 'failed',
            'paymob_transaction_id' => $transactionId,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Payment failed.',
        ]);
    }
}
