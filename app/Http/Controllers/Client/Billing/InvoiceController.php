<?php

namespace App\Http\Controllers\Client\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePaymentReceipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function show(string $id)
    {
        $id = hdecode($id);
        $customerId = session('client_customer_id', 1);

        $invoice = Invoice::with(['items', 'customer', 'receipts'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.billing.invoice-show', compact('invoice'));
    }

    public function uploadReceipt(Request $request, string $id): RedirectResponse
    {
        $id = hdecode($id);
        $customerId = session('client_customer_id', 1);

        $invoice = Invoice::where('customer_id', $customerId)->findOrFail($id);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already marked as paid.');
        }

        $validated = $request->validate([
            'receipt_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'amount_claimed' => ['nullable', 'numeric', 'min:0.01'],
            'paid_on' => ['nullable', 'date', 'before_or_equal:today'],
            'reference' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $validated['receipt_file'];
        $path = $file->store("receipts/{$customerId}/{$invoice->id}", 'local');

        $userId = session('client_user_id');

        InvoicePaymentReceipt::create([
            'invoice_id' => $invoice->id,
            'uploaded_by_customer_user_id' => $userId,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'amount_claimed' => $validated['amount_claimed'] ?? null,
            'paid_on' => $validated['paid_on'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Payment receipt uploaded. Our finance team will verify it shortly.');
    }

    public function download(string $id)
    {
        $id = hdecode($id);
        $customerId = session('client_customer_id', 1);

        $invoice = Invoice::with(['items', 'customer'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        $logoPath = public_path('nrh-logo.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : '';

        $bank = config('billing.bank');

        $statusBadge = match ($invoice->status) {
            'paid' => 'badge-paid',
            'overdue' => 'badge-overdue',
            default => 'badge-unpaid',
        };

        $pdf = Pdf::loadView('client.billing.invoice-pdf', compact('invoice', 'logoSrc', 'bank', 'statusBadge'))
            ->setPaper('a4', 'portrait');

        $filename = $invoice->number.'.pdf';

        return $pdf->download($filename);
    }
}
