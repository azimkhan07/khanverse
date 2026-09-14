<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentGateway;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

/**
 * Order-level payment orchestration built on top of the low-level
 * PaymentService facade / PaymentGatewayManager.
 *
 * Handles: initiating checkout for an order (persists an OrderPayment row),
 * and processing provider webhook/return callbacks (marks the order paid),
 * so the gateway plumbing stays isolated from the rest of the app.
 */
class OrderPaymentService
{
    public function initiate(Order $order, ?PaymentGateway $gateway = null): array
    {
        $gateway = $gateway ?: PaymentService::activeGateway();

        if (!$gateway) {
            throw new \RuntimeException('No active payment gateway configured. Add one from the admin panel.');
        }

        $buyer = $order->buyer;
        $ref = $order->order_number ?: 'ORD-' . $order->id;

        $payload = [
            'order_id' => $ref,
            'amount' => (float) $order->amount,
            'currency' => 'INR',
            'customer_name' => $buyer?->full_name ?: 'Customer',
            'customer_email' => $buyer?->user?->email ?: '',
            'customer_phone' => $buyer?->profile?->phone,
            'redirect_url' => route('api.payment.demo.return', ['gateway' => $gateway->slug]),
            'cancel_url' => route('api.payment.cancel', ['gateway' => $gateway->slug]),
            'notify_url' => route('api.payment.webhook', ['gateway' => $gateway->slug]),
        ];

        $result = PaymentService::createForGateway($gateway, $payload);

        OrderPayment::create([
            'order_id' => $order->id,
            'gateway_id' => $gateway->id,
            'gateway' => $gateway->slug,
            'amount' => $order->amount,
            'status' => 'pending',
            'payload' => $result,
        ]);

        $order->update([
            'payment_status' => 'pending',
            'gateway_id' => $gateway->id,
            'payment_method' => $gateway->slug,
        ]);

        return $result;
    }

    /**
     * Process an inbound provider callback (webhook OR browser return).
     */
    public function handleCallback(string $gatewaySlug, array $input): array
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->first();

        if (!$gateway || !$gateway->is_active) {
            return [
                'success' => false,
                'order' => null,
                'message' => 'Unknown or inactive payment gateway: ' . $gatewaySlug,
            ];
        }

        $result = PaymentService::verifyCallback($input, $gateway);

        $order = $this->resolveOrder($result['order_id'] ?? null);
        if (!$order) {
            return $result + ['order' => null, 'message' => $result['message'] ?: 'Order reference not found.'];
        }

        if ($result['success']) {
            $this->markPaid($order, $gateway, $result);
        } else {
            $order->update(['payment_status' => 'failed']);

            $payment = OrderPayment::firstOrNew(['order_id' => $order->id, 'status' => 'pending']);
            $payment->fill([
                'gateway_id' => $gateway->id,
                'gateway' => $gateway->slug,
                'transaction_id' => $result['transaction_id'],
                'amount' => $order->amount,
                'status' => 'failed',
                'payload' => $result,
            ])->save();
        }

        return $result + ['order' => $order];
    }

    protected function resolveOrder(?string $reference): ?Order
    {
        if (!$reference) {
            return null;
        }

        return Order::where('order_number', $reference)
            ->orWhere('transaction_id', $reference)
            ->first();
    }

    protected function markPaid(Order $order, PaymentGateway $gateway, array $result): void
    {
        DB::transaction(function () use ($order, $gateway, $result) {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $gateway->slug,
                'transaction_id' => $result['transaction_id'] ?: $order->transaction_id,
                'gateway_id' => $gateway->id,
                'paid_at' => now(),
            ]);

            OrderPayment::create([
                'order_id' => $order->id,
                'gateway_id' => $gateway->id,
                'gateway' => $gateway->slug,
                'method' => $gateway->slug,
                'transaction_id' => $result['transaction_id'],
                'amount' => $order->amount,
                'status' => 'success',
                'payload' => $result,
            ]);

            NotificationService::send(
                $order->buyer?->user_id,
                'Payment Successful',
                'Your payment of ₹' . number_format($order->amount, 2) . ' for order #' . $order->id . ' was successful.',
                'order',
                route('buyer.orders.show', $order->id),
                ['order_id' => $order->id, 'transaction_id' => $result['transaction_id']],
            );

            NotificationService::send(
                $order->seller?->user_id,
                'Order Paid',
                'Order "' . ($order->service?->title ?? '#' . $order->id) . '" has been paid and is ready for acceptance.',
                'order',
                route('seller.orders.show', $order->id),
                ['order_id' => $order->id],
            );
        });
    }
}