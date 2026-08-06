<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'conference_date' => $this->conference_date?->format('Y-m-d'),
            'venue_name' => $this->venue_name,
            'venue_address' => $this->venue_address,
            'venue_city' => $this->venue_city,
            'venue_state' => $this->venue_state,
            'active_start_date' => $this->active_start_date?->format('Y-m-d'),
            'active_end_date' => $this->active_end_date?->format('Y-m-d'),
            'google_map_url' => $this->google_map_url,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
