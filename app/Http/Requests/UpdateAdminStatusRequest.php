<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    /**
     * The users.status column is an enum of pending/approved/rejected.
     * For admins, "active" means approved and "inactive" means rejected,
     * so accept the app's vocabulary and normalise it before validation.
     */
    protected function prepareForValidation(): void
    {
        $aliases = [
            'active'   => 'approved',
            'inactive' => 'rejected',
        ];

        $status = strtolower((string) $this->input('status'));

        $this->merge([
            'status' => $aliases[$status] ?? $status,
        ]);
    }

    public function rules(): array
    {
        return [

            'status' => [
                'required',
                Rule::in([
                    'approved',
                    'rejected',
                ]),
            ],

        ];
    }
}
