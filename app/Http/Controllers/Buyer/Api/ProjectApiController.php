<?php

namespace App\Http\Controllers\Buyer\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectHosting;
use App\Services\MailService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $query = Project::with(['seller', 'service'])
            ->where('buyer_id', $buyer->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($projects);
    }

    public function show($id): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $project = Project::with(['seller', 'service', 'attachments', 'deliveries', 'order'])
            ->where('buyer_id', $buyer->id)
            ->findOrFail($id);

        $project->load('hostings');

        return response()->json($project);
    }

    public function submitHosting(Request $request, $id): JsonResponse
    {
        $buyer = Auth::user()->buyer;

        $project = Project::where('buyer_id', $buyer->id)->findOrFail($id);

        if ($project->delivery_method !== 'hosting') {
            return response()->json(['message' => 'This project does not require hosting details.'], 422);
        }

        if ($project->status !== 'completed') {
            return response()->json(['message' => 'Hosting details can only be submitted once the project is completed.'], 422);
        }

        if ($project->status === 'delivered') {
            return response()->json(['message' => 'This project has already been delivered; hosting access has been cleared.'], 422);
        }

        $existing = ProjectHosting::where('project_id', $project->id)->first();

        $rules = [
            'hosting_type' => 'required|in:ftp,cpanel,hosting,git',
            'host' => 'required|string|max:255',
            'port' => 'nullable|string|max:10',
            'username' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        // Password is required on first submission but optional when updating
        // (a blank password keeps the previously stored value).
        $rules['password'] = $existing
            ? 'nullable|string|max:255'
            : 'required|string|max:255';

        $validated = $request->validate($rules);

        if ($existing) {
            if (empty($validated['password'] ?? null)) {
                unset($validated['password']);
            } else {
                $validated['password'] = $request->input('password');
            }
            $existing->update($validated);
            $hosting = $existing;
        } else {
            $hosting = ProjectHosting::create(array_merge($validated, [
                'project_id' => $project->id,
                'order_id' => $project->order?->id,
                'buyer_id' => $buyer->id,
            ]));
        }

        NotificationService::send(
            $project->seller?->user_id,
            'Hosting Details Submitted',
            'Buyer has submitted hosting details for project "' . $project->title . '". You can now view them.',
            'project',
            route('seller.projects.show', $project->id),
            ['project_id' => $project->id],
        );

        $sellerEmail = $project->seller?->user?->email;
        if ($sellerEmail) {
            MailService::sendTemplate(
                'hosting-received',
                $sellerEmail,
                [
                    'project' => $project->title,
                    'project_link' => route('seller.projects.show', $project->id),
                    'order' => $project->order?->order_number,
                ],
            );
        }

        return response()->json([
            'message' => 'Hosting details saved successfully.',
            'hosting' => $hosting->fresh(),
        ]);
    }
}