<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
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
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 5000),
            'currency' => 'SAR',
            'condition' => fake()->randomElement(['new', 'used']),
            'status' => 'active',
            'views_count' => fake()->numberBetween(0, 10000),
            'likes_count' => fake()->numberBetween(0, 500),
        ];
    }

    /**
     * Mark the product as draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Mark the product as sold.
     */
    public function sold(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'sold',
        ]);
    }

    /**
     * Mark the product as used.
     */
    public function used(): static
    {
        return $this->state(fn(array $attributes) => [
            'condition' => 'used',
        ]);
    }
}
