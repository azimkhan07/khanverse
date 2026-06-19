<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required'],
            'slug'   => ['required', 'unique:categories,slug'],
            'icon'   => ['nullable'],
        ]);

        $data['status'] = 1;

        Category::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Category Created Successfully',
            'reload' => true,
        ]);
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required'],
            'slug' => [
                'required',
                Rule::unique('categories', 'slug')
                    ->ignore($category->id)
            ],
            'icon' => ['nullable']
        ]);

        $category->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Category Updated Successfully',
            'reload' => true,
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'status' => !$category->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status Updated Successfully'
        ]);
    }
}