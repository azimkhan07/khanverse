<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $buyer = Auth::user()->buyer;
        $orders = Order::with(['seller.user','service','project'])->where('buyer_id', $buyer->id)->latest()->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $buyer = Auth::user()->buyer;

        if ($order->buyer_id != $buyer->id) {
            abort(403);
        }

        $order->load(['seller.user','service','project','review']);

        return view('buyer.orders.show', compact('order'));
    }
}
