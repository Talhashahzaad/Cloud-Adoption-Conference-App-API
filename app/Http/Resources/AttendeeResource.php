<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'registration_id' => $this->id,

            'approval_status' => $this->approval_status,

            'ticket_reference' => $this->ticket_reference,

            'approved_at' => $this->approved_at,

            'user' => [

                'id' => $this->user?->id,

                'name' => $this->user?->name,

                'email' => $this->user?->email,

                'phone' => $this->user?->phone,

                'company_name' => $this->user?->company_name,

                'designation' => $this->user?->designation,

                'city' => $this->user?->city,

                'state' => $this->user?->state,

            ],

            'conference' => [

                'id' => $this->conference?->id,

                'title' => $this->conference?->title,

            ],
        ];
    }
}