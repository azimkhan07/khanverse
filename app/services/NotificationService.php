<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Create Notification
     *
     * @param int         $userId
     * @param string      $title
     * @param string      $message
     * @param string|null $type
     * @param string|null $url
     * @param array|null  $data
     *
     * @return Notification
     */
    public static function send(
        int $userId,
        string $title,
        string $message,
        ?string $type = null,
        ?string $url = null,
        ?array $data = null
    ) {
        return Notification::create([

            'user_id'  => $userId,

            'title'    => $title,

            'message'  => $message,

            'type'     => $type,

            'url'      => $url,

            'data'     => $data,

            'is_read'  => false,

        ]);
    }

    /**
     * Mark Notification Read
     */
    public static function markAsRead(Notification $notification)
    {
        $notification->update([

            'is_read' => true

        ]);

        return $notification;
    }

    /**
     * Mark All Notifications Read
     */
    public static function markAllAsRead(int $userId)
    {
        return Notification::where(

            'user_id',
            $userId

        )->update([

            'is_read' => true

        ]);
    }

    /**
     * Delete Notification
     */
    public static function delete(Notification $notification)
    {
        return $notification->delete();
    }
}
