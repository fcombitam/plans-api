<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected static ?string $password;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = fake()->company();
        $email = Str::slug(fake()->name())."@".Str::slug($company).".com";
        return [
            'name' => $company,
            'email' => $email,
            'phone_number' => fake()->phoneNumber(),
            'tenant_code' => fake()->bothify("##??##??##??"),
            'password' => static::$password ??= Hash::make('password')
        ];
    }
}
