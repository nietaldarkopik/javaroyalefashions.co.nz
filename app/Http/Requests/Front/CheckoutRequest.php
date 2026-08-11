<?php

namespace App\Http\Requests\Front;

use App\Enums\ShippingArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],

            'address_line1' => ['required', 'string', 'max:200'],
            'address_line2' => ['nullable', 'string', 'max:200'],
            'suburb' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:10'],
            'area' => ['required', Rule::enum(ShippingArea::class)],

            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
