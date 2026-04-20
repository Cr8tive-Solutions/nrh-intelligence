<x-client.layouts.app pageTitle="Invoices">

    <div class="page-head">
        <div>
            <h1>Monthly <em>Invoices</em></h1>
            <div class="sub">Invoices issued by NRH Intelligence</div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h3>Invoice History</h3>
            <span class="count-pill">{{ count($invoices) }} INVOICES</span>
        </div>
        <div style="overflow-x:auto;">
            <div class="table-scroll"><table class="table">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Period</th>
                        <th style="width:140px;">Issued</th>
                        <th style="width:140px;">Due</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:130px;text-align:right;">Amount</th>
                        <th style="width:100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $inv)
                        <tr onclick="location.href='{{ route('client.billing.invoices.show', $inv->id) }}'">
                            <td>
                                <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $inv->number }}</span>
                            </td>
                            <td style="font-weight:600;color:var(--ink-900);">{{ $inv->period }}</td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $inv->issued_at->format('d M Y') }}</td>
                            <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $inv->due_at->format('d M Y') }}</td>
                            <td>
                                <span class="pill {{ $inv->status === 'paid' ? 'pill-clear' : 'pill-review' }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td style="text-align:right;font-weight:600;font-family:var(--font-mono);font-size:13px;color:var(--ink-900);">
                                MYR {{ number_format($inv->total, 2) }}
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                                    <a href="{{ route('client.billing.invoices.show', $inv->id) }}"
                                       class="btn btn-ghost" style="padding:5px 10px;font-size:12px;"
                                       onclick="event.stopPropagation()">View</a>
                                    <a href="{{ route('client.billing.invoices.download', $inv->id) }}"
                                       style="font-size:12px;font-weight:500;color:var(--ink-400);text-decoration:none;transition:color 120ms;"
                                       onclick="event.stopPropagation()">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:60px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0;">No invoices issued yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

</x-client.layouts.app>
