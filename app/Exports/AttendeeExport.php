<?php

namespace App\Exports;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendeeExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Get registrations for export.
     */
    public function query(): Builder
    {
        return Registration::query()
            ->with([
                'user',
                'conference',
            ])
            ->orderBy('conference_id')
            ->orderBy('id');
    }

    /**
     * Map each registration to CSV row.
     */
    public function map($registration): array
    {
        return [
            $registration->conference?->title,
            $registration->user?->name,
            $registration->user?->email,
            $registration->user?->phone,
            $registration->user?->company_name,
            $registration->user?->designation,
            $registration->user?->city,
            $registration->user?->state,
            $registration->user?->area_of_interest,
            $registration->user?->linkedin_url,
            $registration->ticket_reference,
            $registration->approval_status,
        ];
    }

    /**
     * CSV headings.
     */
    public function headings(): array
    {
        return [
            'Conference Name',
            'Name',
            'Email',
            'Phone',
            'Company',
            'Designation',
            'City',
            'State',
            'Area of Interest',
            'LinkedIn',
            'Event Ticket ID',
            'Registration Status',
        ];
    }
}
