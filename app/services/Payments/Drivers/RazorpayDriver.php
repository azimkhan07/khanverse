<?php

namespace App\Services\Payments\Drivers;

use App\Models\PaymentGateway;
use App\Services\Payments\Contracts\PaymentGatewayDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Razorpay driver.
 *
 * Uses Razorpay's API to create an order (access_code = key id,
 * working_key = key secret) and hands the buyer over to the Razorpay hosted
 * checkout page. On return the signature is verified:
 * hash_hmac('sha256', order_id.'|'.payment_id, key_secret).
 */
class RazorpayDriver implements PaymentGatewayDriver
{
    public function name(): string
    {
        return 'razorpay';
    }

    public function createPayment(PaymentGateway $gateway, array $payload): array
    {
        $base = $this->apiBase($gateway);
        $amount = (int) round(((float) ($payload['amount'] ?? 0)) * 100);
        $currency = strtoupper($payload['currency'] ?? 'INR');
        $orderId = (string) ($payload['order_id'] ?? Str::uuid()->toString());

        $response = Http::withBasicAuth((string) $gateway->access_code, (string) $gateway->working_key)
            ->asJson()
            ->timeout(30)
            ->post($base.'/orders', [
                'amount' => $amount,
                'currency' => $currency,
                'receipt' => $orderId,
                'notes' => ['order_id' => $orderId],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Razorpay order creation failed: '.$response->body());
        }

        $data = $response->json();
        $razorpayOrderId = $data['id'] ?? null;

        if (! $razorpayOrderId) {
            throw new \RuntimeException('Razorpay did not return an order id.');
        }

        return [
            'method' => 'GET',
            'url' => "https://checkout.razorpay.com/v1/payment/{$razorpayOrderId}",
            'fields' => [],
            'order_id' => $orderId,
            'gateway_order_id' => $razorpayOrderId,
            'amount' => $amount,
            'currency' => $currency,
        ];
    }

    public function verifyCallback(PaymentGateway $gateway, array $input): array
    {
        $orderId = $input['razorpay_order_id'] ?? null;
        $paymentId = $input['razorpay_payment_id'] ?? null;
        $signature = $input['razorpay_signature'] ?? null;

        if (! $orderId || ! $paymentId || ! is_string($signature)) {
            return $this->fail('Incomplete Razorpay callback data.', $input);
        }

        $expected = hash_hmac('sha256', (string) $orderId.'|'.(string) $paymentId, (string) $gateway->working_key);
        $ok = hash_equals($expected, $signature);

        return [
            'success' => $ok,
            'order_id' => (string) $orderId,
            'gateway_order_id' => (string) $orderId,
            'transaction_id' => (string) $paymentId,
            'message' => $ok ? 'Razorpay payment verified successfully.' : 'Razorpay signature verification failed.',
            'raw' => $input,
        ];
    }

    private function apiBase(PaymentGateway $gateway): string
    {
        $endpoint = trim((string) $gateway->endpoint);

        if ($endpoint !== '' && str_contains($endpoint, 'api.razorpay.com')) {
            return rtrim($endpoint, '/');
        }

        return 'https://api.razorpay.com/v1';
    }

    private function fail(string $message, array $raw): array
    {
        return [
            'success' => false,
            'order_id' => null,
            'transaction_id' => null,
            'message' => $message,
            'raw' => $raw,
        ];
    }
}