<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminSettlement;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['buyer', 'seller', 'service', 'project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $s = "%{$request->search}%";
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', $s)
                    ->orWhereHas('buyer', fn ($b) => $b->where('full_name', 'like', $s))
                    ->orWhereHas('seller', fn ($b) => $b->where('full_name', 'like', $s))
                    ->orWhereHas('service', fn ($b) => $b->where('title', 'like', $s));
            });
        }

        $orders = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($orders);
    }

    public function show($id): JsonResponse
    {
        $order = Order::with(['buyer', 'seller', 'service', 'project', 'review', 'invoices'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function changeStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,active,delivered,completed,cancelled,disputed',
        ]);

        $order = Order::with(['buyer', 'seller', 'service', 'project'])
            ->findOrFail($id);

        $order->update(['status' => $request->status]);

        if ($request->status === 'completed') {
            $invoices = InvoiceService::generateForOrder($order);

            foreach ($invoices as $type => $invoiceData) {
                if (!$invoiceData) {
                    continue;
                }
                $userId = $type === 'buyer' ? $order->buyer?->user_id : $order->seller?->user_id;
                if (!$userId) {
                    continue;
                }
                NotificationService::send(
                    $userId,
                    'Invoice Ready',
                    'Your invoice ' . $invoiceData->invoice_number . ' for "' . ($order->service?->title ?? '#' . $order->id) . '" is ready to download.',
                    'invoice',
                    route($type . '.orders.show', $order->id),
                    ['order_id' => $order->id, 'invoice_id' => $invoiceData->id],
                );
            }

            // Create admin settlement record
            $platformPct = (float) setting('payment', 'platform_fee', 12);
            $platformFee = (float) $order->platform_fee;
            $sellerAmount = (float) $order->amount - $platformFee;

            if (!AdminSettlement::where('order_id', $order->id)->exists()) {
                AdminSettlement::create([
                    'order_id' => $order->id,
                    'project_id' => $order->project_id,
                    'seller_id' => $order->seller_id,
                    'buyer_id' => $order->buyer_id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $invoices['buyer']->invoice_number ?? null,
                    'order_amount' => $order->amount,
                    'platform_fee' => $platformFee,
                    'seller_amount' => $sellerAmount,
                    'platform_pct' => $platformPct,
                    'status' => 'pending',
                ]);
            }
        }

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->fresh(['buyer', 'seller', 'service', 'project', 'invoices']),
        ]);
    }
}

