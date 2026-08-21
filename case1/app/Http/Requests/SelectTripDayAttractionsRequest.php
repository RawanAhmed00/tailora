<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectTripDayAttractionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attraction_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'attraction_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:attractions,id',
            ],
        ];
    }
}