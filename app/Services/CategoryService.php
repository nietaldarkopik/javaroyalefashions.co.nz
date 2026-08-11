<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function allActive(): Collection
    {
        return $this->categories->allActive();
    }

    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->categories->paginateForAdmin($perPage);
    }

    public function findBySlugOrFail(string $slug): Category
    {
        return $this->categories->findBySlug($slug) ?? abort(404);
    }

    public function findByIdOrFail(int $id): Category
    {
        return $this->categories->findById($id) ?? abort(404);
    }

    public function create(array $data, ?UploadedFile $image): Category
    {
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($image) {
            $data['image_path'] = $image->store('categories', 'public');
        }

        return $this->categories->create($data);
    }

    public function update(Category $category, array $data, ?UploadedFile $image): Category
    {
        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        if ($image) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $image->store('categories', 'public');
        }

        return $this->categories->update($category, $data);
    }

    public function delete(Category $category): void
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $this->categories->delete($category);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
