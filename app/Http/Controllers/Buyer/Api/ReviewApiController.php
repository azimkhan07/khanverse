<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $reviews = Review::with(['seller', 'order'])
            ->where('buyer_id', $buyer->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($reviews);
    }

    public function show($id): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $review = Review::with(['seller', 'order'])
            ->where('buyer_id', $buyer->id)
            ->findOrFail($id);

        return response()->json($review);
    }

    public function store(Request $request): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'seller_id' => 'required|exists:sellers,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        $review = Review::create([
            'order_id' => $validated['order_id'],
            'buyer_id' => $buyer->id,
            'seller_id' => $validated['seller_id'],
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }
}
