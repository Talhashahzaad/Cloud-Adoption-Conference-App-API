<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Conference;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    /**
     * Conference Attendees
     */
    public function index(
        Conference $conference,
        Request $request
    ): JsonResponse {


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
     * Single Attendee
     */
    public function show(
        Registration $registration
    ): JsonResponse {


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
