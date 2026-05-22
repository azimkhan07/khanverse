<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:permissions,name',
            'group' => 'required',
            'slug'   => 'required|unique:permissions,slug',
            'module' => 'required',
        ]);

        // Permission::create([
        //     'name'  => $request->name,
        //     'slug'  => Str::slug($request->name),
        //     'group' => $request->group,
        //     'status' => 1
        // ]);

        Permission::create([

            'name'   => $request->name,

            'slug'   => $request->slug,

            'module' => $request->module,

            'group'  => $request->group,

            'status' => $request->status

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully'
        ]);
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {

        // dd($request->slug);
        // dd($permission);
        $request->validate([
            'name'  => 'required|unique:permissions,name,' . $permission->id,
            'group' => 'required',
            'slug'   => 'required',
            'module' => 'required',
        ]);

        // $permission->update([
        //     'name'  => $request->name,
        //     'slug'  => Str::slug($request->name),
        //     'group' => $request->group
        // ]);

        $permission->name = $request->name;
        $permission->slug = $request->slug;
        $permission->module = $request->module;
        $permission->group = $request->group;
        $permission->status = $request->status;

        $permission->save();

        // dd($permission->fresh());

        return response()->json([
            'status' => true,
            'message' => 'Permission updated successfully'
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
