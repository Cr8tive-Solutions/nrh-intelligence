<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['screeningRequests', 'users'])
            ->with('agreement');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('registration_no', 'ilike', "%{$search}%")
                    ->orWhere('contact_email', 'ilike', "%{$search}%");
            });
        }

        $customers = $query->latest()->get();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function show(int $id)
    {
        $customer = Customer::with([
            'agreement',
            'users',
            'screeningRequests' => fn ($q) => $q->withCount('candidates')->latest()->limit(10),
            'invoices' => fn ($q) => $q->latest('issued_at')->limit(5),
            'transactions' => fn ($q) => $q->latest()->limit(5),
            'scopePrices.scopeType',
        ])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }
}
