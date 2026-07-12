<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $sellers = Seller::query()
            ->with([
                'user',
                'services',
                'projects',
                'orders'
            ])
            ->when($request->search, function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('username', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
            })
            ->latest()
            ->paginate(20);

        return view('admin.users.sellers.index', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        $seller->load([
            'user.devices',
            'services.gallery',
            'projects.buyer',
            'orders.buyer'
        ]);

        return view('admin.users.sellers.show', compact('seller'));
    }

    public function toggleStatus(Seller $seller)
    {
        $seller->user->update([
            'status' => !$seller->user->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Seller status updated.'
        ]);
    }

    public function toggleBan(Seller $seller)
    {
        $seller->user->update([
            'is_banned' => !$seller->user->is_banned
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Seller updated.'
        ]);
    }

    public function toggleAvailable(Seller $seller)
    {
        $seller->update([
            'available_for_work' => !$seller->available_for_work
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Availability updated.'
        ]);
    }
}
