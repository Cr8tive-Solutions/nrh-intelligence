<x-client.layouts.app pageTitle="Request Details">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-500);margin-bottom:24px;">
        <a href="{{ route('client.requests.index') }}" style="color:var(--ink-500);text-decoration:none;" onmouseover="this.style.color='var(--emerald-700)'" onmouseout="this.style.color='var(--ink-500)'">Active Requests</a>
        <svg style="width:12px;height:12px;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span style="color:var(--ink-900);font-weight:600;font-family:var(--font-mono);">{{ $request->reference }}</span>
    </div>

    @php
        $scopes = $request->candidates->flatMap(fn($c) => $c->scopeTypes)->unique('id')->pluck('name');
    @endphp

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;">

        {{-- Main --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Candidates --}}
            <div class="nrh-card">
                <div class="card-head">
                    <h3>Candidates</h3>
                    <span style="font-size:12px;color:var(--ink-400);">{{ $request->candidates->count() }} total</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="nrh-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th>Identity No.</th>
                                <th style="width:140px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($request->candidates as $i => $candidate)
                                <tr>
                                    <td style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);">{{ $i + 1 }}</td>
                                    <td style="font-weight:600;color:var(--ink-900);">{{ $candidate->name }}</td>
                                    <td style="font-family:var(--font-mono);font-size:12px;color:var(--ink-500);">{{ $candidate->identity_number }}</td>
                                    <td>
                                        @include('client.partials._status-badge', ['status' => $candidate->status])
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

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div class="nrh-card" style="padding:20px 24px;">
                <h3 style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 16px;">Request Info</h3>
                <dl style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ([
                        ['Reference',    $request->reference,                                        true],
                        ['Status',       ucwords(str_replace('_', ' ', $request->status)),           false],
                        ['Submitted By', $request->submittedBy?->name ?? '—',                       false],
                        ['Date',         $request->created_at->format('d M Y'),                     false],
                    ] as [$label, $value, $mono])
                        <div>
                            <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-400);">{{ $label }}</dt>
                            <dd style="font-size:13px;font-weight:600;color:var(--ink-900);margin:3px 0 0;{{ $mono ? 'font-family:var(--font-mono);' : '' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <a href="{{ route('client.requests.index') }}" class="btn-ghost" style="justify-content:center;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back to Requests
            </a>
        </div>
    </div>

</x-client.layouts.app>
