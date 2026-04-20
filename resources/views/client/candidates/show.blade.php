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

            <div class="checks-list" id="checksList">
                @if ($scopeTypes->count())
                    @foreach ($scopeTypes as $scope)
                        @php
                            $checkStatus = $scope->pivot->status;
                            $iconCls = match($checkStatus) {
                                'flagged'  => 'flag',
                                'review'   => 'review',
                                default    => '',
                            };
                            $pillCls = match($checkStatus) {
                                'complete'    => 'pill-clear',
                                'flagged'     => 'pill-flagged',
                                'review'      => 'pill-review',
                                'in_progress' => 'pill-progress',
                                default       => 'pill-pending',
                            };
                            $pillTxt = match($checkStatus) {
                                'complete'    => 'Cleared',
                                'flagged'     => 'Flagged',
                                'review'      => 'Needs review',
                                'in_progress' => 'In progress',
                                default       => 'Pending',
                            };
                        @endphp
                        <div class="check-row" data-idx="{{ $loop->index }}">
                            <div class="check-icon {{ $iconCls }}">
                                @if ($checkStatus === 'flagged')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                                @elseif ($checkStatus === 'in_progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                @endif
                            </div>
                            <div class="check-info">
                                <div class="t">{{ $scope->name }}</div>
                                <div class="s">
                                    {{-- subtitle text --}}
                                    @if ($scope->description){{ $scope->description }} · @endif<span class="src">{{ strtoupper($scope->name) }}</span>
                                </div>
                            </div>
                            <span class="pill {{ $pillCls }}"><span class="dot"></span>{{ $pillTxt }}</span>
                            <div style="display:flex;align-items:center;gap:14px;">
                                <div class="check-time">{{ $candidate->updated_at->format('M d · H:i') }}<br>{{ $candidate->created_at->diffForHumans($candidate->updated_at, true) }}</div>
                                <div class="check-chev">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="check-body" data-body="{{ $loop->index }}">
                            <div class="check-body-inner">
                                <div>
                                    <div class="detail-label">Check type</div>
                                    <div class="detail-value">{{ $scope->name }}</div>
                                </div>
                                <div>
                                    <div class="detail-label">Status</div>
                                    <div class="detail-value">{{ $pillTxt }}</div>
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
                    {{-- Placeholder checks matching design exactly --}}

                    {{-- 1. Criminal records — pass --}}
                    <div class="check-row" data-idx="0">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3 6 6 .9-4.5 4.2 1 6.4L12 16.8 6.5 19.5l1-6.4L3 8.9l6-.9z"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Criminal records</div>
                            <div class="s">County + State + Federal · 9 jurisdictions · <span class="src">SRC: LEXIS · NCRA · PACER</span></div>
                        </div>
                        <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 18 · 17:40<br>Returned 4h 20m</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="0">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Result</div><div class="detail-value">No records found across 9 county and 3 federal jurisdictions covering the last 7 years.</div></div>
                            <div><div class="detail-label">Jurisdictions searched</div><div class="detail-value mono">Buncombe NC · Mecklenburg NC · Henderson NC · Madison NC · Hamilton TN · Cook IL · Multnomah OR · Federal 4C · Federal 9C</div></div>
                            <div class="detail-note">Analyst note — Cross-referenced against 7 prior addresses on file. Name-variant search returned nil.</div>
                        </div>
                    </div>

                    {{-- 2. Education verification — review --}}
                    <div class="check-row" data-idx="1">
                        <div class="check-icon review">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Education verification</div>
                            <div class="s">2 institutions · <span class="src">SRC: NATIONAL STUDENT CLEARINGHOUSE</span></div>
                        </div>
                        <span class="pill pill-review"><span class="dot"></span>Needs review</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 19 · 09:14<br>Flagged 3h 12m ago</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="1">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Claimed</div><div class="detail-value mono">M.B.A · Duke Fuqua · Class of 2014</div></div>
                            <div><div class="detail-label">Verified</div><div class="detail-value mono" style="color:var(--danger);">M.B.A · Duke Fuqua · Class of 2015</div></div>
                            <div class="detail-note"><b>Discrepancy:</b> Graduation year differs by one year. Candidate may have completed coursework in 2014 but received degree at May 2015 ceremony — common for executive MBA programs. Recommend requesting clarification before adverse action.</div>
                        </div>
                    </div>

                    {{-- 3. Employment verification — pass --}}
                    <div class="check-row" data-idx="2">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M2 11h20"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Employment verification</div>
                            <div class="s">3 of 3 employers confirmed · <span class="src">SRC: THE WORK NUMBER · DIRECT HR</span></div>
                        </div>
                        <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 18 · 15:10<br>Returned 1d 20h</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="2">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Verified employers</div><div class="detail-value mono">Meridian Capital (2019–present) · Northbrook Advisory (2015–2019) · Ernst &amp; Young (2012–2015)</div></div>
                            <div><div class="detail-label">Titles match</div><div class="detail-value" style="color:var(--emerald-700);"><b>3 / 3</b> exact matches</div></div>
                        </div>
                    </div>

                    {{-- 4. Credit report — review --}}
                    <div class="check-row" data-idx="3">
                        <div class="check-icon review">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 5-6"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Credit report</div>
                            <div class="s">Required for fiduciary role · <span class="src">SRC: EXPERIAN · EQUIFAX</span></div>
                        </div>
                        <span class="pill pill-review"><span class="dot"></span>Needs review</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 19 · 08:02<br>Returned 5h ago</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="3">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Summary</div><div class="detail-value">Credit file active 18y. 1 derogatory item — paid collection, 2017. Outside 7-yr FCRA reporting window for adverse consideration.</div></div>
                            <div><div class="detail-label">Action required</div><div class="detail-value" style="color:var(--emerald-700);">None · within compliance window</div></div>
                        </div>
                    </div>

                    {{-- 5. OFAC / Watchlist / Sanctions — pass --}}
                    <div class="check-row" data-idx="4">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">OFAC / Watchlist / Sanctions</div>
                            <div class="s">53 global lists · <span class="src">SRC: DOW JONES RISK · OFAC · UN · EU</span></div>
                        </div>
                        <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 18 · 10:21<br>Returned 2h 40m</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="4">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Hits</div><div class="detail-value" style="color:var(--emerald-700);"><b>0</b> matches</div></div>
                            <div><div class="detail-label">Lists scanned</div><div class="detail-value mono">OFAC SDN · OFAC Non-SDN · UN Sanctions · EU Consolidated · UK HMT · FBI · Interpol · PEP (+46 more)</div></div>
                        </div>
                    </div>

                    {{-- 6. Professional license — pass --}}
                    <div class="check-row" data-idx="5">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Professional license</div>
                            <div class="s">CFA Charter · <span class="src">SRC: CFA INSTITUTE</span></div>
                        </div>
                        <span class="pill pill-clear"><span class="dot"></span>Cleared</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Apr 18 · 11:30<br>Returned 3h</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="5">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Credential</div><div class="detail-value mono">CFA Charter · Active · Issued 2018</div></div>
                            <div><div class="detail-label">Member #</div><div class="detail-value mono">CFA-████-4412</div></div>
                        </div>
                    </div>

                    {{-- 7. Drug screening — in progress --}}
                    <div class="check-row" data-idx="6">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8.5 14.5a5 5 0 0 1 7-7M15.5 9.5a5 5 0 0 1-7 7"/><path d="M4 20l3-3M17 7l3-3"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Drug screening</div>
                            <div class="s">10-panel · Lab pending · <span class="src">SRC: LABCORP</span></div>
                        </div>
                        <span class="pill pill-progress"><span class="dot"></span>In progress</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Collected Apr 18<br>ETA Apr 20</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="6">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Status</div><div class="detail-value">Specimen received at lab · under analysis</div></div>
                            <div><div class="detail-label">Chain of custody</div><div class="detail-value mono" style="color:var(--emerald-700);">Intact · seal verified</div></div>
                        </div>
                    </div>

                    {{-- 8. Social media scan — pending --}}
                    <div class="check-row" data-idx="7">
                        <div class="check-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M6.3 6.3l-1.5-1.5M19.2 19.2l-1.5-1.5M6.3 17.7l-1.5 1.5M19.2 4.8l-1.5 1.5"/></svg>
                        </div>
                        <div class="check-info">
                            <div class="t">Social media scan</div>
                            <div class="s">5-platform AI review · <span class="src">SRC: FAMA</span></div>
                        </div>
                        <span class="pill pill-pending"><span class="dot"></span>Pending</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="check-time">Queued<br>ETA Apr 21</div>
                            <div class="check-chev"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg></div>
                        </div>
                    </div>
                    <div class="check-body" data-body="7">
                        <div class="check-body-inner">
                            <div><div class="detail-label">Scope</div><div class="detail-value">LinkedIn · X · Facebook · Instagram · Reddit · 10-year look-back for violence, drug-use, explicit, or discriminatory content.</div></div>
                        </div>
                    </div>
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

    @push('scripts')
    <script>
(function () {
    const list = document.getElementById('checksList');
    if (!list) { return; }

    list.querySelectorAll('.check-row').forEach(row => {
        row.addEventListener('click', () => {
            const idx  = row.dataset.idx;
            const body = list.querySelector(`[data-body="${idx}"]`);
            const isOpen = row.classList.contains('open');
            list.querySelectorAll('.check-row.open').forEach(r => r.classList.remove('open'));
            list.querySelectorAll('.check-body.open').forEach(b => b.classList.remove('open'));
            if (!isOpen) {
                row.classList.add('open');
                body.classList.add('open');
            }
        });
    });

    // open the first non-cleared check by default
    const firstFlagged = list.querySelector('.check-row[data-idx="1"]');
    if (firstFlagged) { firstFlagged.click(); }
})();
    </script>
    @endpush

</x-client.layouts.app>
