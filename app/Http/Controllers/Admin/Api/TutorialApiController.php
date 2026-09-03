<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TutorialApiController extends Controller
{
    public function index(Request $request)
    {
        $tutorials = Tutorial::query()
            ->latest('id')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%$request->search%"))
            ->paginate($request->per_page ?? 10);

        return response()->json($tutorials);
    }

    public function show(Tutorial $tutorial)
    {
        return response()->json(['tutorial' => $tutorial]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug'] = Str::slug($request->title);
        $data['status'] = $request->boolean('status');
        $data['roles'] = $request->has('roles') ? $request->input('roles') : [];

        $tutorial = Tutorial::create($data);

        return response()->json(['status' => true, 'message' => 'Tutorial Created Successfully', 'tutorial' => $tutorial], 201);
    }

    public function update(Request $request, Tutorial $tutorial)
    {
        $data = $this->validateData($request);

        $data['slug'] = Str::slug($request->title);
        $data['status'] = $request->boolean('status');
        $data['roles'] = $request->has('roles') ? $request->input('roles') : [];

        $tutorial->update($data);

        return response()->json(['status' => true, 'message' => 'Tutorial Updated Successfully', 'tutorial' => $tutorial]);
    }

    public function destroy(Tutorial $tutorial)
    {
        $tutorial->delete();

        return response()->json(['status' => true, 'message' => 'Tutorial Deleted Successfully']);
    }

    public function toggleStatus(Tutorial $tutorial)
    {
        $tutorial->update(['status' => !$tutorial->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully', 'tutorial' => $tutorial]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_url' => ['required', 'string', 'max:500'],
            'thumbnail' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'string', 'max:50'],
            'roles' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);
    }
}
