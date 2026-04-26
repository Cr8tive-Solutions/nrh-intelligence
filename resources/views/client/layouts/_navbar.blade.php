<header class="nrh-topbar sticky top-0 z-30">

    {{-- Mobile menu toggle --}}
    <button
        @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden"
        style="width:32px;height:32px;display:grid;place-items:center;border-radius:var(--radius);border:1px solid transparent;background:transparent;cursor:pointer;color:var(--ink-700);"
        onmouseover="this.style.borderColor='var(--line)';this.style.background='var(--card)'"
        onmouseout="this.style.borderColor='transparent';this.style.background='transparent'"
        aria-label="Toggle menu"
    >
        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>

    {{-- Breadcrumbs --}}
    <div class="breadcrumbs">
        <img src="{{ asset('nrh-logo.png') }}" alt="NRH" style="height:20px;width:auto;">
        <span class="sep">/</span>
        <span>Workspace</span>
        <span class="sep">/</span>
        <b>{{ $pageTitle ?? 'Dashboard' }}</b>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('client.requests.track.search.get') }}" class="topbar-search" @keydown.escape.window="document.getElementById('topbar-q').blur()">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
        <input id="topbar-q" type="text" name="q" placeholder="Search candidates, requests, case IDs…"
            @keydown.meta.k.window.prevent="document.getElementById('topbar-q').focus()"
            @keydown.ctrl.k.window.prevent="document.getElementById('topbar-q').focus()" />
        <span class="kbd">⌘K</span>
    </form>

    {{-- Actions --}}
    <div style="display:flex;align-items:center;gap:8px;">

        {{-- Notifications --}}
        <a href="{{ route('client.notifications') }}" class="icon-btn" title="Notifications" aria-label="Notifications" style="text-decoration:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7v4l-2 3h18l-2-3V9a7 7 0 0 0-7-7z"/><path d="M9 19a3 3 0 0 0 6 0"/></svg>
            <span class="pulse-dot"></span>
        </a>

        {{-- Help --}}
        <button class="icon-btn" title="Help" aria-label="Help">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r=".5" fill="currentColor"/></svg>
        </button>

        {{-- New request --}}
        <a href="{{ route('client.request.new') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New request
        </a>
    </div>

</header>
