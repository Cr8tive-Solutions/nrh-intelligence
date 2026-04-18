<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('client.dashboard.index', [
            'stats' => ['new' => 3, 'pending' => 7, 'complete' => 24, 'total' => 34],
            'balance' => 1250.00,
            'lastTopup' => '14 Apr 2026',
            'agreementExpiry' => '31 Dec 2026',
            'agreementDaysLeft' => 257,
            'recentRequests' => collect(),
        ]);
    }
}
