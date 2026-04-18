<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = collect([
            ['id' => 1, 'number' => 'INV-2026-003', 'period' => 'March 2026',    'amount' => 1250.00, 'status' => 'Paid',   'issued_at' => '2026-03-31', 'due_at' => '2026-04-30'],
            ['id' => 2, 'number' => 'INV-2026-002', 'period' => 'February 2026', 'amount' => 980.00,  'status' => 'Paid',   'issued_at' => '2026-02-28', 'due_at' => '2026-03-30'],
            ['id' => 3, 'number' => 'INV-2026-001', 'period' => 'January 2026',  'amount' => 760.00,  'status' => 'Paid',   'issued_at' => '2026-01-31', 'due_at' => '2026-03-02'],
        ]);

        return view('client.billing.invoices', compact('invoices'));
    }

    public function show(int $id)
    {
        $invoice = [
            'id'        => $id,
            'number'    => 'INV-2026-00' . $id,
            'period'    => 'March 2026',
            'issued_at' => '2026-03-31',
            'due_at'    => '2026-04-30',
            'status'    => 'Paid',
            'company'   => 'NRH Intelligence Sdn. Bhd.',
            'items'     => [
                ['description' => 'REQ-2026-0101 — Criminal Record Check (3 candidates)',    'qty' => 3, 'unit_price' => 50.00,  'total' => 150.00],
                ['description' => 'REQ-2026-0101 — Employment Verification (3 candidates)', 'qty' => 3, 'unit_price' => 80.00,  'total' => 240.00],
                ['description' => 'REQ-2026-0098 — Criminal Record Check (2 candidates)',    'qty' => 2, 'unit_price' => 50.00,  'total' => 100.00],
                ['description' => 'REQ-2026-0095 — Education Verification (5 candidates)',  'qty' => 5, 'unit_price' => 60.00,  'total' => 300.00],
                ['description' => 'REQ-2026-0093 — Credit Check (3 candidates)',            'qty' => 3, 'unit_price' => 45.00,  'total' => 135.00],
            ],
            'subtotal'  => 925.00,
            'tax'       => 55.50,
            'total'     => 980.50,
        ];

        return view('client.billing.invoice-show', compact('invoice'));
    }

    public function download(int $id)
    {
        return view('client.coming-soon');
    }
}
