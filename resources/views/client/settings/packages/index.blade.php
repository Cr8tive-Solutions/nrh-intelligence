<x-client.layouts.app pageTitle="Packages">

    {{--
        Read-only. Packages are configured by NRH in the admin portal; this
        portal never writes to `packages` or `package_scope_type`.
    --}}
    <div class="page-head">
        <div>
            <h1>Request <em>Packages</em></h1>
            <div class="sub">Scope bundles configured for your account — pick one in the request wizard to add all its scopes at once</div>
        </div>
        @can('create-requests')
            <a href="{{ route('client.request.new') }}" class="btn btn-primary">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Request
            </a>
        @endcan
    </div>

    @if ($packages->isNotEmpty())
        <div class="card" style="padding:14px 18px;margin-bottom:18px;display:flex;gap:10px;align-items:flex-start;">
            <svg style="width:15px;height:15px;color:var(--ink-400);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
            </svg>
            <p style="font-size:12px;color:var(--ink-500);margin:0;line-height:1.55;">
                Packages are defined by NRH to match your service agreement. To add, change, or remove one,
                speak to your account manager.
            </p>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @forelse ($packages as $pkg)
            @php
                $currency = $pkg->country?->currency ?? 'MYR';
                $total = $totals[$pkg->id] ?? null;
            @endphp
            <div class="card" style="padding:20px;display:flex;flex-direction:column;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;">
                    <div>
                        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:500;color:var(--ink-900);margin:0;">{{ $pkg->name }}</h3>
                        <p style="font-size:11px;color:var(--ink-400);margin:3px 0 0;font-family:var(--font-mono);letter-spacing:0.05em;">
                            {{ $pkg->country?->name ?? '—' }}
                            &middot; {{ $pkg->scopeTypes->count() }} {{ Str::plural('scope', $pkg->scopeTypes->count()) }}
                        </p>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        @if ($total === null)
                            <span class="pill pill-review">On request</span>
                        @else
                            <div style="font-family:var(--font-mono);font-size:14px;font-weight:600;color:var(--ink-900);white-space:nowrap;">
                                {{ $currency }} {{ number_format($total, 2) }}
                            </div>
                            <div style="font-size:10px;color:var(--ink-400);font-family:var(--font-mono);letter-spacing:0.05em;margin-top:2px;">PER CANDIDATE</div>
                        @endif
                    </div>
                </div>

                @if ($pkg->scopeTypes->isEmpty())
                    <p style="font-size:12px;color:var(--ink-400);margin:0;font-style:italic;">
                        No scopes configured yet — contact your account manager.
                    </p>
                @else
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach ($pkg->scopeTypes as $scope)
                            <span class="pill pill-pending" @if ($scope->turnaround) title="Turnaround: {{ $scope->turnaround }}" @endif>
                                {{ $scope->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($total === null)
                    <p style="font-size:11px;color:var(--gold-700);margin:12px 0 0;line-height:1.5;">
                        This package includes a scope priced on request. We need to agree a price with you before a
                        request using it can be submitted.
                    </p>
                @endif
            </div>
        @empty
            <div style="grid-column:span 2;padding:60px 20px;text-align:center;border:1px dashed var(--line);border-radius:var(--radius-lg);">
                <svg style="width:40px;height:40px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p style="font-size:13px;color:var(--ink-400);margin:0 0 4px;">No packages configured for your account.</p>
                <p style="font-size:12px;color:var(--ink-400);margin:0;">
                    Packages are set up by NRH — ask your account manager to bundle the scopes you order most often.
                </p>
            </div>
        @endforelse
    </div>

</x-client.layouts.app>
