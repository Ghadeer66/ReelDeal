<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'name_en' => ucfirst($name),
            'name_ar' => 'تصنيف ' . fake()->word(),
            'slug' => Str::slug($name),
            'icon_url' => 'https://api.dicebear.com/7.x/icons/svg?seed=' . urlencode($name),
            'is_active' => true,
        ];
    }
}
