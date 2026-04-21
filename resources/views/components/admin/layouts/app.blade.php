<!DOCTYPE html>
<html lang="en" data-theme="light" id="htmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} — NRH Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:400,500,600,700|fraunces:400,500,600,700ital|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         style="position:fixed;inset:0;z-index:30;background:rgba(0,0,0,0.35);"
         class="lg:hidden"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="nrh-sidebar fixed inset-y-0 left-0 z-40 transition-transform duration-300 lg:sticky lg:top-0 lg:translate-x-0 lg:inset-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Brand --}}
        <div style="display:flex;gap:10px;align-items:center;padding:6px 8px 14px;border-bottom:1px solid var(--line);">
            <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:36px;width:auto;flex-shrink:0;">
            <div style="display:flex;flex-direction:column;line-height:1.1;">
                <div style="font-family:var(--font-display);font-weight:600;font-size:16px;letter-spacing:0.01em;color:var(--ink-900);">
                    NRH <em style="font-style:italic;color:var(--gold-600);">Admin</em>
                </div>
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);margin-top:3px;">Operations Portal</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="flex:1;overflow-y:auto;padding:16px 14px;display:flex;flex-direction:column;gap:20px;">

            <div style="display:flex;flex-direction:column;gap:2px;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:var(--ink-500);padding:0 10px 8px;">Operations</div>

                @php $active = request()->routeIs('admin.dashboard'); @endphp
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ $active ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>

                @php $active = request()->routeIs('admin.requests*'); @endphp
                <a href="{{ route('admin.requests.index') }}" class="nav-item {{ $active ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-4"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14h6M9 18h4"/></svg>
                    Requests Queue
                </a>

                @php $active = request()->routeIs('admin.customers*'); @endphp
                <a href="{{ route('admin.customers.index') }}" class="nav-item {{ $active ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75"/></svg>
                    Customers
                </a>
            </div>

        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer" style="padding:14px;">
            <div x-data="{ open: false }" style="position:relative;">
                <div @click="open = !open"
                     style="display:flex;align-items:center;gap:10px;padding:6px 8px;border-radius:var(--radius);cursor:pointer;"
                     onmouseover="this.style.background='rgba(5,150,105,0.06)'"
                     onmouseout="this.style.background=''">
                    <div style="width:28px;height:28px;border-radius:50%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:11px;font-weight:600;font-family:var(--font-mono);flex-shrink:0;">
                        {{ strtoupper(substr(session('admin_name', 'A'), 0, 2)) }}
                    </div>
                    <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
                        <span style="font-size:12px;font-weight:600;color:var(--ink-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ session('admin_name', 'Admin') }}</span>
                        <span style="font-size:10px;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.1em;">{{ ucfirst(str_replace('_', ' ', session('admin_role', 'admin'))) }}</span>
                    </div>
                </div>

                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="position:absolute;bottom:calc(100% + 6px);left:0;right:0;border-radius:var(--radius);background:var(--card);border:1px solid var(--line);box-shadow:var(--shadow-lg);padding:4px 0;z-index:50;">
                    <form method="POST" action="{{ route('admin.logout') }}">
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

    {{-- Main --}}
    <div class="nrh-main">

        {{-- Topbar --}}
        <header class="nrh-topbar sticky top-0 z-30">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden"
                    style="width:32px;height:32px;display:grid;place-items:center;border-radius:var(--radius);border:1px solid transparent;background:transparent;cursor:pointer;color:var(--ink-700);">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
            <div class="breadcrumbs">
                <img src="{{ asset('nrh-logo.png') }}" alt="NRH" style="height:20px;width:auto;">
                <span class="sep">/</span>
                <span>Admin</span>
                <span class="sep">/</span>
                <b>{{ $pageTitle ?? 'Dashboard' }}</b>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;font-family:var(--font-mono);padding:3px 8px;background:rgba(196,69,58,0.1);color:#c4453a;border-radius:999px;font-weight:600;letter-spacing:0.05em;">ADMIN</span>
            </div>
        </header>

        {{-- Content --}}
        <div class="nrh-content">
            @if (session('success'))
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);"
                     x-data x-init="setTimeout(() => $el.remove(), 5000)">
                    <svg style="width:15px;height:15px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p style="font-size:13px;color:var(--emerald-800);font-weight:500;">{{ session('success') }}</p>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>

    @stack('scripts')
</body>
</html>
