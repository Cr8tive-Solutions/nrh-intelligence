<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Package;

/**
 * Read-only view of the customer's scope packages.
 *
 * Packages are authored by NRH in the admin portal — this portal never writes
 * to `packages` or `package_scope_type`. The request wizard reads the same rows
 * to offer one-click scope selection.
 */
class PackageController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id');

        $packages = Package::with(['country', 'scopeTypes'])
            ->where('customer_id', $customerId)
            ->orderBy('country_id')
            ->orderBy('name')
            ->get();

        // A package is sold at its fixed bundle price (per candidate). A 0/blank
        // price means it has not been priced yet — submission is blocked until
        // the account manager sets one.
        $totals = $packages->mapWithKeys(
            fn (Package $package) => [$package->id => (float) $package->price]
        );

        return view('client.settings.packages.index', compact('packages', 'totals'));
    }
}
