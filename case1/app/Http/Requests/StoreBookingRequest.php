<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => [
                'nullable',
                'integer',
                'exists:trips,id',
            ],

            'flight_id' => [
                'nullable',
                'integer',
                'exists:flights,id',
            ],

            'hotel_id' => [
                'nullable',
                'integer',
                'exists:hotels,id',
            ],

            'number_of_nights' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'wants_tour_guide' => [
                'nullable',
                'boolean',
            ],

            'tour_guide_id' => [
                'nullable',
                'integer',
            ],

            'total_price' => [
                'nullable',
                'numeric',
            ],
        ];
    }
}