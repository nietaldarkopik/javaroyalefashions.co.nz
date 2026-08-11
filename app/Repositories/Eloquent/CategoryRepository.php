<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allActive(): Collection
    {
        return Category::query()->active()->ordered()->get();
    }

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('products')
            ->ordered()
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::query()->active()->where('slug', $slug)->first();
    }

    public function findById(int $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function create(array $data): Category
    {
        return Category::query()->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
