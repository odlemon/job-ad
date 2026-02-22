<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $unreadOnly = $request->boolean('unread_only', false);
        $limit = $request->integer('limit', 20);

        if ($unreadOnly) {
            $notifications = $this->notificationService->getUnreadNotifications($user->id, $limit);
        } else {
            $notifications = $this->notificationService->getAllNotifications($user->id, $limit);
        }

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get unread count.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user->id);

        if (!$success) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'message' => 'Notification marked as read',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'message' => 'All notifications marked as read',
            'marked_count' => $count,
        ]);
    }
}
