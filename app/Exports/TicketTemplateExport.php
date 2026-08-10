<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [

            'ticket_reference',

            'ticket_holder_name',

            'ticket_holder_email',

            'ticket_source',

        ];
    }
}
