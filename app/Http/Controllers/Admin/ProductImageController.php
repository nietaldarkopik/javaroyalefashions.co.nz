<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

class ProductImageController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $this->products->deleteGalleryImage($product, $image->id);

        return back()->with('status', 'Image removed.');
    }
}
