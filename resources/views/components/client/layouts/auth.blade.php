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
    <title>{{ $title }} — NRH Intelligence</title>
    <link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:400,500,600,700|fraunces:400,500,600,700ital|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; }
        body {
            font-family: var(--font-ui);
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            font-feature-settings: "cv11", "ss01";
            background: var(--paper);
            color: var(--ink-900);
        }

        .auth-shell {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
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
            padding: 48px;
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
            right: -180px;
            bottom: -180px;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            border: 1px solid rgba(212,175,55,0.25);
            box-shadow:
                inset 0 0 0 20px rgba(212,175,55,0.04),
                inset 0 0 0 21px rgba(212,175,55,0.18),
                inset 0 0 0 60px transparent,
                inset 0 0 0 61px rgba(212,175,55,0.08);
            pointer-events: none;
        }
        .auth-seal {
            position: absolute;
            right: 48px; top: 48px;
            width: 110px; height: 110px;
            z-index: 2;
            opacity: 0.9;
        }

        /* ── Right form panel ── */
        .auth-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            background: var(--paper);
        }
        .auth-right-top {
            position: absolute; top: 28px; right: 32px;
            font-size: 12px; color: var(--ink-500);
        }
        .auth-right-top a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

        .auth-card {
            width: 100%;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .auth-kicker {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.22em;
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
            font-size: 34px;
            font-weight: 500;
            line-height: 1.1;
            letter-spacing: -0.015em;
            margin: 14px 0 0;
            color: var(--ink-900);
        }
        .auth-title em { font-style: italic; color: var(--emerald-700); font-weight: 500; }
        .auth-sub {
            font-size: 14px;
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
            font-size: 11px;
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
        .auth-input {
            width: 100%;
            padding: 12px 14px 12px 38px;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 8px;
            font-family: var(--font-ui);
            font-size: 14px;
            color: var(--ink-900);
            outline: none;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        .auth-input:focus { border-color: var(--emerald-600); box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
        .auth-input::placeholder { color: var(--ink-400); }
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
            padding: 14px;
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
        .btn-auth:hover { background: var(--emerald-800); transform: translateY(-1px); }
        .btn-auth:active { transform: translateY(0); }
        .btn-auth svg { width: 16px; height: 16px; }

        /* Session strip */
        .session-strip {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            background: var(--paper-2);
            border: 1px solid var(--line);
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ink-500);
        }
        .session-strip b { color: var(--ink-900); font-weight: 600; }

        /* Auth meta */
        .auth-meta {
            display: flex; justify-content: space-between;
            font-size: 12px;
            color: var(--ink-500);
            padding-top: 8px;
        }
        .auth-meta a { color: var(--emerald-700); font-weight: 600; cursor: pointer; text-decoration: none; }

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
<body x-data>

    <div class="auth-shell">

        {{-- ── Left panel ── --}}
        <aside class="auth-left">
            @if ($leftPanel ?? false)
                {{ $leftPanel }}
            @else
                {{-- Decorative seal SVG --}}
                <svg class="auth-seal" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="58" stroke="#D4AF37" stroke-width="1" opacity="0.6"/>
                    <circle cx="60" cy="60" r="48" stroke="#D4AF37" stroke-width="0.5" opacity="0.5"/>
                    <path d="M60 22 L84 34 V58 Q84 80 60 94 Q36 80 36 58 V34 Z" stroke="#D4AF37" stroke-width="1.2" fill="none"/>
                    <path d="M50 60 l7 7 l14 -16" stroke="#D4AF37" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <text x="60" y="110" fill="#D4AF37" font-family="Fraunces, serif" font-size="6" text-anchor="middle" letter-spacing="2">EST · MMXIX</text>
                </svg>

                {{-- Brand --}}
                <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:2;">
                    <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:36px;width:auto;flex-shrink:0;">
                    <div style="font-family:var(--font-display);font-weight:600;font-size:17px;color:#f5ecd1;letter-spacing:0.01em;">
                        NRH <em style="color:var(--gold-400);font-style:italic;">Intelligence</em>
                    </div>
                </div>

                {{-- Hero --}}
                <div style="position:relative;z-index:2;max-width:440px;">
                    <div style="font-family:var(--font-mono);font-size:10px;text-transform:uppercase;letter-spacing:0.22em;color:var(--gold-400);margin-bottom:20px;">
                        ◆ &nbsp;Fiduciary-grade screening
                    </div>
                    <h2 style="font-family:var(--font-display);font-weight:400;font-size:44px;line-height:1.1;letter-spacing:-0.015em;color:#f5ecd1;margin:0 0 20px;">
                        The world's most <em style="font-style:italic;color:var(--gold-400);">trusted</em> names verify here.
                    </h2>
                    <p style="font-size:14px;line-height:1.6;color:rgba(245,236,209,0.65);margin:0;">
                        1.2M candidates screened across 184 jurisdictions. SOC 2 Type II, FCRA-certified, and PBSA-accredited since 2021.
                    </p>
                </div>

                {{-- Stats --}}
                <div style="position:relative;z-index:2;display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding-top:28px;border-top:1px solid rgba(212,175,55,0.2);">
                    <div>
                        <div style="font-family:var(--font-display);font-size:26px;font-weight:500;color:#f5ecd1;letter-spacing:-0.02em;">1.2<span style="color:var(--gold-400);font-size:18px;">M</span></div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:rgba(245,236,209,0.5);margin-top:4px;">Screenings</div>
                    </div>
                    <div>
                        <div style="font-family:var(--font-display);font-size:26px;font-weight:500;color:#f5ecd1;letter-spacing:-0.02em;">184</div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:rgba(245,236,209,0.5);margin-top:4px;">Jurisdictions</div>
                    </div>
                    <div>
                        <div style="font-family:var(--font-display);font-size:26px;font-weight:500;color:#f5ecd1;letter-spacing:-0.02em;">99.4<span style="color:var(--gold-400);font-size:18px;">%</span></div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:rgba(245,236,209,0.5);margin-top:4px;">On-time SLA</div>
                    </div>
                </div>
            @endif
        </aside>

        {{-- ── Right panel ── --}}
        <div class="auth-right">
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

                <div class="auth-meta">
                    <span>Protected by reCAPTCHA Enterprise</span>
                    <span>
                        <a href="#">Terms</a> ·
                        <a href="#">Privacy</a> ·
                        <a href="#">FCRA notice</a>
                    </span>
                </div>
            </div>
        </div>

    </div>

@stack('scripts')
</body>
</html>
