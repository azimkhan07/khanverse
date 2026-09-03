<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $roles = Role::withCount('users')->paginate($request->integer('per_page', 10));

        return response()->json($roles);
    }

    public function show($id): JsonResponse
    {
        $role = Role::with(['users', 'permissions'])->findOrFail($id);

        return response()->json($role);
    }
}
