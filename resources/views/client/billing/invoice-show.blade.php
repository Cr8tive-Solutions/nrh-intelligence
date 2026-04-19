<x-client.layouts.app pageTitle="Invoice {{ $invoice->number }}">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-500);margin-bottom:24px;">
        <a href="{{ route('client.billing.invoices') }}" style="color:var(--ink-500);text-decoration:none;" onmouseover="this.style.color='var(--emerald-700)'" onmouseout="this.style.color='var(--ink-500)'">Invoices</a>
        <svg style="width:12px;height:12px;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span style="color:var(--ink-900);font-weight:600;font-family:var(--font-mono);">{{ $invoice->number }}</span>
    </div>

    <div style="max-width:760px;">
        <div class="nrh-card" style="overflow:hidden;">

            {{-- Invoice header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:28px 32px;border-bottom:1px solid var(--line);">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <div style="width:32px;height:32px;border-radius:var(--radius);background:var(--emerald-700);display:flex;align-items:center;justify-content:center;">
                            <svg style="width:16px;height:16px;color:var(--gold-400);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                            </svg>
                        </div>
                        <span style="font-family:var(--font-display);font-size:15px;font-weight:600;color:var(--ink-900);">NRH INTELLIGENCE</span>
                    </div>
                    <p style="font-size:11px;color:var(--ink-400);margin:0;">Background Verification Platform</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-family:var(--font-mono);font-size:22px;font-weight:700;color:var(--ink-900);margin:0;">{{ $invoice->number }}</p>
                    <p style="font-size:12px;color:var(--ink-500);margin:4px 0 8px;">{{ $invoice->period }}</p>
                    <span class="pill {{ $invoice->status === 'paid' ? 'pill-clear' : 'pill-review' }}">
                        <span class="dot"></span>{{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>

            {{-- Meta --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding:20px 32px;border-bottom:1px solid var(--line);background:var(--paper);">
                @foreach ([
                    ['Billed To', $invoice->customer->name],
                    ['Issue Date', $invoice->issued_at->format('d M Y')],
                    ['Due Date',   $invoice->due_at->format('d M Y')],
                ] as [$label, $value])
                    <div>
                        <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);margin:0 0 4px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0;">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Line items --}}
            <div style="padding:20px 32px;">
                <table class="nrh-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width:60px;text-align:center;">Qty</th>
                            <th style="width:120px;text-align:right;">Unit Price</th>
                            <th style="width:120px;text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td style="color:var(--ink-700);">{{ $item->description }}</td>
                                <td style="text-align:center;color:var(--ink-500);font-family:var(--font-mono);font-size:12px;">{{ $item->qty }}</td>
                                <td style="text-align:right;color:var(--ink-500);font-family:var(--font-mono);font-size:12px;">MYR {{ number_format($item->unit_price, 2) }}</td>
                                <td style="text-align:right;font-weight:600;color:var(--ink-900);font-family:var(--font-mono);font-size:12px;">MYR {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div style="padding:16px 32px 20px;border-top:1px solid var(--line);background:var(--paper);">
                <div style="margin-left:auto;max-width:280px;display:flex;flex-direction:column;gap:8px;">
                    @foreach ([
                        ['Subtotal',    number_format($invoice->subtotal, 2), false],
                        ['Tax (6% SST)', number_format($invoice->tax, 2), false],
                    ] as [$lbl, $val, $bold])
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--ink-500);">
                            <span>{{ $lbl }}</span>
                            <span style="font-family:var(--font-mono);">MYR {{ $val }}</span>
                        </div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;color:var(--ink-900);border-top:1px solid var(--line);padding-top:10px;margin-top:4px;">
                        <span>Total Due</span>
                        <span style="font-family:var(--font-mono);">MYR {{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 32px;border-top:1px solid var(--line);">
                <p style="font-size:11px;color:var(--ink-400);margin:0;">Payment via direct bank transfer. Please reference invoice number.</p>
                <a href="{{ route('client.billing.invoices.download', $invoice->id) }}" class="btn-ghost" style="font-size:12px;">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

</x-client.layouts.app>
