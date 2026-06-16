<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::query()
            ->latest('id')
            ->paginate(25);

        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        return view('admin.modules.create');
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

        Module::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Module Created Successfully',
            'reload' => true,
        ]);
    }

    public function edit(Module $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('modules', 'slug')->ignore($module->id),
            ],
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

        return response()->json([
            'status' => true,
            'message' => 'Module Updated Successfully',
            'reload' => true,
        ]);
    }
}