<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Invoice> */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $issued = fake()->dateTimeBetween('-6 months', '-1 month');
        $subtotal = fake()->randomFloat(2, 400, 2000);
        $tax = round($subtotal * 0.06, 2);

        return [
            'customer_id' => Customer::factory(),
            'number' => 'INV-'.now()->format('Y').'-'.fake()->unique()->numerify('###'),
            'period' => Carbon::instance($issued)->format('F Y'),
            'status' => fake()->randomElement(['paid', 'paid', 'paid', 'unpaid']),
            'issued_at' => $issued,
            'due_at' => Carbon::instance($issued)->addDays(30),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }
}
