<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->words(3, true));
        $price = $this->faker->randomFloat(2, 15, 250);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'sale_price' => $this->faker->boolean(20) ? round($price * 0.8, 2) : null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(25),
            'weight_kg' => $this->faker->randomFloat(2, 0.1, 5),
        ];
    }
}
