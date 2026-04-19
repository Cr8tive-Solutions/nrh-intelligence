<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\ScreeningRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScreeningRequest> */
class ScreeningRequestFactory extends Factory
{
    public function definition(): array
    {
        static $seq = 1;

        return [
            'customer_id' => Customer::factory(),
            'customer_user_id' => CustomerUser::factory(),
            'reference' => 'REQ-'.now()->format('Y').'-'.str_pad($seq++, 4, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(['new', 'in_progress', 'in_progress', 'complete', 'flagged']),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => fake()->randomElement(['new', 'in_progress'])]);
    }

    public function complete(): static
    {
        return $this->state(['status' => 'complete']);
    }
}
