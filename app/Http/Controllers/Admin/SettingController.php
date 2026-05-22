<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function admin()
    {
        return $this->renderSettings('admin');
    }

    public function seller()
    {
        return $this->renderSettings('seller');
    }

    public function buyer()
    {
        return $this->renderSettings('buyer');
    }

    public function frontend()
    {
        return $this->renderSettings('frontend');
    }

    public function auth()
    {
        return $this->renderSettings('auth');
    }

    private function renderSettings($group)
    {
        $settings = Setting::where('group', $group)
            ->latest()
            ->get();

        return view('admin.settings.index', compact('settings', 'group'));
    }

    public function create($group)
    {
        return view('admin.settings.create', compact('group'));
    }

    public function edit($id)
    {

        $setting = Setting::findOrFail($id);
        return view('admin.settings.edit', compact('setting'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required',
            'key' => 'required',
            'type' => 'required'
        ]);

        Setting::create([
            'group' => $request->group,
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type
        ]);

        return redirect()
            ->route('admin.settings.' . $request->group)
            ->with('success', 'Setting Added Successfully');
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $setting->update([
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type
        ]);

        return redirect()
            ->route('admin.settings.' . $setting->group)
            ->with('success', 'Setting Updated Successfully');
    }

    // DELETE
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);

        $setting->delete();

        return redirect()
            ->route('admin.settings.' . $group)
            ->with('success', 'Setting Deleted Successfully');
    }
}
