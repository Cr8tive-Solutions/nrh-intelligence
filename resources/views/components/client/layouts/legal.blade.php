@props([
    'title',
    'heading',
    'intro' => null,
    'locale' => 'en',
    'languages' => [],
])
@php
    $labels = [
        'en' => ['legal' => 'Legal', 'effective' => 'Effective', 'issued' => 'Issued by', 'signin' => 'Sign in →', 'terms' => 'Terms', 'privacy' => 'Privacy'],
        'ms' => ['legal' => 'Perundangan', 'effective' => 'Berkuat kuasa', 'issued' => 'Dikeluarkan oleh', 'signin' => 'Log masuk →', 'terms' => 'Terma', 'privacy' => 'Privasi'],
    ][$locale];
    $effectiveDate = \Illuminate\Support\Carbon::parse(config('app.legal_effective_date'))->locale($locale)->translatedFormat('j F Y');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — KYRO</title>
    <script>(function(){try{if(localStorage.getItem('nrh-theme')==='dark'){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}}());</script>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter+Tight:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: var(--font-ui);
            font-size: 15px;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
            background: var(--paper);
            color: var(--ink-700);
        }

        /* Header band */
        .lg-head {
            background:
                radial-gradient(1200px 600px at 20% 0%, rgba(212,175,55,0.09), transparent 60%),
                radial-gradient(900px 600px at 100% 100%, rgba(5,150,105,0.14), transparent 60%),
                linear-gradient(170deg, #023527 0%, #044d39 60%, #011d15 100%);
            color: #e9efeb;
            padding: 28px 16px 56px;
            position: relative;
            overflow: hidden;
        }
        .lg-head::before {
            content: "";
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(212,175,55,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,175,55,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        .lg-wrap { max-width: 760px; margin: 0 auto; position: relative; z-index: 2; }
        .lg-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 48px; }
        .lg-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .lg-brand img { width: 40px; height: 40px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5)); }
        .lg-brand-name { font-family: var(--font-display); font-weight: 600; font-size: 18px; color: #f5ecd1; }
        .lg-brand-sub { font-size: 9px; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(212,175,55,0.6); margin-top: 2px; }
        .lg-nav { display: flex; align-items: center; gap: 18px; font-size: 13px; }
        .lg-nav a { color: rgba(233,239,235,0.7); text-decoration: none; font-weight: 500; }
        .lg-nav a:hover, .lg-nav a.active { color: #d4af37; }
        .lg-toggle {
            width: 32px; height: 32px; display: grid; place-items: center;
            border: 1px solid rgba(212,175,55,0.3); background: transparent; border-radius: 6px;
            color: rgba(233,239,235,0.7); cursor: pointer;
        }
        .lg-toggle:hover { color: #d4af37; border-color: #d4af37; }
        .lg-toggle svg { width: 15px; height: 15px; }
        .lg-eyebrow {
            font-family: var(--font-mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.22em;
            color: #d4af37; margin-bottom: 16px; display: flex; align-items: center; gap: 12px;
        }
        .lg-eyebrow::before { content: "◆"; }
        .lg-head h1 {
            font-family: var(--font-display); font-weight: 400; font-size: 44px; line-height: 1.08;
            letter-spacing: -0.015em; color: #fff; margin: 0 0 14px;
        }
        .lg-head h1 em { font-style: italic; color: #d4af37; font-weight: 500; }
        .lg-head p { margin: 0; color: rgba(233,239,235,0.65); font-size: 14px; max-width: 560px; }

        /* Body */
        .lg-body { padding: 48px 16px 64px; }
        .lg-body h2 {
            font-family: var(--font-display); font-weight: 500; font-size: 22px; line-height: 1.25;
            color: var(--ink-900); margin: 40px 0 12px; letter-spacing: -0.01em;
            scroll-margin-top: 24px;
        }
        .lg-body h2 .num { font-family: var(--font-mono); font-size: 12px; color: var(--emerald-700); margin-right: 10px; letter-spacing: 0.08em; }
        .lg-body h3 { font-size: 15px; font-weight: 600; color: var(--ink-900); margin: 22px 0 6px; }
        .lg-body p { margin: 0 0 12px; }
        .lg-body ul { margin: 0 0 12px; padding-left: 20px; list-style: disc; }
        .lg-body li::marker { color: var(--emerald-700); }
        .lg-body li { margin-bottom: 6px; }
        .lg-body a { color: var(--emerald-700); font-weight: 600; text-decoration: none; }
        .lg-body a:hover { text-decoration: underline; }
        .lg-body b, .lg-body strong { color: var(--ink-900); }
        .lg-meta {
            display: flex; flex-wrap: wrap; gap: 8px 24px;
            padding: 12px 16px; border: 1px solid var(--line); border-radius: 8px; background: var(--paper-2);
            font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-500);
        }
        .lg-meta b { color: var(--ink-900); font-weight: 600; }
        .lg-lang { margin-left: auto; display: inline-flex; gap: 4px; }
        .lg-lang a {
            padding: 2px 8px; border-radius: 999px; border: 1px solid var(--line);
            color: var(--ink-500); text-decoration: none; font-weight: 600;
        }
        .lg-lang a.active { background: var(--emerald-700); border-color: var(--emerald-700); color: #fff; }
        .lg-toc {
            margin: 24px 0 0; padding: 18px 20px; border: 1px solid var(--line); border-radius: 8px; background: var(--card);
        }
        .lg-toc-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.14em; font-weight: 600; color: var(--ink-500); margin-bottom: 10px; }
        .lg-toc ol { margin: 0; padding-left: 20px; list-style: decimal; columns: 2; column-gap: 32px; font-size: 14px; }
        .lg-toc li { margin-bottom: 4px; break-inside: avoid; }
        .lg-table { width: 100%; border-collapse: collapse; margin: 8px 0 16px; font-size: 14px; }
        .lg-table th, .lg-table td { border: 1px solid var(--line); padding: 8px 12px; text-align: left; vertical-align: top; }
        .lg-table th { background: var(--paper-2); color: var(--ink-900); font-weight: 600; }
        .lg-contact { border-left: 3px solid var(--emerald-700); background: var(--card); border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 12px 0; }
        .lg-contact p { margin: 0 0 4px; }

        .lg-foot {
            border-top: 1px solid var(--line); padding: 24px 16px;
            font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--ink-500);
        }
        .lg-foot .lg-wrap { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .lg-foot a { color: var(--emerald-700); text-decoration: none; font-weight: 600; }

        @media (max-width: 640px) {
            .lg-head h1 { font-size: 34px; }
            .lg-nav .hide-sm { display: none; }
            .lg-toc ol { columns: 1; }
            .lg-table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body x-data="{
    dark: document.documentElement.getAttribute('data-theme') === 'dark',
    toggleDark() {
        this.dark = !this.dark;
        document.documentElement.setAttribute('data-theme', this.dark ? 'dark' : 'light');
        try { localStorage.setItem('nrh-theme', this.dark ? 'dark' : 'light'); } catch (e) {}
    }
}">

    <header class="lg-head">
        <div class="lg-wrap">
            <div class="lg-bar">
                <a href="{{ route('client.login') }}" class="lg-brand">
                    <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence">
                    <div>
                        <div class="lg-brand-name">KYRO</div>
                        <div class="lg-brand-sub">Client Portal</div>
                    </div>
                </a>
                <nav class="lg-nav">
                    <a href="{{ route('client.terms') }}" class="{{ request()->routeIs('client.terms') ? 'active' : '' }}">{{ $labels['terms'] }}</a>
                    <a href="{{ route('client.privacy') }}" class="{{ request()->routeIs('client.privacy*') ? 'active' : '' }}">{{ $labels['privacy'] }}</a>
                    <a href="{{ route('client.login') }}" class="hide-sm">{{ $labels['signin'] }}</a>
                    <button type="button" class="lg-toggle" @click="toggleDark()" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                        <svg x-show="!dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="5"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        <svg x-show="dark" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </nav>
            </div>
            <div class="lg-eyebrow">{{ $labels['legal'] }}</div>
            <h1>{!! $heading !!}</h1>
            @if ($intro)
                <p>{{ $intro }}</p>
            @endif
        </div>
    </header>

    <main class="lg-body">
        <div class="lg-wrap">
            <div class="lg-meta">
                <span>{{ $labels['effective'] }} · <b>{{ $effectiveDate }}</b></span>
                <span>{{ $labels['issued'] }} · <b>{{ config('app.company.name') }}</b></span>
                @if ($languages)
                    <span class="lg-lang">
                        @foreach ($languages as $language)
                            <a href="{{ $language['url'] }}" hreflang="{{ $language['locale'] }}" class="{{ $language['locale'] === $locale ? 'active' : '' }}">{{ $language['label'] }}</a>
                        @endforeach
                    </span>
                @endif
            </div>

            {{ $slot }}
        </div>
    </main>

    <footer class="lg-foot">
        <div class="lg-wrap">
            <span>© {{ now()->year }} {{ config('app.company.name') }} · {{ config('app.company.reg_no') }}</span>
            <span><a href="{{ route('client.terms') }}">{{ $labels['terms'] }}</a> · <a href="{{ route('client.privacy') }}">{{ $labels['privacy'] }}</a></span>
        </div>
    </footer>

</body>
</html>
