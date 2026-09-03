<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $buyer = Auth::user()->buyer;
        if (!$buyer) {
            return response()->json(['message' => 'Buyer profile not found.'], 404);
        }

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'requirements' => ['nullable', 'string'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_method' => ['nullable', 'in:digital,hosting'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        if ($service->status !== 'active') {
            return response()->json(['message' => 'This service is not available for ordering.'], 422);
        }

        $seller = $service->seller;

        $platformPct = (float) setting('payment', 'platform_fee', 12);
        $platformFee = round($service->price * $platformPct / 100, 2);

        $order = Order::create([
            'order_number' => InvoiceService::nextOrderNumber(),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller ? $seller->id : null,
            'service_id' => $service->id,
            'amount' => $service->price,
            'platform_fee' => $platformFee,
            'status' => 'pending',
            'requirements' => $validated['requirements'] ?? null,
            'delivery_date' => $validated['delivery_date']
                ?? now()->addDays((int) $service->delivery_days)->toDateString(),
        ]);

        NotificationService::send(
            $seller ? $seller->user_id : null,
            'New Order Received',
            'A new order "' . $service->title . '" worth ₹' . number_format($order->amount, 2) . ' is awaiting your approval.',
            'order',
            route('seller.orders.show', $order->id),
            ['order_id' => $order->id],
        );

        NotificationService::send(
            Auth::id(),
            'Order Placed',
            'Your order for "' . $service->title . '" has been placed and is awaiting seller approval.',
            'order',
            route('buyer.orders.show', $order->id),
            ['order_id' => $order->id],
        );

        $order->load(['seller', 'service', 'project']);

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $order,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $query = Order::with(['seller', 'service', 'project'])
            ->where('buyer_id', $buyer->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    public function show($id): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $order = Order::with(['seller', 'service', 'project', 'review', 'invoices'])
            ->where('buyer_id', $buyer->id)
            ->findOrFail($id);

        return response()->json($order);
    }
}
