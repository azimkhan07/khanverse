<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display Seller Profile
     */
    public function index()
    {
        $user = Auth::user();

        return view('seller.profile.index', compact('user'));
    }

    /**
     * Update Basic Profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email,' . Auth::id(),

            'phone' => 'nullable|string|max:20',

        ]);

        $user = Auth::user();

        $user->name = $request->name;

        $user->email = $request->email;

        $user->phone = $request->phone;

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([

            'current_password' => 'required',

            'password' => 'required|string|min:8|confirmed',

        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {

            return back()->withErrors([

                'current_password' => 'Current password is incorrect.'

            ]);
        }

        $user->password = Hash::make($request->password);

        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update Profile Photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([

            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {

            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {

                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')
                ->store('profile-images', 'public');

            $user->profile_image = $path;

            $user->save();
        }

        return back()->with('success', 'Profile image updated successfully.');
    }

    /**
     * Remove Profile Photo
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {

            Storage::disk('public')->delete($user->profile_image);
        }

        $user->profile_image = null;

        $user->save();

        return back()->with('success', 'Profile image removed successfully.');
    }
}
