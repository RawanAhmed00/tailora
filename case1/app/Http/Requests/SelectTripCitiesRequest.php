<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectTripCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => [
                'required',
                'array',
                'min:1',
            ],

            'days.*.day_number' => [
                'required',
                'integer',
                'min:1',
            ],

            'days.*.city_id' => [
                'required',
                'integer',
                'exists:cities,id',
            ],
        ];
    }
}