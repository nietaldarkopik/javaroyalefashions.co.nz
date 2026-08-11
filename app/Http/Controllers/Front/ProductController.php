<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly CategoryService $categories,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'sort']);

        return view('front.products.index', [
            'products' => $this->products->listForFront($filters),
            'categories' => $this->categories->allActive(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->products->findBySlugOrFail($slug);

        return view('front.products.show', [
            'product' => $product,
            'relatedProducts' => $this->products->related($product),
        ]);
    }
}
