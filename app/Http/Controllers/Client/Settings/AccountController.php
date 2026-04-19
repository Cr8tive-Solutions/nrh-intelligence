<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $customer = Customer::findOrFail(session('client_customer_id', 1));

        return view('client.settings.account', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail(session('client_customer_id', 1));

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:100'],
            'industry' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $customer->update([
            'name' => $validated['company_name'],
            'registration_no' => $validated['registration_no'],
            'address' => $validated['address'],
            'country' => $validated['country'],
            'industry' => $validated['industry'],
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
        ]);

        return back()->with('success', 'Account details updated.');
    }
}
