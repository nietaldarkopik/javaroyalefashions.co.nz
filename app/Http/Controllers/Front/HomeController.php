<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly CategoryService $categories,
    ) {}

    public function index(): View
    {
        return view('front.home.index', [
            'featuredProducts' => $this->products->featured(8),
            'categories' => $this->categories->allActive(),
        ]);
    }
}
