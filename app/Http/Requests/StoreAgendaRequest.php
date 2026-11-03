<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

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
                'after_or_equal:today',
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
