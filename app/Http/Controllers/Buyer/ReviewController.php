<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $buyer = Auth::user()->buyer;
        $reviews = Review::where('buyer_id', $buyer->id)->with(['seller.user', 'order.project'])->latest()->paginate(15);

        return view('buyer.reviews.index', compact('reviews'));
    }

    public function create(Order $order)
    {
        $buyer = Auth::user()->buyer;
        abort_if($order->buyer_id != $buyer->id, 403);
        abort_if($order->status != 'completed', 403);

        if ($order->review) {
            return redirect()->route('buyer.reviews.index')->with('error', 'Review already submitted.');
        }

        return view('buyer.reviews.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating'   => 'required|integer|min:1|max:5',
            'review'   => 'required|string|max:1000',
        ]);

        $buyer = Auth::user()->buyer;
        $order = Order::where('id', $request->order_id)->where('buyer_id', $buyer->id)->where('status', 'completed')->firstOrFail();

        if (Review::where('order_id', $order->id)->exists()) {
            return back()->with(
                'error',
                'You have already reviewed this order.'
            );
        }

        Review::create([
            'order_id'  => $order->id,
            'buyer_id'  => $buyer->id,
            'seller_id' => $order->seller_id,
            'rating'    => $request->rating,
            'review'    => $request->review,
        ]);

        return redirect()->route('buyer.reviews.index')->with('success', 'Review submitted successfully.');
    }

    public function show(Review $review)
    {
        $buyer = Auth::user()->buyer;

        if ($review->buyer_id != $buyer->id) {
            abort(403);
        }

        $review->load(['seller.user', 'order.project',]);

        return view('buyer.reviews.show', compact('review'));
    }
}
