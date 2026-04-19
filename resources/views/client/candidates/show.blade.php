<x-client.layouts.app pageTitle="{{ $candidate->name }}">

    @php
        $req     = $candidate->screeningRequest;
        $initials = collect(explode(' ', $candidate->name))->map(fn ($p) => strtoupper(substr($p, 0, 1)))->take(2)->implode('');
        $pkgNames = ['Standard', 'Executive', 'Clinical', 'Basic'];
        $pkg      = $pkgNames[$candidate->id % 4];

        $verdictMap = [
            'complete'    => ['text' => 'Cleared',        'cls' => 'clear'],
            'flagged'     => ['text' => 'Needs review',   'cls' => ''],
            'in_progress' => ['text' => 'In progress',    'cls' => ''],
            'new'         => ['text' => 'Awaiting consent', 'cls' => ''],
        ];
        $verdict = $verdictMap[$candidate->status] ?? ['text' => 'Pending', 'cls' => ''];

        $scopeTypes  = $candidate->scopeTypes;
        $checksTotal = $scopeTypes->count();
        $checksDone  = $scopeTypes->filter(fn ($s) => $s->pivot->status === 'complete')->count();
        $elapsed     = $candidate->created_at->diffForHumans(null, true);
    @endphp

    {{-- Page head --}}
    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('client.candidates') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Candidates</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $candidate->name }}</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-ghost">Request info</button>
            <button class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                Mark clear
            </button>
        </div>
    </div>

    {{-- Case hero --}}
    <div class="case-hero">
        <div>
            <div class="case-id">
                <span>CASE · {{ $req->reference }}</span>
                <span class="chip">{{ strtoupper($pkg) }} · TIER III</span>
            </div>
            <div class="case-name">{{ $candidate->name }}</div>
            <div class="case-meta">
                <span>IC No. <b>{{ $candidate->identity_number }}</b></span>
                <span>Ordered <b>{{ $req->created_at->format('M d, Y') }}</b></span>
                <span>Due <b>{{ $req->created_at->addDays(5)->format('M d, Y') }}</b></span>
                <span>Consent on file · <b>{{ $candidate->mobile ? 'mobile' : 'on record' }}</b></span>
            </div>
        </div>

        <div class="case-verdict">
            <div class="verdict-label">Provisional verdict</div>
            <div class="verdict-value {{ $verdict['cls'] }}">{{ $verdict['text'] }}</div>
            <div class="verdict-actions">
                <button class="btn btn-ghost">Request info</button>
                <button class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5"/></svg>
                    Mark clear
                </button>
            </div>
        </div>

        @if ($candidate->status === 'flagged')
            <div class="case-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                <div>
                    <b>Flagged for review</b> — This candidate has been flagged and requires manual review before a verdict can be issued.
                </div>
                <div class="spacer"></div>
                <a>Review flags →</a>
            </div>
        @endif
    </div>

    {{-- Summary rail --}}
    <div class="summary-rail">
        <div class="summary-cell">
            <div class="l">Checks complete</div>
            <div class="v">{{ $checksDone }} <span style="color:var(--ink-400);font-size:16px;">of {{ $checksTotal }}</span></div>
        </div>
        <div class="summary-cell">
            <div class="l">Elapsed</div>
            <div class="v">{{ $elapsed }}</div>
        </div>
        <div class="summary-cell">
            <div class="l">Requester</div>
            <div class="v mono">{{ $req->submittedBy?->name ?? 'N/A' }}</div>
        </div>
        <div class="summary-cell">
            <div class="l">Risk score</div>
            @if ($candidate->status === 'flagged')
                <div class="v" style="color:var(--gold-700);">Moderate <span style="color:var(--ink-400);font-size:14px;font-family:var(--font-mono);">· 42/100</span></div>
            @elseif ($candidate->status === 'complete')
                <div class="v" style="color:var(--emerald-700);">Low <span style="color:var(--ink-400);font-size:14px;font-family:var(--font-mono);">· 8/100</span></div>
            @else
                <div class="v" style="color:var(--ink-500);">Pending</div>
            @endif
        </div>
    </div>

    {{-- Detail grid --}}
    <div class="case-grid">

        {{-- Checks list --}}
        <div class="card">
            <div class="card-head">
                <h3>Check results</h3>
                <div class="card-tabs">
                    <button class="active">All</button>
                    <button>Flagged <span style="color:var(--danger);margin-left:4px;">●</span></button>
                    <button>Cleared</button>
                    <button>Pending</button>
                </div>
            </div>

            <div class="checks-list">
                @if ($scopeTypes->count())
                    @foreach ($scopeTypes as $scope)
                        @php
                            $checkStatus = $scope->pivot->status;
                            $iconCls = match($checkStatus) {
                                'complete' => '',
                                'flagged'  => 'flag',
                                default    => '',
                            };
                            $pillCls = match($checkStatus) {
                                'complete'    => 'pill-clear',
                                'flagged'     => 'pill-flagged',
                                'in_progress' => 'pill-progress',
                                default       => 'pill-pending',
                            };
                            $pillTxt = match($checkStatus) {
                                'complete'    => 'Cleared',
                                'flagged'     => 'Flagged',
                                'in_progress' => 'In progress',
                                default       => 'Pending',
                            };
                        @endphp
                        <div class="check-row" onclick="toggleCheck(this)">
                            <div class="check-icon {{ $iconCls }}">
                                @if ($checkStatus === 'complete')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                @elseif ($checkStatus === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @elseif ($checkStatus === 'in_progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                @endif
                            </div>
                            <div class="check-info">
                                <div class="t">{{ $scope->name }}</div>
                                <div class="s">
                                    <span class="pill {{ $pillCls }}" style="font-size:10px;padding:1px 6px;"><span class="dot"></span>{{ $pillTxt }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ $scope->turnaround }}</span>
                                    @if ($scope->description)
                                        <span class="sep">·</span>
                                        <span class="src">{{ strtoupper(Str::limit($scope->description, 40)) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="check-time">
                                {{ strtoupper($candidate->updated_at->format('M d · H:i')) }}
                            </div>
                            <div class="check-chev">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                            </div>
                        </div>
                        <div class="check-body">
                            <div class="check-body-inner">
                                <div>
                                    <div class="detail-label">Scope type</div>
                                    <div class="detail-value">{{ $scope->name }}</div>
                                </div>
                                <div>
                                    <div class="detail-label">Status</div>
                                    <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $checkStatus)) }}</div>
                                </div>
                                @if ($scope->description)
                                    <div style="grid-column:1/-1;">
                                        <div class="detail-label">Description</div>
                                        <div class="detail-value">{{ $scope->description }}</div>
                                    </div>
                                @endif
                                @if ($checkStatus === 'flagged')
                                    <div class="detail-note">
                                        <b>Manual review required</b> — This check has been flagged and requires analyst review before the case can be closed.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Placeholder checks when no scope types linked --}}
                    @php
                        $placeholderChecks = [
                            ['title' => 'Criminal records',           'icon_cls' => '',       'status' => 'complete',    'pill' => 'pill-clear',    'pill_txt' => 'Cleared',     'turnaround' => 'County + State + Federal'],
                            ['title' => 'Education verification',     'icon_cls' => 'review', 'status' => 'flagged',     'pill' => 'pill-flagged',  'pill_txt' => 'Flagged',     'turnaround' => '2 institutions'],
                            ['title' => 'Employment verification',    'icon_cls' => '',       'status' => 'complete',    'pill' => 'pill-clear',    'pill_txt' => 'Cleared',     'turnaround' => '3 of 3 employers confirmed'],
                            ['title' => 'Credit report',              'icon_cls' => 'review', 'status' => 'flagged',     'pill' => 'pill-review',   'pill_txt' => 'Needs review','turnaround' => 'Required for fiduciary role'],
                            ['title' => 'OFAC / Watchlist / Sanctions','icon_cls' => '',      'status' => 'complete',    'pill' => 'pill-clear',    'pill_txt' => 'Cleared',     'turnaround' => '53 global lists'],
                            ['title' => 'Drug screening',             'icon_cls' => '',       'status' => 'in_progress', 'pill' => 'pill-progress', 'pill_txt' => 'In progress', 'turnaround' => '10-panel · Lab pending'],
                            ['title' => 'Social media scan',          'icon_cls' => '',       'status' => 'new',         'pill' => 'pill-pending',  'pill_txt' => 'Pending',     'turnaround' => '5-platform AI review'],
                        ];
                    @endphp
                    @foreach ($placeholderChecks as $i => $check)
                        <div class="check-row" onclick="toggleCheck(this)">
                            <div class="check-icon {{ $check['icon_cls'] }}">
                                @if ($check['status'] === 'complete')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                @elseif ($check['status'] === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @elseif ($check['status'] === 'in_progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                @endif
                            </div>
                            <div class="check-info">
                                <div class="t">{{ $check['title'] }}</div>
                                <div class="s">
                                    <span class="pill {{ $check['pill'] }}" style="font-size:10px;padding:1px 6px;"><span class="dot"></span>{{ $check['pill_txt'] }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ $check['turnaround'] }}</span>
                                </div>
                            </div>
                            <div class="check-time">
                                {{ strtoupper($candidate->updated_at->format('M d · H:i')) }}
                            </div>
                            <div class="check-chev">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                            </div>
                        </div>
                        <div class="check-body">
                            <div class="check-body-inner">
                                <div>
                                    <div class="detail-label">Check type</div>
                                    <div class="detail-value">{{ $check['title'] }}</div>
                                </div>
                                <div>
                                    <div class="detail-label">Status</div>
                                    <div class="detail-value">{{ $check['pill_txt'] }}</div>
                                </div>
                                @if ($check['status'] === 'flagged')
                                    <div class="detail-note">
                                        <b>Manual review required</b> — This check has been flagged and requires analyst review before the case can be closed.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Right col --}}
        <div class="side-col">

            {{-- Identity --}}
            <div class="card">
                <div class="card-head">
                    <h3>Identity</h3>
                    <span class="count-pill">{{ $candidate->status === 'complete' ? 'VERIFIED' : 'PENDING' }}</span>
                </div>
                <div class="identity">
                    <div class="id-row">
                        <span class="k">Legal name</span>
                        <span class="v" style="font-family:var(--font-ui);font-weight:600;white-space:normal;">{{ $candidate->name }}</span>
                        <span class="match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                    </div>
                    <div class="id-row">
                        <span class="k">{{ $candidate->identityType?->name ?? 'ID' }}</span>
                        <span class="v">{{ $candidate->identity_number }}</span>
                        <span class="match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                    </div>
                    @if ($candidate->mobile)
                        <div class="id-row">
                            <span class="k">Mobile</span>
                            <span class="v" style="font-family:var(--font-ui);">{{ $candidate->mobile }}</span>
                            <span class="match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                        </div>
                    @endif
                    <div class="id-row">
                        <span class="k">Order ref.</span>
                        <span class="v">{{ $req->reference }}</span>
                        <span class="match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                    </div>
                    @if ($candidate->remarks)
                        <div class="id-row">
                            <span class="k">Remarks</span>
                            <span class="v" style="font-family:var(--font-ui);font-size:11px;white-space:normal;">{{ $candidate->remarks }}</span>
                            <span class="match warn">!</span>
                        </div>
                    @endif
                    <div class="id-row">
                        <span class="k">Package</span>
                        <span class="v" style="font-family:var(--font-ui);font-weight:600;">{{ $pkg }}</span>
                        <span class="match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
                    </div>
                </div>
            </div>

            {{-- Case timeline --}}
            <div class="card">
                <div class="card-head">
                    <h3>Case timeline</h3>
                </div>
                <div class="timeline">
                    @if ($candidate->status === 'flagged')
                        <div class="tl-item gold">
                            <div class="tl-time">{{ strtoupper($candidate->updated_at->format('M d · H:i')) }}</div>
                            <div class="tl-title">Candidate flagged for review</div>
                            <div class="tl-desc">Auto-flag triggered · queued for analyst.</div>
                        </div>
                    @elseif ($candidate->status === 'complete')
                        <div class="tl-item">
                            <div class="tl-time">{{ strtoupper($candidate->updated_at->format('M d · H:i')) }}</div>
                            <div class="tl-title">All checks cleared</div>
                            <div class="tl-desc">Screening complete · verdict: clear.</div>
                        </div>
                    @elseif ($candidate->status === 'in_progress')
                        <div class="tl-item">
                            <div class="tl-time">{{ strtoupper($candidate->updated_at->format('M d · H:i')) }}</div>
                            <div class="tl-title">Checks in progress</div>
                            <div class="tl-desc">Verification underway across all assigned checks.</div>
                        </div>
                    @endif
                    <div class="tl-item">
                        <div class="tl-time">{{ strtoupper($req->created_at->format('M d · H:i')) }}</div>
                        <div class="tl-title">Consent received</div>
                        <div class="tl-desc">Candidate record submitted · checks initiated.</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-time">{{ strtoupper($req->created_at->subMinutes(8)->format('M d · H:i')) }}</div>
                        <div class="tl-title">Order created</div>
                        <div class="tl-desc">by {{ $req->submittedBy?->name ?? 'requester' }} · {{ $pkg }} package.</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-client.layouts.app>

@push('scripts')
<script>
function toggleCheck(row) {
    const body = row.nextElementSibling;
    const isOpen = row.classList.contains('open');
    // close all
    document.querySelectorAll('.check-row.open').forEach(r => {
        r.classList.remove('open');
        r.nextElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        row.classList.add('open');
        body.classList.add('open');
    }
}
</script>
@endpush
