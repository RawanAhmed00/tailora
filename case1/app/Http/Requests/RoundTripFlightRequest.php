<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoundTripFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin' => [
                'required',
                'string',
                'size:3',
            ],

            'destination' => [
                'required',
                'string',
                'size:3',
            ],

            'departure_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],

            'return_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:departure_date',
            ],

            'adults' => [
                'required',
                'integer',
                'min:1',
                'max:9',
            ],

            'cabin_class' => [
                'required',
                'string',
                'in:economy,premium_economy,business,first',
            ],
        ];
    }
}