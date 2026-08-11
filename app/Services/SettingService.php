<?php

namespace App\Services;

use App\Enums\ShippingArea;
use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingService
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    public function current(): Setting
    {
        return $this->settings->current();
    }

    public function update(array $data): Setting
    {
        return $this->settings->update($data);
    }

    public function shippingRateFor(ShippingArea $area): float
    {
        $setting = $this->current();

        return (float) match ($area) {
            ShippingArea::Urban => $setting->shipping_urban_rate,
            ShippingArea::Rural => $setting->shipping_rural_rate,
        };
    }
}
