<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Tenant::factory()->has(User::factory()->count(10))->count(10)->create();

        Plan::factory(5)->create();

        $tenants = Tenant::all();

        $plans = Plan::all();

        foreach ($tenants as $keyTenant => $tenant) {
            $inactiveSubscriptions = rand(1, 3);

            for ($i = 0; $i < $inactiveSubscriptions; $i++) {
                $start = Carbon::now()->subMonth(rand(6, 24));
                $end = (clone $start)->addMonth(1);

                $plan = $plans->random();

                $tenant->subscriptions()->create([
                    'plan_id' => $plan->id,
                    'date_assign' => $start,
                    'date_expired' => $end,
                    'status' => Subscription::STATUS_INACTIVE,
                    'remaining_users' => 0
                ]);
            }

            $plan = $plans->random();

            $tenant->subscriptions()->create([
                'plan_id' => $plan->id,
                'date_assign' => now(),
                'date_expired' => null,
                'status' => Subscription::STATUS_ACTIVE,
                'remaining_users' => rand(1, $plan->users_limit - 1)
            ]);
        }
    }
}
