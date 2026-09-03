<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'full_name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->has('full_name')) {
            $user->name = $validated['full_name'];
        }

        if ($request->has('email') && $request->get('email') !== $user->email) {
            $validatedEmail = $request->validate(['email' => 'required|email|max:255|unique:users,email,' . $user->id]);
            $user->email = $validatedEmail['email'];
        }

        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('avatar')->store('admin-avatars', 'public');
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => $this->profile()->getData(),
        ]);
    }
}