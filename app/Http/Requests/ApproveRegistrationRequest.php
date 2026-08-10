<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approval_status' => [
                'required',
                Rule::in(['approved', 'rejected']),
            ],

            'approval_remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
