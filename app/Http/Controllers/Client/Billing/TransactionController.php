<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $transactions = Transaction::where('customer_id', $customerId)
            ->latest()
            ->get();

        return view('client.billing.transactions', compact('transactions'));
    }

    public function receipt(int $id)
    {
        $customerId = session('client_customer_id', 1);

        $transaction = Transaction::with('customer')
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.billing.transaction-receipt', compact('transaction'));
    }
}
