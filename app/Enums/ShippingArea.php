<?php

namespace App\Enums;

enum ShippingArea: string
{
    case Urban = 'urban';
    case Rural = 'rural';

    public function label(): string
    {
        return match ($this) {
            self::Urban => 'Urban Area',
            self::Rural => 'Rural Area',
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
