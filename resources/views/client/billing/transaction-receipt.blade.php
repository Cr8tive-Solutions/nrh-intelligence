<x-client.layouts.app pageTitle="Receipt {{ $transaction->reference ?? $transaction->id }}">

    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('client.billing.transactions') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Transactions</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $transaction->reference ?? 'TXN-' . $transaction->id }}</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            {{-- action buttons if any --}}
        </div>
    </div>

    <div style="max-width:600px;">
        <div class="card" style="overflow:hidden;">

            {{-- Header --}}
            <div style="padding:28px 32px;border-bottom:1px solid var(--line);display:flex;align-items:flex-start;justify-content:space-between;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:32px;width:auto;">
                        <span style="font-family:var(--font-display);font-size:15px;font-weight:600;color:var(--ink-900);">NRH INTELLIGENCE</span>
                    </div>
                    <p style="font-size:11px;color:var(--ink-400);margin:0;">Payment Receipt</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-family:var(--font-mono);font-size:20px;font-weight:700;color:var(--ink-900);margin:0;">{{ $transaction->reference ?? '—' }}</p>
                    <p style="font-size:12px;color:var(--ink-500);margin:4px 0 8px;">{{ $transaction->created_at->format('d M Y') }}</p>
                    @php
                        $statusPill = $transaction->status === 'completed' ? 'pill-clear' : ($transaction->status === 'pending' ? 'pill-review' : 'pill-flagged');
                    @endphp
                    <span class="pill {{ $statusPill }}"><span class="dot"></span>{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>

            {{-- Amount spotlight --}}
            <div style="padding:28px 32px;background:var(--emerald-50);border-bottom:1px solid var(--line);text-align:center;">
                <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.14em;color:var(--emerald-700);margin:0 0 8px;">
                    {{ $transaction->type === 'topup' ? 'Amount Credited' : 'Amount Paid' }}
                </p>
                <p style="font-family:var(--font-display);font-size:40px;font-weight:500;letter-spacing:-0.02em;color:var(--ink-900);margin:0;">
                    MYR <span style="color:var(--emerald-700);">{{ number_format($transaction->amount, 2) }}</span>
                </p>
            </div>

            {{-- Details grid --}}
            <div style="padding:24px 32px;display:grid;grid-template-columns:1fr 1fr;gap:20px;border-bottom:1px solid var(--line);">
                @foreach ([
                    ['Transaction Type', ucfirst($transaction->type)],
                    ['Payment Method',   $transaction->method],
                    ['Reference',        $transaction->reference ?? '—'],
                    ['Date & Time',      $transaction->created_at->format('d M Y, H:i')],
                    ['Account',          $transaction->customer->name],
                    ['Status',           ucfirst($transaction->status)],
                ] as [$label, $value])
                    <div>
                        <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin:0 0 4px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;font-family:{{ in_array($label, ['Reference','Date & Time']) ? 'var(--font-mono)' : 'var(--font-ui)' }};font-size:{{ $label === 'Reference' ? '12px' : '13px' }};">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Notes --}}
            @if ($transaction->notes)
                <div style="padding:16px 32px;border-bottom:1px solid var(--line);background:var(--paper);">
                    <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin:0 0 4px;">Notes</p>
                    <p style="font-size:13px;color:var(--ink-700);margin:0;">{{ $transaction->notes }}</p>
                </div>
            @endif

            {{-- Footer --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 32px;">
                <p style="font-size:11px;color:var(--ink-400);margin:0;">Keep this receipt for your records.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('client.billing.transactions.receipt.pdf', hid($transaction->id)) }}" class="btn btn-primary" style="font-size:12px;">
                        <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Download PDF
                    </a>
                    <a href="{{ route('client.billing.transactions') }}" class="btn btn-ghost" style="font-size:12px;">
                        Back to Transactions
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-client.layouts.app>
