<x-client.layouts.app pageTitle="Dashboard">

    {{-- Page header --}}
    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                Good morning, <em style="font-style:italic;color:var(--emerald-700);">{{ session('client_user_name', 'Recruiter') }}.</em>
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">
                <b style="color:var(--ink-900);">{{ $stats['pending'] ?? 0 }}</b> screenings in progress · <b style="color:var(--ink-900);">{{ $stats['complete'] ?? 0 }}</b> cleared this period
            </p>
        </div>
        <a href="{{ route('client.request.new') }}" class="btn-primary">
            <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New screening
        </a>
    </div>

    {{-- Stat strip --}}
    <div class="nrh-card" style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;">
        @php
            $statsStrip = [
                ['label' => 'New',         'value' => $stats['new'] ?? 0,      'color' => 'var(--info)',        'delta' => null],
                ['label' => 'In Progress', 'value' => $stats['pending'] ?? 0,  'color' => 'var(--emerald-600)', 'delta' => null],
                ['label' => 'Completed',   'value' => $stats['complete'] ?? 0, 'color' => 'var(--emerald-700)', 'delta' => null],
                ['label' => 'Total',       'value' => $stats['total'] ?? 0,    'color' => 'var(--ink-500)',     'delta' => null],
            ];
        @endphp
        @foreach ($statsStrip as $i => $s)
            <div style="padding:20px 24px;{{ $i > 0 ? 'border-left:1px solid var(--line);' : '' }}">
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);display:flex;align-items:center;gap:6px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $s['color'] }};display:inline-block;"></span>
                    {{ $s['label'] }}
                </div>
                <div style="font-family:var(--font-display);font-size:32px;font-weight:500;letter-spacing:-0.02em;color:var(--ink-900);margin-top:8px;line-height:1;">
                    {{ number_format($s['value']) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Main grid --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

        {{-- Recent requests table --}}
        <div class="nrh-card">
            <div class="card-head">
                <div style="display:flex;align-items:center;gap:12px;">
                    <h3>Recent Requests</h3>
                    <span class="count-pill">{{ $stats['total'] ?? 0 }} TOTAL</span>
                </div>
                <a href="{{ route('client.requests.index') }}" style="font-size:12px;font-weight:600;color:var(--emerald-700);text-decoration:none;">View all →</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="nrh-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Candidates</th>
                            <th style="width:140px;">Status</th>
                            <th style="width:140px;">Submitted</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRequests ?? [] as $request)
                            <tr onclick="location.href='{{ route('client.requests.details', $request->id) }}'">
                                <td>
                                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:500;color:var(--emerald-700);">{{ $request->reference }}</span>
                                </td>
                                <td>
                                    <span style="color:var(--ink-700);">{{ $request->candidates_count }}</span>
                                </td>
                                <td>
                                    @include('client.partials._status-badge', ['status' => $request->status])
                                </td>
                                <td style="font-size:12px;color:var(--ink-500);font-family:var(--font-mono);">{{ $request->created_at->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('client.requests.details', $request->id) }}"
                                       class="btn-ghost" style="padding:5px 10px;font-size:12px;"
                                       onclick="event.stopPropagation()">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:48px 20px;text-align:center;">
                                    <p style="font-size:13px;color:var(--ink-400);margin:0;">No requests yet.</p>
                                    <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);text-decoration:none;display:inline-block;margin-top:6px;">Submit your first request →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Side column --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Billing summary --}}
            <div style="background:linear-gradient(170deg,var(--emerald-900),var(--emerald-800) 60%,#011d15);border-radius:var(--radius-lg);padding:20px;position:relative;overflow:hidden;">
                <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(212,175,55,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(212,175,55,0.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;"></div>
                <p style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.18em;color:rgba(212,175,55,0.7);position:relative;z-index:1;">Billing Method</p>
                <p style="font-family:var(--font-display);font-size:20px;font-weight:500;color:var(--gold-400);margin:6px 0 2px;position:relative;z-index:1;">Monthly Billing</p>
                <p style="font-size:11px;color:rgba(233,239,235,0.6);position:relative;z-index:1;">Cash / Direct Transfer</p>
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid rgba(212,175,55,0.2);display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1;">
                    <div>
                        <p style="font-size:10px;color:rgba(233,239,235,0.5);text-transform:uppercase;letter-spacing:0.12em;">Next invoice</p>
                        <p style="font-size:13px;font-weight:600;color:var(--gold-300,#f0d060);margin-top:2px;">End of {{ now()->format('F Y') }}</p>
                    </div>
                    <a href="{{ route('client.billing.invoices') }}"
                       style="padding:7px 12px;background:var(--gold-500);border-radius:var(--radius);font-size:12px;font-weight:600;color:#023527;text-decoration:none;transition:background 120ms;"
                       onmouseover="this.style.background='var(--gold-400)'" onmouseout="this.style.background='var(--gold-500)'">
                        View Invoices
                    </a>
                </div>
            </div>

            {{-- Agreement status --}}
            <div class="nrh-card">
                <div class="card-head" style="padding:14px 16px;">
                    <h3 style="font-size:14px;">Agreement</h3>
                    @php $daysLeft = $agreementDaysLeft ?? 90; @endphp
                    @if ($daysLeft > 30)
                        <span class="pill pill-clear"><span class="dot"></span>Active</span>
                    @elseif ($daysLeft > 0)
                        <span class="pill pill-review"><span class="dot"></span>Expiring</span>
                    @else
                        <span class="pill pill-flagged"><span class="dot"></span>Expired</span>
                    @endif
                </div>
                <div style="padding:14px 16px;">
                    <p style="font-size:11px;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.1em;margin:0;">Expires</p>
                    <p style="font-size:14px;font-weight:600;color:var(--ink-900);margin:4px 0 2px;">{{ $agreementExpiry ?? 'N/A' }}</p>
                    <p style="font-size:12px;color:var(--ink-400);margin:0;">{{ $daysLeft > 0 ? $daysLeft . ' days remaining' : 'Please renew' }}</p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="nrh-card">
                <div class="card-head" style="padding:14px 16px;">
                    <h3 style="font-size:14px;">Quick Actions</h3>
                </div>
                <div style="padding:8px 0;">
                    @foreach ([
                        ['route' => 'client.request.new',         'label' => 'New Request',        'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                        ['route' => 'client.requests.track',      'label' => 'Track Candidate',     'icon' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
                        ['route' => 'client.billing.transactions', 'label' => 'Transactions',        'icon' => 'M7 16V4m0 0L3 8m4-4 4 4m6 0v12m0 0 4-4m-4 4-4-4'],
                        ['route' => 'client.history.index',       'label' => 'View History',        'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ] as $action)
                        <a href="{{ route($action['route']) }}"
                           style="display:flex;align-items:center;gap:12px;padding:10px 16px;font-size:13px;font-weight:500;color:var(--ink-700);text-decoration:none;transition:background 120ms ease,color 120ms ease;"
                           onmouseover="this.style.background='rgba(5,150,105,0.05)';this.style.color='var(--ink-900)'"
                           onmouseout="this.style.background='';this.style.color='var(--ink-700)'">
                            <svg style="width:15px;height:15px;color:var(--emerald-600);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                            </svg>
                            {{ $action['label'] }}
                            <svg style="width:12px;height:12px;margin-left:auto;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-client.layouts.app>
