<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionApiController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->latest()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%$request->search%")
                ->orWhere('module', 'like', "%$request->search%")
                ->orWhere('group', 'like', "%$request->search%"))
            ->paginate($request->per_page ?? 10);

        return response()->json($permissions);
    }

    public function show(Permission $permission)
    {
        return response()->json(['permission' => $permission]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'group' => 'required',
            'slug' => 'required|unique:permissions,slug',
            'module' => 'required',
            'status' => 'nullable|boolean',
        ]);

        $permission = Permission::create([
            'name'   => $request->name,
            'slug'   => $request->slug,
            'module' => $request->module,
            'group'  => $request->group,
            'status' => $request->boolean('status', true),
        ]);

        return response()->json(['status' => true, 'message' => 'Permission created successfully', 'permission' => $permission], 201);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name'  => 'required|unique:permissions,name,' . $permission->id,
            'group' => 'required',
            'slug'  => 'required',
            'module' => 'required',
            'status' => 'nullable|boolean',
        ]);

        $permission->name = $request->name;
        $permission->slug = $request->slug;
        $permission->module = $request->module;
        $permission->group = $request->group;
        $permission->status = $request->boolean('status', true);
        $permission->save();

        return response()->json(['status' => true, 'message' => 'Permission updated successfully', 'permission' => $permission]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json(['status' => true, 'message' => 'Permission deleted successfully']);
    }
}

