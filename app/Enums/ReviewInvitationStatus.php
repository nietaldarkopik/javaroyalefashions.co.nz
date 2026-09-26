<?php

namespace App\Enums;

/**
 * Derived, never stored: computed from an invitation's timestamps and how
 * many of the order's products have been reviewed, so it can't drift out
 * of sync with the data it describes.
 */
enum ReviewInvitationStatus: string
{
    case NotSent = 'not_sent';
    case Sent = 'sent';
    case PartiallyReviewed = 'partially_reviewed';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::NotSent => 'Not Sent',
            self::Sent => 'Sent',
            self::PartiallyReviewed => 'Partially Reviewed',
            self::Completed => 'Completed',
            self::Expired => 'Expired',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NotSent => 'secondary',
            self::Sent => 'info',
            self::PartiallyReviewed => 'primary',
            self::Completed => 'success',
            self::Expired => 'dark',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
