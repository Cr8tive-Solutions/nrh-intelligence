<?php

namespace Database\Factories;

use App\Models\IdentityType;
use App\Models\RequestCandidate;
use App\Models\ScreeningRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RequestCandidate> */
class RequestCandidateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'screening_request_id' => ScreeningRequest::factory(),
            'identity_type_id' => IdentityType::factory(),
            'name' => fake()->name(),
            'identity_number' => fake()->numerify('######-##-####'),
            'mobile' => fake()->optional(0.8)->numerify('+60 1# ### ####'),
            'remarks' => fake()->optional(0.3)->sentence(),
            'status' => fake()->randomElement(['new', 'in_progress', 'complete']),
        ];
    }
}
