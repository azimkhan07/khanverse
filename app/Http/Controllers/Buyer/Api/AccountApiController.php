<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\BuyerProfile;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountApiController extends Controller
{
    protected function buyer(): ?Buyer
    {
        return Auth::user()->buyer;
    }

    public function profile(): JsonResponse
    {
        $buyer = $this->buyer();
        if (!$buyer) {
            return response()->json(['message' => 'Buyer profile not found.'], 404);
        }

        return response()->json([
            'full_name' => $buyer->full_name,
            'email' => $buyer->user->email,
            'company_name' => $buyer->company_name,
            'avatar' => $buyer->profile_image ? asset('storage/' . $buyer->profile_image) : null,
            'country' => $buyer->country,
            'city' => $buyer->city,
            'location' => trim(implode(', ', array_filter([$buyer->city, $buyer->country]))),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $buyer = $this->buyer();
        if (!$buyer) {
            return response()->json(['message' => 'Buyer profile not found.'], 404);
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($buyer->profile_image) {
                Storage::disk('public')->delete($buyer->profile_image);
            }
            $validated['profile_image'] = $request->file('avatar')->store('buyer-avatars', 'public');
        }

        unset($validated['avatar']);
        $buyer->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => $this->profile()->getData(),
        ]);
    }

    public function settings(): JsonResponse
    {
        $profile = $this->buyer()?->profile;

        return response()->json([
            'preferences' => [
                'email_notifications' => $profile?->email_notifications ?? true,
                'push_notifications' => $profile?->push_notifications ?? true,
                'order_updates' => $profile?->email_notifications ?? true,
                'two_factor' => false,
            ],
            'profile_visibility' => $profile?->profile_visibility ?? 'public',
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $buyer = $this->buyer();
        $prefs = $request->input('preferences', []);

        $profile = BuyerProfile::firstOrCreate(
            ['buyer_id' => $buyer?->id],
            ['buyer_id' => $buyer?->id]
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
