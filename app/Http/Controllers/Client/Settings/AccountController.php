<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $account = [
            'company_name' => 'NRH Intelligence Sdn. Bhd.',
            'registration_no' => '202001234567',
            'address' => 'Level 12, Menara NRH, No. 1 Jalan Ampang, 50450 Kuala Lumpur',
            'country' => 'Malaysia',
            'industry' => 'Financial Services',
            'contact_name' => 'Demo User',
            'contact_email' => 'demo@nrh-intelligence.com',
            'contact_phone' => '+60 12 345 6789',
        ];

        return view('client.settings.account', compact('account'));
    }
}
