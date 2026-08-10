<?php

namespace App\Http\Requests;

class UpdateAgendaRequest extends StoreAgendaRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'conference_id' => [
                'required',
                'exists:conferences,id',
            ],

            'session_name' => [
                'required',
                'string',
                'max:255',
            ],

            'presenter' => [
                'required',
                'string',
                'max:255',
            ],

            'session_date' => [
                'required',
                'date',
            ],

            'duration' => [
                'required',
                'string',
                'max:100',
            ],

            'place' => [
                'required',
                'string',
                'max:255',
            ],

            'room' => [
                'required',
                'string',
                'max:255',
            ],

        ];
    }

}
