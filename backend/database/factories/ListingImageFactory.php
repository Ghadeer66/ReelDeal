<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory(),
            'image_url' => 'https://picsum.photos/1080/1920?random=' . fake()->unique()->numberBetween(1001, 2000),
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }
}
