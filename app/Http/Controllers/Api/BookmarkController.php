<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Bookmarked Attendees
     */
    public function index(Request $request): JsonResponse
    {
        $bookmarks = Bookmark::with([
            'registration.user',
            'registration.conference',
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookmarks->map(function ($bookmark) {
                return [
                    'id' => $bookmark->id,
                    'attendee' => [
                        'id' => $bookmark->registration->id,
                        'name' => $bookmark->registration->user->name,
                        'email' => $bookmark->registration->user->email,
                        'phone' => $bookmark->registration->user->phone,
                        'company_name' => $bookmark->registration->user->company_name,
                        'designation' => $bookmark->registration->user->designation,
                        'city' => $bookmark->registration->user->city,
                        'state' => $bookmark->registration->user->state,
                        'area_of_interest' => $bookmark->registration->user->area_of_interest,
                        'linkedin_url' => $bookmark->registration->user->linkedin_url,
                    ],
                    'conference' => [
                        'id' => $bookmark->registration->conference->id,
                        'title' => $bookmark->registration->conference->title,
                    ],
                    'created_at' => $bookmark->created_at,
                ];
            }),
        ]);
    }


    /**
     * Bookmark Attendee
     */
    public function store(
        Request $request,
        Registration $registration
    ): JsonResponse {

        // Get logged-in user's approved registration
        $myRegistration = $request->user()
            ->registrations()
            ->where('approval_status', 'approved')
            ->first();

        if (!$myRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'No approved conference registration found.',
            ], 404);
        }

        // Attendee must belong to user's conference
        if ($registration->conference_id !== $myRegistration->conference_id) {
            return response()->json([
                'success' => false,
                'message' => 'Attendee not found.',
            ], 404);
        }

        // Only approved attendees can be bookmarked
        if ($registration->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved attendees can be bookmarked.',
            ], 422);
        }

        $bookmark = Bookmark::firstOrCreate([
            'user_id' => $request->user()->id,
            'registration_id' => $registration->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendee bookmarked successfully.',
            'data' => [
                'id' => $bookmark->id,
                'user_id' => $bookmark->user_id,
                'registration_id' => $bookmark->registration_id,
            ],
        ], 201);
    }

    /**
     * Remove attendee from bookmarks
     */
    public function destroy(
        Request $request,
        Registration $registration
    ): JsonResponse {

        $myRegistration = $request->user()
            ->registrations()
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
                'message' => 'Bookmark not found.',
            ], 404);
        }

        $deleted = Bookmark::where('user_id', $request->user()->id)
            ->where('registration_id', $registration->id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Bookmark not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendee removed from bookmarks.',
        ]);
    }
}