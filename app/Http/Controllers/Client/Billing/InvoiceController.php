<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $invoices = Invoice::where('customer_id', $customerId)
            ->latest('issued_at')
            ->get();

        return view('client.billing.invoices', compact('invoices'));
    }

    public function show(int $id)
    {
        $customerId = session('client_customer_id', 1);

        $invoice = Invoice::with(['items', 'customer'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.billing.invoice-show', compact('invoice'));
    }

    public function download(int $id)
    {
        return view('client.coming-soon');
    }
}
