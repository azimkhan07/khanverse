<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['buyer', 'seller', 'service']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($projects);
    }

    public function show($id): JsonResponse
    {
        $project = Project::with(['buyer', 'seller', 'service', 'attachments', 'deliveries', 'review'])
            ->findOrFail($id);

        return response()->json($project);
    }
}

