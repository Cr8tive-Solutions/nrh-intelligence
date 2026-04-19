<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InvoiceItem> */
class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomElement([45.00, 50.00, 60.00, 70.00, 80.00]);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->randomElement([
                'Criminal Record Check', 'Employment Verification',
                'Education Verification', 'Credit Check', 'Reference Check',
            ]).' ('.$qty.' '.($qty === 1 ? 'candidate' : 'candidates').')',
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total' => $qty * $unitPrice,
        ];
    }
}
