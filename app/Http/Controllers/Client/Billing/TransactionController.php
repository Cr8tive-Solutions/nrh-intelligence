<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function receipt(string $id)
    {
        $id = hdecode($id);
        $customerId = session('client_customer_id', 1);

        $transaction = Transaction::with('customer')
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.billing.transaction-receipt', compact('transaction'));
    }

    public function receiptPdf(string $id)
    {
        $id = hdecode($id);
        $customerId = session('client_customer_id', 1);

        $transaction = Transaction::with('customer')
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        $logoPath = public_path('nrh-logo.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : '';

        $pdf = Pdf::loadView('client.billing.receipt-pdf', compact('transaction', 'logoSrc'))
            ->setPaper('a5', 'portrait');

        $filename = 'receipt-'.($transaction->reference ?? ('TXN-'.$transaction->id)).'.pdf';

        return $pdf->download($filename);
    }
}
