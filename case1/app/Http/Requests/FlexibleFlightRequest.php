<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlexibleFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'legs' => [
                'required',
                'array',
                'min:1',
                'max:2',
            ],

            'legs.*.origin' => [
                'required',
                'string',
                'size:3',
                'alpha',
            ],

            'legs.*.destination' => [
                'required',
                'string',
                'size:3',
                'alpha',
            ],

            'legs.*.departure_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'legs.*.max_stops' => [
                'nullable',
                'integer',
                Rule::in([0, 1, 2]),
            ],

            'legs.*.departure_time_range' => [
                'nullable',
                'array',
            ],

            'legs.*.departure_time_range.earliest_hour' => [
                'nullable',
                'integer',
                'min:0',
                'max:23',
            ],

            'legs.*.departure_time_range.latest_hour' => [
                'nullable',
                'integer',
                'min:0',
                'max:23',
            ],

            'legs.*.departure_time_range.arrival_earliest_hour' => [
                'nullable',
                'integer',
                'min:0',
                'max:23',
            ],

            'legs.*.departure_time_range.arrival_latest_hour' => [
                'nullable',
                'integer',
                'min:0',
                'max:23',
            ],

            'adults' => [
                'nullable',
                'integer',
                'min:1',
                'max:9',
            ],

            'children' => [
                'nullable',
                'integer',
                'min:0',
                'max:8',
            ],

            'infants_in_seat' => [
                'nullable',
                'integer',
                'min:0',
                'max:8',
            ],

            'infants_on_lap' => [
                'nullable',
                'integer',
                'min:0',
                'max:8',
                'lte:adults',
            ],

            'cabin_class' => [
                'nullable',
                Rule::in([
                    'economy',
                    'premium_economy',
                    'business',
                    'first',
                ]),
            ],

            'min_carry_on_bags' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'min_checked_bags' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'max_price' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'airlines_include' => [
                'nullable',
                'array',
            ],

            'airlines_include.*' => [
                'string',
                'size:2',
                'alpha',
            ],

            'airlines_exclude' => [
                'nullable',
                'array',
            ],

            'airlines_exclude.*' => [
                'string',
                'size:2',
                'alpha',
            ],

            'allow_self_transfer' => [
                'nullable',
                'boolean',
            ],

            'market' => [
                'nullable',
                'string',
                'size:2',
                'alpha',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $legs = $this->input('legs', []);

        foreach ($legs as $index => $leg) {
            if (isset($leg['origin'])) {
                $legs[$index]['origin'] = strtoupper($leg['origin']);
            }

            if (isset($leg['destination'])) {
                $legs[$index]['destination'] = strtoupper($leg['destination']);
            }
        }

        $this->merge([
            'legs' => $legs,
            'cabin_class' => $this->cabin_class
                ? strtolower($this->cabin_class)
                : null,
            'market' => $this->market
                ? strtoupper($this->market)
                : null,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $legs = $this->input('legs', []);

            foreach ($legs as $index => $leg) {
                if (
                    isset($leg['origin'], $leg['destination']) &&
                    strtoupper($leg['origin']) === strtoupper($leg['destination'])
                ) {
                    $validator->errors()->add(
                        "legs.$index.destination",
                        'The destination must be different from the origin.'
                    );
                }
            }

            if (count($legs) === 2) {
                $firstDate = $legs[0]['departure_date'] ?? null;
                $secondDate = $legs[1]['departure_date'] ?? null;

                if ($firstDate && $secondDate) {
                    if ($secondDate < $firstDate) {
                        $validator->errors()->add(
                            'legs.1.departure_date',
                            'The second leg departure date must be on or after the first leg departure date.'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'legs.required' => 'At least one flight leg is required.',
            'legs.min' => 'At least one flight leg is required.',
            'legs.max' => 'A maximum of two flight legs is allowed.',

            'legs.*.origin.size' => 'Each origin must be a 3-letter IATA airport code.',
            'legs.*.destination.size' => 'Each destination must be a 3-letter IATA airport code.',
            'legs.*.departure_date.date_format' => 'Each departure date must be in YYYY-MM-DD format.',

            'cabin_class.in' => 'Cabin class must be economy, premium_economy, business, or first.',
            'max_price.integer' => 'The maximum price must be an integer.',
            'market.size' => 'The market must be a 2-letter country code.',
        ];
    }
}
