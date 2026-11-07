@props([
    'title'      => 'Client Portal',
    'footerText' => "New here?",
    'footerLink' => 'Request access →',
    'footerHref' => null,
    'step'       => '1 · 2',
    'stepLabel'  => 'SECURE · TLS 1.3',
    'authTitle'  => 'Welcome <em>back.</em>',
    'authSub'    => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" id="htmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — KYRO</title>
    <script>(function(){try{if(localStorage.getItem('nrh-theme')==='dark'){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}}());</script>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter+Tight:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; }
        body {
            font-family: var(--font-ui);
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            line-height: 1.45;
            background: var(--paper);
            color: var(--ink-900);
        }

        .auth-shell {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            min-height: 100vh;
        }
        @media (max-width: 960px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-left { display: none; }
        }

        /* ── Left marketing panel ── */
        .auth-left {
            background:
                radial-gradient(1200px 600px at 20% 0%, rgba(212,175,55,0.09), transparent 60%),
                radial-gradient(900px 600px at 100% 100%, rgba(5,150,105,0.14), transparent 60%),
                linear-gradient(170deg, #023527 0%, #044d39 60%, #011d15 100%);
            color: #e9efeb;
            padding: 48px 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(212,175,55,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,175,55,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            mask-image: linear-gradient(180deg, transparent, black 20%, black 80%, transparent);
        }
        .auth-left::after {
            content: "";
            position: absolute;
            right: -160px;
            bottom: -160px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            border: 1px solid rgba(212,175,55,0.25);
            box-shadow:
                inset 0 0 0 20px rgba(212,175,55,0.04),
                inset 0 0 0 21px rgba(212,175,55,0.16),
                inset 0 0 0 60px transparent,
                inset 0 0 0 61px rgba(212,175,55,0.07);
            pointer-events: none;
        }

        /* Brand */
        .al-brand { display: flex; align-items: center; gap: 12px; position: relative; z-index: 2; }
        .al-logo { width: 44px; height: 44px; object-fit: contain; flex-shrink: 0; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5)); }
        .al-brand-name { font-family: var(--font-display); font-weight: 600; font-size: 18px; color: #f5ecd1; letter-spacing: 0.01em; }
        .al-brand-sub { font-size: 9px; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(212,175,55,0.6); margin-top: 3px; }

        /* Seal (decorative) */
        .al-seal {
            position: absolute; right: 52px; top: 44px;
            width: 110px; height: 110px;
            z-index: 2; opacity: 0.18;
            object-fit: contain;
            filter: brightness(1.4) saturate(0.3);
        }

        /* Hero copy */
        .al-hero { position: relative; z-index: 2; max-width: 500px; }
        .al-eyebrow {
            font-family: var(--font-mono);
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.22em;
            color: #d4af37; margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px;
        }
        .al-eyebrow::before { content: "◆"; flex-shrink: 0; }
        .al-hero h2 {
            font-family: var(--font-display);
            font-size: 46px; font-weight: 400;
            line-height: 1.05; letter-spacing: -0.015em;
            color: #fff; margin: 0 0 18px;
            text-wrap: pretty;
        }
        .al-hero h2 em { font-style: italic; color: #d4af37; font-weight: 500; }
        .al-hero p {
            font-size: 14px; line-height: 1.6;
            color: rgba(233,239,235,0.65);
            max-width: 420px; margin: 0;
        }

        /* Stats strip */
        .al-stats {
            display: grid; grid-template-columns: repeat(3, 1fr);
            border-top: 1px solid rgba(212,175,55,0.18);
            border-bottom: 1px solid rgba(212,175,55,0.18);
            padding: 18px 0; margin-top: 24px;
            position: relative; z-index: 2;
        }
        .al-stat { padding: 0 16px; border-right: 1px solid rgba(212,175,55,0.12); }
        .al-stat:first-child { padding-left: 0; }
        .al-stat:last-child { border-right: none; }
        .al-stat .v { font-family: var(--font-display); font-size: 24px; font-weight: 500; color: #fff; letter-spacing: -0.01em; }
        .al-stat .l { font-size: 10px; text-transform: uppercase; letter-spacing: 0.14em; color: rgba(233,239,235,0.5); margin-top: 4px; }

        /* Footer badges */
        .al-foot {
            position: relative; z-index: 2;
            display: flex; justify-content: space-between; align-items: center;
            font-family: var(--font-mono);
            font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase;
            color: rgba(233,239,235,0.45);
        }
        .al-badges { display: flex; gap: 10px; }
        .al-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 8px;
            border: 1px solid rgba(212,175,55,0.22);
            border-radius: 3px; color: #d4af37;
        }
        .al-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,0.2); }

        /* Theme toggle */
        .theme-toggle {
            position: absolute; top: 20px; right: 24px;
            width: 32px; height: 32px;
            display: grid; place-items: center;
            border: 1px solid var(--line);
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            color: var(--ink-500);
            transition: color 120ms, background 120ms, border-color 120ms;
            z-index: 10;
        }
        .theme-toggle:hover { border-color: var(--emerald-600); color: var(--emerald-700); background: rgba(4,108,78,0.05); }
        .theme-toggle svg { width: 15px; height: 15px; }

        /* ── Right form panel ── */
        .auth-right {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: max(48px, calc(50vh - 205px)) 48px 48px;
            position: relative;
            background: var(--paper);
        }
        .auth-right-top {
            position: absolute; top: 28px; right: 68px;
            font-size: 12px; color: var(--ink-500);
        }
        .auth-right-top a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

        .auth-card {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .auth-kicker {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--ink-500);
            display: flex; align-items: center; gap: 10px;
        }
        .auth-kicker .step {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            background: var(--emerald-50);
            color: var(--emerald-800);
            font-weight: 600;
        }
        .auth-kicker .step .sdot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--gold-500);
        }
        .auth-title {
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 500;
            line-height: 1.1;
            letter-spacing: -0.015em;
            margin: 14px 0 0;
            color: var(--ink-900);
        }
        .auth-title em { font-style: italic; color: var(--emerald-700); font-weight: 500; }
        .auth-sub {
            font-size: 13px;
            color: var(--ink-500);
            margin: 8px 0 0;
            line-height: 1.5;
        }
        .auth-sub b { color: var(--ink-900); font-weight: 600; }

        /* SSO */
        .sso-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .sso-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 11px 14px;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 8px;
            font-family: var(--font-ui);
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-900);
            cursor: pointer;
            transition: border-color 120ms ease, background 120ms ease;
        }
        .sso-btn:hover { border-color: var(--emerald-600); }
        .sso-btn svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Divider */
        .auth-divider {
            display: flex; align-items: center; gap: 14px;
            color: var(--ink-400);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
        }
        .auth-divider::before, .auth-divider::after {
            content: ""; flex: 1; height: 1px; background: var(--line);
        }

        /* Fields */
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--ink-500);
            font-weight: 600;
            display: flex; align-items: center; justify-content: space-between;
        }
        .field-label a { text-transform: none; letter-spacing: 0; font-size: 12px; color: var(--emerald-700); font-weight: 600; text-decoration: none; }
        .input-wrap { position: relative; }
        .input-wrap > svg.lead {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 16px; height: 16px; color: var(--ink-400);
        }
        input.auth-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 8px;
            font-family: var(--font-ui);
            font-size: 14px;
            color: var(--ink-900);
            outline: none;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        input.auth-input:focus { border-color: var(--emerald-600); box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
        input.auth-input::placeholder { color: var(--ink-400); }
        .eye-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: transparent; border: none; color: var(--ink-400); cursor: pointer;
            width: 28px; height: 28px; display: grid; place-items: center; border-radius: 4px;
        }
        .eye-btn:hover { color: var(--emerald-700); background: var(--emerald-50); }

        /* Checkbox */
        .checkbox-row {
            display: flex; align-items: center; justify-content: space-between;
            font-size: 13px; color: var(--ink-700);
        }
        .cbox { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; }
        .cbox input { display: none; }
        .cbox .cmark {
            width: 16px; height: 16px;
            border: 1px solid var(--ink-300);
            border-radius: 4px;
            background: var(--card);
            display: grid; place-items: center;
            transition: all 120ms ease;
        }
        .cbox .cmark svg { width: 11px; height: 11px; color: #fff; opacity: 0; }
        .cbox input:checked + .cmark { background: var(--emerald-700); border-color: var(--emerald-700); }
        .cbox input:checked + .cmark svg { opacity: 1; }

        /* Primary button */
        .btn-auth {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 13px 16px;
            background: var(--emerald-700);
            border: none;
            border-radius: 8px;
            font-family: var(--font-ui);
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 120ms ease, transform 120ms ease;
            box-shadow: inset 0 0 0 1px rgba(212,175,55,0.3), 0 1px 0 rgba(4,77,57,0.3);
        }
        .btn-auth:hover { background: var(--emerald-800); }
        .btn-auth:active { transform: translateY(1px); }
        .btn-auth svg { width: 15px; height: 15px; }

        /* Session strip */
        .session-strip {
            display: flex;
            justify-content: space-between;
            padding: 10px 14px;
            background: var(--paper-2);
            border: 1px solid var(--line);
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ink-500);
        }
        .session-strip b { color: var(--ink-900); font-weight: 600; }

        /* Auth meta */
        .auth-legal {
            position: absolute; bottom: 28px; left: 0; right: 0;
            display: flex; justify-content: center;
            font-size: 12px;
            color: var(--ink-500);
        }
        .auth-legal a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

        .auth-meta {
            display: flex; justify-content: space-between;
            font-size: 12px;
            color: var(--ink-500);
        }
        .auth-meta a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

        /* Trust chain (verification step) */
        .al-chain {
            display: flex; flex-direction: column; gap: 12px;
            border-top: 1px solid rgba(212,175,55,0.18);
            border-bottom: 1px solid rgba(212,175,55,0.18);
            padding: 18px 0; margin-top: 24px;
        }
        .al-chain-row { display: flex; align-items: center; gap: 12px; font-size: 12px; color: rgba(233,239,235,0.45); }
        .al-chain-row .n { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.14em; color: rgba(233,239,235,0.5); width: 72px; }
        .al-chain-row .pip { width: 6px; height: 6px; border-radius: 50%; background: rgba(212,175,55,0.3); flex-shrink: 0; margin: 0 1px; }
        .al-chain-row.done { color: rgba(233,239,235,0.7); }
        .al-chain-row.done .pip { background: #d4af37; }
        .al-chain-row.done .ok { color: #10b981; margin-left: 4px; }
        .al-chain-row.current { color: #fff; font-weight: 600; }
        .al-chain-row.current .pip { width: 8px; height: 8px; margin: 0; background: #d4af37; animation: al-pulse 2s ease-in-out infinite; }
        @keyframes al-pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(212,175,55,0.2); }
            50%      { box-shadow: 0 0 0 8px rgba(212,175,55,0.08); }
        }

        /* OTP inputs */
        .otp-wrap { display: flex; gap: 8px; justify-content: space-between; }
        .otp-input {
            width: 54px; height: 64px;
            text-align: center;
            font-family: var(--font-display);
            font-size: 28px; font-weight: 500;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 10px;
            color: var(--ink-900);
            outline: none;
            transition: all 150ms ease;
        }
        .otp-input:focus { border-color: var(--emerald-600); box-shadow: 0 0 0 3px rgba(5,150,105,0.14); transform: translateY(-1px); }
        .otp-input.filled { border-color: var(--emerald-600); background: var(--emerald-50); color: var(--emerald-800); }
        .otp-sep {
            display: grid; place-items: center;
            color: var(--ink-300);
            font-family: var(--font-display);
            font-size: 28px; font-weight: 400;
            user-select: none; padding: 0 2px;
        }

        .resend-row { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--ink-500); }
        .resend-row a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

        /* Method picker */
        .method-list { display: flex; flex-direction: column; gap: 8px; }
        .method {
            display: grid; grid-template-columns: 40px 1fr auto;
            gap: 14px; align-items: center;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--card);
            cursor: pointer;
            transition: all 150ms ease;
            text-align: left;
            width: 100%;
            font-family: var(--font-ui);
            color: var(--ink-900);
        }
        .method:hover { border-color: var(--emerald-600); transform: translateY(-1px); }
        .method.primary { border-color: var(--emerald-700); background: var(--emerald-50); }
        .method-icon {
            width: 40px; height: 40px;
            border-radius: 8px;
            display: grid; place-items: center;
            background: var(--paper-2);
            border: 1px solid var(--line);
        }
        .method.primary .method-icon { background: var(--emerald-100); border-color: rgba(4,108,78,0.2); }
        .method-icon svg { width: 18px; height: 18px; color: var(--ink-700); }
        .method.primary .method-icon svg { color: var(--emerald-700); }
        .method-name { font-size: 13px; font-weight: 600; color: var(--ink-900); }
        .method-desc { font-size: 11px; color: var(--ink-500); margin-top: 2px; }
        .method-badge {
            font-family: var(--font-mono); font-size: 9px; font-weight: 600;
            letter-spacing: 0.12em; text-transform: uppercase;
            padding: 2px 6px; border-radius: 999px;
            background: var(--emerald-50); color: var(--emerald-800);
        }
        .method.primary .method-badge { background: var(--emerald-700); color: #fff; }
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

    <div class="auth-shell">

        {{-- ── Left panel ── --}}
        <aside class="auth-left">
            @if ($leftPanel ?? false)
                {{ $leftPanel }}
            @else
                {{-- Brand --}}
                <div class="al-brand">
                    <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" class="al-logo">
                    <div>
                        <div class="al-brand-name">KYRO</div>
                        <div class="al-brand-sub">Client Portal</div>
                    </div>
                </div>

                <img src="{{ asset('nrh-logo.png') }}" alt="" class="al-seal">

                {{-- Hero --}}
                <div class="al-hero">
                    <div class="al-eyebrow">Background screening by NRH Intelligence</div>
                    <h2>Your screening, <em>in one place</em>.</h2>
                    <p>
                        Submit requests, follow every check as it progresses, and download your reports from one secure workspace.
                    </p>
                    <div class="al-stats">
                        <div class="al-stat">
                            <div class="v">VAPT</div>
                            <div class="l">{{ config('app.vapt_date') ? 'Tested '.config('app.vapt_date') : 'Security tested' }}</div>
                        </div>
                        <div class="al-stat">
                            <div class="v">2FA</div>
                            <div class="l">Every sign-in</div>
                        </div>
                        <div class="al-stat">
                            <div class="v">AES-256</div>
                            <div class="l">Encrypted files</div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="al-foot">
                    <span>NRH Intelligence Sdn. Bhd.</span>
                    <div class="al-badges">
                        <span class="al-badge"><span class="dot"></span> System online</span>
                    </div>
                </div>
            @endif
        </aside>

        {{-- ── Right panel ── --}}
        <div class="auth-right">
            <button type="button" class="theme-toggle" @click="toggleDark()" :title="dark ? 'Light mode' : 'Dark mode'" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="!dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="5"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                <svg x-show="dark" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
            <div class="auth-right-top">
                {{ $footerText }}
                <a href="{{ $footerHref ?? route('client.register') }}">{{ $footerLink }}</a>
            </div>

            <div class="auth-card">
                <div>
                    <div class="auth-kicker">
                        <span class="step"><span class="sdot"></span>STEP {{ $step }}</span>
                        <span>{!! $stepLabel !!}</span>
                    </div>
                    <h1 class="auth-title">{!! $authTitle !!}</h1>
                    @if($authSub)
                        <p class="auth-sub">{!! $authSub !!}</p>
                    @endif
                </div>

                {{ $slot }}
            </div>

            <div class="auth-legal">
                <span>
                    <a href="{{ config('app.terms_url') }}" target="_blank" rel="noopener">Terms</a> ·
                    <a href="{{ config('app.privacy_url') }}" target="_blank" rel="noopener">Privacy</a>
                </span>
            </div>
        </div>

    </div>

@stack('scripts')
</body>
</html>
