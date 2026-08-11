<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::query()->firstOrCreate(
            ['heading' => 'Quality goods, shipped across NZ.'],
            [
                'eyebrow' => 'Welcome',
                'subheading' => 'Browse the catalog, add what you need to your cart, and check out as a guest — no account required.',
                'button_text' => 'Shop All Products',
                'button_url' => '/products',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
