<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\AvailabilityService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $buyer = $user->buyer;
        if (!$buyer) {
            return response()->json(['message' => 'Buyer profile not found.'], 404);
        }

        if (!ProfileService::isComplete($user)) {
            return response()->json([
                'message' => 'Please complete your profile before placing an order.',
                'error_code' => 'profile_incomplete',
                'missing' => ProfileService::completeness($user)['missing'],
            ], 422);
        }

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'requirements' => ['required', 'string'],
            'requirements_title' => ['required', 'string'],
            'requirements_type' => ['required', 'string'],
            'requirements_docs' => ['nullable', 'file', 'max:10240'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_method' => ['nullable', 'in:digital,hosting'],
            'force' => ['nullable', 'boolean'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        if ($service->status !== 'active') {
            return response()->json(['message' => 'This service is not available for ordering.'], 422);
        }

        $seller = $service->seller;

        // Non-technical sellers may set weekly availability. When they are not
        // available right now the buyer is told the next open slot, unless they
        // explicitly choose to request the service anyway.
        if ($seller && AvailabilityService::isConfigured($seller) && !AvailabilityService::isAvailableNow($seller)) {
            if (! ($request->boolean('force') || ($validated['force'] ?? false))) {
                return response()->json([
                    'message' => (($seller->user?->name) ?: 'This seller') . ' is not available right now.',
                    'error_code' => 'seller_unavailable',
                    'availability' => AvailabilityService::nextAvailability($seller),
                ], 422);
            }
        }

        $platformPct = (float) setting('payment', 'platform_fee', 12);
        $platformFee = round($service->price * $platformPct / 100, 2);

        $docsPath = null;
        if ($request->hasFile('requirements_docs') && $request->file('requirements_docs')->isValid()) {
            $docsPath = $request->file('requirements_docs')->store('order-docs', 'public');
        }

        $order = Order::create([
            'order_number' => InvoiceService::nextOrderNumber(),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller ? $seller->id : null,
            'service_id' => $service->id,
            'amount' => $service->price,
            'platform_fee' => $platformFee,
            'status' => 'pending',
            'requirements' => $validated['requirements'],
            'requirements_title' => $validated['requirements_title'],
            'requirements_type' => $validated['requirements_type'],
            'requirements_docs' => $docsPath,
            'delivery_date' => $validated['delivery_date']
                ?? now()->addDays((int) $service->delivery_days)->toDateString(),
        ]);

        NotificationService::send(
            $seller ? $seller->user_id : null,
            'New Order Received',
            'Requirement "' . $validated['requirements_title'] . '" for "' . $service->title . '" worth ₹' . number_format($order->amount, 2) . ' is awaiting your approval.',
            'order',
            route('seller.orders.show', $order->id),
            ['order_id' => $order->id],
        );

        NotificationService::send(
            Auth::id(),
            'Order Placed',
            'Your order "' . $validated['requirements_title'] . '" has been placed and is awaiting seller approval.',
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

    public function cancel($id): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $order = Order::where('buyer_id', $buyer->id)
            ->whereNull('deleted_at')
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'You can only cancel an order that is still pending.',
            ], 422);
        }

        if ((string) $order->payment_status === 'paid' || (string) $order->payment_status === 'completed') {
            return response()->json([
                'message' => 'This order is already paid. Please contact support to request a refund.',
                'error_code' => 'paid_order',
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        NotificationService::send(
            $order->seller?->user_id,
            'Order Cancelled',
            'Buyer "' . ($order->buyer->full_name ?? $order->buyer->user->name) . '" has cancelled the order "' . $order->requirements_title . '".',
            'order',
            $order->seller ? route('seller.orders.show', $order->id) : null,
            ['order_id' => $order->id],
        );

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'order' => $order->fresh(['seller', 'service']),
        ]);
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
