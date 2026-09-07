<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminSettlement;
use App\Models\Order;
use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $query = Order::with(['buyer', 'service', 'project'])
            ->where('seller_id', $seller->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    public function show($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $order = Order::with(['buyer', 'service', 'project', 'review', 'invoices'])
            ->where('seller_id', $seller->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    public function changeStatus(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'status' => 'required|in:pending,active,delivered,completed,cancelled,disputed',
            'decline_reason' => 'nullable|string|max:1000',
        ]);

        $order = Order::with(['buyer', 'service'])->where('seller_id', $seller->id)->findOrFail($id);

        if ($request->status === 'active' && $order->status === 'pending') {
            $order->update(['status' => 'active']);

            $project = $this->createProjectForOrder($order, $seller);

            // For hosting projects generate the delivery key now, at accept time,
            // so the seller is shown (and remembers) it before any handover begins.
            $deliveryKey = null;
            if ($project->delivery_method === 'hosting') {
                $deliveryKey = $project->ensureDeliveryKey();
            }

            NotificationService::send(
                $order->buyer?->user_id,
                'Order Accepted',
                'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" has been accepted. A project has been started.',
                'order',
                route('buyer.projects.show', $project->id),
                ['order_id' => $order->id, 'project_id' => $project->id],
            );

            return response()->json([
                'message' => 'Order accepted. Project #' . $project->id . ' has been created.',
                'order' => $order->fresh(['buyer', 'service', 'project']),
                'project' => $project,
                'delivery_key' => $deliveryKey,
            ]);
        }

        if ($request->status === 'delivered' && $order->status === 'active') {
            if (!$order->project || $order->project->status !== 'delivered') {
                return response()->json([
                    'message' => 'Mark the linked project as delivered first, then you can mark the order delivered.',
                ], 422);
            }

            $order->update(['status' => 'delivered']);

            if ($order->project) {
                NotificationService::send(
                    $order->buyer?->user_id,
                    'Order Delivered',
                    'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" has been delivered. Kindly review the work.',
                    'order',
                    route('buyer.projects.show', $order->project_id),
                    ['order_id' => $order->id, 'project_id' => $order->project_id],
                );
            } else {
                NotificationService::send(
                    $order->buyer?->user_id,
                    'Order Delivered',
                    'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" has been delivered. Kindly review the work.',
                    'order',
                    route('buyer.orders.show', $order->id),
                    ['order_id' => $order->id],
                );
            }
        }

        if ($request->status === 'completed' && $order->status === 'delivered') {
            $order->update(['status' => 'completed']);
            if ($order->project) {
                $order->project->update(['status' => 'completed']);
            }

            // Auto-generate buyer + seller invoices for the completed order.
            $invoices = \App\Services\InvoiceService::generateForOrder($order);

            foreach ($invoices as $type => $invoiceData) {
                if (!$invoiceData) {
                    continue;
                }
                if ($type === 'buyer' && $order->buyer) {
                    NotificationService::send(
                        $order->buyer?->user_id,
                        'Invoice Ready',
                        'Your invoice ' . $invoiceData->invoice_number . ' for "' . ($order->service?->title ?? '#' . $order->id) . '" is ready to download.',
                        'invoice',
                        route('buyer.orders.show', $order->id),
                        ['order_id' => $order->id, 'invoice_id' => $invoiceData->id],
                    );
                }
                if ($type === 'seller') {
                    NotificationService::send(
                        $seller->user_id,
                        'Invoice Ready',
                        'Invoice ' . $invoiceData->invoice_number . ' for "' . ($order->service?->title ?? '#' . $order->id) . '" is ready to download.',
                        'invoice',
                        route('seller.orders.show', $order->id),
                        ['order_id' => $order->id, 'invoice_id' => $invoiceData->id],
                    );
                }
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

            NotificationService::send(
                $order->buyer?->user_id,
                'Order Completed',
                'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" has been completed. Thank you!',
                'order',
                $order->project_id ? route('buyer.projects.show', $order->project_id) : route('buyer.orders.show', $order->id),
                ['order_id' => $order->id, 'project_id' => $order->project_id],
            );
        }

        if ($request->status === 'cancelled' && $order->status !== 'completed') {
            $reason = trim((string) $request->input('decline_reason', ''));
            $wasPending = $order->status === 'pending';

            if ($wasPending && $reason === '') {
                return response()->json([
                    'message' => 'Please provide a reason for declining this order.',
                ], 422);
            }

            $order->update([
                'status' => 'cancelled',
                'decline_reason' => $reason ?: $order->decline_reason,
            ]);
            if ($order->project) {
                $order->project->update(['status' => 'cancelled']);
            }

            $notice = $reason
                ? 'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" was declined. Reason: ' . $reason
                : 'Your order "' . ($order->service?->title ?? '#' . $order->id) . '" has been cancelled.';

            NotificationService::send(
                $order->buyer?->user_id,
                $wasPending ? 'Order Declined' : 'Order Cancelled',
                $notice,
                'order',
                $order->project_id ? route('buyer.projects.show', $order->project_id) : route('buyer.orders.show', $order->id),
                ['order_id' => $order->id, 'decline_reason' => $reason],
            );
        }

        NotificationService::send(
            $seller->user_id,
            'Order Status Updated',
            'Order "' . ($order->service?->title ?? '#' . $order->id) . '" is now "' . $request->status . '".',
            'order',
            route('seller.orders.show', $order->id),
            ['order_id' => $order->id],
        );

        return response()->json([
            'message' => 'Order status updated.',
            'order' => $order->fresh(['buyer', 'service', 'project', 'invoices']),
        ]);
    }

    private function createProjectForOrder(Order $order, $seller): Project
    {
        $service = $order->service;
        $title = $service ? $service->title : 'Project #' . $order->id;

        return Project::create([
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $order->id . '-' . Str::random(4),
            'description' => $order->requirements ?: ($service ? $service->description : ''),
            'budget' => $order->amount,
            'deadline' => $order->delivery_date,
            'status' => 'in_progress',
            'buyer_id' => $order->buyer_id,
            'seller_id' => $seller->id,
            'service_id' => $service ? $service->id : null,
            'delivery_method' => $service && $service->delivery_method ? $service->delivery_method : 'digital',
        ])->tap(function ($project) use ($order) {
            $order->update(['project_id' => $project->id]);
        });
    }
}
