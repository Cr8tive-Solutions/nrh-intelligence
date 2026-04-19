<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\ScreeningRequest;

class OldRequestController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $requests = ScreeningRequest::with('candidates')
            ->where('customer_id', $customerId)
            ->complete()
            ->withCount('candidates')
            ->latest()
            ->get();

        return view('client.history.index', compact('requests'));
    }

    public function details(int $id)
    {
        $customerId = session('client_customer_id', 1);

        $request = ScreeningRequest::with([
            'candidates.identityType',
            'candidates.scopeTypes',
            'submittedBy',
        ])
            ->where('customer_id', $customerId)
            ->complete()
            ->findOrFail($id);

        return view('client.history.details', compact('request'));
    }
}
