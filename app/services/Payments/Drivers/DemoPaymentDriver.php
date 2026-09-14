<?php

namespace App\Services\Payments\Drivers;

use App\Models\PaymentGateway;
use App\Services\Payments\Contracts\PaymentGatewayDriver;

/**
 * Demo driver for local/QA testing.
 *
 * Redirects the buyer to our own simulated return URL which auto-approves the
 * payment, so the full checkout -> webhook/return -> mark-paid flow can be
 * exercised without a real merchant account.
 */
class DemoPaymentDriver implements PaymentGatewayDriver
{
    public function name(): string
    {
        return 'demo';
    }

    public function createPayment(PaymentGateway $gateway, array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $query = http_build_query([
            'order_id' => $orderId,
            'amount' => number_format((float) ($payload['amount'] ?? 0), 2, '.', ''),
            'checksum' => $this->checksum($gateway, $orderId),
        ]);

        return [
            'method' => 'GET',
            'url' => route('api.payment.demo.return', ['gateway' => 'demo']) . '?' . $query,
            'fields' => [],
            'order_id' => $orderId,
            'gateway_order_id' => $orderId,
            'amount' => (int) round(((float) ($payload['amount'] ?? 0)) * 100),
            'currency' => strtoupper($payload['currency'] ?? 'INR'),
        ];
    }

    public function verifyCallback(PaymentGateway $gateway, array $input): array
    {
        $orderId = $input['order_id'] ?? null;
        $amount = $input['amount'] ?? null;
        $checksum = $input['checksum'] ?? null;

        if ($checksum && $orderId && hash_equals($this->checksum($gateway, (string) $orderId), (string) $checksum)) {
            return [
                'success' => true,
                'order_id' => (string) $orderId,
                'gateway_order_id' => (string) $orderId,
                'transaction_id' => 'DEMO-' . strtoupper(substr(md5((string) $amount . $orderId), 0, 12)),
                'message' => 'Demo payment verified successfully.',
                'raw' => $input,
            ];
        }

        return [
            'success' => false,
            'order_id' => $orderId ? (string) $orderId : null,
            'gateway_order_id' => null,
            'transaction_id' => null,
            'message' => 'Demo payment signature verification failed.',
            'raw' => $input,
        ];
    }

    private function checksum(PaymentGateway $gateway, string $orderId): string
    {
        return hash('sha256', $orderId . (string) $gateway->working_key);
    }
}