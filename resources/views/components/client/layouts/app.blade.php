<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" id="htmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} — NRH Intelligence</title>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:400,500,600,700|fraunces:400,500,600,700ital|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        style="position:fixed;inset:0;z-index:30;background:rgba(0,0,0,0.35);"
        class="min-[960px]:hidden"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- Sidebar --}}
    @include('client.layouts._sidebar')

    {{-- Main --}}
    <div class="nrh-main">

        {{-- Topbar --}}
        @include('client.layouts._navbar')

        {{-- Page content --}}
        <div class="nrh-content">

            {{-- Flash success --}}
            @if (session('success'))
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);"
                     x-data x-init="setTimeout(() => $el.remove(), 5000)">
                    <svg style="width:15px;height:15px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <p style="font-size:13px;color:var(--emerald-800);font-weight:500;">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fbeeec;border:1px solid rgba(196,69,58,0.2);border-radius:var(--radius);"
                     x-data x-init="setTimeout(() => $el.remove(), 5000)">
                    <svg style="width:15px;height:15px;color:var(--danger);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                    </svg>
                    <p style="font-size:13px;color:var(--danger);font-weight:500;">{{ session('error') }}</p>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    @stack('scripts')

    <script>
        // Theme toggle — persists to localStorage
        (function() {
            const saved = localStorage.getItem('nrh-theme') || 'light';
            document.getElementById('htmlRoot').setAttribute('data-theme', saved);
        })();
        function toggleTheme() {
            const root = document.getElementById('htmlRoot');
            const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            root.setAttribute('data-theme', next);
            localStorage.setItem('nrh-theme', next);
            const label = document.getElementById('themeLabel');
            if (label) label.textContent = next === 'dark' ? 'Dark mode' : 'Light mode';
        }
    </script>
</body>
</html>
