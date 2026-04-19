<x-client.layouts.app pageTitle="Candidates">

    {{-- Page head --}}
    <div class="page-head">
        <div>
            <h1>Candidates. <em>{{ $stats['active'] }} active</em> &middot; {{ $stats['consent'] }} awaiting consent.</h1>
            <div class="sub">All screening candidates across <b>{{ session('client_company', 'your company') }}</b> &middot; billing period {{ now()->startOfMonth()->format('M d') }} &ndash; {{ now()->endOfMonth()->format('M d') }}</div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                Export CSV
            </button>
            <button class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20v-6M6 20v-10M18 20v-4"/></svg>
                Columns
            </button>
            <a href="{{ route('client.request.new') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                New order
            </a>
        </div>
    </div>

    {{-- Pipeline health strip --}}
    <div class="card" style="padding:0;">
        <div style="padding:18px 24px;display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;">
            <div>
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);margin-bottom:12px;">Pipeline health &middot; {{ $stats['active'] }} active candidates</div>
                <div style="display:flex;height:10px;border-radius:5px;overflow:hidden;gap:2px;">
                    @php
                        $total = max($stats['total'], 1);
                        $consentPct   = round($stats['consent']    / $total * 100);
                        $collectPct   = round($stats['collecting'] / $total * 100);
                        $reviewPct    = round($stats['review']     / $total * 100);
                        $completePct  = round($stats['complete']   / $total * 100);
                        $draftPct     = max(0, 100 - $consentPct - $collectPct - $reviewPct - $completePct);
                    @endphp
                    <div style="width:{{ $draftPct }}%;background:var(--ink-300);" title="New"></div>
                    <div style="width:{{ $consentPct }}%;background:var(--gold-500);" title="Awaiting consent"></div>
                    <div style="width:{{ $collectPct }}%;background:var(--emerald-500);" title="In progress"></div>
                    <div style="width:{{ $reviewPct }}%;background:var(--gold-600);" title="Needs review"></div>
                    <div style="width:{{ $completePct }}%;background:var(--emerald-700);" title="Complete"></div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:14px;font-size:11px;">
                    <div><span style="display:inline-block;width:8px;height:8px;background:var(--ink-300);border-radius:2px;margin-right:6px;vertical-align:middle;"></span>New <b style="margin-left:4px;">{{ $stats['consent'] }}</b></div>
                    <div><span style="display:inline-block;width:8px;height:8px;background:var(--gold-500);border-radius:2px;margin-right:6px;vertical-align:middle;"></span>Consent <b style="margin-left:4px;">{{ $stats['consent'] }}</b></div>
                    <div><span style="display:inline-block;width:8px;height:8px;background:var(--emerald-500);border-radius:2px;margin-right:6px;vertical-align:middle;"></span>In progress <b style="margin-left:4px;">{{ $stats['collecting'] }}</b></div>
                    <div><span style="display:inline-block;width:8px;height:8px;background:var(--gold-600);border-radius:2px;margin-right:6px;vertical-align:middle;"></span>Review <b style="margin-left:4px;">{{ $stats['review'] }}</b></div>
                    <div><span style="display:inline-block;width:8px;height:8px;background:var(--emerald-700);border-radius:2px;margin-right:6px;vertical-align:middle;"></span>Complete <b style="margin-left:4px;">{{ $stats['complete'] }}</b></div>
                </div>
            </div>
            <div style="display:flex;gap:24px;padding-left:24px;border-left:1px solid var(--line);">
                <div style="text-align:center;">
                    <div style="font-family:var(--font-display);font-size:30px;font-weight:500;letter-spacing:-0.02em;color:var(--emerald-700);">94%</div>
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);margin-top:4px;">On-SLA</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-family:var(--font-display);font-size:30px;font-weight:500;letter-spacing:-0.02em;color:var(--gold-700);">{{ $stats['review'] }}</div>
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);margin-top:4px;">At risk</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Candidates table card --}}
    <div class="card">
        <div class="card-head">
            <div style="display:flex;align-items:center;gap:12px;">
                <h3>All candidates</h3>
                <span class="count-pill">{{ $stats['total'] }} TOTAL</span>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <div class="search" style="width:240px;margin:0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input placeholder="Search candidate, ID, request&hellip;">
                </div>
                <div class="card-tabs">
                    <button class="active">Active</button>
                    <button>New</button>
                    <button>Complete</button>
                    <button>All</button>
                </div>
            </div>
        </div>

        <div class="filter-bar">
            <span class="chip on">All statuses <span class="n">{{ $stats['total'] }}</span></span>
            <span class="chip">Awaiting consent <span class="n">{{ $stats['consent'] }}</span></span>
            <span class="chip">Needs review <span class="n">{{ $stats['review'] }}</span></span>
            <span class="chip">In progress <span class="n">{{ $stats['collecting'] }}</span></span>
            <span class="chip">Complete <span class="n">{{ $stats['complete'] }}</span></span>
            <span style="margin-left:auto;font-size:11px;color:var(--ink-500);font-family:var(--font-mono);letter-spacing:0.05em;">SORT &middot; RECENT &#9662;</span>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:28px;">
                        <label class="cbox" style="margin:0;">
                            <input type="checkbox">
                            <span class="mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                        </label>
                    </th>
                    <th>Order</th>
                    <th>Candidate</th>
                    <th style="width:120px;">Package</th>
                    <th style="width:130px;">Stage</th>
                    <th style="width:140px;">Submitted</th>
                    <th style="width:110px;">Requester</th>
                    <th style="width:40px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidates as $candidate)
                    @php
                        $initials = collect(explode(' ', $candidate->name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->implode('');
                        $req = $candidate->screeningRequest;

                        $stageMap = [
                            'new'         => ['cls' => 'pill-review',   'text' => 'Awaiting consent', 'fill' => 'gold', 'pct' => 4],
                            'in_progress' => ['cls' => 'pill-progress', 'text' => 'In progress',       'fill' => '',     'pct' => 55],
                            'flagged'     => ['cls' => 'pill-review',   'text' => 'Needs review',      'fill' => 'gold', 'pct' => 80],
                            'complete'    => ['cls' => 'pill-clear',    'text' => 'Complete',           'fill' => '',     'pct' => 100],
                        ];
                        $stage = $stageMap[$candidate->status] ?? ['cls' => 'pill-pending', 'text' => 'Unknown', 'fill' => '', 'pct' => 0];

                        $pkgNames = ['Standard', 'Executive', 'Clinical', 'Basic'];
                        $pkg = $pkgNames[$candidate->id % 4];
                    @endphp
                    <tr onclick="location.href='{{ route('client.requests.details', $req->id) }}'">
                        <td onclick="event.stopPropagation()">
                            <label class="cbox" style="margin:0;">
                                <input type="checkbox">
                                <span class="mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                            </label>
                        </td>
                        <td>
                            <div style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:var(--ink-900);">{{ $req->reference }}</div>
                            <div style="font-size:10px;color:var(--ink-400);font-family:var(--font-mono);letter-spacing:0.05em;margin-top:2px;">{{ strtoupper($req->created_at->format('M d')) }} &middot; {{ $req->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="cand">
                                <div class="av">{{ $initials }}</div>
                                <div>
                                    <div class="name">{{ $candidate->name }}</div>
                                    <div class="role">{{ $candidate->identity_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-weight:600;">{{ $pkg }}</span></td>
                        <td>
                            <span class="pill {{ $stage['cls'] }}"><span class="dot"></span>{{ $stage['text'] }}</span>
                            <div class="tprogress" style="margin-top:6px;">
                                <div class="bar"><div class="fill {{ $stage['fill'] }}" style="width:{{ $stage['pct'] }}%;"></div></div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--ink-700);">{{ $req->created_at->format('d M Y') }}</td>
                        <td style="font-size:12px;color:var(--ink-700);">{{ $req->submittedBy?->name ?? '—' }}</td>
                        <td style="text-align:right;color:var(--ink-400);">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:48px 20px;text-align:center;">
                            <p style="font-size:13px;color:var(--ink-400);margin:0 0 8px;">No candidates yet.</p>
                            <a href="{{ route('client.request.new') }}" style="font-size:13px;font-weight:600;color:var(--emerald-700);">Submit your first screening request →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-top:1px solid var(--line);font-size:12px;color:var(--ink-500);">
            <span style="font-family:var(--font-mono);font-size:11px;letter-spacing:0.05em;">
                SHOWING {{ $candidates->firstItem() ?? 0 }}&ndash;{{ $candidates->lastItem() ?? 0 }} OF {{ $candidates->total() }}
            </span>
            <div style="display:flex;gap:4px;align-items:center;">
                @if ($candidates->onFirstPage())
                    <button class="icon-btn" style="width:28px;height:28px;border:1px solid var(--line);opacity:0.4;" disabled>
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                @else
                    <a href="{{ $candidates->previousPageUrl() }}" class="icon-btn" style="width:28px;height:28px;border:1px solid var(--line);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @endif

                @foreach ($candidates->getUrlRange(1, $candidates->lastPage()) as $page => $url)
                    @if ($page === $candidates->currentPage())
                        <button style="width:28px;height:28px;border-radius:var(--radius);border:1px solid var(--emerald-700);background:var(--emerald-50);color:var(--emerald-800);font-size:12px;font-weight:600;cursor:default;">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" style="width:28px;height:28px;border-radius:var(--radius);border:1px solid var(--line);background:var(--card);color:var(--ink-700);font-size:12px;display:grid;place-items:center;text-decoration:none;">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($candidates->hasMorePages())
                    <a href="{{ $candidates->nextPageUrl() }}" class="icon-btn" style="width:28px;height:28px;border:1px solid var(--line);">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @else
                    <button class="icon-btn" style="width:28px;height:28px;border:1px solid var(--line);opacity:0.4;" disabled>
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

</x-client.layouts.app>
