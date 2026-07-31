<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Project;
use App\Models\ProjectAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $buyer = Auth::user()->buyer;

        $query = Project::with(['seller', 'buyer'])->where('buyer_id', $buyer->id);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(10);

        // Dashboard Cards
        $totalProjects = Project::where('buyer_id', $buyer->id)->count();
        $openProjects = Project::where('buyer_id', $buyer->id)->where('status', 'open')->count();
        $inProgressProjects = Project::where('buyer_id', $buyer->id)->where('status', 'in_progress')->count();
        $completedProjects = Project::where('buyer_id', $buyer->id)->where('status', 'completed')->count();

        return view('buyer.projects.index', compact('projects', 'totalProjects', 'openProjects', 'inProgressProjects', 'completedProjects'));
    }

    public function show(Project $project)
    {
        $buyer = Auth::user()->buyer;
        if ($project->buyer_id != $buyer->id) {
            abort(403);
        }
        $project->load(['seller.user', 'service', 'order', 'attachments', 'deliveries', 'order.review']);

        return view('buyer.projects.show', compact('project'));
    }

    // Attachments

    public function attachments($id)
    {
        $buyer = Buyer::where('user_id', Auth::id())->first();
        $project = Project::with('attachments')->where('buyer_id', optional($buyer)->id)->findOrFail($id);

        return view('buyer.projects.attachments', compact('buyer', 'project'));
    }

    public function downloadAttachment($id)
    {
        $attachment = ProjectAttachment::with('project')->findOrFail($id);
        $buyer = Auth::user()->buyer;

        if (!$buyer || $attachment->project->buyer_id != $buyer->id) {
            abort(403, 'Unauthorized Access');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}
