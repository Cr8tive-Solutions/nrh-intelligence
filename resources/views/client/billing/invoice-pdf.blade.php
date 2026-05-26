<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 11px;
        color: #1a1a1a;
        background: #fff;
    }

    .page { padding: 40px 44px; }

    /* ── Header ── */
    .header {
        display: table;
        width: 100%;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 3px solid #064e3b;
    }
    .header-left  { display: table-cell; vertical-align: top; width: 55%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }

    .logo { height: 44px; width: auto; margin-bottom: 8px; }
    .company-name {
        font-size: 13px; font-weight: 700; color: #064e3b;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .company-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }

    .inv-title {
        font-size: 28px; font-weight: 700; color: #064e3b;
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 10px;
    }
    .inv-meta-row { margin-bottom: 5px; }
    .inv-meta-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; }
    .inv-meta-value { font-size: 11px; font-weight: 600; color: #111827; font-family: 'Courier New', monospace; }

    /* ── Status badge ── */
    .badge {
        display: inline-block; padding: 3px 12px; border-radius: 99px;
        font-size: 9px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.12em;
    }
    .badge-paid     { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-unpaid   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-overdue  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* ── Bill-to / invoice-info band ── */
    .info-band {
        display: table; width: 100%;
        background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;
        padding: 16px 20px; margin-bottom: 28px;
    }
    .info-col { display: table-cell; vertical-align: top; width: 50%; }
    .info-col + .info-col { border-left: 1px solid #e5e7eb; padding-left: 20px; }
    .info-label {
        font-size: 9px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.12em; color: #9ca3af; margin-bottom: 6px;
    }
    .info-value { font-size: 12px; font-weight: 600; color: #111827; }
    .info-sub   { font-size: 10px; color: #6b7280; margin-top: 3px; }

    /* ── Line items ── */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .items-table thead tr {
        background: #064e3b; color: #fff;
    }
    .items-table thead th {
        padding: 9px 12px; font-size: 9px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.12em;
    }
    .items-table thead th:last-child,
    .items-table thead th:nth-child(2),
    .items-table thead th:nth-child(3) { text-align: right; }

    .items-table tbody tr { border-bottom: 1px solid #f3f4f6; }
    .items-table tbody tr:nth-child(even) { background: #fafafa; }
    .items-table tbody td { padding: 10px 12px; font-size: 11px; color: #374151; vertical-align: top; }
    .items-table tbody td:nth-child(2),
    .items-table tbody td:nth-child(3),
    .items-table tbody td:last-child {
        text-align: right; font-family: 'Courier New', monospace; font-size: 10px; color: #4b5563;
    }
    .items-table tbody td:last-child { font-weight: 600; color: #111827; }

    /* ── Totals ── */
    .totals-wrap { display: table; width: 100%; margin-top: 0; }
    .totals-spacer { display: table-cell; width: 55%; }
    .totals-box {
        display: table-cell; vertical-align: top;
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-top: none; padding: 16px 20px;
    }
    .totals-row { display: table; width: 100%; margin-bottom: 8px; }
    .totals-row:last-child { margin-bottom: 0; }
    .totals-lbl { display: table-cell; font-size: 10px; color: #6b7280; }
    .totals-val { display: table-cell; text-align: right; font-size: 10px; color: #374151; font-family: 'Courier New', monospace; }
    .totals-divider { border: none; border-top: 1px solid #e5e7eb; margin: 10px 0; }
    .totals-grand-lbl { display: table-cell; font-size: 13px; font-weight: 700; color: #064e3b; }
    .totals-grand-val { display: table-cell; text-align: right; font-size: 13px; font-weight: 700; color: #064e3b; font-family: 'Courier New', monospace; }

    /* ── Bank details ── */
    .bank-section {
        margin-top: 28px; border-top: 1px solid #e5e7eb; padding-top: 20px;
        display: table; width: 100%;
    }
    .bank-left  { display: table-cell; vertical-align: top; width: 55%; padding-right: 24px; }
    .bank-right { display: table-cell; vertical-align: top; }
    .section-title {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.12em; color: #064e3b; margin-bottom: 10px;
    }
    .bank-row { margin-bottom: 6px; }
    .bank-lbl { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; }
    .bank-val { font-size: 11px; font-weight: 600; color: #111827; margin-top: 1px; font-family: 'Courier New', monospace; }
    .bank-note { font-size: 10px; color: #6b7280; line-height: 1.6; }
    .ref-highlight {
        display: inline-block;
        background: #ecfdf5; border: 1px solid #a7f3d0;
        border-radius: 4px; padding: 2px 8px;
        font-family: 'Courier New', monospace; font-weight: 700; color: #064e3b; font-size: 11px;
    }

    /* ── Footer ── */
    .footer {
        margin-top: 28px; border-top: 1px solid #e5e7eb; padding-top: 14px;
        display: table; width: 100%;
    }
    .footer-left  { display: table-cell; vertical-align: middle; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; }
    .footer-text  { font-size: 8.5px; color: #9ca3af; line-height: 1.6; }
    .watermark    { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.16em; color: #d1d5db; }
</style>
</head>
<body>
<div class="page">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-left">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="logo" alt="NRH Intelligence">
            @endif
            <div class="company-name">NRH Intelligence Sdn. Bhd.</div>
            <div class="company-sub">Background Verification &amp; Due Diligence</div>
        </div>
        <div class="header-right">
            <div class="inv-title">Invoice</div>
            <div class="inv-meta-row">
                <div class="inv-meta-label">Invoice No.</div>
                <div class="inv-meta-value">{{ $invoice->number }}</div>
            </div>
            <div class="inv-meta-row" style="margin-top:8px;">
                <span class="badge {{ $statusBadge }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    {{-- ── Bill-to / dates band ── --}}
    <div class="info-band">
        <div class="info-col">
            <div class="info-label">Bill To</div>
            <div class="info-value">{{ $invoice->customer->name }}</div>
            <div class="info-sub">{{ $invoice->period }}</div>
        </div>
        <div class="info-col">
            <div style="display:table;width:100%;">
                <div style="display:table-row;">
                    <div style="display:table-cell;padding-bottom:8px;">
                        <div class="info-label">Issue Date</div>
                        <div class="info-value">{{ $invoice->issued_at->format('d M Y') }}</div>
                    </div>
                    <div style="display:table-cell;padding-bottom:8px;text-align:right;">
                        <div class="info-label">Due Date</div>
                        <div class="info-value" style="{{ $invoice->status !== 'paid' && $invoice->due_at->isPast() ? 'color:#dc2626;' : '' }}">
                            {{ $invoice->due_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Line items ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="text-align:left;">Description</th>
                <th>Qty</th>
                <th>Unit Price (MYR)</th>
                <th>Total (MYR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totals ── --}}
    <div class="totals-wrap">
        <div class="totals-spacer"></div>
        <div class="totals-box">
            <div class="totals-row">
                <div class="totals-lbl">Subtotal</div>
                <div class="totals-val">MYR {{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-lbl">Tax (6% SST)</div>
                <div class="totals-val">MYR {{ number_format($invoice->tax, 2) }}</div>
            </div>
            <hr class="totals-divider">
            <div class="totals-row">
                <div class="totals-grand-lbl">Total Due</div>
                <div class="totals-grand-val">MYR {{ number_format($invoice->total, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- ── Payment instructions ── --}}
    @if($invoice->status !== 'paid')
    <div class="bank-section">
        <div class="bank-left">
            <div class="section-title">Payment Instructions</div>
            <div class="bank-row">
                <div class="bank-lbl">Bank</div>
                <div class="bank-val">{{ $bank['name'] }}</div>
            </div>
            <div class="bank-row">
                <div class="bank-lbl">Account Holder</div>
                <div class="bank-val">{{ $bank['account_holder'] }}</div>
            </div>
            <div class="bank-row">
                <div class="bank-lbl">Account Number</div>
                <div class="bank-val">{{ $bank['account_number'] }}</div>
            </div>
            @if(!empty($bank['swift']))
            <div class="bank-row">
                <div class="bank-lbl">SWIFT / BIC</div>
                <div class="bank-val">{{ $bank['swift'] }}</div>
            </div>
            @endif
        </div>
        <div class="bank-right">
            <div class="section-title">Reference</div>
            <div class="bank-note" style="margin-bottom:10px;">
                Please use the invoice number as your payment reference:
            </div>
            <div><span class="ref-highlight">{{ $invoice->number }}</span></div>
            <div class="bank-note" style="margin-top:12px;">
                Upload your payment receipt on the invoice page to notify our finance team.
            </div>
        </div>
    </div>
    @else
    <div class="bank-section">
        <div class="bank-left">
            <div class="section-title">Payment Status</div>
            <div class="bank-note">This invoice has been paid in full. Thank you.</div>
        </div>
    </div>
    @endif

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-text">
                NRH Intelligence Sdn. Bhd. &nbsp;·&nbsp; background verification &amp; due diligence<br>
                This is a computer-generated invoice and does not require a signature.
            </div>
        </div>
        <div class="footer-right">
            <div class="watermark">NRH Intelligence</div>
        </div>
    </div>

</div>
</body>
</html>
