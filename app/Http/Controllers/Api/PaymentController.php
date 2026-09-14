<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(protected OrderPaymentService $payments)
    {
    }

    /**
     * Initiate checkout for an order (buyer only, order owner only).
     */
    public function initiate(Request $request, $id): JsonResponse
    {
        $order = Order::where('buyer_id', Auth::user()->buyer?->id)->findOrFail($id);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'This order is already paid.',
                'redirect_url' => '/buyer/orders/' . $order->id,
            ]);
        }

        try {
            $result = $this->payments->initiate($order);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Could not initiate payment.',
            ], 422);
        }

        return response()->json([
            'message' => 'Payment initiated.',
            'method' => $result['method'] ?? 'GET',
            'url' => $result['url'] ?? null,
            'fields' => $result['fields'] ?? [],
            'order_id' => $result['order_id'] ?? null,
            'gateway_order_id' => $result['gateway_order_id'] ?? null,
            'amount' => $result['amount'] ?? null,
            'currency' => $result['currency'] ?? 'INR',
        ]);
    }

    /**
     * Gateway webhook entry point (CSRF-exempt, no session auth required).
     */
    public function webhook(Request $request, string $gateway): JsonResponse
    {
        $input = array_merge($request->query(), $request->input());
        $result = $this->payments->handleCallback($gateway, $input);

        if ($result['success'] ?? false) {
            return response()->json(['status' => 'success']);
        }

        return response()->json([
            'status' => 'failed',
            'message' => $result['message'] ?? 'Payment verification failed.',
        ], 422);
    }

    /**
     * Demo gateway simulated return URL (CSRF-exempt).
     */
    public function demoReturn(Request $request, string $gateway)
    {
        $input = array_merge($request->query(), $request->input());
        $result = $this->payments->handleCallback($gateway, $input);

        $order = $result['order'] ?? null;

        if ($result['success'] ?? false) {
            return redirect('/buyer/orders/' . ($order?->id ?? ''));
        }

        return redirect('/buyer/orders/' . ($order?->id ?? ''))->withErrors([
            'payment' => $result['message'] ?? 'Payment was not completed.',
        ]);
    }

    /**
     * Cancelled payment return URL.
     */
    public function cancel(Request $request, string $gateway)
    {
        $order = Order::where('order_number', $request->input('order_id'))->first();

        return redirect('/buyer/orders/' . ($order?->id ?? ''));
    }
}