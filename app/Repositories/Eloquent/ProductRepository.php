<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginateForFront(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['category', 'variants' => fn ($q) => $q->active()])
            ->inCategory($filters['category'] ?? null)
            ->search($filters['search'] ?? null);

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query()->with(['category', 'variants'])->withCount('variants')->latest();

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::query()->active()
            ->with(['category', 'images', 'variants' => fn ($q) => $q->active()])
            ->where('slug', $slug)->first();
    }

    public function findById(int $id): ?Product
    {
        return Product::query()->with(['images', 'variants'])->find($id);
    }

    public function featured(int $limit = 8): Collection
    {
        return Product::query()->active()->featured()
            ->with(['variants' => fn ($q) => $q->active()])
            ->latest()->limit($limit)->get();
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return Product::query()
            ->active()
            ->with(['variants' => fn ($q) => $q->active()])
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
