<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'pending',
            'total' => fake()->randomFloat(2, 20, 10000),
            'currency' => 'SAR',
            'shipping_address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'country' => 'SA',
                'postal_code' => fake()->postcode(),
            ],
        ];
    }

    /**
     * Mark the order as paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Mark the order as delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'delivered',
        ]);
    }
}
