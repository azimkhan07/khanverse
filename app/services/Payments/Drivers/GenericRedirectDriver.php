<?php

namespace App\Services\Payments\Drivers;

use App\Models\PaymentGateway;
use App\Services\Payments\Contracts\PaymentGatewayDriver;

/**
 * Generic "signed redirect" driver.
 *
 * Covers the classic hash/checksum based gateways (PayU, Paytm, Instamojo,
 * Cashfree legacy, etc.): build fields, sign them with a HMAC-SHA256 signature
 * using the working key, POST to the endpoint, then verify the callback with
 * the same signature routine. Works for any account of such a provider purely
 * from the admin form data.
 */
class GenericRedirectDriver implements PaymentGatewayDriver
{
    public function name(): string
    {
        return 'generic-redirect';
    }

    public function createPayment(PaymentGateway $gateway, array $payload): array
    {
        $fields = [
            'merchant_id' => (string) $gateway->merchant_id ?: (string) $gateway->access_code,
            'order_id' => (string) ($payload['order_id'] ?? ''),
            'amount' => number_format((float) ($payload['amount'] ?? 0), 2, '.', ''),
            'currency' => strtoupper($payload['currency'] ?? 'INR'),
            'customer_name' => $payload['customer_name'] ?? '',
            'customer_email' => $payload['customer_email'] ?? '',
            'customer_phone' => $payload['customer_phone'] ?? '',
            'return_url' => $payload['return_url'] ?? '',
            'notify_url' => $payload['notify_url'] ?? '',
        ];

        $fields['signature'] = $this->sign($gateway, $fields);

        return [
            'method' => 'POST',
            'url' => (string) $gateway->endpoint,
            'fields' => $fields,
            'order_id' => (string) $payload['order_id'],
            'gateway_order_id' => null,
            'amount' => (int) round(((float) ($payload['amount'] ?? 0)) * 100),
            'currency' => strtoupper($payload['currency'] ?? 'INR'),
        ];
    }

    public function verifyCallback(PaymentGateway $gateway, array $input): array
    {
        $signature = $input['signature'] ?? $input['hash'] ?? $input['checksum'] ?? null;

        if (! is_string($signature)) {
            return $this->fail('No signature supplied in the callback.', $input);
        }

        $expected = $this->sign($gateway, $input);
        $ok = hash_equals($expected, $signature);

        return [
            'success' => $ok,
            'order_id' => $input['order_id'] ?? null,
            'gateway_order_id' => null,
            'transaction_id' => $input['txn_id'] ?? $input['transaction_id'] ?? null,
            'message' => $ok ? 'Payment signature verified.' : 'Payment signature verification failed.',
            'raw' => $input,
        ];
    }

    /**
     * HMAC-SHA256 over the sorted "key=value" pairs joined by "|".
     * Signature meta fields are excluded so both sides sign the same data.
     */
    private function sign(PaymentGateway $gateway, array $data): string
    {
        foreach (['signature', 'hash', 'checksum', 'status', 'txn_id', 'gateway_txn'] as $token) {
            unset($data[$token]);
        }

        ksort($data);

        return hash_hmac(
            'sha256',
            implode('|', array_map(fn ($v) => (string) $v, array_values($data))),
            (string) $gateway->working_key
        );
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