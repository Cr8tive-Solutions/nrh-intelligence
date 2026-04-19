<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Transaction> */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => fake()->randomElement(['topup', 'payment', 'adjustment']),
            'amount' => fake()->randomFloat(2, 100, 3000),
            'reference' => fake()->optional(0.7)->bothify('TXN-####-????'),
            'status' => fake()->randomElement(['completed', 'completed', 'completed', 'pending']),
            'method' => fake()->randomElement(['Bank Transfer', 'Credit Card', 'Cheque', 'Online Transfer']),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
