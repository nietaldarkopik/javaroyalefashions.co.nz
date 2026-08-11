<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'nullable', 'string', 'max:60',
                Rule::unique('product_variants', 'sku')->ignore($this->route('variant')),
            ],
            'size' => ['nullable', 'string', 'max:60'],
            'color' => ['nullable', 'string', 'max:60'],
            'attribute_name' => ['nullable', 'string', 'max:60', 'required_with:attribute_value'],
            'attribute_value' => ['nullable', 'string', 'max:120', 'required_with:attribute_name'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'attribute_name.required_with' => 'Give the extra attribute a name (e.g. Material) if you set a value.',
            'attribute_value.required_with' => 'Give the extra attribute a value if you set a name.',
        ];
    }
}
