<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerScopePrice;
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

        // Fetched once and passed in, so the per-package total costs no extra query.
        // A null total means the package holds a price-on-request scope with no
        // agreed price — the submission guard in CreateRequestController rejects it.
        $overrides = CustomerScopePrice::where('customer_id', $customerId)
            ->pluck('price', 'scope_type_id');

        $totals = $packages->mapWithKeys(
            fn (Package $package) => [$package->id => $package->effectiveTotal($overrides)]
        );

        return view('client.settings.packages.index', compact('packages', 'totals'));
    }
}
