<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $buyer = $user->buyer;

        $devices = $user->devices()->latest()->get();

        return view(
            'buyer.settings.index',
            compact(
                'user',
                'buyer',
                'devices'
            )
        );
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
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $buyer = Auth::user()->buyer;

        $buyer->update([
            'email_notifications' => $request->has('email_notifications'),
            'push_notifications' => $request->has('push_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification settings updated successfully.'
        ]);
    }

    public function updatePrivacy(Request $request)
    {
        $buyer = Auth::user()->buyer;

        $buyer->update([
            'profile_visibility' => $request->profile_visibility,
            'show_email' => $request->has('show_email'),
            'show_phone' => $request->has('show_phone'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Privacy settings updated successfully.'
        ]);
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
}
