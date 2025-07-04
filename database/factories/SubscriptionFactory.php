<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::inRandomOrder()->value('id'),
            'plan_id' => Plan::inRandomOrder()->value('id'),
            'date_assign' => now(),
            'date_expired' => now(),
            'status' => fake()->randomElement([Subscription::STATUS_ACTIVE, Subscription::STATUS_INACTIVE])
        ];
    }
}
