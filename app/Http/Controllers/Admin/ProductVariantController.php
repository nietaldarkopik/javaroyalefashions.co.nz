<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductVariantRequest;
use App\Http\Requests\Admin\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

class ProductVariantController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $this->products->createVariant(
            $product,
            $request->safe()->except(['image', 'gallery_images']),
            $request->file('image'),
            $request->file('gallery_images', []),
        );

        return back()->with('status', 'Variant added.');
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $this->products->updateVariant(
            $variant,
            $request->safe()->except(['image', 'gallery_images']),
            $request->file('image'),
            $request->file('gallery_images', []),
        );

        return back()->with('status', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $this->products->deleteVariant($variant);

        return back()->with('status', 'Variant removed.');
    }
}
