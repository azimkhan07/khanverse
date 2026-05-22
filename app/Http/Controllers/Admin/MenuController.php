<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        $parents = MenuItem::whereNull('parent_id')->get();
        return view('admin.menu.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        MenuItem::create([
            'title' => $request->title,
            'icon' => $request->icon,
            'route_name' => $request->route_name,
            'parent_id' => $request->parent_id,
            'roles' => json_encode($request->roles ?? ['admin']),
            'permission' => $request->permission,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? 1,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu created');
    }

    public function edit($id)
    {
        $menu = MenuItem::findOrFail($id);
        $parents = MenuItem::whereNull('parent_id')->where('id', '!=', $id)->get();

        return view('admin.menu.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $menu = MenuItem::findOrFail($id);

        $menu->update([
            'title' => $request->title,
            'icon' => $request->icon,
            'route_name' => $request->route_name,
            'parent_id' => $request->parent_id,
            'roles' => json_encode($request->roles ?? ['admin']),
            'permission' => $request->permission,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? 1,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu updated');
    }

    public function destroy($id)
    {
        MenuItem::destroy($id);
        return back()->with('success', 'Menu deleted');
    }
}