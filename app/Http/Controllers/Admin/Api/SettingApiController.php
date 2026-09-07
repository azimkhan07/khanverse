<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Setting::query();

        if ($request->group) {
            $query->where('group', $request->group);
        }

        $settings = $query->latest()->get();

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($validated['settings'] as $item) {
            Setting::where('id', $item['id'])->update(['value' => $item['value']]);
        }

        return response()->json([
            'message' => 'Settings updated successfully.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group' => 'required|in:admin,seller,buyer,frontend,auth,website',
            'key' => ['required', 'regex:/^[a-z0-9._-]+$/i'],
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,image,html',
        ], [
            'key.regex' => 'The key may only contain letters, numbers, dots, underscores and dashes.',
        ]);

        $exists = Setting::where('group', $validated['group'])
            ->where('key', $validated['key'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A setting with this key already exists in the selected group.',
            ], 422);
        }

        $setting = Setting::create([
            'group' => $validated['group'],
            'key' => $validated['key'],
            'value' => $validated['value'] ?? '',
            'type' => $validated['type'],
        ]);

        return response()->json([
            'message' => 'Setting created successfully.',
            'setting' => $setting,
        ], 201);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found.',
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully.',
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|image|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->store('settings', 'public');

        return response()->json([
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }
}
