<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReviewInvitation;
use App\Models\User;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductReviewService
{
    public function __construct(
        private readonly ProductReviewRepositoryInterface $reviews,
    ) {}

    /**
     * Whether a customer may still use this invitation right now: not
     * expired, and the order still paid (not cancelled since sending).
     */
    public function canSubmit(ReviewInvitation $invitation): bool
    {
        return ! $invitation->isExpired() && $invitation->order->isReviewable();
    }

    /**
     * Save every review the customer filled in on the order review page,
     * all or nothing.
     *
     * Only the product keys are taken from the client, and each is checked
     * against the invitation's own order — the order, order line and
     * product actually stored all come from the server side. Products
     * already reviewed are skipped rather than overwritten, which also
     * makes a double-clicked submit harmless.
     *
     * @param  array<int|string, array{rating?: int|string|null, title?: string|null, comment?: string|null, customer_name?: string|null}>  $submitted
     * @return array{created: int, already_reviewed: int}
     *
     * @throws ValidationException
     */
    public function submit(ReviewInvitation $invitation, array $submitted): array
    {
        $order = $invitation->order()->with('items')->firstOrFail();
        $eligible = $order->reviewableItems();

        $unknown = collect(array_keys($submitted))
            ->reject(fn ($productId) => $eligible->has((int) $productId) && (string) (int) $productId === (string) $productId);

        if ($unknown->isNotEmpty()) {
            throw ValidationException::withMessages([
                'reviews' => 'One or more of the products submitted are not part of this order.',
            ]);
        }

        // A product counts as reviewed once it has a rating or a comment;
        // entirely blank ones are products the customer chose to skip.
        $filled = collect($submitted)
            ->filter(fn ($entry) => filled($entry['rating'] ?? null) || filled($entry['comment'] ?? null));

        if ($filled->isEmpty()) {
            throw ValidationException::withMessages([
                'reviews' => 'Please rate and review at least one product before submitting.',
            ]);
        }

        try {
            return DB::transaction(function () use ($invitation, $order, $eligible, $filled) {
                // Serialise concurrent submits for the same invitation so
                // the "already reviewed" check below can't be raced.
                $locked = ReviewInvitation::query()->whereKey($invitation->id)->lockForUpdate()->firstOrFail();

                if ($locked->isExpired() || ! $order->isReviewable()) {
                    throw ValidationException::withMessages([
                        'reviews' => 'This review link is no longer valid.',
                    ]);
                }

                $alreadyReviewed = ProductReview::query()->where('order_id', $order->id)->pluck('product_id')->all();
                $created = 0;
                $skipped = 0;

                foreach ($filled as $productId => $entry) {
                    $productId = (int) $productId;

                    if (in_array($productId, $alreadyReviewed, true)) {
                        $skipped++;

                        continue;
                    }

                    ProductReview::query()->create([
                        'review_invitation_id' => $locked->id,
                        'order_id' => $order->id,
                        'order_item_id' => $eligible[$productId]['item']->id,
                        'product_id' => $productId,
                        'customer_name' => $entry['customer_name'],
                        'rating' => (int) $entry['rating'],
                        'title' => filled($entry['title'] ?? null) ? $entry['title'] : null,
                        'comment' => $entry['comment'],
                        'status' => ReviewStatus::Pending,
                        'submitted_at' => now(),
                    ]);
                    $created++;
                }

                $this->refreshCompletion($locked);

                return ['created' => $created, 'already_reviewed' => $skipped];
            });
        } catch (UniqueConstraintViolationException) {
            // The unique (order_id, product_id) index caught a duplicate
            // the lock didn't (e.g. a database without row locking). The
            // transaction rolled back, so nothing was half-saved.
            return ['created' => 0, 'already_reviewed' => $filled->count()];
        }
    }

    /**
     * Mark the invitation completed once every reviewable product in the
     * order has a review — or clear that flag again if a review was
     * deleted and a product is open for review once more.
     */
    public function refreshCompletion(ReviewInvitation $invitation): void
    {
        $order = $invitation->order()->with('items')->first();

        if (! $order) {
            return;
        }

        $reviewed = ProductReview::query()->where('order_id', $order->id)->pluck('product_id')->unique();
        $remaining = $order->reviewableItems()->keys()->diff($reviewed);

        if ($remaining->isEmpty() && ! $invitation->completed_at) {
            $invitation->update(['completed_at' => now()]);
        } elseif ($remaining->isNotEmpty() && $invitation->completed_at) {
            $invitation->update(['completed_at' => null]);
        }
    }

    public function moderate(ProductReview $review, ReviewStatus $status, User $admin): ProductReview
    {
        $review->update([
            'status' => $status,
            'moderated_at' => now(),
            'moderated_by' => $admin->id,
        ]);

        return $review;
    }

    public function delete(ProductReview $review): void
    {
        $invitation = $review->invitation;

        $review->delete();

        if ($invitation) {
            $this->refreshCompletion($invitation);
        }
    }

    /**
     * @param  array{status?: string, product_id?: string|int, order?: string}  $filters
     */
    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        return $this->reviews->paginateForAdmin($filters);
    }

    public function approvedForProduct(Product $product): LengthAwarePaginator
    {
        return $this->reviews->approvedForProduct($product, config('reviews.per_page'));
    }

    /**
     * @return array{average: float, count: int, distribution: array<int, int>}
     */
    public function approvedSummaryForProduct(Product $product): array
    {
        return $this->reviews->approvedSummaryForProduct($product);
    }

    public function countByStatus(ReviewStatus $status): int
    {
        return $this->reviews->countByStatus($status);
    }
}
