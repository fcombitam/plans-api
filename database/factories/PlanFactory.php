<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->word(),
            'price_per_month' => fake()->randomNumber(6, false),
            'users_limit' => rand(3, 10),
            'features' => json_encode(['caracteristica' => "valor", 'caracteristica2' => "valor2"])
        ];
    }
}
