<x-client.layouts.app pageTitle="Track Request">

    <div style="max-width:680px;">

        {{-- Search card --}}
        <div class="card" style="padding:24px;margin-bottom:16px;">
            <h3 style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0 0 4px;">Search by Candidate</h3>
            <p style="font-size:12px;color:var(--ink-500);margin:0 0 16px;">Enter a candidate name or identity number to track their verification status.</p>
            <form method="POST" action="{{ route('client.requests.track.search') }}" style="display:flex;gap:10px;">
                @csrf
                <div class="search" style="flex:1;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Name or identity number…" autofocus />
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        {{-- Results --}}
        @if ($results !== null)
            @if ($results->isEmpty())
                <div class="card" style="padding:60px 20px;text-align:center;">
                    <svg style="width:40px;height:40px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <p style="font-size:13px;color:var(--ink-400);margin:0;">No candidates found for <strong style="color:var(--ink-600);">"{{ $query }}"</strong></p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ($results as $result)
                        @php
                            $stepMap = ['new' => 1, 'in_progress' => 2, 'flagged' => 2, 'complete' => 3];
                            $currentStep = $stepMap[$result->status] ?? 1;
                        @endphp
                        <div class="card" style="padding:20px 24px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                                <div>
                                    <p style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0;">{{ $result->name }}</p>
                                    <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:3px 0 0;">{{ $result->identity_number }}</p>
                                </div>
                                <a href="{{ route('client.requests.details', $result->screeningRequest->id) }}"
                                   style="font-size:12px;font-weight:600;color:var(--emerald-700);text-decoration:none;">
                                    {{ $result->screeningRequest->reference }} →
                                </a>
                            </div>

                            {{-- Step tracker --}}
                            <div style="display:flex;align-items:center;margin-bottom:16px;">
                                @php
                                    $trackSteps = [1 => 'Received', 2 => 'Processing', 3 => 'Complete'];
                                @endphp
                                @foreach ($trackSteps as $stepNum => $stepLabel)
                                    <div style="display:flex;align-items:center;{{ $stepNum < count($trackSteps) ? 'flex:1;' : '' }}">
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                                            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;
                                                {{ $stepNum <= $currentStep ? 'background:var(--emerald-700);color:white;' : 'background:var(--line);color:var(--ink-400);' }}">
                                                @if ($stepNum < $currentStep)
                                                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                @else
                                                    {{ $stepNum }}
                                                @endif
                                            </div>
                                            <span style="font-size:11px;{{ $stepNum <= $currentStep ? 'color:var(--ink-700);font-weight:600;' : 'color:var(--ink-400);' }}">{{ $stepLabel }}</span>
                                        </div>
                                        @if ($stepNum < count($trackSteps))
                                            <div style="flex:1;height:1px;margin:0 8px 16px;{{ $stepNum < $currentStep ? 'background:var(--emerald-400);' : 'background:var(--line);' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Scopes --}}
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach ($result->scopeTypes as $scope)
                                    <span class="pill pill-pending">{{ $scope->name }}</span>
                                @endforeach
                            </div>
                            <p style="font-size:11px;color:var(--ink-400);font-family:var(--font-mono);margin:10px 0 0;">Last updated: {{ $result->updated_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

</x-client.layouts.app>
