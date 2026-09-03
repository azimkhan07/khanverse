<?php

namespace App\Services\Payments;

use App\Models\PaymentGateway;
use App\Services\Payments\Contracts\PaymentGatewayDriver;
use App\Services\Payments\Drivers\GenericRedirectDriver;
use App\Services\Payments\Drivers\RazorpayDriver;

/**
 * Routes a gateway row to the driver that knows how to talk to that provider.
 *
 * slug => driver class. Unknown slugs fall back to the generic signed-redirect
 * driver, so hash/checksum based gateways (PayU, Paytm, Instamojo, ...) work
 * straight from the admin form with no extra code per account.
 */
class PaymentGatewayManager
{
    protected array $drivers;

    public function __construct(array $drivers = [])
    {
        $this->drivers = $drivers ?: [
            'razorpay' => RazorpayDriver::class,
        ];
    }

    public function driver(PaymentGateway $gateway): PaymentGatewayDriver
    {
        $class = $this->drivers[$gateway->slug] ?? GenericRedirectDriver::class;

        return app($class);
    }

    public function createPayment(PaymentGateway $gateway, array $payload): array
    {
        return $this->driver($gateway)->createPayment($gateway, $payload);
    }

    public function verifyCallback(PaymentGateway $gateway, array $input): array
    {
        try {
            return $this->driver($gateway)->verifyCallback($gateway, $input);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'message' => $e->getMessage(),
                'raw' => $input,
            ];
        }
    }
}