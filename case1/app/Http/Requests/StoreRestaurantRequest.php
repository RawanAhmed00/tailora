<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'locality' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'cuisines' => 'nullable|string',
            'average_cost_for_two' => 'required|integer|min:0',
            'currency' => 'nullable|string|max:50',
            'has_table_booking' => 'boolean',
            'has_online_delivery' => 'boolean',
            'is_delivering_now' => 'boolean',
            'price_range' => 'required|integer|min:1|max:5',
            'rating' => 'nullable|numeric|between:0,5',
            'votes' => 'nullable|integer|min:0',
        ];
    }
}