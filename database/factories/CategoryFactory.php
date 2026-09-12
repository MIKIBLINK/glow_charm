<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'type' => fake()->randomElement(['product', 'service']),
            'description' => fake()->sentence(),
            'image' => null,
            'status' => true,
        ];
    }
}
