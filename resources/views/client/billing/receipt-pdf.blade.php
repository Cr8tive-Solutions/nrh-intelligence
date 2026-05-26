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

    .page {
        padding: 36px 40px;
    }

    /* ── Header ── */
    .header {
        display: table;
        width: 100%;
        border-bottom: 2px solid #064e3b;
        padding-bottom: 20px;
        margin-bottom: 24px;
    }
    .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }

    .logo { height: 40px; width: auto; }
    .brand {
        font-size: 14px;
        font-weight: 700;
        color: #064e3b;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-top: 6px;
    }
    .brand-sub {
        font-size: 9px;
        color: #6b7280;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .receipt-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #6b7280;
    }
    .receipt-ref {
        font-size: 18px;
        font-weight: 700;
        color: #064e3b;
        font-family: 'Courier New', Courier, monospace;
        margin-top: 4px;
    }
    .receipt-date {
        font-size: 10px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* ── Amount spotlight ── */
    .amount-block {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 22px 28px;
        text-align: center;
        margin-bottom: 24px;
    }
    .amount-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #065f46;
        margin-bottom: 8px;
    }
    .amount-value {
        font-size: 34px;
        font-weight: 700;
        color: #064e3b;
        letter-spacing: -0.02em;
    }
    .amount-currency {
        font-size: 18px;
        font-weight: 400;
        color: #374151;
    }

    /* ── Detail rows ── */
    .details {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    .details td {
        padding: 9px 12px;
        font-size: 11px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }
    .details .lbl {
        width: 38%;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 9px;
        color: #9ca3af;
        padding-top: 11px;
    }
    .details .val {
        color: #111827;
        font-weight: 500;
    }
    .details .mono { font-family: 'Courier New', Courier, monospace; font-size: 10px; }
    .details tr:last-child td { border-bottom: none; }

    /* ── Status badge ── */
    .badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-yellow { background: #fef9c3; color: #713f12; }
    .badge-red { background: #fee2e2; color: #991b1b; }

    /* ── Notes ── */
    .notes-block {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 24px;
    }
    .notes-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        margin-bottom: 6px;
    }
    .notes-text { font-size: 11px; color: #374151; line-height: 1.5; }

    /* ── Footer ── */
    .footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
        display: table;
        width: 100%;
    }
    .footer-left  { display: table-cell; vertical-align: middle; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; }
    .footer-text { font-size: 9px; color: #9ca3af; line-height: 1.6; }
    .footer-legal { font-size: 8px; color: #d1d5db; margin-top: 6px; }
    .watermark {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #d1d5db;
    }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="logo" alt="NRH Intelligence">
            @endif
            <div class="brand">NRH Intelligence Sdn. Bhd.</div>
            <div class="brand-sub">Background Verification &amp; Due Diligence</div>
        </div>
        <div class="header-right">
            <div class="receipt-label">Official Receipt</div>
            <div class="receipt-ref">{{ $transaction->reference ?? ('TXN-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT)) }}</div>
            <div class="receipt-date">{{ $transaction->created_at->format('d F Y, H:i') }}</div>
        </div>
    </div>

    {{-- Amount spotlight --}}
    <div class="amount-block">
        <div class="amount-label">
            {{ $transaction->type === 'topup' ? 'Amount Credited' : 'Amount Paid' }}
        </div>
        <div class="amount-value">
            <span class="amount-currency">MYR </span>{{ number_format($transaction->amount, 2) }}
        </div>
    </div>

    {{-- Detail rows --}}
    <table class="details">
        <tr>
            <td class="lbl">Received From</td>
            <td class="val">{{ $transaction->customer->name }}</td>
        </tr>
        <tr>
            <td class="lbl">Transaction Type</td>
            <td class="val">{{ ucfirst($transaction->type) }}</td>
        </tr>
        <tr>
            <td class="lbl">Payment Method</td>
            <td class="val">{{ $transaction->method ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Reference</td>
            <td class="val mono">{{ $transaction->reference ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Date &amp; Time</td>
            <td class="val mono">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <td class="lbl">Status</td>
            <td class="val">
                @php
                    $badgeClass = match($transaction->status) {
                        'completed' => 'badge-green',
                        'pending'   => 'badge-yellow',
                        default     => 'badge-red',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($transaction->status) }}</span>
            </td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($transaction->notes)
    <div class="notes-block">
        <div class="notes-label">Notes</div>
        <div class="notes-text">{{ $transaction->notes }}</div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-text">
                NRH Intelligence Sdn. Bhd.<br>
                Keep this receipt for your records.
            </div>
            <div class="footer-legal">This is a computer-generated receipt and does not require a signature.</div>
        </div>
        <div class="footer-right">
            <div class="watermark">NRH Intelligence</div>
        </div>
    </div>

</div>
</body>
</html>
