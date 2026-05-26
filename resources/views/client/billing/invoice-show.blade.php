<x-client.layouts.app pageTitle="Invoice {{ $invoice->number }}">

    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('client.billing.invoices') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Invoices</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $invoice->number }}</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            {{-- action buttons if any --}}
        </div>
    </div>

    <div style="max-width:760px;">
        <div class="card" style="overflow:hidden;">

            {{-- Invoice header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:28px 32px;border-bottom:1px solid var(--line);">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:32px;width:auto;">
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
                <table class="table">
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
                <a href="{{ route('client.billing.invoices.download', hid($invoice->id)) }}" class="btn btn-ghost" style="font-size:12px;">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>

        {{-- ── Payment Receipts ── --}}
        <div class="card" style="margin-top:16px;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:13px;font-weight:700;color:var(--ink-900);">Payment Receipts</div>
                    <div style="font-size:12px;color:var(--ink-500);margin-top:2px;">Upload your bank transfer confirmation to unblock processing.</div>
                </div>
                @if($invoice->status !== 'paid')
                @can('manage-billing')
                <button type="button"
                        onclick="document.getElementById('receipt-upload-form-{{ $invoice->id }}').style.display='block';this.style.display='none'"
                        class="btn btn-primary" style="font-size:12px;">
                    + Upload Receipt
                </button>
                @endcan
                @endif
            </div>

            {{-- Flash message --}}
            @if(session('status'))
                <div style="padding:12px 24px;background:rgba(5,150,105,0.07);border-bottom:1px solid rgba(5,150,105,0.2);font-size:12px;color:var(--emerald-700);font-weight:500;">
                    {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div style="padding:12px 24px;background:rgba(239,68,68,0.07);border-bottom:1px solid rgba(239,68,68,0.2);font-size:12px;color:#dc2626;font-weight:500;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Upload form (hidden until button click) --}}
            @if($invoice->status !== 'paid')
            @can('manage-billing')
            <div id="receipt-upload-form-{{ $invoice->id }}" style="display:none;padding:20px 24px;border-bottom:1px solid var(--line);background:var(--paper);">
                <form method="POST"
                      action="{{ route('client.billing.invoices.receipts.store', hid($invoice->id)) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Receipt File <span style="color:#dc2626;">*</span></label>
                            <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                                   style="width:100%;padding:6px 10px;border:1px solid var(--line);border-radius:6px;font-size:12px;font-family:inherit;background:var(--card);">
                            @error('receipt_file')<p style="font-size:11px;color:#dc2626;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Amount Paid (MYR)</label>
                            <input type="number" name="amount_claimed" step="0.01" min="0.01"
                                   value="{{ old('amount_claimed') }}"
                                   placeholder="{{ number_format($invoice->total, 2) }}"
                                   style="width:100%;padding:6px 10px;border:1px solid var(--line);border-radius:6px;font-size:12px;font-family:inherit;background:var(--card);box-sizing:border-box;">
                            @error('amount_claimed')<p style="font-size:11px;color:#dc2626;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Payment Date</label>
                            <input type="date" name="paid_on" value="{{ old('paid_on', now()->toDateString()) }}"
                                   style="width:100%;padding:6px 10px;border:1px solid var(--line);border-radius:6px;font-size:12px;font-family:inherit;background:var(--card);box-sizing:border-box;">
                            @error('paid_on')<p style="font-size:11px;color:#dc2626;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Bank Reference No.</label>
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   placeholder="e.g. TT2026051200012"
                                   style="width:100%;padding:6px 10px;border:1px solid var(--line);border-radius:6px;font-size:12px;font-family:inherit;background:var(--card);box-sizing:border-box;">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Notes (optional)</label>
                            <textarea name="notes" rows="2"
                                      style="width:100%;padding:6px 10px;border:1px solid var(--line);border-radius:6px;font-size:12px;font-family:inherit;background:var(--card);resize:vertical;box-sizing:border-box;">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary" style="font-size:12px;">Submit Receipt</button>
                        <button type="button"
                                onclick="this.closest('#receipt-upload-form-{{ $invoice->id }}').style.display='none';document.querySelector('[onclick*=receipt-upload-form-{{ $invoice->id }}]').style.display=''"
                                class="btn btn-ghost" style="font-size:12px;">Cancel</button>
                    </div>
                </form>
            </div>
            @endcan
            @endif

            {{-- Receipt list --}}
            @if($invoice->receipts->isEmpty())
                <div style="padding:32px 24px;text-align:center;">
                    <p style="font-size:12px;color:var(--ink-400);margin:0;">No receipts uploaded yet.</p>
                </div>
            @else
                @foreach($invoice->receipts as $receipt)
                @php
                    $pillClass = match($receipt->status) {
                        'verified' => 'pill-clear',
                        'rejected' => 'pill-flagged',
                        default    => 'pill-review',
                    };
                @endphp
                <div style="display:flex;align-items:center;gap:14px;padding:14px 24px;border-bottom:1px solid var(--line);">
                    <div style="width:34px;height:34px;border-radius:8px;background:var(--paper);border:1px solid var(--line);display:grid;place-items:center;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:var(--ink-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $receipt->file_name }}</div>
                        <div style="font-size:11px;color:var(--ink-400);margin-top:2px;">
                            Uploaded {{ $receipt->created_at->format('d M Y, H:i') }}
                            @if($receipt->amount_claimed) · MYR {{ number_format($receipt->amount_claimed, 2) }} @endif
                            @if($receipt->paid_on) · Paid {{ $receipt->paid_on->format('d M Y') }} @endif
                            @if($receipt->reference) · Ref: <span style="font-family:var(--font-mono);">{{ $receipt->reference }}</span> @endif
                        </div>
                        @if($receipt->isRejected() && $receipt->verification_note)
                            <div style="font-size:11px;color:#dc2626;margin-top:4px;">Rejected: {{ $receipt->verification_note }}</div>
                        @endif
                    </div>
                    <span class="pill {{ $pillClass }}">
                        <span class="dot"></span>{{ ucfirst($receipt->status) }}
                    </span>
                </div>
                @endforeach
            @endif
        </div>
    </div>

</x-client.layouts.app>
