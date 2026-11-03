<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfographicsController extends Controller
{
    /**
     * End User Home - Infographics
     * Only for logged-in user's registered conference.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Get user's approved conference registration
        |--------------------------------------------------------------------------
        */

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

        $conferenceId = $registration->conference_id;

        /*
        |--------------------------------------------------------------------------
        | Total Approved Attendees
        |--------------------------------------------------------------------------
        */

        $totalAttendees = Registration::where(
            'conference_id',
            $conferenceId
        )
            ->where('approval_status', 'approved')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Geographic Distribution
        |--------------------------------------------------------------------------
        */

        $geographicDistribution = Registration::query()
            ->where('registrations.conference_id', $conferenceId)
            ->where('registrations.approval_status', 'approved')
            ->join(
                'users',
                'registrations.user_id',
                '=',
                'users.id'
            )
            ->select(
                'users.city',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('users.city')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'city' => $item->city,
                    'total' => (int) $item->total,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Area Of Interest Breakdown
        |--------------------------------------------------------------------------
        */

        $areaOfInterestBreakdown = Registration::query()
            ->where('registrations.conference_id', $conferenceId)
            ->where('registrations.approval_status', 'approved')
            ->join(
                'users',
                'registrations.user_id',
                '=',
                'users.id'
            )
            ->select(
                'users.area_of_interest',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('users.area_of_interest')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'area_of_interest' => $item->area_of_interest,
                    'total' => (int) $item->total,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'conference_id' => $conferenceId,
                'total_attendees' => $totalAttendees,
                'geographic_distribution' => $geographicDistribution,
                'area_of_interest_breakdown' => $areaOfInterestBreakdown,
            ],
        ]);
    }
}
