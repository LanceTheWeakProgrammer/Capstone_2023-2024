<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->notifications; 
        $unreadNotifications = $user->unreadNotifications; 

        return response()->json([
            'all_notifications' => $notifications,
            'unread_notifications' => $unreadNotifications,
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read.'], 200);
        }

        return response()->json(['message' => 'Notification not found.'], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.'], 200);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($notificationId)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted successfully.'], 200);
        }

        return response()->json(['message' => 'Notification not found.'], 404);
    }
}
