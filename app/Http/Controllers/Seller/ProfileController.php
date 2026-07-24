<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display Seller Profile
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Auth User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Seller Profile
        |--------------------------------------------------------------------------
        */

        $seller = Seller::with([

            'social',

            'documents',

            'bankAccount',

            'languages',

            'certificates',

            'experiences',

            'portfolios',

            'setting'

        ])->where('user_id', $user->id)->first();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('seller.profile.index', compact(

            'user',

            'seller'

        ));
    }

    /**
     * Update Seller Profile
     */
    public function updateProfile(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name'                => 'required|string|max:255',

            'email'               => 'required|email|unique:users,email,' . Auth::id(),

            'phone'               => 'nullable|string|max:20',

            'full_name'           => 'required|string|max:255',

            'bio'                 => 'nullable|string',

            'skills'              => 'nullable|string',

            'country'             => 'nullable|string|max:100',

            'city'                => 'nullable|string|max:100',

            'hourly_rate'         => 'nullable|numeric|min:0',

            'experience_level'    => 'required|in:junior,mid,senior',

            'available_for_work'  => 'nullable|boolean',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Update User & Seller
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            $user = Auth::user();

            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */

            $user->update([

                'name'  => $request->name,

                'email' => $request->email,

                'phone' => $request->phone,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Seller Profile
            |--------------------------------------------------------------------------
            */

            $seller = Seller::firstOrCreate(

                ['user_id' => $user->id],

                ['full_name' => $request->full_name]

            );

            /*
            |--------------------------------------------------------------------------
            | Update Seller
            |--------------------------------------------------------------------------
            */

            $seller->update([

                'full_name'          => $request->full_name,

                'bio'                => $request->bio,

                'skills'             => $request->skills,

                'country'            => $request->country,

                'city'               => $request->city,

                'hourly_rate'        => $request->hourly_rate,

                'experience_level'   => $request->experience_level,

                'available_for_work' => $request->boolean('available_for_work'),

            ]);

            DB::commit();

            return back()->with(

                'success',

                'Profile updated successfully.'

            );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(

                'error',

                $e->getMessage()

            );
        }
    }

    /**
     * Update Seller Password
     */
    public function updatePassword(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'current_password' => 'required',

            'password' => 'required|string|min:8|confirmed',

        ]);

        /*
    |--------------------------------------------------------------------------
    | Auth User
    |--------------------------------------------------------------------------
    */

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Check Current Password
    |--------------------------------------------------------------------------
    */

        if (!Hash::check($request->current_password, $user->password)) {

            return back()->withErrors([

                'current_password' => 'Current password is incorrect.'

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        $user->update([

            'password' => Hash::make($request->password)

        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return back()->with(

            'success',

            'Password updated successfully.'

        );
    }

    /**
     * Update Seller Profile Photo
     */
    public function updatePhoto(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Auth User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Seller Profile
        |--------------------------------------------------------------------------
        */

        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {

            return back()->with(
                'error',
                'Seller profile not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_image')) {

            /*
            |--------------------------------------------------------------------------
            | Delete Old Image
            |--------------------------------------------------------------------------
            */

            if (
                $seller->profile_image &&
                Storage::disk('public')->exists($seller->profile_image)
            ) {

                Storage::disk('public')->delete($seller->profile_image);
            }

            /*
            |--------------------------------------------------------------------------
            | Store New Image
            |--------------------------------------------------------------------------
            */

            $path = $request->file('profile_image')->store(

                'seller/profile',

                'public'

            );

            /*
            |--------------------------------------------------------------------------
            | Update Database
            |--------------------------------------------------------------------------
            */

            $seller->update([

                'profile_image' => $path

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return back()->with(

            'success',

            'Profile photo updated successfully.'

        );
    }

    /**
     * Remove Seller Profile Photo
     */
    public function deletePhoto()
    {
        /*
        |--------------------------------------------------------------------------
        | Auth User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Seller Profile
    |--------------------------------------------------------------------------
    */

        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {

            return back()->with(

                'error',

                'Seller profile not found.'

            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Old Image
        |--------------------------------------------------------------------------
        */

        if (

            $seller->profile_image &&

            Storage::disk('public')->exists($seller->profile_image)

        ) {

            Storage::disk('public')->delete(

                $seller->profile_image

            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Image From Database
        |--------------------------------------------------------------------------
        */

        $seller->update([

            'profile_image' => null

        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return back()->with(

            'success',

            'Profile photo removed successfully.'

        );
    }
}
