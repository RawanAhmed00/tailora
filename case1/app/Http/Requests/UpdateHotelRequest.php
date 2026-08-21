<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'price_per_night' => 'sometimes|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
            'review_count' => 'nullable|integer|min:0',
            'amenities' => 'nullable|string',
            'available_rooms' => 'sometimes|integer|min:0',
        ];
    }
}