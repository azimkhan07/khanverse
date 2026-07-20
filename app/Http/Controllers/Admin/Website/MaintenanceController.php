<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenance = MaintenanceSetting::first();

        if (!$maintenance) {

            $maintenance = MaintenanceSetting::create([
                'title' => 'Website Under Maintenance',
                'status' => 0,
            ]);
        }

        return view('admin.website.maintenance.index', compact('maintenance'));
    }

    public function update(Request $request)
    {
        $request->validate([

            'title' => 'required|max:255',

            'message' => 'nullable',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'button_text' => 'nullable|max:255',

            'button_url' => 'nullable|url',

            'status' => 'required|boolean',

            'start_at' => 'nullable|date',

            'end_at' => 'nullable|date|after_or_equal:start_at',

        ]);

        $maintenance = MaintenanceSetting::first();

        if (!$maintenance) {

            $maintenance = new MaintenanceSetting();
        }

        $image = $maintenance->image;

        if ($request->hasFile('image')) {

            if ($image && Storage::disk('public')->exists($image)) {

                Storage::disk('public')->delete($image);
            }

            $image = $request->file('image')->store('maintenance', 'public');
        }

        $maintenance->update([

            'title' => $request->title,

            'message' => $request->message,

            'image' => $image,

            'button_text' => $request->button_text,

            'button_url' => $request->button_url,

            'status' => $request->status,

            'start_at' => $request->start_at,

            'end_at' => $request->end_at,

        ]);

        return redirect()
            ->route('admin.website.maintenance.index')
            ->with('success', 'Maintenance settings updated successfully.');
    }
}
