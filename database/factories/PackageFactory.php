<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Package> */
class PackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'country_id' => Country::factory(),
            'name' => fake()->randomElement(['Standard Screening', 'Premium Package', 'Finance Sector Pack', 'Executive Check']),
        ];
    }
}
