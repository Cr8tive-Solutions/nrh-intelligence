<x-client.layouts.app pageTitle="Scope Maps">

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                Scope <em style="font-style:italic;color:var(--emerald-700);">Maps</em>
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Countries where background verification services are available</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        @foreach ($countries as $country)
            <a href="{{ route('client.maps.country', $country['id']) }}"
               class="nrh-card" style="padding:20px;display:block;text-decoration:none;transition:border-color 150ms,box-shadow 150ms;"
               onmouseover="this.style.borderColor='rgba(5,150,105,0.4)';this.style.boxShadow='var(--shadow)'" onmouseout="this.style.borderColor='var(--line)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <span style="font-size:28px;line-height:1;">{{ $country['flag'] }}</span>
                    <span class="pill pill-pending" style="font-family:var(--font-mono);letter-spacing:0.05em;">{{ $country['code'] }}</span>
                </div>
                <h3 style="font-size:14px;font-weight:600;color:var(--ink-900);margin:0 0 2px;">{{ $country['name'] }}</h3>
                <p style="font-size:12px;color:var(--ink-500);margin:0 0 14px;">{{ $country['region'] }}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--line);">
                    <span style="font-size:12px;color:var(--ink-500);">
                        <span style="font-weight:700;color:var(--ink-900);">{{ $country['scope_count'] }}</span> scopes available
                    </span>
                    <svg style="width:14px;height:14px;color:var(--ink-400);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

</x-client.layouts.app>
