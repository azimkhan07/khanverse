<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(15);

        return view('buyer.notifications.index', compact('notifications'));
    }

    public function latest()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'count' => $count,
            'html' => view('components.notification-items', compact('notifications'))->render()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $notification->update(['is_read' => 1]);

        return response()->json([
            'success' => true
        ]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())->where('is_read', 0)->update(['is_read' => 1]);

        return response()->json([
            'success' => true
        ]);
    }
}
