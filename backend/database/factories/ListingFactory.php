<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\MediaType;
use App\Enums\PriceFlag;
use App\Enums\SellerType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 10, 500);
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title_en' => fake()->sentence(4),
            'title_ar' => 'منتج ' . fake()->word(),
            'description_en' => fake()->paragraph(),
            'description_ar' => 'وصف المنتج ' . fake()->sentence(),
            'price' => $price,
            'currency' => 'USD',
            'condition' => fake()->randomElement(ListingCondition::cases())->value,
            'seller_type' => fake()->randomElement(SellerType::cases())->value,
            'status' => ListingStatus::Live->value,
            'media_type' => fake()->randomElement(MediaType::cases())->value,
            'video_url' => 'https://dl.dropboxusercontent.com/scl/fi/g183t5wrtosb4y4ndp34d/sample-vertical-video.mp4?rlkey=4ns9of8u9k1qmy8s60d8p9h6z&st=ixkofbmb&raw=1',
            'thumbnail_url' => 'https://picsum.photos/1080/1920?random=' . fake()->unique()->numberBetween(1, 1000),
            'likes_count' => fake()->numberBetween(0, 500),
            'saves_count' => fake()->numberBetween(0, 100),
            'views_count' => fake()->numberBetween(100, 5000),
            'price_flag' => PriceFlag::Normal->value,
        ];
    }
}
