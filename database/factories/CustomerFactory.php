<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = fake()->company();

        return [
            'name' => $company.' Sdn. Bhd.',
            'registration_no' => fake()->numerify('######-##-#####'),
            'address' => fake()->streetAddress().', '.fake()->city().', Malaysia',
            'country' => 'Malaysia',
            'industry' => fake()->randomElement(['Financial Services', 'Technology', 'Healthcare', 'Manufacturing', 'Retail', 'Logistics']),
            'contact_name' => fake()->name(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => '+60 '.fake()->numerify('1# ### ####'),
            'balance' => fake()->randomFloat(2, 500, 5000),
        ];
    }
}
