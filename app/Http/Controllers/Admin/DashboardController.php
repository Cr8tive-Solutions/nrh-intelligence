<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ScreeningRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_requests' => ScreeningRequest::whereIn('status', ['new', 'in_progress'])->count(),
            'flagged_requests' => ScreeningRequest::where('status', 'flagged')->count(),
            'completed_today' => ScreeningRequest::where('status', 'complete')
                ->whereDate('updated_at', today())->count(),
            'unpaid_invoices' => Invoice::whereIn('status', ['unpaid', 'overdue'])->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
        ];

        $recentRequests = ScreeningRequest::with(['customer', 'candidates'])
            ->withCount('candidates')
            ->latest()
            ->limit(10)
            ->get();

        $pendingRequests = ScreeningRequest::with('customer')
            ->where('status', 'new')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'pendingRequests'));
    }
}
