<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'line_key' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
