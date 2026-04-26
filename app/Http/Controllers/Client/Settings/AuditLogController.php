<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $customerId = (int) Auth::guard('customer_user')->user()?->customer_id;

        $teamUserIds = CustomerUser::where('customer_id', $customerId)->pluck('id');

        $query = Activity::query()
            ->with('causer', 'subject')
            ->where(function ($q) use ($teamUserIds) {
                $q->where('causer_type', CustomerUser::class)
                    ->whereIn('causer_id', $teamUserIds);
            })
            ->latest();

        if ($request->filled('event')) {
            $query->where('event', $request->string('event'));
        }

        if ($request->filled('log')) {
            $query->where('log_name', $request->string('log'));
        }

        if ($request->filled('user')) {
            $query->where('causer_id', (int) $request->input('user'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $activities = $query->paginate(25)->withQueryString();

        $teamUsers = CustomerUser::where('customer_id', $customerId)->orderBy('name')->get(['id', 'name']);
        $events = Activity::query()
            ->where('causer_type', CustomerUser::class)
            ->whereIn('causer_id', $teamUserIds)
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('client.settings.audit-log.index', compact('activities', 'teamUsers', 'events'));
    }
}
