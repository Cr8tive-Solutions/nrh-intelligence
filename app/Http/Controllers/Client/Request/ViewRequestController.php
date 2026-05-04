<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\ScreeningRequest;

class ViewRequestController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $requests = ScreeningRequest::with('candidates')
            ->where('customer_id', $customerId)
            ->active()
            ->withCount('candidates')
            ->latest()
            ->get();

        return view('client.requests.index', compact('requests'));
    }

    public function details(int $id)
    {
        $customerId = session('client_customer_id', 1);

        $request = ScreeningRequest::with([
            'candidates.identityType',
            'candidates.scopeTypes',
            'submittedBy',
            'currentReportVersions',
        ])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('client.requests.details', compact('request'));
    }
}
