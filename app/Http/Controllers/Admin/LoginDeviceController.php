<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class LoginDeviceController extends Controller
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
            ->paginate(20);

        return view('admin.users.devices.index', compact('devices'));
    }
}
