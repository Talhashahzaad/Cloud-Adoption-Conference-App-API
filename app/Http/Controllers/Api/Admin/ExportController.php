<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\AttendeeExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Export attendee registration data as CSV.
     */
    public function attendeeExport(): BinaryFileResponse
    {
        return Excel::download(
            new AttendeeExport(),
            'attendees.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
