<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Services\Payments\PaymentGatewayManager;

/**
 * Provider-agnostic payment flow.
 *
 * Every call dispatches to a driver for the active/default gateway stored in
 * the payment_gateways table. Switching a gateway from the admin panel is a
 * data change - the code path stays identical.
 *
 * @see \App\Services\Payments\Contracts\PaymentGatewayDriver
 */
class PaymentService
{
    /**
     * Resolve the gateway that should actually be used for payments.
     * Prefers the default one, then any active one.
     */
    public static function activeGateway(): ?PaymentGateway
    {
        return PaymentGateway::where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }

    /**
     * Build the payment request for the active gateway.
     */
    public static function createPayment(array $payload): array
    {
        $gateway = self::activeGateway();

        if (! $gateway) {
            throw new \RuntimeException('No active payment gateway configured. Add one from the admin panel.');
        }

        return self::manager()->createPayment($gateway, $payload);
    }

    /**
     * Build the payment request for a specific gateway (e.g. one stored on an order).
     */
    public static function createForGateway(PaymentGateway $gateway, array $payload): array
    {
        return self::manager()->createPayment($gateway, $payload);
    }

    /**
     * Verify a callback/return. Uses the given gateway, or falls back to the
     * active/default one.
     */
    public static function verifyCallback(array $input, ?PaymentGateway $gateway = null): array
    {
        $gateway = $gateway ?? self::activeGateway();

        if (! $gateway) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'message' => 'No active payment gateway configured.',
                'raw' => $input,
            ];
        }

        return self::manager()->verifyCallback($gateway, $input);
    }

    private static function manager(): PaymentGatewayManager
    {
        return new PaymentGatewayManager();
    }
}