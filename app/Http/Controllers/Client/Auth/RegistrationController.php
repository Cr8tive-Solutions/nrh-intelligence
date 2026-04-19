<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('client.auth.register');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:customer_users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        $customer = Customer::create([
            'name' => $validated['company_name'],
            'contact_name' => $validated['first_name'].' '.$validated['last_name'],
            'contact_email' => $validated['email'],
            'contact_phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        CustomerUser::create([
            'customer_id' => $customer->id,
            'name' => $validated['first_name'].' '.$validated['last_name'],
            'email' => $validated['email'],
            'password' => Str::random(32),
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        return redirect()->route('client.register.success');
    }

    public function success()
    {
        return view('client.auth.register-success');
    }
}
