<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Country> */
class CountryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'code' => strtoupper(fake()->unique()->lexify('??')),
            'flag' => '🏳',
            'region' => fake()->randomElement(['Southeast Asia', 'East Asia', 'South Asia']),
        ];
    }
}
