<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ScreeningRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);
        $customer = Customer::with('agreement')->findOrFail($customerId);

        $requests = ScreeningRequest::where('customer_id', $customerId)->get();

        $stats = [
            'in_progress' => $requests->whereIn('status', ['new', 'in_progress'])->count(),
            'cleared' => $requests->where('status', 'complete')->count(),
            'needs_review' => $requests->where('status', 'flagged')->count(),
            'total' => $requests->count(),
        ];

        $recentRequests = ScreeningRequest::where('customer_id', $customerId)
            ->withCount('candidates')
            ->latest()
            ->limit(8)
            ->get();

        return view('client.dashboard.index', [
            'userName' => session('client_user_name', 'User'),
            'companyName' => $customer->name,
            'stats' => $stats,
            'agreementExpiry' => $customer->agreement?->expiry_date?->format('d M Y') ?? '—',
            'agreementDaysLeft' => $customer->agreement?->days_left ?? 0,
            'recentRequests' => $recentRequests,
        ]);
    }
}
