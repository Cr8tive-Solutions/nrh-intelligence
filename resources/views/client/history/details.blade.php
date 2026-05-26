<x-client.layouts.app pageTitle="{{ $request->reference }}">

    @php
        $candidates    = $request->candidates;
        $reports       = $request->currentReportVersions;
        $totalChecks   = $candidates->sum(fn ($c) => $c->scopeTypes->count());
        $flaggedChecks = $candidates->sum(fn ($c) => $c->scopeTypes->where('pivot.status', 'flagged')->count());
        $allCleared    = $flaggedChecks === 0;

        $typeLabel = match ($request->type) {
            'malaysia'           => 'Malaysia Screening',
            'global'             => 'Global Screening',
            'kyc'                => 'KYC · Customer',
            'kyb'                => 'KYB · Business',
            'kys'                => 'KYS · Supplier',
            'employment_malaysia'=> 'Employment · Malaysia',
            default              => ucwords(str_replace('_', ' ', (string) $request->type)),
        };

        $submittedAt  = $request->created_at;
        $completedAt  = $request->updated_at;
        $durationMins = $submittedAt->diffInMinutes($completedAt);
        $durationHuman = match (true) {
            $durationMins < 60   => $durationMins . ' min',
            $durationMins < 1440 => round($durationMins / 60, 1) . ' hrs',
            default              => round($durationMins / 1440, 1) . ' days',
        };
    @endphp

    {{-- Page head --}}
    <div class="page-head">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <a href="{{ route('client.history.index') }}" class="case-back" aria-label="Back to history">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-size:10px;color:var(--ink-400);letter-spacing:0.1em;font-family:var(--font-mono);text-transform:uppercase;">Completed request</div>
                <div style="font-size:15px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">{{ $request->reference }}</div>
            </div>
            <span style="padding:3px 10px;background:var(--gold-100);color:var(--gold-700);border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">{{ $typeLabel }}</span>
        </div>
        @if ($reports->isNotEmpty())
            <div style="display:flex;gap:8px;">
                @php $latestFull = $reports->firstWhere('type', 'full') ?? $reports->first(); @endphp
                <a href="{{ route('client.requests.reports.download', [$request->id, $latestFull->id]) }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                    Download report
                </a>
            </div>
        @endif
    </div>

    {{-- Completion hero --}}
    <div style="margin-bottom:20px;padding:22px 24px;background:{{ $allCleared ? 'var(--emerald-50)' : 'rgba(184,147,31,0.06)' }};border:1px solid {{ $allCleared ? 'var(--emerald-600)' : 'var(--gold-500)' }};border-radius:var(--radius-lg);display:flex;align-items:flex-start;gap:18px;">
        <div style="width:46px;height:46px;border-radius:50%;background:{{ $allCleared ? 'var(--emerald-600)' : 'var(--gold-500)' }};display:grid;place-items:center;flex-shrink:0;">
            @if ($allCleared)
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            @else
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            @endif
        </div>
        <div style="flex:1;">
            <div style="font-size:19px;font-weight:700;color:var(--ink-900);line-height:1.3;">
                {{ $allCleared ? 'All checks cleared' : 'Completed with flags' }}
            </div>
            <div style="font-size:14px;color:var(--ink-600);margin-top:5px;line-height:1.5;">
                {{ $candidates->count() }} {{ Str::plural('candidate', $candidates->count()) }},
                {{ $totalChecks }} {{ Str::plural('check', $totalChecks) }} —
                completed {{ $completedAt->format('d M Y') }} in <b>{{ $durationHuman }}</b>.
            </div>
            <div style="display:flex;gap:20px;margin-top:14px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--ink-500);">
                    <svg style="width:13px;height:13px;color:var(--emerald-600);" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    Submitted by <b style="color:var(--ink-700);">{{ $request->submittedBy?->name ?? '—' }}</b>
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--ink-500);">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    Requested on <b style="color:var(--ink-700);">{{ $submittedAt->format('d M Y') }}</b>
                </div>
                @if ($reports->isNotEmpty())
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--ink-500);">
                        <svg style="width:13px;height:13px;color:var(--emerald-600);" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        <b style="color:var(--ink-700);">{{ $reports->count() }}</b> {{ Str::plural('report', $reports->count()) }} available
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main layout --}}
    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

        {{-- Left: per-candidate check breakdown --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach ($candidates as $candidate)
                @php
                    $candScopes   = $candidate->scopeTypes;
                    $candFlagged  = $candScopes->where('pivot.status', 'flagged')->count();
                    $isRedacted   = $candidate->isRedacted();
                    $initials     = $isRedacted ? '··' : strtoupper(substr($candidate->name, 0, 2));
                @endphp
                <div class="card">
                    {{-- Candidate header --}}
                    <div style="display:flex;align-items:center;gap:14px;padding:16px 20px;border-bottom:1px solid var(--line);">
                        <div style="width:40px;height:40px;border-radius:50%;background:{{ $candFlagged ? 'rgba(184,147,31,0.12)' : 'var(--emerald-50)' }};color:{{ $candFlagged ? 'var(--gold-700)' : 'var(--emerald-700)' }};display:grid;place-items:center;font-size:13px;font-weight:700;flex-shrink:0;{{ $isRedacted ? 'color:var(--ink-400);background:var(--paper);' : '' }}">
                            {{ $initials }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:700;color:{{ $isRedacted ? 'var(--ink-400)' : 'var(--ink-900)' }};{{ $isRedacted ? 'font-style:italic;' : '' }}">
                                {{ $isRedacted ? 'Candidate erased' : $candidate->name }}
                            </div>
                            <div style="font-size:12px;color:var(--ink-400);font-family:var(--font-mono);margin-top:1px;">
                                {{ $isRedacted ? 'Data erased ' . $candidate->redacted_at->format('d M Y') : ($candidate->identityType?->name ?? 'ID') . ' · ' . $candidate->identity_number }}
                            </div>
                        </div>
                        <span class="pill {{ $candFlagged ? 'pill-flagged' : 'pill-clear' }}">
                            <span class="dot"></span>{{ $candFlagged ? 'Flagged' : 'Cleared' }}
                        </span>
                    </div>

                    {{-- Check rows --}}
                    @if ($candScopes->isNotEmpty())
                        <div>
                            @foreach ($candScopes as $scope)
                                @php
                                    $pivot      = $scope->pivot;
                                    $pStatus    = $pivot->status;
                                    $findings   = $pivot->findings ?? [];
                                    $pillCls    = match ($pStatus) {
                                        'complete'    => 'pill-clear',
                                        'flagged'     => 'pill-flagged',
                                        'in_progress' => 'pill-progress',
                                        default       => 'pill-pending',
                                    };
                                    $pillTxt    = match ($pStatus) {
                                        'complete' => 'Cleared',
                                        'flagged'  => 'Flagged',
                                        default    => ucfirst($pStatus),
                                    };
                                    $tatMins = $pivot->started_at && $pivot->completed_at
                                        ? \Carbon\Carbon::parse($pivot->started_at)->diffInMinutes(\Carbon\Carbon::parse($pivot->completed_at))
                                        : null;
                                    $tatLabel = match (true) {
                                        $tatMins === null     => null,
                                        $tatMins < 60         => $tatMins . ' min',
                                        $tatMins < 1440       => round($tatMins / 60, 1) . ' hrs',
                                        default               => round($tatMins / 1440, 1) . ' days',
                                    };
                                    $comment = $findings['comment'] ?? null;
                                @endphp
                                <div style="padding:14px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px;">
                                    {{-- Status icon --}}
                                    <div style="width:30px;height:30px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:{{ $pStatus === 'complete' ? 'var(--emerald-50)' : ($pStatus === 'flagged' ? 'rgba(196,69,58,0.08)' : 'var(--paper)') }};">
                                        @if ($pStatus === 'complete')
                                            <svg style="width:14px;height:14px;color:var(--emerald-600);" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @elseif ($pStatus === 'flagged')
                                            <svg style="width:14px;height:14px;color:var(--danger);" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                                        @else
                                            <svg style="width:14px;height:14px;color:var(--ink-400);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>
                                        @endif
                                    </div>

                                    {{-- Name + findings --}}
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ $scope->name }}</div>
                                        @if ($comment)
                                            <div style="font-size:12px;color:var(--ink-500);margin-top:2px;">{{ $comment }}</div>
                                        @elseif ($scope->description)
                                            <div style="font-size:12px;color:var(--ink-400);margin-top:2px;">{{ $scope->description }}</div>
                                        @endif
                                    </div>

                                    {{-- TAT --}}
                                    @if ($tatLabel)
                                        <div style="text-align:right;flex-shrink:0;">
                                            <div style="font-size:11px;font-weight:600;color:var(--ink-500);font-family:var(--font-mono);">{{ $tatLabel }}</div>
                                            <div style="font-size:10px;color:var(--ink-400);margin-top:1px;">turnaround</div>
                                        </div>
                                    @endif

                                    <span class="pill {{ $pillCls }}" style="flex-shrink:0;"><span class="dot"></span>{{ $pillTxt }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding:24px 20px;font-size:13px;color:var(--ink-400);text-align:center;">No checks on record.</div>
                    @endif

                    {{-- Remarks --}}
                    @if ($candidate->remarks)
                        <div style="padding:12px 20px;background:var(--paper);border-top:1px solid var(--line);font-size:12px;color:var(--ink-500);">
                            <b style="color:var(--ink-700);">Remarks:</b> {{ $candidate->remarks }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Request details --}}
            <div class="card">
                <div class="card-head"><h3>Request details</h3></div>
                <div class="identity">
                    <div class="id-row">
                        <span class="k">Request number</span>
                        <span class="v" style="font-family:var(--font-mono);">{{ $request->reference }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Type</span>
                        <span class="v" style="font-weight:600;white-space:normal;">{{ $typeLabel }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Submitted by</span>
                        <span class="v" style="white-space:normal;">{{ $request->submittedBy?->name ?? '—' }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Submitted</span>
                        <span class="v">{{ $submittedAt->format('d M Y · H:i') }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Completed</span>
                        <span class="v">{{ $completedAt->format('d M Y · H:i') }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Turnaround</span>
                        <span class="v" style="font-weight:700;color:var(--emerald-700);">{{ $durationHuman }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Candidates</span>
                        <span class="v">{{ $candidates->count() }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Total checks</span>
                        <span class="v">{{ $totalChecks }} {{ $flaggedChecks ? "($flaggedChecks flagged)" : '— all cleared' }}</span>
                    </div>
                </div>
            </div>

            {{-- Reports --}}
            @if ($reports->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>Reports</h3>
                        <span class="count-pill">{{ $reports->count() }}</span>
                    </div>
                    <div style="padding:6px 0;">
                        @foreach ($reports as $rv)
                            @php
                                $reportLabel = match (true) {
                                    $rv->type === 'prelim'                             => 'Preliminary report',
                                    $rv->type === 'full' && $request->status === 'updated' => 'Updated report',
                                    $rv->type === 'full'                               => 'Full report',
                                    $rv->type === 'basic'                              => 'Basic report',
                                    default                                            => ucfirst($rv->type) . ' report',
                                };
                            @endphp
                            <a href="{{ route('client.requests.reports.download', [$request->id, $rv->id]) }}"
                               style="display:flex;align-items:center;gap:10px;padding:14px 18px;border-bottom:1px solid var(--line);text-decoration:none;color:inherit;transition:background 120ms;"
                               onmouseover="this.style.background='var(--paper)'"
                               onmouseout="this.style.background='transparent'">
                                <div style="width:34px;height:34px;border-radius:var(--radius);background:var(--emerald-50);color:var(--emerald-700);display:grid;place-items:center;flex-shrink:0;">
                                    <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 12-3-3m0 0-3 3m3-3v6m1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ $reportLabel }} <span style="color:var(--ink-400);font-family:var(--font-mono);font-weight:400;font-size:11px;">v{{ $rv->version }}</span></div>
                                    <div style="font-size:11px;color:var(--ink-500);margin-top:1px;">{{ \Carbon\Carbon::parse($rv->generated_at)->format('d M Y · H:i') }}</div>
                                </div>
                                <svg style="width:15px;height:15px;color:var(--ink-400);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-client.layouts.app>
