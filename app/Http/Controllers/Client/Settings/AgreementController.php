<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Agreement;

class AgreementController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $agreement = Agreement::where('customer_id', $customerId)->firstOrFail();

        return view('client.settings.agreement', compact('agreement'));
    }
}
