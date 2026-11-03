<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'conference_id' => [
                'sometimes',
                'required',
                'exists:conferences,id',
            ],

            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'sometimes',
                'required',
                'string',
            ],

            'scheduled_at' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],

        ];
    }
}