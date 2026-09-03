<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\Review;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $buyer = $user->buyer;

        $totalOrders = Order::where('buyer_id', $buyer->id)->count();
        $activeProjects = Project::where('buyer_id', $buyer->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $completedProjects = Project::where('buyer_id', $buyer->id)
            ->where('status', 'completed')
            ->count();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0, 'withdrawn_balance' => 0]
        );

        $recentOrders = Order::with(['seller', 'service'])
            ->where('buyer_id', $buyer->id)
            ->latest()
            ->take(5)
            ->get();

        $reviews = Review::with(['seller', 'order'])
            ->where('buyer_id', $buyer->id)
            ->latest()
            ->take(5)
            ->get();

        $projects = Project::with(['seller', 'service'])
            ->where('buyer_id', $buyer->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_orders' => $totalOrders,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'wallet_balance' => $wallet->balance,
            ],
            'recent_orders' => $recentOrders,
            'recent_reviews' => $reviews,
            'recent_projects' => $projects,
        ]);
    }
}
