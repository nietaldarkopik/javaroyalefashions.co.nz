<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'show_site_name_with_logo' => ['sometimes', 'boolean'],
            'favicon' => ['nullable', 'image', 'max:512'],

            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],

            'bank_name' => ['required', 'string', 'max:120'],
            'bank_account_name' => ['required', 'string', 'max:150'],
            'bank_account_number' => ['required', 'string', 'max:60'],
            'bank_swift_code' => ['nullable', 'string', 'max:20'],

            'shipping_urban_rate' => ['required', 'numeric', 'min:0'],
            'shipping_rural_rate' => ['required', 'numeric', 'min:0'],
            'currency_code' => ['required', 'string', 'size:3'],

            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
