<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'age' => 'nullable|integer|min:1',
            'dist_country' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'role' => 'nullable|in:user,admin,t_guide',
            'phone_num' => 'nullable|string|max:20',
        ];
    }
}