<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly CategoryService $categories,
    ) {}

    public function show(Request $request, string $slug): View
    {
        $category = $this->categories->findBySlugOrFail($slug);
        $filters = array_merge($request->only(['search', 'sort']), ['category' => $category->slug]);

        return view('front.products.index', [
            'products' => $this->products->listForFront($filters),
            'categories' => $this->categories->allActive(),
            'filters' => $filters,
            'activeCategory' => $category,
        ]);
    }
}
