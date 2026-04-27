<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Invoice;
use App\Models\ScreeningRequest;
use App\Models\Transaction;

class NotificationController extends Controller
{
    public function index()
    {
        $customerId = session('client_customer_id', 1);

        $notifications = collect();

        // Agreement expiry warning
        $agreement = Agreement::where('customer_id', $customerId)->first();
        if ($agreement && $agreement->days_left <= 60 && $agreement->days_left >= 0) {
            $notifications->push([
                'type' => $agreement->days_left <= 14 ? 'danger' : 'warning',
                'icon' => 'shield',
                'title' => $agreement->days_left === 0 ? 'Agreement has expired' : 'Agreement expiring in '.$agreement->days_left.' days',
                'body' => 'Your service agreement expires on '.$agreement->expiry_date->format('d M Y').'. Please contact your account manager to renew.',
                'time' => null,
                'read' => false,
            ]);
        }

        // Unpaid / overdue invoices
        Invoice::where('customer_id', $customerId)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->latest('issued_at')
            ->get()
            ->each(function ($inv) use (&$notifications) {
                $notifications->push([
                    'type' => $inv->status === 'overdue' ? 'danger' : 'warning',
                    'icon' => 'invoice',
                    'title' => 'Invoice '.$inv->number.' is '.$inv->status,
                    'body' => 'Amount due: MYR '.number_format($inv->total, 2).'. Due date: '.$inv->due_at->format('d M Y').'.',
                    'time' => $inv->issued_at,
                    'read' => false,
                ]);
            });

        // Recent completed requests
        ScreeningRequest::where('customer_id', $customerId)
            ->complete()
            ->latest('updated_at')
            ->limit(3)
            ->get()
            ->each(function ($req) use (&$notifications) {
                $notifications->push([
                    'type' => 'success',
                    'icon' => 'check',
                    'title' => 'Request '.$req->reference.' completed',
                    'body' => 'All verifications have been finalised. View the report for details.',
                    'time' => $req->updated_at,
                    'read' => true,
                    'link' => route('client.history.details', $req->id),
                ]);
            });

        // Recent transactions
        Transaction::where('customer_id', $customerId)
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($txn) use (&$notifications) {
                $notifications->push([
                    'type' => 'info',
                    'icon' => 'transaction',
                    'title' => ucfirst($txn->type).' of MYR '.number_format($txn->amount, 2).' recorded',
                    'body' => $txn->reference ? 'Reference: '.$txn->reference.'.' : 'Processed via '.$txn->method.'.',
                    'time' => $txn->created_at,
                    'read' => true,
                    'link' => route('client.billing.transactions.receipt', $txn->id),
                ]);
            });

        // New / in-progress requests
        ScreeningRequest::where('customer_id', $customerId)
            ->active()
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($req) use (&$notifications) {
                $notifications->push([
                    'type' => 'info',
                    'icon' => 'request',
                    'title' => 'Request '.$req->reference.' is '.str_replace('_', ' ', $req->status),
                    'body' => 'Your request is being processed by our verification team.',
                    'time' => $req->created_at,
                    'read' => true,
                    'link' => route('client.requests.details', $req->id),
                ]);
            });

        $notifications = $notifications->sortByDesc(fn ($n) => $n['time'])->values();
        $unreadCount = $notifications->where('read', false)->count();

        return view('client.notifications.index', compact('notifications', 'unreadCount'));
    }
}
