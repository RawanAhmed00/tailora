<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'num_days'=>'nullable|integer|min:1',
            'travel_style'=>'nullable|string|max:255',
            'dis_country'=>'nullable|string|max:255',
            'budget'=>'nullable|numeric',
            'interests'=>'nullable|string',
            'number_of_travelers'=>'nullable|integer|min:1',
            'user_id'=>'nullable|exists:users,id',
        ];
    }
    //     protected function passedValidation(): void
    //     {
    //        $filteredData=array_filter($this->validated(), function($value){
    //      return !is_null($value) && $value !=='';
    //        });
    //        $this->replace($filteredData);
    //     }
     }

