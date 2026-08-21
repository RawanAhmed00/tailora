<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ignav_id' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ignav_id.required' => 'The Ignav flight ID is required.',
            'ignav_id.string' => 'The Ignav flight ID must be a string.',
        ];
    }
}