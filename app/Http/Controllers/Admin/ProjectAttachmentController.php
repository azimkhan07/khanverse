<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectAttachmentController extends Controller
{
    public function index(Project $project)
    {
        $attachments = $project->attachments()->latest()->get();

        return view('admin.projects.attachments.index', compact(
            'project',
            'attachments'
        ));
    }
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|max:51200'
        ]);

        $file = $request->file('file');

        $path = $file->store('project-attachments', 'public');

        $project->attachments()->create([

            'uploaded_by' => 'admin',

            'user_id' => auth()->id(),

            'file_name' => $file->getClientOriginalName(),

            'file_path' => $path,

            'file_size' => $file->getSize(),

            'mime_type' => $file->getMimeType(),

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Attachment Uploaded',

            'reload' => true

        ]);
    }

    public function download(Project $project, ProjectAttachment $attachment)
    {
        return Storage::disk('public')
            ->download(
                $attachment->file_path,
                $attachment->file_name
            );
    }

    public function destroy(Project $project, ProjectAttachment $attachment)
    {
        Storage::disk('public')
            ->delete($attachment->file_path);

        $attachment->delete();

        return response()->json([

            'status' => true,

            'message' => 'Attachment Deleted'

        ]);
    }

    public function create(Project $project)
    {
        return view('admin.projects.attachments.create', compact('project'));
    }
}
