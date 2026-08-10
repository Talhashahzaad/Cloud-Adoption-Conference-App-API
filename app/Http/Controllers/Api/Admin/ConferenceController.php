<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreConferenceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateConferenceRequest;
use App\Http\Requests\UpdateConferenceStatusRequest;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Conference::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conferences = $query
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => ConferenceResource::collection($conferences),
            'meta' => [
                'current_page' => $conferences->currentPage(),
                'last_page' => $conferences->lastPage(),
                'per_page' => $conferences->perPage(),
                'total' => $conferences->total(),
            ],
        ]);
    }

    public function show(Conference $conference): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ConferenceResource($conference),
        ]);
    }
    public function store(StoreConferenceRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $conference = Conference::create([

                'title' => $request->title,

                'slug' => Str::slug($request->title),

                'conference_date' => $request->conference_date,

                'venue_name' => $request->venue_name,

                'venue_address' => $request->venue_address,

                'venue_city' => $request->venue_city,

                'venue_state' => $request->venue_state,

                'active_start_date' => $request->active_start_date,

                'active_end_date' => $request->active_end_date,

                'google_map_url' => $request->google_map_url,

                'status' => $request->status,

                'created_by' => $request->user()->id,

            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conference created successfully.',
                'data' => new ConferenceResource($conference),
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Conference creation failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(UpdateConferenceRequest $request, Conference $conference): JsonResponse
    {
        DB::beginTransaction();

        try {

            $conference->update([

                'title' => $request->title,

                'slug' => Str::slug($request->title),

                'conference_date' => $request->conference_date,

                'venue_name' => $request->venue_name,

                'venue_address' => $request->venue_address,

                'venue_city' => $request->venue_city,

                'venue_state' => $request->venue_state,

                'active_start_date' => $request->active_start_date,

                'active_end_date' => $request->active_end_date,

                'google_map_url' => $request->google_map_url,

                'status' => $request->status,

            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conference updated successfully.',
                'data' => new ConferenceResource($conference->fresh()),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Conference update failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(Conference $conference): JsonResponse
    {
        DB::beginTransaction();

        try {

            $conference->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conference deleted successfully.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Conference deletion failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function updateStatus(
        UpdateConferenceStatusRequest $request,
        Conference $conference
    ): JsonResponse {

        $conference->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conference status updated successfully.',
            'data' => new ConferenceResource($conference->fresh()),
        ]);
    }
}
