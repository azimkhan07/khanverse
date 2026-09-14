<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuyerProfile;
use App\Models\SellerProfile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AccountSettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'profile' => ProfileService::data($request->user()),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $updates = array_filter([
            'name' => $validated['name'] ?? null,
            'username' => $validated['username'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ], fn ($value) => $value !== null);

        if ($updates) {
            $user->update($updates);
        }

        $geo = array_filter([
            'country_id' => $validated['country_id'] ?? null,
            'state_id' => $validated['state_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
        ], fn ($value) => $value !== null);

        if ($geo) {
            if ($user->role === 'seller' && $user->seller) {
                SellerProfile::updateOrCreate(['seller_id' => $user->seller->id], $geo);
            } elseif ($user->role === 'buyer' && $user->buyer) {
                BuyerProfile::updateOrCreate(['buyer_id' => $user->buyer->id], $geo);
            }
        }

        if ($request->hasFile('avatar')) {
            $user->update([
                'profile_image' => $request->file('avatar')->store('user-avatars', 'public'),
            ]);
        }

        return response()->json([
            'message' => 'Account updated successfully.',
            'profile' => ProfileService::data($user->fresh()),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors' => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}