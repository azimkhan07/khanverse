<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $menus = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->paginate($request->integer('per_page', 10));

        return response()->json($menus);
    }

    public function parents(): JsonResponse
    {
        $menus = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return response()->json($menus);
    }

    public function show($id): JsonResponse
    {
        $menu = MenuItem::with('children', 'parent')->findOrFail($id);

        return response()->json($menu);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'roles' => 'nullable|array',
            'permission' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['roles'])) {
            $validated['roles'] = json_encode($validated['roles']);
        }

        $menu = MenuItem::create($validated);

        return response()->json([
            'message' => 'Menu item created successfully.',
            'menu' => $menu,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $menu = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'roles' => 'nullable|array',
            'permission' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['roles'])) {
            $validated['roles'] = json_encode($validated['roles']);
        }

        $menu->update($validated);

        return response()->json([
            'message' => 'Menu item updated successfully.',
            'menu' => $menu,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        MenuItem::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Menu item deleted successfully.',
        ]);
    }
}
