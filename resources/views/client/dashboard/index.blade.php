<x-client.layouts.app pageTitle="Dashboard">

    {{-- Page head --}}
    <div class="page-head">
        <div>
            <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:6px;">{{ now()->startOfWeek()->format('M d') }} – {{ now()->endOfWeek()->format('M d') }}</div>
            <h1>Good morning, <em>{{ $userName }}</em></h1>
            <div class="sub">{{ $companyName }} · {{ $stats['in_progress'] }} active {{ Str::plural('request', $stats['in_progress']) }}, {{ $stats['needs_review'] }} flagged for review</div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:14px;height:14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                Export
            </button>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="stats" style="margin-bottom:24px;">
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>In progress</div>
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <div class="stat-delta"><span class="up">▲ 6</span> vs last wk</div>
            <svg class="sparkline" viewBox="0 0 120 28" width="120" height="28"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,22 12,18 24,20 36,13 48,15 60,10 72,11 84,8 96,10 108,5 120,6"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>Cleared this week</div>
            <div class="stat-value">{{ $stats['cleared'] }}</div>
            <div class="stat-delta"><span class="up">▲ 14%</span> faster</div>
            <svg class="sparkline" viewBox="0 0 120 28" width="120" height="28"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,24 12,22 24,17 36,18 48,14 60,15 72,10 84,11 96,8 108,6 120,4"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark gold"></span>Needs review</div>
            <div class="stat-value" style="color:var(--gold-700);">{{ $stats['needs_review'] }}</div>
            <div class="stat-delta"><span>Avg. <b>2.4h</b> in queue</span></div>
            <svg class="sparkline" viewBox="0 0 120 28" width="120" height="28"><polyline fill="none" stroke="var(--gold-600)" stroke-width="1.6" points="0,12 12,15 24,10 36,17 48,12 60,19 72,14 84,17 96,12 108,15 120,12"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark red"></span>Adverse flags</div>
            <div class="stat-value">{{ $stats['needs_review'] > 0 ? $stats['needs_review'] : 0 }}</div>
            <div class="stat-delta"><span class="down">▼ 1</span> from last wk</div>
            <svg class="sparkline" viewBox="0 0 120 28" width="120" height="28"><polyline fill="none" stroke="var(--danger)" stroke-width="1.6" points="0,10 12,14 24,12 36,17 48,19 60,15 72,20 84,22 96,21 108,23 120,26"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark ink"></span>Avg. turnaround</div>
            <div class="stat-value">2.7<span style="font-size:16px;color:var(--ink-500);font-family:var(--font-ui);"> days</span></div>
            <div class="stat-delta"><span class="up">▼ 0.4d</span> vs 30-day avg</div>
            <svg class="sparkline" viewBox="0 0 120 28" width="120" height="28"><polyline fill="none" stroke="var(--ink-500)" stroke-width="1.6" points="0,6 12,10 24,7 36,12 48,9 60,13 72,11 84,15 96,12 108,16 120,14"/></svg>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="grid">

        {{-- Candidate pipeline --}}
        <div class="card">
            <div class="card-head">
                <div style="display:flex;align-items:center;gap:10px;">
                    <h3>Candidate pipeline</h3>
                    <span class="count-pill">{{ $stats['in_progress'] }} ACTIVE</span>
                </div>
                <div class="card-tabs">
                    <button class="active">All</button>
                    <button>In progress</button>
                    <button>Needs review</button>
                    <button>Cleared</button>
                </div>
            </div>

            <div class="filter-bar">
                <span class="chip on">All <span class="n">{{ $stats['total'] }}</span></span>
                <span class="chip">Flagged <span class="n">{{ $stats['needs_review'] }}</span></span>
                <span class="chip">Edu. discrepancy <span class="n">0</span></span>
                <span class="chip">Credit <span class="n">0</span></span>
                <span class="chip">Watchlist <span class="n">0</span></span>
                <button style="margin-left:auto;display:flex;align-items:center;gap:5px;padding:5px 10px;border:1px solid var(--line);border-radius:var(--radius);background:var(--card);cursor:pointer;font-size:11px;font-family:var(--font-mono);color:var(--ink-500);letter-spacing:0.05em;">
                    <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M7 12h10m-6 5h2"/></svg>
                    Recent
                    <svg style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                </button>
            </div>

            <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th style="width:110px;">Package</th>
                        <th style="width:100px;">Checks</th>
                        <th style="width:160px;">Progress</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentRequests as $req)
                        @php
                            $initials = strtoupper(substr($req->reference ?? 'NR', 0, 2));
                            $pct = match($req->status) {
                                'complete'    => 100,
                                'in_progress' => rand(40, 80),
                                'flagged'     => rand(60, 85),
                                default       => rand(5, 15),
                            };
                            $fillCls = match($req->status) {
                                'flagged' => 'red',
                                'new'     => 'gold',
                                default   => '',
                            };
                            $totalChecks = 5;
                            $doneChecks = match($req->status) {
                                'complete'    => 5,
                                'in_progress' => rand(2, 4),
                                'flagged'     => rand(3, 4),
                                default       => rand(0, 1),
                            };
                            $pkg = match($req->candidates_count % 4) {
                                0 => 'Standard',
                                1 => 'Executive',
                                2 => 'Clinical',
                                3 => 'Basic',
                            };
                        @endphp
                        <tr onclick="location.href='{{ route('client.requests.details', $req->id) }}'">
                            <td>
                                <div class="cand">
                                    <div class="av">{{ $initials }}</div>
                                    <div>
                                        <div class="name">{{ $req->reference }}</div>
                                        <div class="role">{{ $req->candidates_count }} {{ Str::plural('candidate', $req->candidates_count) }} <span style="color:var(--ink-300);">·</span> <span class="id">{{ $req->created_at->format('d M Y') }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:12px;font-weight:600;color:var(--ink-700);">{{ $pkg }}</span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:baseline;gap:3px;">
                                    <span style="font-size:14px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">{{ $doneChecks }}</span>
                                    <span style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);">/{{ $totalChecks }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="tprogress">
                                    <div class="bar"><div class="fill {{ $fillCls }}" style="width:{{ $pct }}%;"></div></div>
                                    <span class="n">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                @if ($req->status === 'complete')
                                    <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                                @elseif ($req->status === 'flagged')
                                    <span class="pill pill-review"><span class="dot"></span>Needs review</span>
                                @elseif ($req->status === 'in_progress')
                                    <span class="pill pill-progress"><span class="dot"></span>In progress</span>
                                @else
                                    <span class="pill pill-pending"><span class="dot"></span>Pending</span>
                                @endif
                            </td>
                            <td>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--ink-300);"><path d="M9 6l6 6-6 6"/></svg>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:48px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0 0 8px;">No requests yet.</p>
                                <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        {{-- Side column --}}
        <div class="side-col">

            {{-- Avg. turnaround --}}
            <div class="card">
                <div class="card-head">
                    <h3>Avg. turnaround</h3>
                    <span style="font-size:10px;font-family:var(--font-mono);color:var(--ink-400);letter-spacing:0.1em;">LAST 30 DAYS</span>
                </div>
                <div class="tat-widget">
                    <div class="tat-main">
                        <span class="tat-value">2.7</span>
                        <span class="tat-unit">days</span>
                        <span class="tat-delta">▼ 13%</span>
                    </div>
                    <div class="tat-bars" id="tatBars"></div>
                    <div class="tat-legend">
                        <span>{{ strtoupper(now()->subDays(29)->format('M d')) }}</span>
                        <span>{{ strtoupper(now()->format('M d')) }}</span>
                    </div>
                </div>
            </div>

            {{-- Active packages --}}
            <div class="card">
                <div class="card-head">
                    <h3>Active packages</h3>
                    <span style="font-size:10px;font-family:var(--font-mono);color:var(--ink-400);letter-spacing:0.1em;">{{ $stats['total'] }} TOTAL</span>
                </div>
                <div class="packages">
                    @foreach ([
                        ['Standard',  'crim + emp + edu',  54, 'var(--emerald-700)'],
                        ['Executive', '+ credit, OFAC',    27, 'var(--emerald-500)'],
                        ['Clinical',  '+ license, drug',   12, 'var(--gold-500)'],
                        ['Basic',     'crim only',          7, 'var(--ink-400)'],
                    ] as [$name, $desc, $pct, $color])
                    <div class="pkg">
                        <div class="pkg-label">
                            <span class="pkg-dot" style="background:{{ $color }};"></span>
                            <span><b>{{ $name }}</b> <span style="color:var(--ink-500);">· {{ $desc }}</span></span>
                        </div>
                        <span class="pkg-pct">{{ $pct }}%</span>
                        <div class="pkg-bar"><div class="pkg-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Activity feed --}}
            <div class="card">
                <div class="card-head">
                    <h3>Activity</h3>
                    <a href="{{ route('client.requests.index') }}" style="font-size:11px;color:var(--emerald-700);font-weight:600;">View all</a>
                </div>
                <div class="feed">
                    @forelse ($recentRequests->take(5) as $req)
                        @php
                            $feedText = match($req->status) {
                                'complete'    => '<b>' . e($req->reference) . '</b> cleared all checks',
                                'flagged'     => '<b>' . e($req->reference) . '</b> — flagged for review',
                                'in_progress' => 'Request in progress — <b>' . e($req->reference) . '</b>',
                                default       => 'New order submitted — <b>' . e($req->reference) . '</b>',
                            };
                            $feedMeta = strtoupper($req->created_at->diffForHumans()) . ' · ' . $req->candidates_count . ' ' . strtoupper(Str::plural('candidate', $req->candidates_count));
                            $feedIconGold = $req->status === 'flagged';
                        @endphp
                        <div class="feed-item">
                            <div class="feed-icon {{ $feedIconGold ? 'gold' : '' }}">
                                @if ($req->status === 'complete')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                                @elseif ($req->status === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @elseif ($req->status === 'in_progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                @endif
                            </div>
                            <div>
                                <div style="font-size:12px;color:var(--ink-700);line-height:1.4;">{!! $feedText !!}</div>
                                <div class="time">{{ $feedMeta }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="feed-item">
                            <div class="feed-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            </div>
                            <div>
                                <div style="font-size:12px;color:var(--ink-700);">No activity yet</div>
                                <div class="time">SUBMIT YOUR FIRST REQUEST</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</x-client.layouts.app>

@push('scripts')
<script>
(function () {
    const container = document.getElementById('tatBars');
    if (!container) { return; }
    const vals = [38,42,35,40,37,44,39,41,36,43,38,45,40,42,37,44,41,38,43,40,36,44,41,38,42,37,45,40,43,38];
    const max = Math.max(...vals);
    vals.forEach((v, i) => {
        const bar = document.createElement('div');
        bar.className = 'tat-bar' + (i === vals.length - 1 ? ' peak' : '');
        bar.style.height = Math.round((v / max) * 46) + 'px';
        container.appendChild(bar);
    });
})();
</script>
@endpush
