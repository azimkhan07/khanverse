<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $sellerId = Auth::user()->seller->id;
        $reviews = Review::with(['buyer', 'order'])->where('seller_id', $sellerId)->latest()->paginate(20);

        return view('seller.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        abort_if($review->seller_id != Auth::user()->seller->id, 403);

        $review->load(['buyer', 'order']);

        return view('seller.reviews.show', compact('review'));
    }

    public function statistics()
    {
        $sellerId = Auth::user()->seller->id;

        return [
            'total' => Review::where('seller_id', $sellerId)->count(),
            'average' => round(Review::where('seller_id', $sellerId)->avg('rating'),1 ),

            'five' => Review::where('seller_id', $sellerId)->where('rating', 5)->count(),
            'four' => Review::where('seller_id',$sellerId )->where('rating', 4)->count(),
            'three' => Review::where( 'seller_id', $sellerId)->where('rating', 3)->count(),
            'two' => Review::where('seller_id', $sellerId )->where('rating', 2)->count(),
            'one' => Review::where('seller_id', $sellerId)->where('rating', 1)->count(),
        ];
    }
}
