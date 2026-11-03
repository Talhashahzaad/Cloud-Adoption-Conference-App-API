<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            'phone' => $this->phone,

            'company_name' => $this->company_name,

            'designation' => $this->designation,

            'city' => $this->city,

            'state' => $this->state,

            'area_of_interest' => $this->area_of_interest,

            'linkedin_url' => $this->linkedin_url,

            'remarks' => $this->remarks,

            'status' => $this->status,

            'role' => [
                'id' => $this->role?->id,
                'name' => $this->role?->name,
            ],

            'email_verified_at' => $this->email_verified_at,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}
