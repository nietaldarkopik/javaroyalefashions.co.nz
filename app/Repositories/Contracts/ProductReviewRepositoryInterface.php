<?php

namespace App\Repositories\Contracts;

use App\Enums\ReviewStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductReviewRepositoryInterface
{
    /**
     * @param  array{status?: string, product_id?: string|int, order?: string}  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function approvedForProduct(Product $product, int $perPage): LengthAwarePaginator;

    /**
     * Average rating, approved review count and per-star counts (5 → 1)
     * across a product's approved reviews.
     *
     * @return array{average: float, count: int, distribution: array<int, int>}
     */
    public function approvedSummaryForProduct(Product $product): array;

    public function countByStatus(ReviewStatus $status): int;
}
