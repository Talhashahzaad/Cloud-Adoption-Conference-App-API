<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],

            'conference' => [
                'id' => $this->conference?->id,
                'title' => $this->conference?->title,
            ],

            'ticket_reference' => $this->ticket_reference,

            'ticket_source' => $this->ticket_source,

            'approval_status' => $this->approval_status,

            'approval_remarks' => $this->approval_remarks,

            'approved_by' => $this->approved_by,

            'approved_at' => $this->approved_at,

            'created_at' => $this->created_at,

        ];
    }
}
