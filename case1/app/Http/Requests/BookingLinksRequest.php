<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Option 1: ignav_id
            |--------------------------------------------------------------------------
            */

            'ignav_id' => [
                'nullable',
                'string',
                'required_without:origin',
            ],

            /*
            |--------------------------------------------------------------------------
            | Option 2: Manual Flight Details
            |--------------------------------------------------------------------------
            */

            'origin' => [
                'nullable',
                'string',
                'size:3',
                'alpha',
                'required_without:ignav_id',
            ],

            'destination' => [
                'nullable',
                'string',
                'size:3',
                'alpha',
                'required_without:ignav_id',
            ],

            'departure_date' => [
                'nullable',
                'date_format:Y-m-d',
                'required_without:ignav_id',
            ],

            'outbound_carrier_code' => [
                'nullable',
                'string',
                'size:2',
                'alpha',
                'required_without:ignav_id',
            ],

            'outbound_flight_number' => [
                'nullable',
                'string',
                'required_without:ignav_id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Return Flight - Optional
            |--------------------------------------------------------------------------
            */

            'return_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:departure_date',
            ],

            'inbound_carrier_code' => [
                'nullable',
                'string',
                'size:2',
                'alpha',
                'required_with:return_date',
            ],

            'inbound_flight_number' => [
                'nullable',
                'string',
                'required_with:return_date',
            ],

            /*
            |--------------------------------------------------------------------------
            | Passenger Information
            |--------------------------------------------------------------------------
            */

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
            ],

            'infants_in_seat' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'infants_on_lap' => [
                'nullable',
                'integer',
                'min:0',
                'lte:adults',
            ],

            /*
            |--------------------------------------------------------------------------
            | Market
            |--------------------------------------------------------------------------
            */

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
        $this->merge([
            'ignav_id' => $this->ignav_id
                ? trim($this->ignav_id)
                : null,

            'origin' => $this->origin
                ? strtoupper($this->origin)
                : null,

            'destination' => $this->destination
                ? strtoupper($this->destination)
                : null,

            'outbound_carrier_code' => $this->outbound_carrier_code
                ? strtoupper($this->outbound_carrier_code)
                : null,

            'inbound_carrier_code' => $this->inbound_carrier_code
                ? strtoupper($this->inbound_carrier_code)
                : null,

            'market' => $this->market
                ? strtoupper($this->market)
                : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'ignav_id.required_without' =>
                'The ignav_id is required when manual flight details are not provided.',

            'origin.required_without' =>
                'The origin is required when ignav_id is not provided.',

            'destination.required_without' =>
                'The destination is required when ignav_id is not provided.',

            'departure_date.required_without' =>
                'The departure date is required when ignav_id is not provided.',

            'outbound_carrier_code.required_without' =>
                'The outbound carrier code is required when ignav_id is not provided.',

            'outbound_flight_number.required_without' =>
                'The outbound flight number is required when ignav_id is not provided.',

            'inbound_carrier_code.required_with' =>
                'The inbound carrier code is required when return_date is provided.',

            'inbound_flight_number.required_with' =>
                'The inbound flight number is required when return_date is provided.',

            'return_date.after_or_equal' =>
                'The return date must be on or after the departure date.',
        ];
    }
}
