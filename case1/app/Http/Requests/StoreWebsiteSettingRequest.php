<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebsiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    
    public function rules(): array
{
    return [
        'site_name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string',

        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'homepage_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

        'social_media_links' => 'nullable|array',

        'social_media_links.*.type' => 'required|in:facebook,instagram,twitter,linkedin,youtube',

        'social_media_links.*.link' => 'required|url',
    ];
}
}
