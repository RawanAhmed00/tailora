<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AirportSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'q.required' => 'The airport search query is required.',
            'q.min' => 'The airport search query must be at least 2 characters.',

            'limit.integer' => 'The limit must be an integer.',
            'limit.min' => 'The limit must be at least 1.',
            'limit.max' => 'The limit cannot exceed 20.',
        ];
    }
}

