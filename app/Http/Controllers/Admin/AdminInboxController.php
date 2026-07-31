<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * In-app notifications for the logged-in admin (navbar bell icon).
 */
class AdminInboxController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    /**
     * List notifications for the admin user (navbar dropdown).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min(50, max(5, (int) $request->get('limit', 20)));
        $unreadOnly = $request->boolean('unread_only', false);
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
     * Unread count for navbar badge.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user->id);
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $user = Auth::user();
        $ok = $this->notificationService->markAsRead($id, $user->id);
        if (!$ok) {
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
