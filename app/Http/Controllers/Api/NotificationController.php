<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\MarkNotificationReadRequest;
use App\Http\Requests\Api\SendNotificationRequest;
use App\Http\Requests\Api\StoreDeviceTokenRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\UserDeviceToken;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends ApiController
{
    public function index(): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', request()->user()->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get();

        return $this->success([
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $notifications->where('is_read', false)->count(),
        ], 'Notifications fetched successfully.');
    }

    public function storeDeviceToken(StoreDeviceTokenRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $deviceToken = UserDeviceToken::withTrashed()
            ->where('fcm_token', $validated['fcm_token'])
            ->first();

        if ($deviceToken) {
            $deviceToken->restore();
            $deviceToken->update([
                'user_id' => $request->user()->id,
                'device_type' => $validated['device_type'] ?? $deviceToken->device_type,
            ]);
        } else {
            $deviceToken = UserDeviceToken::create([
                'user_id' => $request->user()->id,
                'fcm_token' => $validated['fcm_token'],
                'device_type' => $validated['device_type'] ?? null,
            ]);
        }

        return $this->success([
            'device_token' => [
                'id' => $deviceToken->id,
                'fcm_token' => $deviceToken->fcm_token,
                'device_type' => $deviceToken->device_type,
            ],
        ], 'Device token saved successfully.');
    }

    public function markAsRead(MarkNotificationReadRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false);

        if (! empty($validated['notification_id'])) {
            $query->where('id', $validated['notification_id']);
        }

        $updated = $query->update(['is_read' => true]);

        return $this->success([
            'updated' => $updated,
        ], 'Notifications marked as read.');
    }

    public function send(SendNotificationRequest $request, FirebaseNotificationService $firebase): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'] ?? null,
            'data' => $validated['data'] ?? null,
            'is_read' => false,
            'status' => 'active',
        ]);

        $push = $firebase->sendToUser(
            $userId,
            $validated['title'],
            $validated['body'],
            array_merge(
                ['notification_id' => (string) $notification->id],
                $validated['data'] ?? []
            )
        );

        return $this->success([
            'notification' => new NotificationResource($notification),
            'push' => [
                'success' => $push['success'],
                'sent' => $push['sent'] ?? 0,
                'device_type' => $push['device_type'] ?? $request->user()->device_type,
                'message' => $push['message'],
            ],
        ], 'Notification sent successfully.');
    }
}
