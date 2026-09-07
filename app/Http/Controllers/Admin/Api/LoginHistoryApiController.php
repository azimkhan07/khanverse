<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryApiController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginHistory::query()
            ->with('user')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('user', function ($query) use ($request) {
                    $query->where('email', 'like', '%' . $request->search . '%')
                        ->orWhere('name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('is_successful'), function ($q) use ($request) {
                $q->where('is_successful', $request->boolean('is_successful'));
            })
            ->latest('login_at');

        $perPage = min(100, $request->integer('per_page', 15));
        $history = $query->paginate($perPage);

        return response()->json($history);
    }
}
