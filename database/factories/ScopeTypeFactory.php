<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\ScopeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScopeType> */
class ScopeTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'name' => fake()->randomElement([
                'Criminal Record Check', 'Employment Verification', 'Education Verification',
                'Credit Check', 'Reference Check', 'Social Media Screening',
                'Bankruptcy Search', 'Directorship Search',
            ]),
            'turnaround' => fake()->randomElement(['1-2 days', '2-3 days', '3-5 days', '5-7 days', '7-10 days']),
            'price' => fake()->randomElement([40.00, 45.00, 50.00, 60.00, 70.00, 80.00, 90.00, 110.00]),
            'description' => fake()->sentence(),
        ];
    }
}
