<x-client.layouts.app pageTitle="Dashboard">

    {{-- Page head --}}
    <div class="page-head">
        <div>
            <h1>Good morning, <em>{{ $userName }}.</em></h1>
            <div class="sub">
                Showing activity across <b>{{ $companyName }}</b> · {{ now()->format('d M Y') }}
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                Export
            </button>
            <a href="{{ route('client.request.new') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                New screening
            </a>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="dash-stats">
        <div class="dash-stat">
            <div class="dash-stat-label"><span class="dash-stat-mark"></span>In progress</div>
            <div class="dash-stat-value">{{ number_format($stats['in_progress']) }}</div>
            <div class="dash-stat-delta"><span class="up">Active</span> screenings</div>
            <svg class="dash-sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,18 12,14 24,16 36,10 48,12 60,8 72,9 84,6 96,8 108,4 120,5"/></svg>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-label"><span class="dash-stat-mark"></span>Cleared</div>
            <div class="dash-stat-value">{{ number_format($stats['cleared']) }}</div>
            <div class="dash-stat-delta"><span class="up">Completed</span> checks</div>
            <svg class="dash-sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--emerald-600)" stroke-width="1.6" points="0,20 12,18 24,14 36,15 48,11 60,12 72,8 84,9 96,6 108,5 120,3"/></svg>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-label"><span class="dash-stat-mark gold"></span>Needs review</div>
            <div class="dash-stat-value" style="color:var(--gold-700);">{{ number_format($stats['needs_review']) }}</div>
            <div class="dash-stat-delta"><span>Awaiting</span> decision</div>
            <svg class="dash-sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--gold-600)" stroke-width="1.6" points="0,10 12,12 24,8 36,14 48,10 60,16 72,12 84,14 96,10 108,13 120,10"/></svg>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-label"><span class="dash-stat-mark ink"></span>Total requests</div>
            <div class="dash-stat-value">{{ number_format($stats['total']) }}</div>
            <div class="dash-stat-delta"><span>All time</span></div>
            <svg class="dash-sparkline" viewBox="0 0 120 24" width="120" height="24"><polyline fill="none" stroke="var(--ink-500)" stroke-width="1.6" points="0,20 12,17 24,18 36,14 48,15 60,11 72,12 84,9 96,10 108,7 120,6"/></svg>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-label"><span class="dash-stat-mark ink"></span>Agreement</div>
            <div class="dash-stat-value" @if(($agreementDaysLeft ?? 0) < 30) style="color:var(--gold-700);" @endif>
                {{ $agreementDaysLeft ?? '—' }}<span style="font-size:16px;color:var(--ink-500);font-family:var(--font-ui);"> days</span>
            </div>
            <div class="dash-stat-delta">Expires <span>{{ $agreementExpiry ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="dash-grid">

        {{-- Candidate pipeline --}}
        <div class="nrh-card">
            <div class="nrh-card-head">
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
                <span class="chip">Flagged <span class="n">{{ $stats['needs_review'] }}</span></span>
                <span class="chip">Cleared <span class="n">{{ $stats['cleared'] }}</span></span>
                <span style="margin-left:auto;font-size:11px;color:var(--ink-500);font-family:var(--font-mono);letter-spacing:0.05em;">SORT · RECENT ▾</span>
            </div>

            <table class="pipeline-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th style="width:120px;">Candidates</th>
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
                                'complete' => 100,
                                'in_progress' => 60,
                                'flagged' => 80,
                                default => 20,
                            };
                            $dotClass = match($req->status) {
                                'complete' => 'pass',
                                'flagged'  => 'flag',
                                'in_progress' => 'prog',
                                default => 'pend',
                            };
                            $barColor = match($req->status) {
                                'complete'    => '',
                                'flagged'     => 'gold',
                                'in_progress' => '',
                                default       => '',
                            };
                        @endphp
                        <tr onclick="location.href='{{ route('client.requests.details', $req->id) }}'">
                            <td>
                                <div class="cand-cell">
                                    <div class="cand-av">{{ $initials }}</div>
                                    <div>
                                        <div class="cand-name">{{ $req->reference }}</div>
                                        <div class="cand-role">{{ $req->candidates_count }} candidate{{ $req->candidates_count !== 1 ? 's' : '' }}</div>
                                        <div class="cand-id">{{ $req->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-family:var(--font-mono);font-size:12px;">{{ $req->candidates_count }}</td>
                            <td>
                                <div class="checks-row">
                                    <div class="check-dot {{ $dotClass }}">
                                        @if ($req->status === 'complete')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><path d="M20 6L9 17l-5-5"/></svg>
                                        @elseif ($req->status === 'flagged')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                        @elseif ($req->status === 'in_progress')
                                            {{-- spinning ring via CSS ::after --}}
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/></svg>
                                        @endif
                                    </div>
                                    <div class="check-dot pend"></div>
                                    <div class="check-dot pend"></div>
                                </div>
                            </td>
                            <td>
                                <div class="tprogress">
                                    <div class="bar">
                                        <div class="fill {{ $barColor }}" style="width:{{ $pct }}%;"></div>
                                    </div>
                                    <span class="n">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                @if ($req->status === 'complete')
                                    <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                                @elseif ($req->status === 'flagged')
                                    <span class="pill pill-review"><span class="dot"></span>Review</span>
                                @elseif ($req->status === 'in_progress')
                                    <span class="pill pill-progress"><span class="dot"></span>In progress</span>
                                @else
                                    <span class="pill pill-pending"><span class="dot"></span>New</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('client.requests.details', $req->id) }}"
                                   class="btn btn-ghost"
                                   style="padding:5px 10px;font-size:12px;"
                                   onclick="event.stopPropagation()">View</a>
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
        <div class="dash-side-col">

            {{-- Avg. turnaround --}}
            <div class="nrh-card">
                <div class="nrh-card-head">
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
                        <span>{{ now()->subDays(29)->format('M d') }}</span>
                        <span>{{ now()->format('M d') }}</span>
                    </div>
                </div>
            </div>

            {{-- Active packages --}}
            <div class="nrh-card">
                <div class="nrh-card-head">
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
            <div class="nrh-card">
                <div class="nrh-card-head">
                    <h3>Activity</h3>
                    <a href="{{ route('client.requests.index') }}" style="font-size:11px;color:var(--emerald-700);font-weight:600;">View all</a>
                </div>
                <div class="feed">
                    @forelse ($recentRequests->take(5) as $req)
                        @php
                            $iconClass = match($req->status) {
                                'complete'    => '',
                                'flagged'     => 'gold',
                                'in_progress' => '',
                                default       => '',
                            };
                            $label = match($req->status) {
                                'complete'    => 'cleared all checks',
                                'flagged'     => 'flagged for review',
                                'in_progress' => 'screening in progress',
                                default       => 'request submitted',
                            };
                        @endphp
                        <div class="feed-item">
                            <div class="feed-icon {{ $iconClass }}">
                                @if ($req->status === 'complete')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                                @elseif ($req->status === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                @endif
                            </div>
                            <div>
                                <div>Request <b>{{ $req->reference }}</b> {{ $label }}</div>
                                <div class="feed-time">{{ strtoupper($req->created_at->diffForHumans()) }} · {{ $req->candidates_count }} CANDIDATE{{ $req->candidates_count !== 1 ? 'S' : '' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="feed-item">
                            <div class="feed-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            </div>
                            <div>
                                <div>No recent activity</div>
                                <div class="feed-time">GET STARTED · NEW REQUEST</div>
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
    // Render TAT sparkline bars
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
