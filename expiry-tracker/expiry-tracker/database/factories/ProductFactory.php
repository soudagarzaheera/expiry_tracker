<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true),
            'batch_number' => strtoupper(fake()->bothify('BAT-####')),
            'quantity' => fake()->numberBetween(1, 100),
            'unit' => fake()->randomElement(['Pieces', 'Boxes', 'Bottles', 'Kilograms']),
            'manufacturing_date' => now()->subMonths(2),
            'expiry_date' => now()->addDays(60),
            'description' => fake()->sentence(),
            'disposal_status' => 'active',
        ];
    }
}
