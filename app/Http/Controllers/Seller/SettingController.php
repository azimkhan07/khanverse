<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $seller = $user->seller;
        $devices = $user->devices()->latest()->get();

        return view('seller.settings.index', compact('user', 'seller', 'devices'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    public function devices()
    {
        $devices = Auth::user()->devices()->latest()->get();

        return view('seller.settings.devices', compact('devices'));
    }

    public function removeDevice($id)
    {
        $device = Auth::user()->devices()->findOrFail($id);
        $device->delete();

        return response()->json([
            'status' => true,
            'message' => 'Device removed successfully.'
        ]);
    }

    public function notifications()
    {
        return view('seller.settings.notifications');
    }

    public function updateNotifications(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Notification preferences updated successfully.'
        ]);
    }

    public function privacy()
    {
        return view('seller.settings.privacy');
    }

    public function updatePrivacy(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Privacy settings updated successfully.'
        ]);
    }

    public function deleteAccount()
    {
        return view('seller.settings.delete-account');
    }

    public function destroyAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password is incorrect.'
            ]);
        }

        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Account Deleted Successfully.');
    }
}
