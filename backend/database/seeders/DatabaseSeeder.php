<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use App\Enums\UserType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create robust category tree
        $electronics = Category::factory()->create(['name_en' => 'Electronics']);
        $fashion = Category::factory()->create(['name_en' => 'Fashion']);
        $home = Category::factory()->create(['name_en' => 'Home & Garden']);

        // 2. Create users
        $testUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password123'),
            'user_type' => UserType::Merchant->value,
        ]);

        $sellers = User::factory(5)->create();

        // 3. Seed listings
        foreach ($sellers->merge([$testUser]) as $seller) {
            $cat = fake()->randomElement([$electronics, $fashion, $home]);
            $listings = Listing::factory(3)->create([
                'user_id' => $seller->id,
                'category_id' => $cat->id,
            ]);

            foreach ($listings as $listing) {
                \App\Models\ListingImage::factory(2)->create([
                    'listing_id' => $listing->id,
                ]);
            }
        }
    }
}
