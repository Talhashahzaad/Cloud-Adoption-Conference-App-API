<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'ticket_reference' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'digits_between:10,15'
            ],

            'company_name' => [
                'required',
                'string',
                'max:255'
            ],

            'designation' => [
                'required',
                'string',
                'max:255'
            ],

            'city' => [
                'required',
                'string',
                'max:255'
            ],

            'state' => [
                'required',
                'string',
                'max:255'
            ],

            'area_of_interest' => [
                'nullable',
                'string',
                'max:255'
            ],

            'linkedin_url' => [
                'nullable',
                'url'
            ],

            'remarks' => [
                'nullable',
                'string'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

        ];
    }
}
