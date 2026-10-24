<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(500, 20000);

        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'subtotal' => $subtotal,
            'shipping' => 0,
            'total' => $subtotal,
            'customer_name' => fake()->name(),
            'phone' => '9'.fake()->numerify('#########'),
            'address_line' => fake()->streetAddress(),
            'city' => 'Tirupati',
            'state' => 'Andhra Pradesh',
            'postal_code' => '517102',
            'payment_method' => 'cod',
        ];
    }
}
