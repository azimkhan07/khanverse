<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceTypeApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceType::query()->with('category');

        if ($request->has('q') && $request->q !== '') {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('all')) {
            $data = $query->latest()->get();
            return response()->json(['data' => $data]);
        }

        $types = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json($types);
    }

    public function show($id): JsonResponse
    {
        $type = ServiceType::with('category')->findOrFail($id);

        return response()->json($type);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name',
            'slug' => 'nullable|string|max:255|unique:service_types,slug',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $type = ServiceType::create($validated);

        return response()->json([
            'message' => 'Service type created successfully.',
            'service_type' => $type->load('category'),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $type = ServiceType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:service_types,name,' . $id,
            'slug' => 'sometimes|nullable|string|max:255|unique:service_types,slug,' . $id,
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (array_key_exists('slug', $validated) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'] ?? $type->name);
        }

        $type->update($validated);

        return response()->json([
            'message' => 'Service type updated successfully.',
            'service_type' => $type->fresh('category'),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $type = ServiceType::findOrFail($id);
        $type->delete();

        return response()->json([
            'message' => 'Service type deleted successfully.',
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $type = ServiceType::findOrFail($id);
        $type->update(['is_active' => !$type->is_active]);

        return response()->json([
            'message' => 'Service type status updated.',
            'service_type' => $type,
        ]);
    }
}