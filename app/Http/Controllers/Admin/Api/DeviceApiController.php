<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceApiController extends Controller
{
    public function index(Request $request)
    {
        $devices = UserDevice::query()
            ->with('user')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', function ($query) use ($request) {
                    $query->where('username', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->latest('last_activity')
            ->paginate($request->per_page ?? 10);

        return response()->json($devices);
    }
}

