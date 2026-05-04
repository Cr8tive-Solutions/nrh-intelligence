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
            'complete' => ['text' => 'Complete', 'cls' => 'pill-clear'],
            'flagged' => ['text' => 'Needs review', 'cls' => 'pill-flagged'],
            'in_progress' => ['text' => 'In progress', 'cls' => 'pill-progress'],
            'new' => ['text' => 'Awaiting consent', 'cls' => 'pill-pending'],
            default => ['text' => ucwords(str_replace('_', ' ', $request->status)), 'cls' => 'pill-pending'],
        };

        $typeLabel = match ($request->type) {
            'malaysia' => 'Malaysia Screening',
            'global' => 'Global Screening',
            'kyc' => 'KYC · Customer',
            'kyb' => 'KYB · Business',
            'kys' => 'KYS · Supplier',
            default => ucfirst((string) $request->type),
        };

        $dueDate = $request->created_at->copy()->addDays(5);

        // Pipeline stages — derive completion from request status
        $stageMap = [
            'submitted' => 1,
            'consent' => 2,
            'verifying' => 3,
            'review' => 4,
            'complete' => 5,
        ];
        $currentStage = match ($request->status) {
            'new' => 2,           // submitted, awaiting consent
            'in_progress' => 3,   // verifying
            'flagged' => 4,       // analyst review
            'complete' => 5,
            default => 1,
        };
        $isFlagged = $request->status === 'flagged' || $flaggedChecks > 0;
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

    {{-- Banner --}}
    <div class="request-banner">
        <div>
            <div class="ref">
                <span>REF</span>
                <b>{{ $request->reference }}</b>
                <span style="padding:2px 8px;background:var(--gold-100);color:var(--gold-700);border-radius:3px;font-size:10px;font-weight:600;text-transform:uppercase;">{{ $typeLabel }}</span>
            </div>
            <div class="meta">
                <span>Submitted <b>{{ $request->created_at->format('d M Y') }}</b></span>
                <span>by <b>{{ $request->submittedBy?->name ?? '—' }}</b></span>
                <span>Due <b>{{ $dueDate->format('d M Y') }}</b></span>
                <span>{{ $candidates->count() }} {{ Str::plural('candidate', $candidates->count()) }} · {{ $doneChecks }}/{{ $totalChecks }} {{ Str::plural('check', $totalChecks) }}</span>
            </div>
        </div>
        <div class="verdict">
            <span class="pill {{ $statusVerdict['cls'] }}"><span class="dot"></span>{{ $statusVerdict['text'] }}</span>
            <span style="font-family:var(--font-mono);font-size:11px;color:var(--ink-500);">{{ $progressPct }}% COMPLETE</span>
        </div>
    </div>

    {{-- Pipeline tracker --}}
    <div class="tracker" style="--steps: 5;">
        <div class="tracker-rail">
            @php
                $stages = [
                    ['key' => 'submitted', 'label' => 'Submitted', 'when' => $request->created_at->format('d M')],
                    ['key' => 'consent',   'label' => 'Consent',   'when' => $currentStage >= 2 ? $request->created_at->copy()->addHours(2)->format('d M') : ''],
                    ['key' => 'verifying', 'label' => 'Verifying', 'when' => $currentStage >= 3 ? $request->created_at->copy()->addDay()->format('d M') : ''],
                    ['key' => 'review',    'label' => 'Review',    'when' => $currentStage >= 4 ? $request->updated_at->format('d M') : ''],
                    ['key' => 'complete',  'label' => 'Complete',  'when' => $currentStage >= 5 ? $request->updated_at->format('d M') : 'Est. ' . $dueDate->format('d M')],
                ];
            @endphp
            @foreach ($stages as $i => $stage)
                @php
                    $stageNum = $i + 1;
                    $cls = '';
                    if ($stageNum < $currentStage) {
                        $cls = 'is-done';
                    } elseif ($stageNum === $currentStage) {
                        $cls = $isFlagged && $stage['key'] === 'review' ? 'is-flagged' : 'is-current';
                    }
                @endphp
                <div class="tracker-step {{ $cls }}">
                    <div class="dot">
                        @if ($stageNum < $currentStage)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                        @elseif ($stageNum === $currentStage && $isFlagged && $stage['key'] === 'review')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 9v4M12 17h.01"/></svg>
                        @elseif ($stageNum === $currentStage)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>
                        @endif
                    </div>
                    <div class="label">{{ $stage['label'] }}</div>
                    @if (! empty($stage['when']))
                        <div class="when">{{ $stage['when'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($isFlagged)
            <div style="margin-top:18px;padding:10px 14px;background:var(--gold-100);border:1px solid rgba(184,147,31,0.2);border-left:3px solid var(--gold-500);border-radius:6px;display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gold-700);">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M12 2L2 22h20z"/></svg>
                <span><b>{{ $flaggedChecks > 0 ? $flaggedChecks : 'One or more' }} {{ Str::plural('check', max($flaggedChecks, 1)) }} flagged</b> — analyst review pending. You'll be notified once resolved.</span>
            </div>
        @endif
    </div>

    {{-- Candidates + sidebar --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

        {{-- Candidates as cards --}}
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin:6px 0 12px;">
                <h2 style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0;">Candidates <span style="color:var(--ink-400);font-weight:400;">· {{ $candidates->count() }}</span></h2>
            </div>

            @if ($candidates->isEmpty())
                <div class="card" style="padding:48px 20px;text-align:center;">
                    <p style="font-size:13px;color:var(--ink-400);margin:0;">No candidates on this request.</p>
                </div>
            @else
                <div class="cand-grid">
                    @foreach ($candidates as $candidate)
                        @php
                            $candDone = $candidate->scopeTypes->where('pivot.status', 'complete')->count();
                            $candTotal = $candidate->scopeTypes->count();
                            $candProgress = $candTotal > 0 ? round($candDone / $candTotal * 100) : 0;
                            $candPill = match ($candidate->status) {
                                'complete' => ['cls' => 'pill-clear', 'txt' => 'Cleared'],
                                'flagged' => ['cls' => 'pill-flagged', 'txt' => 'Flagged'],
                                'in_progress' => ['cls' => 'pill-progress', 'txt' => 'In progress'],
                                default => ['cls' => 'pill-pending', 'txt' => 'Pending'],
                            };
                        @endphp
                        <a href="{{ route('client.candidates.show', $candidate->id) }}" class="cand-card {{ $candidate->status === 'flagged' ? 'is-flagged' : '' }}">
                            @php
                                $isRedacted = $candidate->isRedacted();
                                $avatarTxt = $isRedacted ? '··' : strtoupper(substr($candidate->name, 0, 2));
                            @endphp
                            <div class="head">
                                <div class="avatar" @if($isRedacted) style="background:var(--paper-2);color:var(--ink-400);" @endif>{{ $avatarTxt }}</div>
                                <div style="min-width:0;flex:1;">
                                    <div class="name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;{{ $isRedacted ? 'color:var(--ink-400);font-style:italic;' : '' }}">{{ $isRedacted ? 'Candidate erased' : $candidate->name }}</div>
                                    <div class="id">{{ $isRedacted ? 'Data erased '.$candidate->redacted_at->format('d M Y') : $candidate->identity_number }}</div>
                                </div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $candProgress }}%;"></div>
                            </div>
                            <div class="row">
                                <span>{{ $candDone }}/{{ $candTotal }} checks</span>
                                <span class="pill {{ $candPill['cls'] }}"><span class="dot"></span>{{ $candPill['txt'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sidebar: meta + scopes --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                <div class="card-head">
                    <h3>Order details</h3>
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

            @php $allScopes = $candidates->flatMap(fn ($c) => $c->scopeTypes)->unique('id'); @endphp
            @if ($allScopes->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>Scope of work</h3>
                        <span class="count-pill">{{ $allScopes->count() }}</span>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px;">
                        @foreach ($allScopes as $scope)
                            <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ink-700);">
                                <svg style="width:12px;height:12px;color:var(--emerald-600);flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M9 12l2 2 4-4"/></svg>
                                {{ $scope->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($request->currentReportVersions->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>Reports</h3>
                        <span class="count-pill">{{ $request->currentReportVersions->count() }}</span>
                    </div>
                    <div style="padding:6px 0;">
                        @foreach ($request->currentReportVersions as $rv)
                            <a href="{{ route('client.requests.reports.download', [$request->id, $rv->id]) }}"
                               style="display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid var(--line);text-decoration:none;color:inherit;transition:background 120ms;"
                               onmouseover="this.style.background='var(--paper)'"
                               onmouseout="this.style.background='transparent'">
                                <div style="width:32px;height:32px;border-radius:var(--radius);background:var(--emerald-50);color:var(--emerald-700);display:grid;place-items:center;flex-shrink:0;">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 12-3-3m0 0-3 3m3-3v6m1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ ucfirst($rv->type) }} report <span style="color:var(--ink-400);font-family:var(--font-mono);font-weight:500;">v{{ $rv->version }}</span></div>
                                    <div style="font-size:11px;color:var(--ink-500);margin-top:2px;">Issued {{ $rv->generated_at->diffForHumans() }} · {{ $rv->generated_at->format('d M Y') }}</div>
                                </div>
                                <svg style="width:14px;height:14px;color:var(--ink-400);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-client.layouts.app>
