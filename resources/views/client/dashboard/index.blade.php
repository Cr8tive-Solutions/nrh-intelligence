<x-client.layouts.app pageTitle="Dashboard">

    {{-- Page head --}}
    <div class="page-head">
        <div>
            <h1>Good morning, {{ $userName }}. <em>{{ $stats['in_progress'] }} cases</em> need a decision.</h1>
            <div class="sub">Showing activity across <b>{{ $companyName }}</b> · week of {{ now()->startOfWeek()->format('M d') }} – {{ now()->endOfWeek()->format('M d') }}</div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                Export
            </button>
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18"/></svg>
                {{ now()->startOfWeek()->format('M d') }} – {{ now()->endOfWeek()->format('M d') }}
            </button>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="stats">
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>In progress</div>
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <div class="stat-delta"><span class="up">▲ 6</span> vs last wk</div>
            <svg class="sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,18 12,14 24,16 36,10 48,12 60,8 72,9 84,6 96,8 108,4 120,5"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark"></span>Cleared this week</div>
            <div class="stat-value">{{ $stats['cleared'] }}</div>
            <div class="stat-delta"><span class="up">▲ 14%</span> faster turnaround</div>
            <svg class="sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,20 12,18 24,14 36,15 48,11 60,12 72,8 84,9 96,6 108,5 120,3"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark gold"></span>Needs review</div>
            <div class="stat-value" style="color:var(--gold-700);">{{ $stats['needs_review'] }}</div>
            <div class="stat-delta"><span>Avg. <b>2.4h</b> in queue</span></div>
            <svg class="sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--gold-600)" stroke-width="1.6" points="0,10 12,12 24,8 36,14 48,10 60,16 72,12 84,14 96,10 108,13 120,10"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark red"></span>Adverse flags</div>
            <div class="stat-value">{{ $stats['needs_review'] }}</div>
            <div class="stat-delta"><span class="down">▼ 1</span> from last wk</div>
            <svg class="sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--danger)" stroke-width="1.6" points="0,8 12,12 24,10 36,14 48,16 60,13 72,17 84,19 96,18 108,20 120,22"/></svg>
        </div>
        <div class="stat">
            <div class="stat-label"><span class="mark ink"></span>Avg. turnaround</div>
            <div class="stat-value">2.7<span style="font-size:16px;color:var(--ink-500);font-family:var(--font-ui);"> days</span></div>
            <div class="stat-delta"><span class="up">▼ 0.4d</span> vs 30-day avg</div>
            <svg class="sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--ink-500)" stroke-width="1.6" points="0,4 12,8 24,5 36,10 48,7 60,11 72,9 84,13 96,10 108,14 120,12"/></svg>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="grid">

        {{-- Candidate pipeline --}}
        <div class="card">
            <div class="card-head">
                <div style="display:flex;align-items:center;gap:12px;">
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
                <span class="chip on">All statuses <span class="n">{{ $stats['total'] }}</span></span>
                <span class="chip">Criminal flag <span class="n">{{ $stats['needs_review'] }}</span></span>
                <span class="chip">Edu. discrepancy <span class="n">0</span></span>
                <span class="chip">Credit <span class="n">0</span></span>
                <span class="chip">Watchlist <span class="n">0</span></span>
                <span style="margin-left:auto;font-size:11px;color:var(--ink-500);font-family:var(--font-mono);letter-spacing:0.05em;">SORT · RECENT ▾</span>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th style="width:120px;">Package</th>
                        <th style="width:140px;">Checks</th>
                        <th style="width:140px;">Progress</th>
                        <th style="width:130px;">Status</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentRequests as $req)
                        @php
                            $initials = strtoupper(substr($req->reference ?? 'NR', 0, 2));
                            $pct = match($req->status) {
                                'complete'    => 100,
                                'in_progress' => 60,
                                'flagged'     => 72,
                                default       => 4,
                            };
                            $fillCls = match($req->status) {
                                'flagged' => 'red',
                                'new'     => 'gold',
                                default   => '',
                            };
                            $dotCls = match($req->status) {
                                'complete'    => 'pass',
                                'flagged'     => 'flag',
                                'in_progress' => 'prog',
                                default       => 'pend',
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
                                        <div class="role">{{ $req->candidates_count }} candidate{{ $req->candidates_count !== 1 ? 's' : '' }} <span style="color:var(--ink-300);">·</span> <span class="id">{{ $req->created_at->format('d M Y') }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight:600;">{{ $pkg }}</span></td>
                            <td>
                                <div class="checks-row">
                                    <div class="check-dot {{ $dotCls }}">
                                        @if ($req->status === 'complete')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                                        @elseif ($req->status === 'flagged')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 9v4M12 17h.01"/></svg>
                                        @elseif ($req->status === 'in_progress')
                                            {{-- spinning ring via CSS ::after --}}
                                        @else
                                            <span style="font-family:var(--font-mono);font-size:9px;color:var(--ink-400);">C</span>
                                        @endif
                                    </div>
                                    <div class="check-dot pend"><span style="font-family:var(--font-mono);font-size:9px;color:var(--ink-400);">E</span></div>
                                    <div class="check-dot pend"><span style="font-family:var(--font-mono);font-size:9px;color:var(--ink-400);">G</span></div>
                                    @if ($req->status === 'complete')
                                        <div class="check-dot pass"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
                                        <div class="check-dot pass"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
                                    @else
                                        <div class="check-dot pend"><span style="font-family:var(--font-mono);font-size:9px;color:var(--ink-400);">F</span></div>
                                        <div class="check-dot pend"><span style="font-family:var(--font-mono);font-size:9px;color:var(--ink-400);">W</span></div>
                                    @endif
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
                                    <span class="pill pill-pending"><span class="dot"></span>Pending consent</span>
                                @endif
                            </td>
                            <td style="text-align:right;color:var(--ink-400);">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:48px 20px;text-align:center;">
                                <p style="font-size:13px;color:var(--ink-400);margin:0 0 8px;">No screening requests yet.</p>
                                <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                    <div class="pkg">
                        <div class="pkg-label"><span class="pkg-dot" style="background:var(--emerald-700);"></span><b>Standard</b> <span style="color:var(--ink-500);">· crim + emp + edu</span></div>
                        <span class="pkg-pct">54%</span>
                        <div class="pkg-bar"><div class="pkg-fill" style="width:54%;background:var(--emerald-700);"></div></div>
                    </div>
                    <div class="pkg">
                        <div class="pkg-label"><span class="pkg-dot" style="background:var(--emerald-500);"></span><b>Executive</b> <span style="color:var(--ink-500);">· + credit, OFAC</span></div>
                        <span class="pkg-pct">27%</span>
                        <div class="pkg-bar"><div class="pkg-fill" style="width:27%;background:var(--emerald-500);"></div></div>
                    </div>
                    <div class="pkg">
                        <div class="pkg-label"><span class="pkg-dot" style="background:var(--gold-500);"></span><b>Clinical</b> <span style="color:var(--ink-500);">· + license, drug</span></div>
                        <span class="pkg-pct">12%</span>
                        <div class="pkg-bar"><div class="pkg-fill" style="width:12%;background:var(--gold-500);"></div></div>
                    </div>
                    <div class="pkg">
                        <div class="pkg-label"><span class="pkg-dot" style="background:var(--ink-400);"></span><b>Basic</b> <span style="color:var(--ink-500);">· crim only</span></div>
                        <span class="pkg-pct">7%</span>
                        <div class="pkg-bar"><div class="pkg-fill" style="width:7%;background:var(--ink-400);"></div></div>
                    </div>
                </div>
            </div>

            {{-- Activity feed --}}
            <div class="card">
                <div class="card-head">
                    <h3>Activity</h3>
                    <a href="{{ route('client.requests.index') }}" style="font-size:11px;color:var(--emerald-700);cursor:pointer;font-weight:600;">View all</a>
                </div>
                <div class="feed">
                    @forelse ($recentRequests->take(5) as $req)
                        @php
                            $feedIconCls = match($req->status) {
                                'flagged'  => 'gold',
                                'complete' => '',
                                default    => '',
                            };
                            $feedText = match($req->status) {
                                'complete'    => '<b>' . e($req->reference) . '</b> cleared all checks',
                                'flagged'     => '<b>' . e($req->reference) . '</b> — flagged for review',
                                'in_progress' => 'Screening in progress — <b>' . e($req->reference) . '</b>',
                                default       => 'New order submitted — <b>' . e($req->reference) . '</b>',
                            };
                            $feedMeta = strtoupper($req->created_at->diffForHumans()) . ' · ' . $req->candidates_count . ' CANDIDATE' . ($req->candidates_count !== 1 ? 'S' : '');
                        @endphp
                        <div class="feed-item">
                            <div class="feed-icon {{ $feedIconCls }}">
                                @if ($req->status === 'complete')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                                @elseif ($req->status === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @elseif ($req->status === 'in_progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                @endif
                            </div>
                            <div>
                                <div>{!! $feedText !!}</div>
                                <div class="time">{{ $feedMeta }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="feed-item">
                            <div class="feed-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            </div>
                            <div>
                                <div>No activity yet</div>
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
