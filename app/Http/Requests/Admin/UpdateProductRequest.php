<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'sku' => [
                'nullable', 'string', 'max:60',
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $product = $this->route('product');
            $existingGallery = $product->images()->count();
            $hasPrimary = $this->hasFile('image') || $product->image_path;
            $newGallery = count($this->file('gallery_images', []));
            $total = ($hasPrimary ? 1 : 0) + $existingGallery + $newGallery;

            if ($total > 10) {
                $remaining = max(0, 10 - ($hasPrimary ? 1 : 0) - $existingGallery);
                $validator->errors()->add('gallery_images', "This product already has {$existingGallery} gallery image(s); you can add at most {$remaining} more (10 images total, including the primary image).");
            }
        });
    }
}
