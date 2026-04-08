<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductMedia>
 */
class ProductMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isVideo = fake()->boolean(40);

        return [
            'product_id' => Product::factory(),
            'type' => $isVideo ? 'video' : 'image',
            'path' => $isVideo
                ? 'products/videos/sample-' . fake()->uuid() . '.mp4'
                : 'products/images/sample-' . fake()->uuid() . '.jpg',
            'mime_type' => $isVideo ? 'video/mp4' : 'image/jpeg',
            'size_bytes' => $isVideo
                ? fake()->numberBetween(1_000_000, 8_000_000)
                : fake()->numberBetween(50_000, 500_000),
            'sort_order' => 0,
            'is_primary' => true,
        ];
    }

    /**
     * Set as video media.
     */
    public function video(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'video',
            'path' => 'products/videos/sample-' . fake()->uuid() . '.mp4',
            'mime_type' => 'video/mp4',
            'size_bytes' => fake()->numberBetween(1_000_000, 8_000_000),
        ]);
    }

    /**
     * Set as image media.
     */
    public function image(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'image',
            'path' => 'products/images/sample-' . fake()->uuid() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => fake()->numberBetween(50_000, 500_000),
        ]);
    }
}
