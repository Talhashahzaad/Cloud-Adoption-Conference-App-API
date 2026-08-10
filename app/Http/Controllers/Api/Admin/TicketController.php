<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportTicketRequest;
use App\Http\Resources\TicketResource;
use App\Imports\TicketsImport;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketTemplateExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::query();

        if ($request->filled('conference_id')) {
            $query->where('conference_id', $request->conference_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {

                $q->where('ticket_reference', 'like', "%{$request->search}%")
                    ->orWhere('ticket_holder_name', 'like', "%{$request->search}%")
                    ->orWhere('ticket_holder_email', 'like', "%{$request->search}%");

            });
        }

        $tickets = $query
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => TicketResource::collection($tickets),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'total' => $tickets->total(),
            ]
        ]);
    }

    public function import(
        ImportTicketRequest $request
    ): JsonResponse {

        Excel::import(

            new TicketsImport(
                $request->conference_id,
                auth()->id()
            ),

            $request->file('file')

        );

        return response()->json([
            'success' => true,
            'message' => 'Tickets imported successfully.',
        ]);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(

            new TicketTemplateExport(),

            'tickets_template.xlsx'

        );
    }
}
