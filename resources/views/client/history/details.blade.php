<x-client.layouts.app pageTitle="Completed Request">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-500);margin-bottom:24px;">
        <a href="{{ route('client.history.index') }}" style="color:var(--ink-500);text-decoration:none;" onmouseover="this.style.color='var(--emerald-700)'" onmouseout="this.style.color='var(--ink-500)'">History</a>
        <svg style="width:12px;height:12px;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span style="color:var(--ink-900);font-weight:600;font-family:var(--font-mono);">{{ $request->reference }}</span>
    </div>

    @php
        $scopes = $request->candidates->flatMap(fn($c) => $c->scopeTypes)->unique('id')->pluck('name');
    @endphp

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;">

        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Completed banner --}}
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);">
                <svg style="width:16px;height:16px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p style="font-size:13px;font-weight:500;color:var(--emerald-800);margin:0;">All verifications completed on {{ $request->updated_at->format('d M Y') }}</p>
            </div>

            {{-- Candidates --}}
            <div class="nrh-card">
                <div class="card-head">
                    <h3>Candidates</h3>
                </div>
                <div style="overflow-x:auto;">
                    <table class="nrh-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th>Identity No.</th>
                                <th style="width:120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($request->candidates as $i => $candidate)
                                <tr>
                                    <td style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);">{{ $i + 1 }}</td>
                                    <td style="font-weight:600;color:var(--ink-900);">{{ $candidate->name }}</td>
                                    <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);">{{ $candidate->identity_number }}</td>
                                    <td>
                                        <span class="pill pill-clear"><span class="dot"></span>Complete</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Scopes --}}
            <div class="nrh-card" style="padding:20px 24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 12px;">Verification Scopes</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach ($scopes as $scope)
                        <span class="pill pill-pending">{{ $scope }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;">
            <div class="nrh-card" style="padding:20px 24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Request Info</h3>
                <dl style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ([
                        ['Reference',    $request->reference,                        true],
                        ['Submitted By', $request->submittedBy?->name ?? '—',       false],
                        ['Submitted',    $request->created_at->format('d M Y'),     false],
                        ['Completed',    $request->updated_at->format('d M Y'),     false],
                    ] as [$label, $value, $mono])
                        <div>
                            <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);">{{ $label }}</dt>
                            <dd style="font-size:13px;font-weight:600;color:var(--ink-900);margin:3px 0 0;{{ $mono ? 'font-family:var(--font-mono);' : '' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            <a href="{{ route('client.history.index') }}" class="btn-ghost" style="justify-content:center;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back to History
            </a>
        </div>
    </div>

</x-client.layouts.app>
