<?php

namespace App\Repositories\Eloquent;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\ProductReview;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductReviewRepository implements ProductReviewRepositoryInterface
{
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = ProductReview::query()->with(['product', 'order'])->latest('submitted_at')->latest('id');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['product_id'])) {
            $query->where('product_id', (int) $filters['product_id']);
        }

        if (! empty($filters['order'])) {
            $term = $filters['order'];
            $query->whereHas('order', fn ($q) => $q->where('order_number', 'like', "%{$term}%"));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function approvedForProduct(Product $product, int $perPage): LengthAwarePaginator
    {
        return $product->reviews()->approved()
            ->latest('submitted_at')->latest('id')
            ->paginate($perPage, ['*'], 'reviews_page')
            ->withQueryString()
            ->fragment('reviews');
    }

    public function approvedSummaryForProduct(Product $product): array
    {
        $counts = $product->reviews()->approved()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $distribution = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $distribution[$star] = (int) ($counts[$star] ?? 0);
        }

        $count = array_sum($distribution);
        $sum = array_sum(array_map(fn ($star, $n) => $star * $n, array_keys($distribution), $distribution));

        return [
            'average' => $count > 0 ? round($sum / $count, 1) : 0.0,
            'count' => $count,
            'distribution' => $distribution,
        ];
    }

    public function countByStatus(ReviewStatus $status): int
    {
        return ProductReview::query()->where('status', $status->value)->count();
    }
}
