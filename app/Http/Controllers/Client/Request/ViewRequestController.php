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
        $awaitingPaymentCount = $isCashBilled
            ? $requests->where('status', 'new')->where('payment_slip_path', null)->count()
            : 0;

        return view('client.requests.index', compact('requests', 'isCashBilled', 'awaitingPaymentCount'));
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
