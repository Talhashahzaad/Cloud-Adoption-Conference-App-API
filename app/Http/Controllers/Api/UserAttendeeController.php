<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAttendeeController extends Controller
{
    /**
     * Logged-in user's conference attendees
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $registration = $user->registrations()
            ->where('approval_status', 'approved')
            ->with('conference')
            ->first();

        if (!$registration || !$registration->conference) {
            return response()->json([
                'success' => false,
                'message' => 'No approved conference registration found.',
            ], 404);
        }

        $conference = $registration->conference;

        $query = Registration::with([
            'user',
            'conference',
        ])
            ->where('conference_id', $conference->id)
            ->where('approval_status', 'approved');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $attendees = $query
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => AttendeeResource::collection($attendees),
            'meta' => [
                'current_page' => $attendees->currentPage(),
                'last_page' => $attendees->lastPage(),
                'per_page' => $attendees->perPage(),
                'total' => $attendees->total(),
            ],
        ]);
    }

    /**
     * Single attendee from logged-in user's conference
     */
    public function show(
        Request $request,
        Registration $registration
    ): JsonResponse {
        $user = $request->user();

        $myRegistration = $user->registrations()
            ->where('approval_status', 'approved')
            ->first();

        if (!$myRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'No approved conference registration found.',
            ], 404);
        }

        if ($registration->conference_id !== $myRegistration->conference_id) {
            return response()->json([
                'success' => false,
                'message' => 'Attendee not found.',
            ], 404);
        }

        if ($registration->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Attendee not found.',
            ], 404);
        }

        $registration->load([
            'user',
            'conference',
        ]);

        return response()->json([
            'success' => true,
            'data' => new AttendeeResource($registration),
        ]);
    }
}
