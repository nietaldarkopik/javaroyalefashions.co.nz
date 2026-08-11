<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.products.index', [
            'products' => $this->products->listForAdmin($request->only(['search', 'category_id'])),
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->products->create(
            $request->safe()->except(['image', 'gallery_images']),
            $request->file('image'),
            $request->file('gallery_images', []),
        );

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load('images'),
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->products->update(
            $product,
            $request->safe()->except(['image', 'gallery_images']),
            $request->file('image'),
            $request->file('gallery_images', []),
        );

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->delete($product);

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }
}
