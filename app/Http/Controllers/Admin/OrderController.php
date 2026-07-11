<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with([
            'buyer.user',
            'seller.user',
            'service',
            'project'
        ])

            ->when($request->search, function ($q) use ($request) {

                $search = $request->search;

                $q->whereHas('buyer.user', function ($buyer) use ($search) {

                    $buyer->where('username', 'like', "%{$search}%");
                })

                    ->orWhereHas('seller.user', function ($seller) use ($search) {

                        $seller->where('username', 'like', "%{$search}%");
                    })

                    ->orWhereHas('service', function ($service) use ($search) {

                        $service->where('title', 'like', "%{$search}%");
                    });
            })

            ->when($request->status, function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->latest()

            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {

        $order->load([
            'buyer.user',
            'seller.user',
            'service',
            'project'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function changeStatus(Request $request, Order $order)
    {

        $request->validate([

            'status' => 'required'

        ]);

        $order->update([

            'status' => $request->status

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Order status updated.',

            'reload' => true

        ]);
    }
}
