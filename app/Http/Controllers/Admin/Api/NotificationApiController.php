<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate($request->per_page ?? 10);

        $data = $notifications->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'url' => $n->url,
                'type' => $n->type,
                'read' => (bool) $n->is_read,
                'time' => $n->created_at->diffForHumans(),
                'created_at' => $n->created_at->toISOString(),
            ];
        });

        return response()->json([
            'data' => $data,
            'unread_count' => Notification::where('user_id', Auth::id())->unread()->count(),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => Notification::where('user_id', Auth::id())->unread()->count(),
        ]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        NotificationService::markAsRead($notification);
        return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
    }

    public function markAllRead(): JsonResponse
    {
        NotificationService::markAllAsRead(Auth::id());
        return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        NotificationService::delete($notification);
        return response()->json(['success' => true, 'message' => 'Notification deleted.']);
    }
}

