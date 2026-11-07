<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\View\View;

class AgreementController extends Controller
{
    /**
     * Show the customer's current agreement, or an empty state when none is on file.
     */
    public function index(): View
    {
        $customer = Customer::findOrFail(session('client_customer_id'));

        return view('client.settings.agreement', [
            'agreement' => $customer->currentAgreement(),
        ]);
    }
}
