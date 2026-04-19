<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = Customer::with('agreement', 'screeningRequests')->findOrFail(session('client_customer_id', 1));

        $requests = $customer->screeningRequests;

        $stats = [
            'new' => $requests->where('status', 'new')->count(),
            'pending' => $requests->whereIn('status', ['new', 'in_progress'])->count(),
            'complete' => $requests->where('status', 'complete')->count(),
            'total' => $requests->count(),
        ];

        $recentRequests = $customer->screeningRequests()
            ->withCount('candidates')
            ->latest()
            ->limit(5)
            ->get();

        return view('client.dashboard.index', [
            'stats' => $stats,
            'balance' => $customer->balance,
            'lastTopup' => $customer->transactions()
                ->where('type', 'topup')
                ->latest()
                ->value('created_at')
                ?->format('d M Y') ?? '—',
            'agreementExpiry' => $customer->agreement?->expiry_date->format('d M Y') ?? '—',
            'agreementDaysLeft' => $customer->agreement?->days_left ?? 0,
            'recentRequests' => $recentRequests,
        ]);
    }
}
