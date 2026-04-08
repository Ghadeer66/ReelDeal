<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Electronics', 'name_ar' => 'إلكترونيات', 'slug' => 'electronics', 'icon' => 'smartphone'],
            ['name_en' => 'Fashion', 'name_ar' => 'أزياء', 'slug' => 'fashion', 'icon' => 'shirt'],
            ['name_en' => 'Home & Garden', 'name_ar' => 'المنزل والحديقة', 'slug' => 'home-garden', 'icon' => 'home'],
            ['name_en' => 'Sports', 'name_ar' => 'رياضة', 'slug' => 'sports', 'icon' => 'dumbbell'],
            ['name_en' => 'Vehicles', 'name_ar' => 'مركبات', 'slug' => 'vehicles', 'icon' => 'car'],
            ['name_en' => 'Beauty', 'name_ar' => 'جمال', 'slug' => 'beauty', 'icon' => 'sparkles'],
            ['name_en' => 'Books', 'name_ar' => 'كتب', 'slug' => 'books', 'icon' => 'book-open'],
            ['name_en' => 'Food & Drinks', 'name_ar' => 'طعام ومشروبات', 'slug' => 'food-drinks', 'icon' => 'utensils'],
            ['name_en' => 'Kids & Babies', 'name_ar' => 'أطفال ورضع', 'slug' => 'kids-babies', 'icon' => 'baby'],
            ['name_en' => 'Services', 'name_ar' => 'خدمات', 'slug' => 'services', 'icon' => 'wrench'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
