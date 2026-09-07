<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roleData']);

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($users);
    }

    public function show($id): JsonResponse
    {
        $user = User::with(['roleData', 'buyer', 'seller', 'wallet'])
            ->findOrFail($id);

        return response()->json($user);
    }

    public function buyers(Request $request): JsonResponse
    {
        $query = User::where('role', 'buyer')->with(['buyer']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $buyers = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($buyers);
    }

    public function sellers(Request $request): JsonResponse
    {
        $query = User::where('role', 'seller')->with(['seller']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $sellers = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($sellers);
    }

    public function toggleStatus($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => !$user->status]);

        return response()->json([
            'message' => 'User status updated.',
            'user' => $user,
        ]);
    }

    public function toggleBan($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => !$user->is_banned]);

        return response()->json([
            'message' => 'User ban status updated.',
            'user' => $user,
        ]);
    }
}

