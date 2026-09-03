<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectDelivery;
use App\Models\ProjectHosting;
use App\Services\MailService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user()->seller;

        $query = Project::with(['buyer', 'service'])
            ->where('seller_id', $seller->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($projects);
    }

    public function show($id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $project = Project::with(['buyer', 'service', 'attachments', 'deliveries', 'order'])
            ->where('seller_id', $seller->id)
            ->findOrFail($id);

        // Expose hostings: full (decrypted) only when the key is verified,
        // otherwise a locked preview (host + username only).
        $project->setRelation('hostings', $this->hostingsFor($project));
        $project->key_verified = ! is_null($project->delivery_key_verified_at);

        return response()->json($project);
    }

    public function changeStatus(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'status' => 'required|in:open,in_progress,delivered,completed,cancelled',
        ]);

        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        if ($request->status === 'delivered' && $project->delivery_method === 'hosting' && ! $project->delivery_key_verified_at) {
            return response()->json([
                'message' => 'Verify the delivery key first to unlock hosting access before marking the project delivered.',
            ], 422);
        }

        $project->update(['status' => $request->status]);

        if ($request->status === 'delivered') {
            $this->handleDelivered($project);
        }

        if ($request->status === 'completed') {
            $this->handleCompleted($project);
        }

        return response()->json([
            'message' => 'Project status updated.',
            'project' => $project->fresh(['buyer', 'service', 'attachments', 'deliveries', 'order']),
        ]);
    }

    public function showDeliveryKey($id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        if ($project->delivery_method !== 'hosting') {
            return response()->json(['message' => 'Delivery key is only for hosting projects.'], 422);
        }

        if ($project->delivery_key_shown_at) {
            return response()->json([
                'message' => 'Key already shown at ' . $project->delivery_key_shown_at->format('d M Y, h:i A') . '.',
                'shown_at' => $project->delivery_key_shown_at,
                'verified' => ! is_null($project->delivery_key_verified_at),
            ], 409);
        }

        $key = $project->ensureDeliveryKey();

        return response()->json([
            'key' => $key,
            'shown_at' => $project->delivery_key_shown_at,
            'message' => 'Save this key. It will not be shown again.',
        ]);
    }

    public function verifyKey(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        $request->validate([
            'key' => 'required|string',
        ]);

        if (! $project->delivery_key_hash) {
            return response()->json(['message' => 'No delivery key exists for this project.'], 422);
        }

        $inputHash = hash('sha256', strtoupper(trim($request->key)));

        if (! hash_equals($project->delivery_key_hash, $inputHash)) {
            return response()->json(['message' => 'Invalid delivery key.'], 422);
        }

        $project->update(['delivery_key_verified_at' => now()]);

        return response()->json([
            'message' => 'Key verified. Hosting details unlocked.',
            'verified' => true,
        ]);
    }

    public function forgotKey($id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        if ($project->delivery_method !== 'hosting') {
            return response()->json(['message' => 'Delivery key is only for hosting projects.'], 422);
        }

        $newKey = $project->rotateDeliveryKey();

        NotificationService::send(
            Auth::id(),
            'Delivery Key Reset',
            'Your new delivery key for project "' . $project->title . '" is: ' . $newKey . '. Please save it securely.',
            'project',
            route('seller.projects.show', $project->id),
            ['project_id' => $project->id],
        );

        $email = Auth::user()->email;
        if ($email) {
            MailService::sendTemplate(
                'delivery-key-recovered',
                $email,
                [
                    'project' => $project->title,
                    'delivery_key' => $newKey,
                    'project_link' => route('seller.projects.show', $project->id),
                ],
            );
        }

        return response()->json([
            'message' => 'New delivery key sent to your notifications and email. Check your notification panel.',
        ]);
    }

    public function viewHostings($id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);
        $verified = ! is_null($project->delivery_key_verified_at);

        $hostings = $project->hostings()->get()->map(function ($h) use ($verified) {
            if (! $verified) {
                $h->makeHidden(['provider', 'domain', 'port', 'protocol', 'password', 'notes']);
            }
            return $h;
        });

        return response()->json([
            'hostings' => $hostings,
            'key_verified' => $verified,
            'locked' => ! $verified,
        ]);
    }

    public function submitDelivery(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        if ($project->delivery_method === 'hosting' && ! $project->delivery_key_verified_at) {
            return response()->json(['message' => 'Verify delivery key before submitting.'], 422);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        $order = $project->order;
        $latestVersion = ProjectDelivery::where('project_id', $project->id)->max('version') ?? 0;

        $delivery = ProjectDelivery::create([
            'project_id' => $project->id,
            'order_id' => $order?->id,
            'seller_id' => $seller->id,
            'buyer_id' => $project->buyer_id,
            'title' => $request->title,
            'description' => $request->description,
            'delivery_notes' => $request->delivery_notes,
            'version' => $latestVersion + 1,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        NotificationService::send(
            $project->buyer?->user_id,
            'Delivery Submitted',
            'Seller has submitted a delivery for project "' . $project->title . '". Please review.',
            'project',
            route('buyer.projects.show', $project->id),
            ['project_id' => $project->id, 'delivery_id' => $delivery->id],
        );

        return response()->json([
            'message' => 'Delivery submitted successfully.',
            'delivery' => $delivery,
        ], 201);
    }

    public function attachments($id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);
        $attachments = $project->attachments()->latest()->get();
        return response()->json($attachments);
    }

    public function uploadAttachment(Request $request, $id): JsonResponse
    {
        $seller = Auth::user()->seller;
        $project = Project::where('seller_id', $seller->id)->findOrFail($id);

        if (! $request->hasFile('attachments') && ! $request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded.'], 422);
        }

        $request->validate([
            'attachments' => 'array',
            'attachments.*' => 'file|max:10240',
            'file' => 'nullable|file|max:10240',
        ]);

        $files = $request->hasFile('attachments')
            ? $request->file('attachments')
            : [$request->file('file')];

        $created = [];
        foreach ($files as $file) {
            $path = $file->store('project-attachments/' . $project->id, 'public');
            $created[] = ProjectAttachment::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'uploaded_by' => 'seller',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return response()->json([
            'message' => count($created) . ' file(s) uploaded.',
            'attachments' => $created,
        ], 201);
    }

    public function downloadAttachment($id): JsonResponse
    {
        $attachment = ProjectAttachment::findOrFail($id);
        $path = storage_path('app/public/' . $attachment->file_path);

        if (! file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->json([
            'url' => Storage::disk('public')->url($attachment->file_path),
            'file_name' => $attachment->file_name,
        ]);
    }

    public function deleteAttachment($id): JsonResponse
    {
        $attachment = ProjectAttachment::findOrFail($id);
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['message' => 'Attachment deleted.']);
    }

    /**
     * Hostings limited to the non-secret identity fields unless the key is verified.
     */
    private function hostingsFor(Project $project)
    {
        $verified = ! is_null($project->delivery_key_verified_at);

        return $project->hostings()->get()->map(function ($h) use ($verified) {
            if (! $verified) {
                $h->makeHidden(['provider', 'domain', 'port', 'protocol', 'password', 'notes']);
            }
            return $h;
        });
    }

    private function handleDelivered(Project $project): void
    {
        if ($project->delivery_method === 'hosting') {
            $project->hostings()->delete();
        }

        NotificationService::send(
            $project->buyer?->user_id,
            'Project Delivered',
            'Your project "' . $project->title . '" has been delivered and is now live.',
            'project',
            route('buyer.projects.show', $project->id),
            ['project_id' => $project->id],
        );

        $email = $project->buyer?->user?->email;
        if ($email) {
            MailService::sendTemplate(
                'project-delivered',
                $email,
                [
                    'project' => $project->title,
                    'project_link' => route('buyer.projects.show', $project->id),
                    'order' => $project->order?->order_number,
                ],
            );
        }
    }

    private function handleCompleted(Project $project): void
    {
        $hosting = $project->delivery_method === 'hosting';

        NotificationService::send(
            $project->buyer?->user_id,
            'Project Completed',
            'Your project "' . $project->title . '" has been completed by the seller.' .
                ($hosting
                    ? ' Please provide your hosting access details to take the project live.'
                    : ' You can now view and download the deliverable files.'),
            'project',
            route('buyer.projects.show', $project->id),
            ['project_id' => $project->id],
        );

        $email = $project->buyer?->user?->email;
        if ($email) {
            MailService::sendTemplate(
                'project-completed',
                $email,
                [
                    'project' => $project->title,
                    'project_link' => route('buyer.projects.show', $project->id),
                    'order' => $project->order?->order_number,
                    'delivery_type' => $hosting ? 'hosting' : 'digital',
                ],
            );
        }
    }
}