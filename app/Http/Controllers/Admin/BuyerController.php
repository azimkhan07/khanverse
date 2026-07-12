<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $buyers = Buyer::query()
            ->with([
                'user',
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

        return view('admin.users.buyers.index', compact('buyers'));
    }

    public function show(Buyer $buyer)
    {
        $buyer->load([
            'user.devices',
            'projects.service',
            'orders.service',
            'orders.seller'
        ]);

        return view('admin.users.buyers.show', compact('buyer'));
    }

    public function toggleStatus(Buyer $buyer)
    {
        $buyer->user->update([
            'status' => !$buyer->user->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Buyer status updated.'
        ]);
    }

    public function toggleBan(Buyer $buyer)
    {
        $buyer->user->update([
            'is_banned' => !$buyer->user->is_banned
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Buyer updated.'
        ]);
    }
}
