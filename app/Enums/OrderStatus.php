<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case WaitingVerification = 'waiting_verification';
    case Paid = 'paid';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Pending Payment',
            self::WaitingVerification => 'Waiting Verification',
            self::Paid => 'Paid',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Bootstrap 5 / AdminLTE badge color for this status.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::PendingPayment => 'secondary',
            self::WaitingVerification => 'warning',
            self::Paid => 'info',
            self::Processing => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Statuses an admin may move this order to next.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PendingPayment => [self::WaitingVerification, self::Cancelled],
            self::WaitingVerification => [self::Paid, self::PendingPayment, self::Cancelled],
            self::Paid => [self::Processing, self::Cancelled],
            self::Processing => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
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
