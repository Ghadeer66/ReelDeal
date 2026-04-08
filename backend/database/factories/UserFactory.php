<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserType;
use App\Enums\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type' => fake()->randomElement(UserType::cases())->value,
            'preferred_language' => Language::English->value,
            'is_active' => true,
        ];
    }
}
