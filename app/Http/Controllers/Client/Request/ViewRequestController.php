<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ScreeningRequest;

class ViewRequestController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $customer = Customer::with('agreement')->findOrFail($customerId);

        $requests = ScreeningRequest::with('candidates')
            ->where('customer_id', $customerId)
            ->active()
            ->withCount('candidates')
            ->latest()
            ->get();

        $isCashBilled = $customer->isCashBilled();

        // Banner: only "needs upload" — that's the call-to-action.
        // Tab: "needs upload" + "uploaded but not yet verified" — full pre-processing pipeline.
        $awaitingPaymentCount = $isCashBilled
            ? $requests->where('status', 'new')->whereNull('payment_slip_path')->count()
            : 0;
        $paymentTabCount = $isCashBilled
            ? $requests->where('status', 'new')->whereNull('payment_verified_at')->count()
            : 0;

        return view('client.requests.index', compact('requests', 'isCashBilled', 'awaitingPaymentCount', 'paymentTabCount'));
    }

    public function details(int $id)
    {
        $customerId = session('client_customer_id', 1);

        $request = ScreeningRequest::with([
            'candidates.identityType',
            'candidates.scopeTypes',
            'submittedBy',
            'currentReportVersions',
            'customer.agreement',
        ])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.requests.details', compact('request'));
    }
}
