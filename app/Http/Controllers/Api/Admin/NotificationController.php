<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use App\Jobs\SendNotificationJob;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::with([
            'conference',
            'creator',
        ]);

        if ($request->filled('conference_id')) {

            $query->where(
                'conference_id',
                $request->conference_id
            );

        }

        $notifications = $query
            ->latest()
            ->paginate(
                $request->integer('per_page', 10)
            );

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreNotificationRequest $request
    ): JsonResponse {

        $notification = Notification::create([

            'conference_id' => $request->conference_id,

            'title' => $request->title,

            'message' => $request->message,

            'scheduled_at' => $request->scheduled_at,

            'created_by' => auth()->id(),

        ]);

        $notification->load([
            'conference',
            'creator',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully.',
            'data' => new NotificationResource($notification),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Notification $notification
    ): JsonResponse {

        $notification->load([
            'conference',
            'creator',
        ]);

        return response()->json([
            'success' => true,
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateNotificationRequest $request,
        Notification $notification
    ): JsonResponse {

        if ($notification->sent_at) {
            return response()->json([
                'success' => false,
                'code' => 'already_sent',
                'message' => 'A notification that has already been sent cannot be edited.',
            ], 409);
        }

        $notification->update(
            $request->only([
                'conference_id',
                'title',
                'message',
                'scheduled_at',
            ])
        );

        $notification->load([
            'conference',
            'creator',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully.',
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Notification $notification
    ): JsonResponse {
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }

    public function send(
        Notification $notification
    ): JsonResponse {

        if ($notification->sent_at) {
            return response()->json([
                'success' => false,
                'code' => 'already_sent',
                'message' => 'This notification has already been sent.',
            ], 409);
        }

        SendNotificationJob::dispatch($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification queued for delivery.',
            'data' => [
                'notification_id' => $notification->id,
            ],
        ]);
    }
}