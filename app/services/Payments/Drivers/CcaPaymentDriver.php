<?php

namespace App\Services\Payments\Drivers;

use App\Models\PaymentGateway;
use App\Services\Payments\Contracts\PaymentGatewayDriver;

/**
 * CCAvenue-style hosted transaction driver.
 *
 * Builds the merchant fields (merchant_id, order_id, amount, currency,
 * redirect_url, cancel_url, ...), signs them with the CCAvenue SHA-256 scheme
 * (sorted concatenation + working key) and returns the POST form payload for
 * the configured endpoint. Incoming callbacks are verified the same way.
 */
class CcaPaymentDriver implements PaymentGatewayDriver
{
    public function name(): string
    {
        return 'ccavenue';
    }

    public function createPayment(PaymentGateway $gateway, array $payload): array
    {
        $fields = [
            'merchant_id' => (string) $gateway->merchant_id ?: (string) $gateway->access_code,
            'order_id' => (string) ($payload['order_id'] ?? ''),
            'amount' => number_format((float) ($payload['amount'] ?? 0), 2, '.', ''),
            'currency' => strtoupper($payload['currency'] ?? 'INR'),
            'redirect_url' => (string) ($payload['redirect_url'] ?? $payload['return_url'] ?? ''),
            'cancel_url' => (string) ($payload['cancel_url'] ?? ''),
            'language' => 'EN',
            'billing_name' => (string) ($payload['customer_name'] ?? ''),
            'billing_email' => (string) ($payload['customer_email'] ?? ''),
            'billing_tel' => (string) ($payload['customer_phone'] ?? ''),
        ];

        ksort($fields);
        $fields['signature'] = $this->signature($gateway, $fields);
        $fields['request_type'] = 'json';

        $endpoint = rtrim((string) $gateway->endpoint, '/');
        if (!str_contains($endpoint, 'transaction')) {
            $endpoint .= '/transaction/transaction.do?command=initiateTransaction';
        }

        return [
            'method' => 'POST',
            'url' => $endpoint,
            'fields' => $fields,
            'order_id' => (string) ($payload['order_id'] ?? ''),
            'gateway_order_id' => null,
            'amount' => (int) round(((float) ($payload['amount'] ?? 0)) * 100),
            'currency' => strtoupper($payload['currency'] ?? 'INR'),
        ];
    }

    public function verifyCallback(PaymentGateway $gateway, array $input): array
    {
        $orderId = $input['order_id'] ?? $input['orderNo'] ?? null;
        $transactionId = $input['tracking_id'] ?? $input['transaction_id'] ?? null;
        $signature = $input['signature'] ?? $input['checksum'] ?? null;

        if ($signature) {
            $expected = $this->signature($gateway, $input);
            if (!hash_equals($expected, (string) $signature)) {
                return $this->fail('CCAvenue signature verification failed.', $input);
            }
        }

        $status = strtolower((string) ($input['order_status'] ?? $input['orderStatus'] ?? ''));

        $ok = $status === 'success' || $status === 'paid' || $status === 'authorized';

        return [
            'success' => $ok,
            'order_id' => $orderId ? (string) $orderId : null,
            'gateway_order_id' => null,
            'transaction_id' => $transactionId ? (string) $transactionId : ($orderId ? (string) $orderId : null),
            'message' => $ok ? 'CCAvenue payment verified successfully.' : trim((string) ($input['status_message'] ?? 'Payment was not completed.')),
            'raw' => $input,
        ];
    }

    private function signature(PaymentGateway $gateway, array $data): string
    {
        $data = array_filter($data, fn ($k) => !in_array($k, ['signature', 'checksum', 'encResp'], true), ARRAY_FILTER_USE_KEY);

        ksort($data);

        return hash('sha256', implode('', array_map(fn ($v) => (string) $v, $data)) . (string) $gateway->working_key);
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