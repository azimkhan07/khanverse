<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuspiciousUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->select(
                'users.*',
                DB::raw('(SELECT COUNT(*) FROM user_devices ud WHERE ud.ip_address = (
                    SELECT ip_address
                    FROM user_devices
                    WHERE user_id = users.id
                    ORDER BY last_activity DESC
                    LIMIT 1
                )) as same_ip_accounts')
            )
            ->with(['buyer', 'seller'])
            ->latest()
            ->paginate(20);

        return view('admin.users.suspicious.index', compact('users'));
    }
}
