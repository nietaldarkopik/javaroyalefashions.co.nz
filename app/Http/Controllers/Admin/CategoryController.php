<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categories,
    ) {}

    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => $this->categories->listForAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = $this->categories->create(
            $request->safe()->except('image'),
            $request->file('image'),
        );

        return redirect()->route('admin.categories.edit', $category)
            ->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categories->update(
            $category,
            $request->safe()->except('image'),
            $request->file('image'),
        );

        return redirect()->route('admin.categories.edit', $category)
            ->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category deleted.');
    }
}
