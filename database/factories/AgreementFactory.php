<?php

namespace Database\Factories;

use App\Models\Agreement;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Agreement> */
class AgreementFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->subMonths(6)->startOfMonth();

        return [
            'customer_id' => Customer::factory(),
            'type' => 'Annual Service Agreement',
            'start_date' => $start,
            'expiry_date' => $start->copy()->addYear(),
            'sla_tat' => '5 Business Days',
            'billing' => 'Monthly',
            'payment' => 'Bank Transfer',
            'terms' => [
                'Minimum 10 checks per month',
                'Turnaround time as per agreed SLA',
                'Reports delivered via secure portal',
                'Data handled in compliance with PDPA 2010',
                'Invoiced at end of each calendar month',
            ],
        ];
    }
}
