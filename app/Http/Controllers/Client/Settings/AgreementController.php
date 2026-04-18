<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function index()
    {
        $agreement = [
            'type'        => 'Corporate Service Agreement',
            'start_date'  => '2026-01-01',
            'expiry_date' => '2026-12-31',
            'days_left'   => 256,
            'sla_tat'     => '5-7 business days',
            'billing'     => 'Monthly',
            'payment'     => 'Direct Bank Transfer',
            'terms' => [
                'Background checks are conducted in accordance with applicable laws.',
                'Results are confidential and for authorised use only.',
                'Turnaround times are estimates and may vary by country.',
                'Monthly invoices are issued on the last business day of each month.',
                'Payment is due within 30 days of invoice date.',
            ],
        ];

        return view('client.settings.agreement', compact('agreement'));
    }
}
