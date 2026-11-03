<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
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

            'email_verified_at' => $this->email_verified_at,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

            'role' => $this->whenLoaded('role', function () {
                return [
                    'id' => $this->role->id,
                    'name' => $this->role->name,
                ];
            }),

            'registrations' => $this->whenLoaded('registrations', function () {

                return $this->registrations->map(function ($registration) {

                    return [

                        'id' => $registration->id,

                        'conference_id' => $registration->conference_id,

                        'ticket_reference' => $registration->ticket_reference,

                        'approval_status' => $registration->approval_status,

                        'conference' => $registration->conference
                            ? [
                                'id' => $registration->conference->id,
                                'title' => $registration->conference->title,
                            ]
                            : null,

                    ];

                });

            }),

        ];
    }
}