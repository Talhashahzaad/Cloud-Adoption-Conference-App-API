<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'conference_id' => $this->conference_id,

            'ticket_reference' => $this->ticket_reference,

            'ticket_holder_name' => $this->ticket_holder_name,

            'ticket_holder_email' => $this->ticket_holder_email,

            'ticket_source' => $this->ticket_source,

            'status' => $this->status,

            'imported_by' => $this->imported_by,

            'used_by_user_id' => $this->used_by_user_id,

            'used_at' => $this->used_at,

            'created_at' => $this->created_at,

        ];
    }
}
