<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\ScopeType;

class PackageController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $packages = Package::with(['country', 'scopeTypes'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        $allScopes = ScopeType::with('country')->get();

        return view('client.settings.packages.index', compact('packages', 'allScopes'));
    }
}
