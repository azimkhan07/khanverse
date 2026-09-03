<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $seller = $user->seller;

        $totalServices = Service::where('seller_id', $seller->id)->count();
        $totalOrders = Order::where('seller_id', $seller->id)->count();
        $activeProjects = Project::where('seller_id', $seller->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $completedOrders = Order::where('seller_id', $seller->id)
            ->where('status', 'completed')
            ->count();
        $totalEarnings = $seller->total_earning ?? 0;
        $averageRating = Review::where('seller_id', $seller->id)->avg('rating');

        $recentOrders = Order::with(['buyer', 'service'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(5)
            ->get();

        $recentProjects = Project::with(['buyer', 'service'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_services' => $totalServices,
                'total_orders' => $totalOrders,
                'active_projects' => $activeProjects,
                'completed_orders' => $completedOrders,
                'total_earnings' => $totalEarnings,
                'average_rating' => round($averageRating ?? 0, 2),
            ],
            'recent_orders' => $recentOrders,
            'recent_projects' => $recentProjects,
        ]);
    }
}
