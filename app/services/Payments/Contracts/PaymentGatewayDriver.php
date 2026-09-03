<?php

namespace App\Services\Payments\Contracts;

use App\Models\PaymentGateway;

/**
 * A payment gateway driver knows how to talk to one specific provider
 * (create the payment and verify the callback). The rest of the code only
 * ever works with this contract - so adding/switching a provider is a data
 * change plus one small driver class (which is shared for ALL accounts of
 * that provider).
 */
interface PaymentGatewayDriver
{
    public function name(): string;

    /**
     * Prepare everything needed to start a payment.
     *
     * @return array{method: string, url: string, fields: array, order_id: string, gateway_order_id: string|null, amount: int, currency: string}
     */
    public function createPayment(PaymentGateway $gateway, array $payload): array;

    /**
     * Validate a callback/return from the provider.
     *
     * @return array{success: bool, order_id: string|null, transaction_id: string|null, message: string, raw: array}
     */
    public function verifyCallback(PaymentGateway $gateway, array $input): array;
}