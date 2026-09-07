<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\StoreMenuItemRequest;
use App\Http\Requests\Menu\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if ($request->filled('panel')) {
            $query->where('panel', $request->input('panel'));
        }

        $menus = $query->paginate($request->integer('per_page', 10));

        return response()->json($menus);
    }

    public function parents(Request $request): JsonResponse
    {
        $query = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if ($request->filled('panel')) {
            $query->where('panel', $request->input('panel'));
        }

        $menus = $query->get();

        return response()->json($menus);
    }

    public function sidebar(Request $request): JsonResponse
    {
        $panel = $request->input('panel');

        if (!$panel && auth()->check()) {
            $panel = auth()->user()->role;
        }

        $panel = in_array($panel, ['admin', 'seller', 'buyer']) ? $panel : 'admin';

        $items = MenuItem::where('panel', $panel)
            ->where('is_active', 1)
            ->whereNotNull('path')
            ->orderBy('sort_order')
            ->get();

        $grouped = $items->groupBy('section')->map(function ($sectionItems, $section) {
            return [
                'title' => $section ?? 'General',
                'items' => $sectionItems->map(function ($item) {
                    return [
                        'path' => $item->path,
                        'label' => $item->title,
                        'icon' => $item->icon,
                        'exact' => false,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($grouped);
    }

    public function show($id): JsonResponse
    {
        $menu = MenuItem::with('children', 'parent')->findOrFail($id);

        return response()->json($menu);
    }

    public function store(StoreMenuItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $menu = MenuItem::create($validated);

        return response()->json([
            'message' => 'Menu item created successfully.',
            'menu' => $menu,
        ], 201);
    }

    public function update(UpdateMenuItemRequest $request, $id): JsonResponse
    {
        $menu = MenuItem::findOrFail($id);

        $validated = $request->validated();

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
