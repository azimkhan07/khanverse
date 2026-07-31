<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\Review;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $buyer = $user->buyer;
        $totalOrders = Order::where('buyer_id', $buyer->id)->count();
        $activeProjects = Project::where('buyer_id', $buyer->id)->whereNotIn('status', ['completed', 'cancelled'])->count();
        $completedProjects = Project::where('buyer_id', $buyer->id)->where('status', 'completed')->count();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'withdrawn_balance' => 0,
            ]
        );

        $recentOrders = Order::with(['seller.user', 'service',])->where('buyer_id', $buyer->id)->latest()->take(5)->get();
        $reviews = Review::with(['seller.user', 'order',])->where('buyer_id', $buyer->id)->latest()->take(5)->get();
        $projects = Project::with(['seller.user', 'service'])->where('buyer_id', $buyer->id)->latest()->take(5)->get();

        return view('buyer.dashboard.index', compact('totalOrders', 'activeProjects', 'projects', 'completedProjects', 'wallet', 'recentOrders', 'reviews'));
    }
}
