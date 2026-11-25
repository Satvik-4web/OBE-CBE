<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(
        Request $request,
        Notification $notification
    ): JsonResponse {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notification marked as read.',
            'notification' => $notification->fresh(),
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(
        Request $request,
        Notification $notification
    ): JsonResponse {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted.',
        ]);
    }
}