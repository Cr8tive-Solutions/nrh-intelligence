<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = collect([
            ['id' => 1, 'date' => '2026-03-31', 'reference' => 'TXN-2026-031',  'description' => 'Monthly billing — March 2026',   'amount' => 1250.00, 'method' => 'Monthly Billing',  'recorded_by' => 'Admin'],
            ['id' => 2, 'date' => '2026-02-28', 'reference' => 'TXN-2026-028',  'description' => 'Monthly billing — February 2026', 'amount' => 980.00,  'method' => 'Monthly Billing',  'recorded_by' => 'Admin'],
            ['id' => 3, 'date' => '2026-02-10', 'reference' => 'TXN-2026-010',  'description' => 'Direct bank transfer',             'amount' => 500.00,  'method' => 'Cash Transfer',    'recorded_by' => 'Admin'],
            ['id' => 4, 'date' => '2026-01-31', 'reference' => 'TXN-2026-005',  'description' => 'Monthly billing — January 2026',   'amount' => 760.00,  'method' => 'Monthly Billing',  'recorded_by' => 'Admin'],
        ]);

        return view('client.billing.transactions', compact('transactions'));
    }

    public function receipt(int $id)
    {
        return view('client.coming-soon');
    }
}
