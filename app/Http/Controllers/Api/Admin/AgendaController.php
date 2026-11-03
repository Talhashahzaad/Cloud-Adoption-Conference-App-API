<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Requests\UpdateAgendaRequest;
use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Conference $conference): JsonResponse
    {
        $agendas = $conference->agendas()
            ->orderBy('session_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AgendaResource::collection($agendas),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        Conference $conference,
        StoreAgendaRequest $request
    ): JsonResponse {
        $agenda = $conference->agendas()->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Agenda created successfully.',
            'data' => new AgendaResource($agenda),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Conference $conference,
        Agenda $agenda
    ): JsonResponse {


        $this->ensureAgendaBelongsToConference($conference, $agenda);

        return response()->json([
            'success' => true,
            'data' => new AgendaResource($agenda),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Conference $conference,
        Agenda $agenda,
        UpdateAgendaRequest $request
    ): JsonResponse {
        $this->ensureAgendaBelongsToConference($conference, $agenda);

        $agenda->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Agenda updated successfully.',
            'data' => new AgendaResource($agenda->fresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Conference $conference,
        Agenda $agenda
    ): JsonResponse {
        $this->ensureAgendaBelongsToConference($conference, $agenda);

        $agenda->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agenda deleted successfully.',
        ]);
    }

    private function ensureAgendaBelongsToConference(
        Conference $conference,
        Agenda $agenda
    ): void {
        abort_if(
            $agenda->conference_id !== $conference->id,
            404,
            'Agenda not found for this conference.'
        );
    }

}
