<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'place_to_visit' => ['required', 'string', 'max:255'],
            'activities' => ['required', 'string'],
            'transportation_tips' => ['nullable', 'string'],
            'daily_expenses' => ['required', 'numeric', 'min:0'],
        ];
    }
}