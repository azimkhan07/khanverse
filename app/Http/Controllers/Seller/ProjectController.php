<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\Seller;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display Seller Projects
     */
    public function index()
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $projects = Project::where('seller_id', optional($seller)->id)
            ->latest()
            ->paginate(10);

        return view('seller.projects.index', compact(
            'seller',
            'projects'
        ));
    }

    /**
     * Show Create Project Form
     */
    public function create()
    {
        return view('seller.projects.create');
    }

    /**
     * Store New Project
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show Project Details
     */
    public function show($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $project = Project::with([
            'buyer',
            'seller',
            'service',
            'attachments'
        ])
            ->where('seller_id', optional($seller)->id)
            ->findOrFail($id);

        return view(
            'seller.projects.show',
            compact(
                'seller',
                'project'
            )
        );
    }


    /**
     * Show Upload / Attachment Page
     */
    public function attachments($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        $project = Project::with('attachments')
            ->where('seller_id', optional($seller)->id)
            ->findOrFail($id);

        return view(
            'seller.projects.attachments',
            compact(
                'seller',
                'project'
            )
        );
    }


    /**
     * Upload Attachment
     */
    public function uploadAttachment(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();

        $project = Project::where('seller_id', optional($seller)->id)
            ->findOrFail($id);

        $file = $request->file('file');

        $path = $file->store(
            'project-attachments',
            'public'
        );

        ProjectAttachment::create([

            'project_id'  => $project->id,

            'user_id'     => Auth::id(),

            'uploaded_by' => 'seller',

            'file_name'   => $file->getClientOriginalName(),

            'file_path'   => $path,

            'file_size'   => $file->getSize(),

            'mime_type'   => $file->getMimeType(),

        ]);

        return back()->with(
            'success',
            'File uploaded successfully.'
        );
    }

    /**
     * Download Attachment
     */
    public function downloadAttachment($id)
    {
        $attachment = ProjectAttachment::findOrFail($id);

        $seller = Seller::where('user_id', Auth::id())->first();

        if ($attachment->project->seller_id != optional($seller)->id) {
            abort(403);
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }


    /**
     * Delete Attachment
     */
    public function deleteAttachment($id)
    {
        $attachment = ProjectAttachment::findOrFail($id);

        $seller = Seller::where('user_id', Auth::id())->first();

        if ($attachment->project->seller_id != optional($seller)->id) {
            abort(403);
        }

        if (
            $attachment->file_path &&
            Storage::disk('public')->exists($attachment->file_path)
        ) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment deleted successfully.'
        );
    }


    /**
     * Change Project Status
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,completed,cancelled',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();

        $project = Project::where('seller_id', optional($seller)->id)
            ->findOrFail($id);

        $project->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Project status updated successfully.'
        );
    }

}
