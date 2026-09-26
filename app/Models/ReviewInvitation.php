<?php

namespace App\Models;

use App\Enums\ReviewInvitationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewInvitation extends Model
{
    protected $fillable = [
        'order_id',
        'customer_email',
        'token_hash',
        'expires_at',
        'sent_at',
        'send_count',
        'last_failed_at',
        'last_error',
        'completed_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'last_failed_at' => 'datetime',
            'completed_at' => 'datetime',
            'send_count' => 'integer',
        ];
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function status(): ReviewInvitationStatus
    {
        $reviewCount = $this->reviews_count ?? ($this->relationLoaded('reviews')
            ? $this->reviews->count()
            : $this->reviews()->count());

        return match (true) {
            $this->isCompleted() => ReviewInvitationStatus::Completed,
            $this->sent_at === null => ReviewInvitationStatus::NotSent,
            $this->isExpired() => ReviewInvitationStatus::Expired,
            $reviewCount > 0 => ReviewInvitationStatus::PartiallyReviewed,
            default => ReviewInvitationStatus::Sent,
        };
    }

    /**
     * Narrow a query to invitations currently in the given derived status.
     */
    public function scopeInStatus($query, ?string $status)
    {
        return match (ReviewInvitationStatus::tryFrom((string) $status)) {
            ReviewInvitationStatus::Completed => $query->whereNotNull('completed_at'),
            ReviewInvitationStatus::NotSent => $query->whereNull('completed_at')->whereNull('sent_at'),
            ReviewInvitationStatus::Expired => $query->whereNull('completed_at')->whereNotNull('sent_at')
                ->where('expires_at', '<=', now()),
            ReviewInvitationStatus::PartiallyReviewed => $query->whereNull('completed_at')->whereNotNull('sent_at')
                ->where('expires_at', '>', now())->has('reviews'),
            ReviewInvitationStatus::Sent => $query->whereNull('completed_at')->whereNotNull('sent_at')
                ->where('expires_at', '>', now())->doesntHave('reviews'),
            null => $query,
        };
    }
}
