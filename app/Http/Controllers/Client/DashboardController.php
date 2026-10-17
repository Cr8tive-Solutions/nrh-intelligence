<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Package;
use App\Models\ScreeningRequest;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id');
        $customer = Customer::with('agreement')->findOrFail($customerId);

        $requests = ScreeningRequest::where('customer_id', $customerId)->get();

        $isCashBilled = $customer->isCashBilled();

        $stats = [
            'in_progress' => $requests->whereIn('status', ['new', 'in_progress'])->count(),
            'cleared' => $requests->where('status', 'complete')->count(),
            'needs_review' => ScreeningRequest::where('customer_id', $customerId)
                ->whereHas('candidates', fn ($q) => $q->where('status', 'flagged'))
                ->count(),
            'total' => $requests->count(),
            'awaiting_payment' => $isCashBilled
                ? $requests->where('status', 'new')->whereNull('payment_slip_path')->count()
                : 0,
        ];

        $recentRequests = ScreeningRequest::where('customer_id', $customerId)
            ->withCount('candidates')
            ->with('package')
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
            'isCashBilled' => $isCashBilled,
            'packageUsage' => $this->packageUsage($customerId, $stats['total']),
        ]);
    }

    /**
     * How this customer's requests break down by package, largest share first,
     * with ad-hoc selections grouped last. Only meaningful since `package_id`
     * was added — older requests have no attribution and fall into "Custom".
     *
     * @return list<array{name: string, scopes: string, count: int, pct: int}>
     */
    private function packageUsage(?int $customerId, int $totalRequests): array
    {
        if ($totalRequests === 0) {
            return [];
        }

        $counts = ScreeningRequest::where('customer_id', $customerId)
            ->selectRaw('package_id, count(*) as total')
            ->groupBy('package_id')
            ->pluck('total', 'package_id');

        $packages = Package::with('scopeTypes')
            ->whereIn('id', $counts->keys()->filter())
            ->get()
            ->keyBy('id');

        $rows = [];

        foreach ($counts as $packageId => $count) {
            $package = $packageId ? $packages->get($packageId) : null;

            $rows[] = [
                'name' => $package?->name ?? 'Custom selection',
                'scopes' => $package
                    ? $package->scopeTypes->count().' '.Str::plural('scope', $package->scopeTypes->count())
                    : 'scopes picked individually',
                'count' => (int) $count,
                'pct' => (int) round(($count / $totalRequests) * 100),
                'is_package' => $package !== null,
            ];
        }

        // Largest share first, but keep the un-attributed bucket at the bottom.
        usort($rows, fn ($a, $b) => [$b['is_package'], $b['count']] <=> [$a['is_package'], $a['count']]);

        return $rows;
    }
}
