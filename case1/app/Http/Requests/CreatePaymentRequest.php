<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payable_type' => [
                'required',
                'string',
                Rule::in([
                    'booking',
                ]),
            ],

            'payable_id' => [
                'required',
                'integer',
                'exists:bookings,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
                'in:EGP',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'in:card',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payable_type.in' =>
                'The selected payment type is invalid.',

            'payable_id.exists' =>
                'The selected booking does not exist.',

            'amount.min' =>
                'The payment amount must be greater than zero.',

            'currency.in' =>
                'Only EGP currency is supported.',

            'payment_method.in' =>
                'Only card payments are currently supported.',
        ];
    }
}