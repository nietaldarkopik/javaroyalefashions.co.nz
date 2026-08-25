<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

class ProductVariantImageController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function destroy(Product $product, ProductVariant $variant, ProductVariantImage $image): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);
        abort_unless($image->product_variant_id === $variant->id, 404);

        $this->products->deleteVariantGalleryImage($variant, $image->id);

        return back()->with('status', 'Image removed.');
    }
}
