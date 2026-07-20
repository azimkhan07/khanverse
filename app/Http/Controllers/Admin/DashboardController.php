<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Order;
use App\Models\Project;
use App\Models\Seller;
use App\Models\Service;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $totalSellers = Seller::count();

        $totalBuyers = Buyer::count();

        $totalServices = Service::count();

        $totalOrders = Order::count();

        $totalProjects = Project::count();

        $totalRevenue = Order::where('status', 'completed')->sum('amount');

        $pendingOrders = Order::where('status', 'pending')->count();

        $completedOrders = Order::where('status', 'completed')->count();


        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders = Order::with(['buyer', 'seller'])
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Recent Users
        |--------------------------------------------------------------------------
        */

        $recentSellers = User::where('role', 'seller')
            ->latest()
            ->take(5)
            ->get();

        $recentBuyers = User::where('role', 'buyer')
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Recent Activities
        |--------------------------------------------------------------------------
        */

        $activities = collect();

        foreach ($recentOrders as $order) {

            $activities->push([

                'type'  => 'order',

                'title' => 'New Order #' . $order->id,

                'time'  => $order->created_at->diffForHumans(),

            ]);
        }

        foreach ($recentSellers as $seller) {

            $activities->push([

                'type'  => 'seller',

                'title' => $seller->name . ' joined as Seller',

                'time'  => $seller->created_at->diffForHumans(),

            ]);
        }

        foreach ($recentBuyers as $buyer) {

            $activities->push([

                'type'  => 'buyer',

                'title' => $buyer->name . ' joined as Buyer',

                'time'  => $buyer->created_at->diffForHumans(),

            ]);
        }

        $activities = $activities->sortByDesc('time')->take(10);


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard.index', compact(

            'totalSellers',
            'totalBuyers',
            'totalServices',
            'totalOrders',
            'totalProjects',
            'totalRevenue',
            'pendingOrders',
            'completedOrders',

            'recentOrders', 'recentSellers', 'recentBuyers', 'activities'
        ));
    }
}
