<aside
    id="sidebar"
    class="nrh-sidebar fixed inset-y-0 left-0 z-40 transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    {{-- Brand --}}
    <div style="display:flex;gap:10px;align-items:center;padding:6px 8px 14px;border-bottom:1px solid var(--line);">
        <div class="brand-mark">
            <span>N</span>
        </div>
        <div style="display:flex;flex-direction:column;line-height:1.1;">
            <div style="font-family:var(--font-display);font-weight:600;font-size:16px;letter-spacing:0.01em;color:var(--ink-900);">
                NRH <em style="font-style:italic;color:var(--gold-600);">Intelligence</em>
            </div>
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);margin-top:3px;">Screening · Est. 2019</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1;overflow-y:auto;padding:16px 14px;display:flex;flex-direction:column;gap:20px;">

        {{-- Workspace --}}
        <div style="display:flex;flex-direction:column;gap:2px;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);padding:0 10px 8px;">Workspace</div>

            @php $active = request()->routeIs('client.dashboard*'); @endphp
            <a href="{{ route('client.dashboard') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>

            @php $active = request()->routeIs('client.request*'); @endphp
            <a href="{{ route('client.request.new') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                New Request
            </a>

            @php $active = request()->routeIs('client.requests*'); @endphp
            <a href="{{ route('client.requests.index') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-4"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14h6M9 18h4"/></svg>
                Active Requests
            </a>

            @php $active = request()->routeIs('client.history*'); @endphp
            <a href="{{ route('client.history.index') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M10 13l2 2 4-4"/></svg>
                Reports
            </a>
        </div>

        {{-- Billing --}}
        <div style="display:flex;flex-direction:column;gap:2px;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);padding:0 10px 8px;">Billing</div>

            @php $active = request()->routeIs('client.billing.transactions*'); @endphp
            <a href="{{ route('client.billing.transactions') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 5-6"/></svg>
                Transactions
            </a>

            @php $active = request()->routeIs('client.billing.invoices*'); @endphp
            <a href="{{ route('client.billing.invoices') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Invoices
            </a>
        </div>

        {{-- Scope --}}
        <div style="display:flex;flex-direction:column;gap:2px;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);padding:0 10px 8px;">Scope</div>

            @php $active = request()->routeIs('client.maps*'); @endphp
            <a href="{{ route('client.maps') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 4v4M16 4v4"/></svg>
                Scope Maps
            </a>
        </div>

        {{-- Settings --}}
        <div style="display:flex;flex-direction:column;gap:2px;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);padding:0 10px 8px;">Settings</div>

            @php $active = request()->routeIs('client.settings.account*'); @endphp
            <a href="{{ route('client.settings.account') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                Account
            </a>

            @php $active = request()->routeIs('client.settings.users*'); @endphp
            <a href="{{ route('client.settings.users') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>

            @php $active = request()->routeIs('client.settings.packages*'); @endphp
            <a href="{{ route('client.settings.packages') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
                Packages
            </a>

            @php $active = request()->routeIs('client.settings.security*'); @endphp
            <a href="{{ route('client.settings.security') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a7 7 0 0 0-7 7v4l-2 3h18l-2-3V9a7 7 0 0 0-7-7z"/><path d="M9 19a3 3 0 0 0 6 0"/></svg>
                Security
            </a>

            @php $active = request()->routeIs('client.settings.agreement*'); @endphp
            <a href="{{ route('client.settings.agreement') }}" class="nav-item {{ $active ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M10 13l2 2 4-4"/></svg>
                Agreement
            </a>
        </div>

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer" style="padding:14px;">
        <button onclick="toggleTheme()"
            style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);cursor:pointer;font-size:12px;color:var(--ink-700);background:transparent;width:100%;font-family:var(--font-ui);"
            onmouseover="this.style.borderColor='var(--emerald-600)'"
            onmouseout="this.style.borderColor='var(--line)'"
        >
            <span id="themeLabel">Light mode</span>
            <span style="width:8px;height:8px;border-radius:50%;background:var(--gold-500);box-shadow:0 0 0 3px rgba(212,175,55,0.15);display:inline-block;"></span>
        </button>

        <div x-data="{ open: false }" style="position:relative;">
            <div @click="open = !open"
                style="display:flex;align-items:center;gap:10px;padding:6px 8px;border-radius:var(--radius);cursor:pointer;"
                onmouseover="this.style.background='rgba(5,150,105,0.06)'"
                onmouseout="this.style.background=''"
            >
                <div style="width:28px;height:28px;border-radius:50%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:11px;font-weight:600;font-family:var(--font-mono);box-shadow:inset 0 0 0 1px rgba(212,175,55,0.4);flex-shrink:0;">
                    {{ strtoupper(substr(session('client_user_name', 'U'), 0, 2)) }}
                </div>
                <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
                    <span style="font-size:12px;font-weight:600;color:var(--ink-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ session('client_user_name', 'User') }}</span>
                    <span style="font-size:10px;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.1em;">{{ session('client_company', 'Company') }}</span>
                </div>
            </div>

            <div x-show="open" @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                style="position:absolute;bottom:calc(100% + 6px);left:0;right:0;border-radius:var(--radius);background:var(--card);border:1px solid var(--line);box-shadow:var(--shadow-lg);padding:4px 0;z-index:50;"
            >
                <a href="{{ route('client.settings.profile') }}"
                   style="display:flex;align-items:center;gap:8px;padding:8px 12px;font-size:13px;color:var(--ink-700);text-decoration:none;"
                   onmouseover="this.style.background='rgba(5,150,105,0.06)';this.style.color='var(--ink-900)'"
                   onmouseout="this.style.background='';this.style.color='var(--ink-700)'">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    My Profile
                </a>
                <a href="{{ route('client.notifications') }}"
                   style="display:flex;align-items:center;gap:8px;padding:8px 12px;font-size:13px;color:var(--ink-700);text-decoration:none;"
                   onmouseover="this.style.background='rgba(5,150,105,0.06)';this.style.color='var(--ink-900)'"
                   onmouseout="this.style.background='';this.style.color='var(--ink-700)'">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    Notifications
                </a>
                <div style="margin:4px 0;border-top:1px solid var(--line);"></div>
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit"
                        style="display:flex;width:100%;align-items:center;gap:8px;padding:8px 12px;font-size:13px;color:#ef4444;background:none;border:none;cursor:pointer;font-family:var(--font-ui);"
                        onmouseover="this.style.background='rgba(239,68,68,0.08)'"
                        onmouseout="this.style.background=''">
                        <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
