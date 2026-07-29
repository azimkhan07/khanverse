<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Notification Listing
     */
    public function index()
    {
        $notifications = Notification::where(
            'user_id',
            Auth::id()
        )
            ->latest()
            ->paginate(20);

        return view(
            'seller.notifications.index',
            compact('notifications')
        );
    }

    /**
     * Latest Notifications (AJAX)
     */
    public function latest()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->take(10)->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'url' => $notification->url,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Unread Count (AJAX)
     */
    public function unreadCount()
    {
        $count = Notification::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'is_read',
                false
            )
            ->count();

        return response()->json([

            'success' => true,

            'count' => $count

        ]);
    }

    /**
     * Mark Single Notification Read
     */
    public function markRead($id)
    {
        $notification = Notification::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $notification->update([

            'is_read' => true

        ]);

        if ($notification->url) {

            return redirect($notification->url);
        }

        return back()->with(
            'success',
            'Notification marked as read.'
        );
    }

    /**
     * Mark All Notifications Read
     */
    public function markAllRead()
    {
        Notification::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'is_read',
                false
            )
            ->update([

                'is_read' => true

            ]);

        return response()->json([

            'success' => true,

            'message' => 'All notifications marked as read.'

        ]);
    }

    /**
     * Delete Notification
     */
    public function destroy($id)
    {
        $notification = Notification::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $notification->delete();

        return response()->json([

            'success' => true,

            'message' => 'Notification deleted successfully.'

        ]);
    }
}
