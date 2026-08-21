<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'budget' => [
                'required',
                'numeric',
                'min:0',
            ],

            'travel_style' => [
                'required',
                'string',
                'max:100',
            ],

            'interests' => [
                'nullable',
                'array',
            ],

            'interests.*' => [
                'string',
                'max:100',
            ],

            'travelers' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}