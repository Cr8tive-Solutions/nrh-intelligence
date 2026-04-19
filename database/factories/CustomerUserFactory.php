<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CustomerUser> */
class CustomerUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => fake()->randomElement(['admin', 'user']),
            'status' => 'active',
        ];
    }
}
