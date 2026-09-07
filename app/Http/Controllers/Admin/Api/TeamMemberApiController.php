<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberApiController extends Controller
{
    public function index(Request $request)
    {
        $members = TeamMember::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%$request->search%"))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->boolean('status')))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($request->per_page ?? 10);

        return response()->json($members);
    }

    public function show(TeamMember $teamMember)
    {
        return response()->json(['member' => $teamMember]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['status'] = $request->boolean('status');

        $member = TeamMember::create($data);

        return response()->json(['status' => true, 'message' => 'Team Member Created Successfully', 'member' => $member], 201);
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $data = $this->validateData($request);
        $data['status'] = $request->boolean('status');

        $teamMember->update($data);

        return response()->json(['status' => true, 'message' => 'Team Member Updated Successfully', 'member' => $teamMember]);
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return response()->json(['status' => true, 'message' => 'Team Member Deleted Successfully']);
    }

    public function toggleStatus(TeamMember $teamMember)
    {
        $teamMember->update(['status' => !$teamMember->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully', 'member' => $teamMember]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);
    }
}