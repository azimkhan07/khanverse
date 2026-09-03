<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $reviews = Review::with(['buyer', 'order'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($reviews);
    }

    public function show($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $review = Review::with(['buyer', 'order'])
            ->where('seller_id', $seller->id)
            ->findOrFail($id);

        return response()->json($review);
    }
}
