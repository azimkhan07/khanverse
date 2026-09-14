<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::withCount('services')
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($categories);
    }

    public function show($id): JsonResponse
    {
        $category = Category::withCount('services')->findOrFail($id);

        return response()->json($category);
    }

    public function store(Request $request): JsonResponse
    {
$validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:255',
            'category_type' => 'sometimes|in:technical,field',
            'form_fields' => 'nullable|array',
            'form_fields.*.key' => 'required|string',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|in:text,number,textarea,select,multiselect,checkbox,tel',
            'form_fields.*.required' => 'sometimes|boolean',
            'form_fields.*.options' => 'sometimes|array',
            'status' => 'boolean',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::findOrFail($id);

$validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'category_type' => 'sometimes|in:technical,field',
            'form_fields' => 'nullable|array',
            'form_fields.*.key' => 'required|string',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|in:text,number,textarea,select,multiselect,checkbox,tel',
            'form_fields.*.required' => 'sometimes|boolean',
            'form_fields.*.options' => 'sometimes|array',
            'status' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->update(['status' => !$category->status]);

        return response()->json([
            'message' => 'Category status updated.',
            'category' => $category,
        ]);
    }
}

