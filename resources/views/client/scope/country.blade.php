<x-client.layouts.app pageTitle="Scopes — {{ $country['name'] }}">

    <div class="page-head">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('client.maps') }}"
               style="display:grid;place-items:center;width:32px;height:32px;border:1px solid var(--line);border-radius:var(--radius);color:var(--ink-500);flex-shrink:0;"
               onmouseover="this.style.borderColor='var(--emerald-600)';this.style.color='var(--emerald-700)'"
               onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-500)'">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--ink-400);letter-spacing:0.1em;text-transform:uppercase;">Scope Maps</div>
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $country['flag'] }} {{ $country['name'] }}</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @foreach ($scopes as $scope)
            <div class="card" style="padding:20px 24px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;gap:12px;">
                    <h3 style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0;">{{ $scope['name'] }}</h3>
                    <span class="pill pill-clear" style="flex-shrink:0;font-family:var(--font-mono);">{{ $scope['turnaround'] }}</span>
                </div>
                <p style="font-size:12px;color:var(--ink-500);line-height:1.6;margin:0;">{{ $scope['description'] }}</p>
            </div>
        @endforeach
    </div>

</x-client.layouts.app>
