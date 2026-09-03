<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleApiController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::query()
            ->latest('id')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%$request->search%"))
            ->paginate($request->per_page ?? 10);

        return response()->json($modules);
    }

    public function show(Module $module)
    {
        return response()->json(['module' => $module]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:modules,slug'],
            'route' => ['required', 'string', 'max:255'],
            'view_path' => ['required', 'string', 'max:255'],
            'controller' => ['required', 'string', 'max:255'],
            'panel' => ['required', 'string', Rule::in(['admin', 'seller', 'buyer', 'frontend'])],
            'roles' => ['nullable', 'array'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['roles'] = $data['roles'] ?? [];
        $data['status'] = $request->boolean('status', true);

        $module = Module::create($data);

        if (!MenuItem::where('route_name', $module->route)->exists()) {
            $menu = MenuItem::create([
                'title'       => $module->name,
                'icon'        => 'feather icon-grid',
                'route_name'  => $module->route,
                'parent_id'   => null,
                'sorting'     => 0,
                'roles'       => $module->roles,
                'permission'  => null,
                'sort_order'  => 0,
                'is_active'   => $module->status,
            ]);
            $module->update(['menu_id' => $menu->id]);
        }

        return response()->json(['status' => true, 'message' => 'Module Created Successfully', 'module' => $module], 201);
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('modules', 'slug')->ignore($module->id)],
            'route' => ['required', 'string', 'max:255'],
            'view_path' => ['required', 'string', 'max:255'],
            'controller' => ['required', 'string', 'max:255'],
            'panel' => ['required', 'string', Rule::in(['admin', 'seller', 'buyer', 'frontend'])],
            'roles' => ['nullable', 'array'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['roles'] = $data['roles'] ?? [];
        $data['status'] = $request->boolean('status');

        $module->update($data);

        if ($module->menu_id) {
            MenuItem::where('id', $module->menu_id)->update([
                'title'      => $module->name,
                'route_name' => $module->route,
                'roles'      => $module->roles,
                'is_active'  => $module->status,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Module Updated Successfully', 'module' => $module]);
    }

    public function destroy(Module $module)
    {
        if ($module->menu_id) {
            MenuItem::where('id', $module->menu_id)->delete();
        }
        $module->delete();

        return response()->json(['status' => true, 'message' => 'Module Deleted Successfully']);
    }

    public function toggleStatus(Module $module)
    {
        $newStatus = !$module->status;
        $module->update(['status' => $newStatus]);

        if ($module->menu_id) {
            MenuItem::where('id', $module->menu_id)->update(['is_active' => $newStatus]);
        }

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully', 'module' => $module]);
    }
}

