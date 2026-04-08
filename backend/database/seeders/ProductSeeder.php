<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a few seller users
        $sellers = User::factory()->count(5)->create([
            'role' => 'seller',
        ]);

        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');

            return;
        }

        // Create 20 sample products across sellers and categories
        foreach ($sellers as $seller) {
            Product::factory()
                ->count(4)
                ->recycle($seller)
                ->recycle($categories->random())
                ->has(
                    ProductMedia::factory()->video()->state(['is_primary' => true]),
                    'media'
                )
                ->has(
                    ProductMedia::factory()->image()->count(2)->state(['is_primary' => false]),
                    'media'
                )
                ->create();
        }
    }
}
