<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function current(): Setting
    {
        return Setting::query()->first() ?? Setting::query()->create([
            'site_name' => config('app.name', 'Product Catalog'),
            'contact_email' => 'admin@javaroyalefashions.co.nz',
            'bank_name' => 'Bank Name',
            'bank_account_name' => 'Account Name',
            'bank_account_number' => '00-0000-0000000-00',
        ]);
    }

    public function update(array $data): Setting
    {
        $setting = $this->current();
        $setting->update($data);

        return $setting->fresh();
    }
}
