<x-client.layouts.app pageTitle="Scopes — {{ $country['name'] }}">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-500);margin-bottom:24px;">
        <a href="{{ route('client.maps') }}" style="color:var(--ink-500);text-decoration:none;" onmouseover="this.style.color='var(--emerald-700)'" onmouseout="this.style.color='var(--ink-500)'">Scope Maps</a>
        <svg style="width:12px;height:12px;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span style="color:var(--ink-900);font-weight:600;">{{ $country['flag'] }} {{ $country['name'] }}</span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @foreach ($scopes as $scope)
            <div class="nrh-card" style="padding:20px 24px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;gap:12px;">
                    <h3 style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0;">{{ $scope['name'] }}</h3>
                    <span class="pill pill-clear" style="flex-shrink:0;font-family:var(--font-mono);">{{ $scope['turnaround'] }}</span>
                </div>
                <p style="font-size:12px;color:var(--ink-500);line-height:1.6;margin:0;">{{ $scope['description'] }}</p>
            </div>
        @endforeach
    </div>

</x-client.layouts.app>
