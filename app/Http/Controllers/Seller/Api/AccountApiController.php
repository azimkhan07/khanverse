<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Seller;
use App\Models\SellerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountApiController extends Controller
{
    protected function seller(): ?Seller
    {
        return Auth::user()->seller;
    }

    public function profile(): JsonResponse
    {
        $seller = $this->seller();
        if (!$seller) {
            return response()->json(['message' => 'Seller profile not found.'], 404);
        }

        return response()->json([
            'full_name' => $seller->full_name,
            'email' => $seller->user->email,
            'bio' => $seller->bio,
            'avatar' => $seller->profile_image ? asset('storage/' . $seller->profile_image) : null,
            'skills' => $seller->skills,
            'location' => trim(implode(', ', array_filter([$seller->city, $seller->country]))),
            'country' => $seller->country,
            'city' => $seller->city,
            'hourly_rate' => $seller->hourly_rate,
            'experience_level' => $seller->experience_level,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $seller = $this->seller();
        if (!$seller) {
            return response()->json(['message' => 'Seller profile not found.'], 404);
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'skills' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'hourly_rate' => 'nullable|numeric|min:0',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($seller->profile_image) {
                Storage::disk('public')->delete($seller->profile_image);
            }
            $validated['profile_image'] = $request->file('avatar')->store('seller-avatars', 'public');
        }

        unset($validated['avatar']);
        $seller->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => $this->profile()->getData(),
        ]);
    }

    public function settings(): JsonResponse
    {
        $seller = $this->seller();
        $profile = $seller?->profile;

        return response()->json([
            'preferences' => [
                'email_notifications' => $profile?->email_notifications ?? true,
                'push_notifications' => $profile?->push_notifications ?? true,
                'order_updates' => $profile?->email_notifications ?? true,
                'two_factor' => false,
            ],
            'profile_visibility' => $profile?->profile_visibility ?? 'public',
            'show_email' => $profile?->show_email ?? true,
            'show_phone' => $profile?->show_phone ?? true,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $seller = $this->seller();
        $prefs = $request->input('preferences', []);

        $profile = SellerProfile::firstOrCreate(
            ['seller_id' => $seller?->id],
            ['seller_id' => $seller?->id]
        );

        $profile->update([
            'email_notifications' => $prefs['email_notifications'] ?? true,
            'push_notifications' => $prefs['push_notifications'] ?? true,
            'profile_visibility' => $request->input('profile_visibility', 'public'),
        ]);

        return response()->json([
            'message' => 'Settings updated successfully.',
        ]);
    }

    public function notifications(): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(20);

        return response()->json([
            'data' => $notifications->getCollection()->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'url' => $n->url,
                    'type' => $n->type,
                    'read' => $n->is_read,
                    'time' => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at->toISOString(),
                ];
            }),
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }
}
