<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\AgendaResource;

class UserAgendaController extends Controller
{
    /**
     * Logged-in user's conference agenda
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

        $today = now()->toDateString();

        if (
            $conference->status !== 'active' ||
            $today < $conference->active_start_date ||
            $today > $conference->active_end_date
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Conference is not currently active.',
            ], 403);
        }

        $agendas = $conference->agendas()
            ->orderBy('session_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AgendaResource::collection($agendas),
        ]);
    }
}
