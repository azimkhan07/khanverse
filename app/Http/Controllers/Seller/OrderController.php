<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display Seller Orders
     */
    public function index()
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $orders = Order::with([
            'buyer',
            'service',
            'project'
        ])
            ->where('seller_id', optional($seller)->id)
            ->latest()
            ->paginate(10);

        return view(
            'seller.orders.index',
            compact(
                'seller',
                'orders'
            )
        );
    }

    /**
     * Show Order Details
     */
    public function show($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $order = Order::with([
            'buyer',
            'seller.user',
            'service',
            'project'
        ])
            ->where('seller_id', optional($seller)->id)
            ->findOrFail($id);

        return view(
            'seller.orders.show',
            compact(
                'seller',
                'order'
            )
        );
    }

    /**
     * Change Order Status
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,active,delivered,completed,cancelled',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();

        $order = Order::where(
            'seller_id',
            optional($seller)->id
        )->findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Order status updated successfully.'
        );
    }

    /**
     * Mark Order as Completed
     */
    public function complete($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $order = Order::where(
            'seller_id',
            optional($seller)->id
        )->findOrFail($id);

        if ($order->status != 'delivered') {

            return back()->with(
                'error',
                'Only delivered orders can be completed.'
            );
        }

        $order->update([
            'status' => 'completed',
        ]);

        // Future:
        // Wallet Credit
        // Notification
        // Review Enabled

        return back()->with(
            'success',
            'Order completed successfully.'
        );
    }

    /**
     * Seller cannot delete Orders
     */
    public function destroy($id)
    {
        abort(403);
    }

    /**
     * Placeholder
     * Buyer requests revision.
     */
    public function requestRevision($id)
    {
        return back()->with(
            'info',
            'Revision feature will be available after Buyer module.'
        );
    }
}
