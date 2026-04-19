<?php

namespace Database\Factories;

use App\Models\IdentityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<IdentityType> */
class IdentityTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['NRIC', 'Passport', 'Army / Police ID', 'MyPR']),
        ];
    }
}
