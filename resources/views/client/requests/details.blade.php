<x-client.layouts.app pageTitle="Request {{ $request->reference }}">

    @php
        $candidates = $request->candidates;
        $totalChecks = 0;
        $doneChecks = 0;
        $flaggedChecks = 0;
        foreach ($candidates as $c) {
            foreach ($c->scopeTypes as $s) {
                $totalChecks++;
                if ($s->pivot->status === 'complete') {
                    $doneChecks++;
                }
                if ($s->pivot->status === 'flagged') {
                    $flaggedChecks++;
                }
            }
        }
        $progressPct = $totalChecks > 0 ? round($doneChecks / $totalChecks * 100) : 0;

        $statusVerdict = match ($request->status) {
            'complete' => ['text' => 'Complete', 'cls' => 'clear'],
            'flagged' => ['text' => 'Needs review', 'cls' => 'flagged'],
            'in_progress' => ['text' => 'In progress', 'cls' => ''],
            'new' => ['text' => 'Awaiting consent', 'cls' => ''],
            default => ['text' => ucwords(str_replace('_', ' ', $request->status)), 'cls' => ''],
        };

        $typeLabel = match ($request->type) {
            'malaysia' => 'Malaysia Screening',
            'global' => 'Global Screening',
            'kyc' => 'KYC · Customer',
            'kyb' => 'KYB · Business',
            'kys' => 'KYS · Supplier',
            default => ucfirst((string) $request->type),
        };

        $elapsed = $request->created_at->diffForHumans(null, true);
        $dueDate = $request->created_at->copy()->addDays(5);
    @endphp

    {{-- Page head --}}
    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('client.requests.index') }}" class="case-back" aria-label="Back to requests">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Request</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $request->reference }}</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            @if ($request->status === 'complete')
                <button type="button" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                    Download report
                </button>
            @else
                <button type="button" class="btn btn-ghost">Request update</button>
            @endif
        </div>
    </div>

    {{-- Case hero --}}
    <div class="case-hero">
        <div>
            <div class="case-id">
                <span>REQUEST · {{ $request->reference }}</span>
                <span class="chip">{{ strtoupper($typeLabel) }}</span>
            </div>
            <div class="case-name">{{ $candidates->count() }} {{ Str::plural('candidate', $candidates->count()) }}</div>
            <div class="case-meta">
                <span>Submitted <b>{{ $request->created_at->format('d M Y') }}</b></span>
                <span>by <b>{{ $request->submittedBy?->name ?? '—' }}</b></span>
                <span>Due <b>{{ $dueDate->format('d M Y') }}</b></span>
                <span>{{ $doneChecks }} of {{ $totalChecks }} {{ Str::plural('check', $totalChecks) }} complete</span>
            </div>
        </div>

        <div class="case-verdict">
            <div class="verdict-label">Status</div>
            <div class="verdict-value {{ $statusVerdict['cls'] }}">{{ $statusVerdict['text'] }}</div>
            <div style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);margin-top:4px;">{{ $progressPct }}% complete</div>
        </div>

        @if ($request->status === 'flagged' || $flaggedChecks > 0)
            <div class="case-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                <div>
                    <b>{{ $flaggedChecks > 0 ? $flaggedChecks : 'One or more' }} {{ Str::plural('check', max($flaggedChecks, 1)) }} flagged</b>
                    — analyst review pending. You'll be notified once resolved.
                </div>
            </div>
        @endif
    </div>

    {{-- Summary rail --}}
    <div class="summary-rail">
        <div class="summary-cell">
            <div class="l">Candidates</div>
            <div class="v">{{ $candidates->count() }}</div>
        </div>
        <div class="summary-cell">
            <div class="l">Checks complete</div>
            <div class="v">{{ $doneChecks }} <span style="color:var(--ink-400);font-size:16px;">of {{ $totalChecks }}</span></div>
        </div>
        <div class="summary-cell">
            <div class="l">Elapsed</div>
            <div class="v">{{ $elapsed }}</div>
        </div>
        <div class="summary-cell">
            <div class="l">Flagged</div>
            <div class="v" style="{{ $flaggedChecks > 0 ? 'color:var(--gold-700);' : '' }}">{{ $flaggedChecks }}</div>
        </div>
    </div>

    {{-- Detail grid --}}
    <div class="case-grid">

        {{-- Candidates list --}}
        <div class="card">
            <div class="card-head">
                <h3>Candidates</h3>
                <span class="count-pill">{{ $candidates->count() }} TOTAL</span>
            </div>

            <div class="checks-list">
                @forelse ($candidates as $candidate)
                    @php
                        $candDone = $candidate->scopeTypes->where('pivot.status', 'complete')->count();
                        $candTotal = $candidate->scopeTypes->count();
                        $candFlagged = $candidate->scopeTypes->where('pivot.status', 'flagged')->count();
                        $candProgress = $candTotal > 0 ? round($candDone / $candTotal * 100) : 0;

                        $candPill = match ($candidate->status) {
                            'complete' => ['cls' => 'pill-clear', 'txt' => 'Cleared'],
                            'flagged' => ['cls' => 'pill-flagged', 'txt' => 'Flagged'],
                            'in_progress' => ['cls' => 'pill-progress', 'txt' => 'In progress'],
                            default => ['cls' => 'pill-pending', 'txt' => 'Pending'],
                        };
                    @endphp
                    <a href="{{ route('client.candidates.show', $candidate->id) }}"
                       class="check-row" style="text-decoration:none;color:inherit;">
                        <div style="width:36px;height:36px;border-radius:50%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:11px;font-weight:600;font-family:var(--font-mono);box-shadow:inset 0 0 0 1px rgba(212,175,55,0.4);flex-shrink:0;">
                            {{ strtoupper(substr($candidate->name, 0, 2)) }}
                        </div>
                        <div class="check-info">
                            <div class="t">{{ $candidate->name }}</div>
                            <div class="s">
                                {{ $candidate->identityType?->name ?? 'ID' }}
                                <span class="src">{{ $candidate->identity_number }}</span>
                                @if ($candTotal > 0)
                                    · {{ $candDone }}/{{ $candTotal }} checks
                                @endif
                            </div>
                        </div>
                        <span class="pill {{ $candPill['cls'] }}"><span class="dot"></span>{{ $candPill['txt'] }}</span>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div style="width:80px;height:6px;background:var(--paper-2);border-radius:3px;overflow:hidden;">
                                <div style="width:{{ $candProgress }}%;height:100%;background:{{ $candidate->status === 'flagged' ? 'var(--gold-500)' : 'var(--emerald-600)' }};"></div>
                            </div>
                            <div class="check-chev">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="padding:48px 20px;text-align:center;">
                        <p style="font-size:13px;color:var(--ink-400);margin:0;">No candidates on this request.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right col --}}
        <div class="side-col">

            {{-- Request meta --}}
            <div class="card">
                <div class="card-head">
                    <h3>Request info</h3>
                </div>
                <div class="identity">
                    <div class="id-row">
                        <span class="k">Reference</span>
                        <span class="v">{{ $request->reference }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Type</span>
                        <span class="v" style="font-family:var(--font-ui);font-weight:600;white-space:normal;">{{ $typeLabel }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Submitted by</span>
                        <span class="v" style="font-family:var(--font-ui);font-weight:600;white-space:normal;">{{ $request->submittedBy?->name ?? '—' }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Submitted</span>
                        <span class="v">{{ $request->created_at->format('d M Y · H:i') }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Due date</span>
                        <span class="v">{{ $dueDate->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Verification scopes --}}
            @php
                $allScopes = $candidates->flatMap(fn ($c) => $c->scopeTypes)->unique('id');
            @endphp
            @if ($allScopes->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>Verification scopes</h3>
                        <span class="count-pill">{{ $allScopes->count() }}</span>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach ($allScopes as $scope)
                            <span class="pill pill-pending" style="font-family:var(--font-ui);">{{ $scope->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-client.layouts.app>
