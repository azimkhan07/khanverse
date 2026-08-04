<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $buyer = Buyer::with('profile')->where('user_id', Auth::id())->firstOrFail();

        $countries = Country::where('status', 1)
            ->orderBy('name')
            ->get();

        $states = collect();

        $cities = collect();

        if (optional($buyer->profile)->country_id) {
            $states = State::where('country_id', $buyer->profile->country_id)
                ->orderBy('name')
                ->get();
        }

        if (optional($buyer->profile)->state_id) {
            $cities = City::where('state_id', $buyer->profile->state_id)
                ->orderBy('name')
                ->get();
        }

        return view('buyer.profile.index', compact(
            'buyer',
            'countries',
            'states',
            'cities'
        ));
    }

    public function update(Request $request)
    {
        $buyer = Buyer::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string|max:2000',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',

            'profile_visibility' => 'required|in:public,private',

            'show_email' => 'nullable|boolean',
            'show_phone' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($buyer, $request) {

            $buyer->update([
                'full_name' => $request->full_name,
                'company_name' => $request->company_name,
            ]);

            if ($request->hasFile('profile_image')) {

                if ($buyer->profile_image && Storage::disk('public')->exists($buyer->profile_image)) {
                    Storage::disk('public')->delete($buyer->profile_image);
                }

                $buyer->profile_image = $request->file('profile_image')
                    ->store('buyers/profile', 'public');

                $buyer->save();
            }

            BuyerProfile::updateOrCreate(

                [
                    'buyer_id' => $buyer->id
                ],

                [
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'postal_code' => $request->postal_code,
                    'address' => $request->address,
                    'bio' => $request->bio,

                    'email_notifications' => $request->boolean('email_notifications'),
                    'push_notifications' => $request->boolean('push_notifications'),
                    'sms_notifications' => $request->boolean('sms_notifications'),

                    'profile_visibility' => $request->profile_visibility,

                    'show_email' => $request->boolean('show_email'),
                    'show_phone' => $request->boolean('show_phone'),
                ]

            );
        });

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([

            'current_password' => ['required'],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->password);

        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public function getStates($country)
    {
        return response()->json(

            State::where('country_id', $country)
                ->orderBy('name')
                ->get()

        );
    }

    public function getCities($state)
    {
        return response()->json(

            City::where('state_id', $state)
                ->orderBy('name')
                ->get()

        );
    }
}
