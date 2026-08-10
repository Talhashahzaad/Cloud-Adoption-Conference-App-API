<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    public function index(): JsonResponse
    {
        $conferences = Conference::active()
            ->orderBy('conference_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ConferenceResource::collection($conferences),
        ]);
    }

    public function show(Conference $conference): JsonResponse
    {
        if ($conference->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Conference not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ConferenceResource($conference),
        ]);
    }


}
