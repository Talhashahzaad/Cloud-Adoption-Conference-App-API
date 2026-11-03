<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'conference' => [
                'id' => $this->conference?->id,
                'title' => $this->conference?->title,
            ],

            'title' => $this->title,

            'message' => $this->message,

            'scheduled_at' => $this->scheduled_at,

            'sent_at' => $this->sent_at,

            'created_by' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}
