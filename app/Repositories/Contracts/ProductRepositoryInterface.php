<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * @param  array{search?: string, category?: string, sort?: string}  $filters
     */
    public function paginateForFront(array $filters, int $perPage = 12): LengthAwarePaginator;

    /**
     * @param  array{search?: string, category_id?: int}  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    public function findById(int $id): ?Product;

    public function featured(int $limit = 8): Collection;

    public function related(Product $product, int $limit = 4): Collection;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;
}
