<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Home & Living', 'Outdoor & Garden', 'Kitchenware', 'Gifts & Accessories'];

        foreach ($categories as $index => $name) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Placeholder description for {$name}.",
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
