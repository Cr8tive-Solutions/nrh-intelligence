<x-client.layouts.app pageTitle="Packages">

    <div class="page-head">
        <div>
            <h1 style="font-family:var(--font-display);font-weight:500;font-size:30px;letter-spacing:-0.01em;margin:0;color:var(--ink-900);">
                Screening <em style="font-style:italic;color:var(--emerald-700);">Packages</em>
            </h1>
            <p style="margin-top:6px;font-size:13px;color:var(--ink-500);">Saved scope bundles for quick request creation</p>
        </div>
        <button x-data @click="$dispatch('open-create-package')" class="btn-primary">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Package
        </button>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @forelse ($packages as $pkg)
            <div class="nrh-card" style="padding:20px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <div>
                        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:500;color:var(--ink-900);margin:0;">{{ $pkg->name }}</h3>
                        <p style="font-size:11px;color:var(--ink-400);margin:3px 0 0;font-family:var(--font-mono);letter-spacing:0.05em;">{{ $pkg->country->name }}</p>
                    </div>
                    <button style="background:none;border:none;cursor:pointer;color:var(--ink-400);padding:4px;" onmouseover="this.style.color='var(--ink-700)'" onmouseout="this.style.color='var(--ink-400)'">
                        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                        </svg>
                    </button>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach ($pkg->scopeTypes as $scope)
                        <span class="pill pill-pending">{{ $scope->name }}</span>
                    @endforeach
                </div>
                <p style="font-size:11px;color:var(--ink-400);margin:12px 0 0;font-family:var(--font-mono);">Created {{ $pkg->created_at->format('d M Y') }}</p>
            </div>
        @empty
            <div style="grid-column:span 2;padding:60px 20px;text-align:center;border:1px dashed var(--line);border-radius:var(--radius-lg);">
                <svg style="width:40px;height:40px;color:var(--ink-200);margin:0 auto 12px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p style="font-size:13px;color:var(--ink-400);margin:0;">No packages saved yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Create package modal --}}
    <div
        x-data="{ open: false }"
        @open-create-package.window="open = true"
        x-show="open"
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);" @click="open = false"></div>
        <div style="position:relative;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);width:100%;max-width:420px;padding:24px;max-height:90vh;overflow-y:auto;"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:var(--font-display);font-size:18px;font-weight:500;color:var(--ink-900);margin:0;">New Package</h3>
                <button @click="open = false" style="background:none;border:none;cursor:pointer;color:var(--ink-400);" onmouseover="this.style.color='var(--ink-900)'" onmouseout="this.style.color='var(--ink-400)'">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @php $inp = "width:100%;padding:10px 14px;border:1px solid var(--line);background:var(--card);border-radius:var(--radius);font-size:13px;color:var(--ink-900);outline:none;font-family:var(--font-ui);box-sizing:border-box;"; @endphp
            <form style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:6px;">Package Name</label>
                    <input type="text" placeholder="e.g. Standard Screening" style="{{ $inp }}" onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'"/>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);margin-bottom:8px;">Select Scopes</label>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        @foreach ($allScopes as $scope)
                            <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--line);border-radius:var(--radius);cursor:pointer;transition:background 120ms;"
                                onmouseover="this.style.background='rgba(5,150,105,0.04)'" onmouseout="this.style.background=''">
                                <input type="checkbox" value="{{ $scope->id }}" style="width:14px;height:14px;border-radius:3px;accent-color:var(--emerald-700);">
                                <span style="font-size:13px;color:var(--ink-700);">{{ $scope->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="button" @click="open = false" class="btn-ghost" style="flex:1;justify-content:center;">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Save Package</button>
                </div>
            </form>
        </div>
    </div>

</x-client.layouts.app>
