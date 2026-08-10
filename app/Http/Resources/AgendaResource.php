<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'conference_id' => $this->conference_id,

            'session_name' => $this->session_name,

            'presenter' => $this->presenter,

            'session_date' => $this->session_date?->format('Y-m-d'),

            'duration' => $this->duration,

            'place' => $this->place,

            'room' => $this->room,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}
