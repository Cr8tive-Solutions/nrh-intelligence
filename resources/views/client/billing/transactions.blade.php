<x-client.layouts.app pageTitle="Transactions">

    <div class="page-head">
        <div>
            <h1>
                <em style="font-style:italic;color:var(--emerald-700);">Payment</em> Transactions
            </h1>
            <div class="sub">Payment history recorded by admin</div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h3>All Transactions</h3>
            @if(count($transactions) > 0)
                <span style="font-family:var(--font-mono);font-size:11px;color:var(--ink-500);letter-spacing:0.08em;">
                    TOTAL · <b style="color:var(--ink-900);">MYR {{ number_format($transactions->sum('amount'), 2) }}</b>
                </span>
            @endif
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:140px;">Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th style="width:140px;">Method</th>
                        <th style="width:120px;text-align:right;">Amount</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $txn)
                        <tr>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $txn->created_at->format('d M Y') }}</td>
                            <td>
                                <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $txn->reference ?? '—' }}</span>
                            </td>
                            <td style="color:var(--ink-700);">{{ $txn->notes ?? ucfirst($txn->type) }}</td>
                            <td>
                                <span class="pill {{ $txn->method === 'Monthly Billing' ? 'pill-progress' : 'pill-pending' }}">
                                    <span class="dot"></span>
                                    {{ $txn->method }}
                                </span>
                            </td>
                            <td style="text-align:right;font-weight:600;font-family:var(--font-mono);font-size:13px;color:var(--ink-900);">
                                MYR {{ number_format($txn->amount, 2) }}
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('client.billing.transactions.receipt', $txn->id) }}"
                                   class="btn btn-ghost" style="padding:5px 12px;font-size:12px;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:60px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No transactions recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client.layouts.app>
