<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAppBuildsApiController extends Controller
{
    private const GROUP = 'app';

    private function builds(): array
    {
        $raw = Setting::where('group', self::GROUP)->where('key', 'apk_builds')->value('value');
        if (!$raw) {
            return [];
        }
        $arr = json_decode($raw, true);
        return is_array($arr) ? $arr : [];
    }

    private function saveBuilds(array $builds): void
    {
        Setting::updateOrCreate(
            ['group' => self::GROUP, 'key' => 'apk_builds'],
            ['value' => json_encode($builds), 'type' => 'json']
        );
    }

    public function index()
    {
        $enabled = Setting::where('group', self::GROUP)->where('key', 'install_prompt_enabled')->value('value') !== '0';

        return response()->json([
            'builds' => array_reverse($this->builds()),
            'enabled' => (bool) $enabled,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'apk_file' => ['required', 'file'],
        ]);

        $file = $request->file('apk_file');
        $original = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext !== 'apk' && !str_ends_with(strtolower($original), '.apk')) {
            return response()->json(['status' => false, 'message' => 'Only .apk files are allowed.'], 422);
        }

        $storedName = date('Ymd-His') . '-' . Str::slug(pathinfo($original, PATHINFO_FILENAME)) . '.apk';
        $path = $file->storeAs('app-apks', $storedName, 'public');

        $build = [
            'id' => (string) Str::uuid(),
            'filename' => $original,
            'stored_name' => basename((string) $path),
            'url' => url('storage/' . $path),
            'version' => $request->input('version') ?: '1.0',
            'size' => $this->formatBytes($file->getSize()),
            'uploaded_at' => now()->toDateTimeString(),
            'active' => false,
        ];

        $builds = $this->builds();
        if (count($builds) === 0) {
            $build['active'] = true;
        }
        $builds[] = $build;
        $this->saveBuilds($builds);

        return response()->json([
            'status' => true,
            'message' => 'APK uploaded successfully.',
            'build' => $build,
        ], 201);
    }

    public function setActive(Request $request, $id)
    {
        $builds = $this->builds();
        $found = false;

        foreach ($builds as &$b) {
            $b['active'] = ((string) ($b['id'] ?? '') === (string) $id);
            if ($b['active']) {
                $found = true;
            }
        }
        unset($b);

        if (!$found) {
            return response()->json(['status' => false, 'message' => 'Build not found.'], 404);
        }

        $this->saveBuilds($builds);

        return response()->json(['status' => true, 'message' => 'Active build updated.', 'builds' => array_reverse($builds)]);
    }

    public function destroy($id)
    {
        $builds = $this->builds();
        $remaining = [];

        foreach ($builds as $b) {
            if ((string) ($b['id'] ?? '') === (string) $id) {
                $stored = $b['stored_name'] ?? null;
                if ($stored) {
                    $full = storage_path('app/public/' . $stored);
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
                continue;
            }
            $remaining[] = $b;
        }

        if (count($remaining) > 0 && !collect($remaining)->contains(fn ($b) => !empty($b['active']))) {
            $remaining[0]['active'] = true;
        }

        $this->saveBuilds($remaining);

        return response()->json(['status' => true, 'message' => 'Build deleted successfully.', 'builds' => array_reverse($remaining)]);
    }

    public function updatePrompt(Request $request)
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        Setting::updateOrCreate(
            ['group' => self::GROUP, 'key' => 'install_prompt_enabled'],
            ['value' => $request->boolean('enabled') ? '1' : '0', 'type' => 'string']
        );

        return response()->json([
            'status' => true,
            'message' => 'Install prompt setting updated.',
            'enabled' => $request->boolean('enabled'),
        ]);
    }

    private function formatBytes($bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}