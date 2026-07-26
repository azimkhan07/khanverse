<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display Seller Projects
     */
    public function index()
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {

            return back()->with('error', 'Seller profile not found.');
        }

        $projects = Project::with([

            'buyer',
            'service'

        ])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(10);

        return view('seller.projects.index', compact(

            'seller',

            'projects'

        ));
    }

    /**
     * Display Single Project
     */
    public function show($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {

            return back()->with('error', 'Seller profile not found.');
        }

        $project = Project::with([

            'buyer',

            'service',

            'attachments'

        ])
            ->where('seller_id', $seller->id)
            ->findOrFail($id);

        return view('seller.projects.show', compact(

            'project'

        ));
    }

    /**
     * Update Project Status
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([

            'status' => 'required|in:open,in_progress,completed,cancelled',

        ]);

        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {

            return back()->with('error', 'Seller profile not found.');
        }

        $project = Project::where('seller_id', $seller->id)
            ->findOrFail($id);

        $project->update([

            'status' => $request->status

        ]);

        return back()->with(

            'success',

            'Project status updated successfully.'

        );
    }

    /**
     * Display Project Attachments
     */
    public function attachments($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {

            return back()->with('error', 'Seller profile not found.');
        }

        $project = Project::where('seller_id', $seller->id)
            ->with('attachments')
            ->findOrFail($id);

        $attachments = $project->attachments()
            ->latest()
            ->get();

        return view('seller.projects.attachments', compact(

            'project',

            'attachments'

        ));
    }

    /**
     * Upload Project Attachment
     */
    public function uploadAttachment(Request $request, $id)
    {
        $request->validate([

            'file' => 'required|file|max:10240',

            'attribute' => 'nullable|string|max:255',

        ]);

        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {

            return back()->with('error', 'Seller profile not found.');
        }

        $project = Project::where('seller_id', $seller->id)
            ->findOrFail($id);

        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $path = $file->store('project-attachments', 'public');

            ProjectAttachment::create([

                'project_id' => $project->id,

                'user_id' => Auth::id(),

                'uploaded_by' => 'seller',

                'file_name' => $file->getClientOriginalName(),

                'file_path' => $path,

                'file_size' => $file->getSize(),

                'mime_type' => $file->getMimeType(),

                'attribute' => $request->attribute,

            ]);
        }

        return back()->with(

            'success',

            'Attachment uploaded successfully.'

        );
    }

    /**
     * Download Project Attachment
     */
    public function downloadAttachment($id)
    {
        $attachment = ProjectAttachment::findOrFail($id);

        if (!Storage::disk('public')->exists($attachment->file_path)) {

            return back()->with(

                'error',

                'File not found.'

            );
        }

        return Storage::disk('public')->download(

            $attachment->file_path,

            $attachment->file_name

        );
    }

    /**
     * Delete Project Attachment
     */
    public function deleteAttachment($id)
    {
        $attachment = ProjectAttachment::findOrFail($id);

        if (

            Storage::disk('public')->exists(

                $attachment->file_path

            )

        ) {

            Storage::disk('public')->delete(

                $attachment->file_path

            );
        }

        $attachment->delete();

        return back()->with(

            'success',

            'Attachment deleted successfully.'

        );
    }
}
