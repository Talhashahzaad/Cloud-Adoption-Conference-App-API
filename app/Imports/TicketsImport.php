<?php

namespace App\Imports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TicketsImport implements ToCollection, WithHeadingRow
{
    protected int $conferenceId;

    protected int $adminId;

    public function __construct(int $conferenceId, int $adminId)
    {
        $this->conferenceId = $conferenceId;
        $this->adminId = $adminId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {

            if (empty($row['ticket_reference'])) {
                continue;
            }

            Ticket::firstOrCreate(

                [
                    'conference_id' => $this->conferenceId,
                    'ticket_reference' => trim($row['ticket_reference']),
                ],

                [
                    'ticket_holder_name' => $row['ticket_holder_name'] ?? '',

                    'ticket_holder_email' => strtolower(
                        trim($row['ticket_holder_email'] ?? '')
                    ),

                    'ticket_source' => $row['ticket_source'] ?? null,

                    'status' => 'unused',

                    'imported_by' => $this->adminId,
                ]

            );
        }
    }
}
