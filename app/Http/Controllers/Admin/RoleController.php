<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully'
        ]);
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    public function permissions(Role $role)
    {
        $permissions = Permission::where('status', 1)
            ->orderBy('module')
            ->get()
            ->groupBy('module');

        $assignedPermissions = $role->permissions
            ->pluck('id')
            ->toArray();

        return view(
            'admin.roles.permissions',
            compact(
                'role',
                'permissions',
                'assignedPermissions'
            )
        );
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $role->permissions()->sync(
            $request->permissions ?? []
        );

        return response()->json([

            'status' => true,

            'message' => 'Permissions Assigned Successfully'

        ]);
    }
}
