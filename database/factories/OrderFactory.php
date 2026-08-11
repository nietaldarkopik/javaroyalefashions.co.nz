<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\ShippingArea;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 20, 500);
        $area = $this->faker->randomElement(ShippingArea::cases());
        $shipping = $area === ShippingArea::Urban ? 13.00 : 18.00;
        $customer = Customer::factory();

        return [
            'order_number' => 'NZ'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
            'customer_id' => $customer,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => '02'.$this->faker->numerify('########'),
            'shipping_address_line1' => $this->faker->streetAddress(),
            'shipping_city' => $this->faker->city(),
            'shipping_postcode' => $this->faker->numerify('####'),
            'shipping_area' => $area,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'grand_total' => round($subtotal + $shipping, 2),
            'currency' => 'NZD',
            'status' => $this->faker->randomElement(OrderStatus::cases()),
        ];
    }
}
