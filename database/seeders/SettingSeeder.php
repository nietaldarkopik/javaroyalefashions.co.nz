<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->firstOrCreate(['id' => 1], [
            'site_name' => 'NZ Product Catalog',
            'site_tagline' => 'Quality goods, shipped across New Zealand.',
            'contact_email' => 'hello@example.co.nz',
            'contact_phone' => '09 123 4567',
            'contact_whatsapp' => '+64 21 123 4567',
            'address' => '123 Queen Street, Auckland 1010, New Zealand',
            'bank_name' => 'ANZ Bank New Zealand',
            'bank_account_name' => 'Example Store Ltd',
            'bank_account_number' => '01-1234-5678901-00',
            'bank_swift_code' => 'ANZBNZ22',
            'shipping_urban_rate' => 13.00,
            'shipping_rural_rate' => 18.00,
            'currency_code' => 'NZD',
            'meta_title' => 'NZ Product Catalog — Quality Goods, Shipped Nationwide',
            'meta_description' => 'Browse our catalog and order online. Manual bank transfer, guest checkout, no account required.',
        ]);
    }
}
