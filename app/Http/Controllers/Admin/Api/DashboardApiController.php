<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Order;
use App\Models\Project;
use App\Models\Seller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        $totalSellers = Seller::count();
        $totalBuyers = Buyer::count();
        $totalServices = Service::count();
        $totalOrders = Order::count();
        $totalProjects = Project::count();
        $totalRevenue = Order::where('status', 'completed')->sum('amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        $recentOrders = Order::with(['buyer', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        $recentSellers = User::where('role', 'seller')
            ->latest()
            ->take(5)
            ->get();

        $recentBuyers = User::where('role', 'buyer')
            ->latest()
            ->take(5)
            ->get();

        $activities = collect();

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'title' => 'New Order #' . $order->id,
                'time' => $order->created_at->diffForHumans(),
            ]);
        }

        foreach ($recentSellers as $seller) {
            $activities->push([
                'type' => 'seller',
                'title' => $seller->name . ' joined as Seller',
                'time' => $seller->created_at->diffForHumans(),
            ]);
        }

        foreach ($recentBuyers as $buyer) {
            $activities->push([
                'type' => 'buyer',
                'title' => $buyer->name . ' joined as Buyer',
                'time' => $buyer->created_at->diffForHumans(),
            ]);
        }

        $activities = $activities->sortByDesc('time')->take(10)->values();

        return response()->json([
            'stats' => [
                'total_sellers' => $totalSellers,
                'total_buyers' => $totalBuyers,
                'total_services' => $totalServices,
                'total_orders' => $totalOrders,
                'total_projects' => $totalProjects,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
            ],
            'recent_orders' => $recentOrders,
            'activities' => $activities,
        ]);
    }
}
