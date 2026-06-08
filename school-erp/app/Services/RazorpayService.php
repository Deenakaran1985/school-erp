<?php
namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    // ── Create Razorpay order ──────────────────────────────
    public function createOrder(
        float  $amount,
        string $currency  = 'INR',
        string $receipt   = '',
        array  $notes     = []
    ): array {
        try {
            $order = $this->api->order->create([
                'amount'          => (int) round($amount * 100), // paise
                'currency'        => $currency,
                'receipt'         => $receipt ?: 'RCP-' . time(),
                'notes'           => $notes,
                'payment_capture' => 1,
            ]);

            return [
                'success'  => true,
                'order_id' => $order->id,
                'amount'   => $order->amount,
                'currency' => $order->currency,
                'key'      => config('services.razorpay.key'),
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Verify payment signature ───────────────────────────
    public function verifyPayment(
        string $orderId,
        string $paymentId,
        string $signature
    ): bool {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::warning('Razorpay signature mismatch: ' . $e->getMessage());
            return false;
        }
    }

    // ── Fetch payment details from Razorpay ────────────────
    public function fetchPayment(string $paymentId): ?array
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);
            return [
                'id'     => $payment->id,
                'amount' => $payment->amount / 100,
                'status' => $payment->status,
                'method' => $payment->method,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay fetch failed: ' . $e->getMessage());
            return null;
        }
    }
}

// ── config/services.php — add: ─────────────────────────────
// 'razorpay' => [
//     'key'    => env('RAZORPAY_KEY'),
//     'secret' => env('RAZORPAY_SECRET'),
// ],