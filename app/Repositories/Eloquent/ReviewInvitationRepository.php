<?php

namespace App\Repositories\Eloquent;

use App\Models\ReviewInvitation;
use App\Repositories\Contracts\ReviewInvitationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewInvitationRepository implements ReviewInvitationRepositoryInterface
{
    public function findByToken(string $token): ?ReviewInvitation
    {
        return ReviewInvitation::query()
            ->where('token_hash', ReviewInvitation::hashToken($token))
            ->first();
    }

    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = ReviewInvitation::query()
            ->with(['order.items'])
            ->withCount('reviews')
            ->inStatus($filters['status'] ?? null)
            ->latest('updated_at');

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('customer_email', 'like', "%{$term}%")
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%"));
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
