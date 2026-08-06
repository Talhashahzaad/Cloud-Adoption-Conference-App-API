<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'conference_date' => [
                'required',
                'date',
            ],

            'venue_name' => [
                'required',
                'string',
                'max:255',
            ],

            'venue_address' => [
                'required',
                'string',
            ],

            'venue_city' => [
                'required',
                'string',
                'max:255',
            ],

            'venue_state' => [
                'required',
                'string',
                'max:255',
            ],

            'active_start_date' => [
                'required',
                'date',
            ],

            'active_end_date' => [
                'required',
                'date',
                'after_or_equal:active_start_date',
            ],

            'google_map_url' => [
                'required',
                'url',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ];
    }
}
