<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
            $projects = Project::query()

                ->with([
                    'buyer',
                    'seller',
                    'service'
                ])

                ->when($request->search, function ($q) use ($request) {

                    $q->where('title', 'like', '%' . $request->search . '%');
                })

                ->when($request->status, function ($q) use ($request) {

                    $q->where('status', $request->status);
                })

                ->latest()

                ->paginate(20);
        // dd($projects);
        return view(
            'admin.projects.index',
            compact('projects')
        );
    }

    public function show(Project $project)
    {
        $project->load([

            'buyer',

            'seller',

            'service',

            'order'

        ]);

        return view(
            'admin.projects.show',
            compact('project')
        );
    }
}
