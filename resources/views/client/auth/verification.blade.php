@php
    $authSubValue = $method === 'app'
        ? 'Open your authenticator app and enter the current 6-digit code.'
        : 'We emailed a 6-digit code to '.e($email).'.';
    $stepLabelValue = $method === 'app'
        ? 'AUTHENTICATOR APP'
        : '<span id="otpTimer">EXPIRES IN 10:00</span>';
@endphp
<x-client.layouts.auth
    title="Two-Factor Verification"
    authTitle='Enter your <em>6-digit</em> code.'
    :authSub="$authSubValue"
    step="2 · 2"
    :stepLabel="$stepLabelValue"
    footerText="Wrong account?"
    footerLink="← Back to sign in"
    :footerHref="route('client.login')"
>

    {{-- Custom left panel: trust chain --}}
    <x-slot name="leftPanel">

        {{-- Seal --}}
        <svg class="auth-seal" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="60" r="58" stroke="#D4AF37" stroke-width="1" opacity="0.6"/>
            <circle cx="60" cy="60" r="48" stroke="#D4AF37" stroke-width="0.5" opacity="0.5"/>
            <rect x="42" y="50" width="36" height="28" rx="3" stroke="#D4AF37" stroke-width="1.2" fill="none"/>
            <path d="M48 50 V42 a12 12 0 0 1 24 0 V50" stroke="#D4AF37" stroke-width="1.2" fill="none"/>
            <circle cx="60" cy="63" r="2" fill="#D4AF37"/>
            <path d="M60 65 v5" stroke="#D4AF37" stroke-width="1.4"/>
            <text x="60" y="110" fill="#D4AF37" font-family="Fraunces, serif" font-size="6" text-anchor="middle" letter-spacing="2">SECOND · FACTOR</text>
        </svg>

        {{-- Brand --}}
        <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:2;">
            <img src="{{ asset('nrh-logo.png') }}" alt="NRH Intelligence" style="height:36px;width:auto;flex-shrink:0;">
            <div style="font-family:var(--font-display);font-weight:600;font-size:17px;color:#f5ecd1;letter-spacing:0.01em;">
                NRH <em style="color:var(--gold-400);font-style:italic;">Intelligence</em>
            </div>
        </div>

        {{-- Hero --}}
        <div style="position:relative;z-index:2;max-width:460px;">
            <div style="font-family:var(--font-mono);font-size:10px;text-transform:uppercase;letter-spacing:0.22em;color:var(--gold-400);margin-bottom:20px;">
                ◆ &nbsp;Zero-trust verification
            </div>
            <h2 style="font-family:var(--font-display);font-weight:400;font-size:40px;line-height:1.1;letter-spacing:-0.015em;color:#f5ecd1;margin:0 0 20px;">
                One more step to protect <em style="font-style:italic;color:var(--gold-400);">every candidate's</em> record.
            </h2>
            <p style="font-size:14px;line-height:1.6;color:rgba(245,236,209,0.65);margin:0;">
                NRH holds sensitive PII — SSNs, criminal histories, credit files. A second factor isn't optional. It's a covenant with the people being screened.
            </p>
        </div>

        {{-- Trust chain --}}
        <div style="position:relative;z-index:2;display:flex;flex-direction:column;gap:10px;padding-top:28px;border-top:1px solid rgba(212,175,55,0.2);">
            <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:rgba(245,236,209,0.7);">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--gold-400);flex-shrink:0;"></span>
                <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.15em;color:rgba(245,236,209,0.5);width:80px;">LAYER 01</span>
                Password · <span style="color:var(--emerald-500);margin-left:4px;">✓ verified</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:#f5ecd1;">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--gold-400);box-shadow:0 0 0 4px rgba(212,175,55,0.2);flex-shrink:0;animation:pulse 2s ease-in-out infinite;"></span>
                <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.15em;color:rgba(245,236,209,0.5);width:80px;">LAYER 02</span>
                <b>Two-factor authentication</b>
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:rgba(245,236,209,0.45);">
                <span style="width:6px;height:6px;border-radius:50%;background:rgba(212,175,55,0.3);flex-shrink:0;"></span>
                <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.15em;color:rgba(245,236,209,0.4);width:80px;">LAYER 03</span>
                Workspace access
            </div>
        </div>

        <style>
            @keyframes pulse {
                0%, 100% { box-shadow: 0 0 0 4px rgba(212,175,55,0.2); }
                50%       { box-shadow: 0 0 0 8px rgba(212,175,55,0.08); }
            }
        </style>

    </x-slot>

    {{-- Status --}}
    @if (session('status'))
        <div style="display:flex;align-items:center;gap:10px;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);padding:10px 14px;">
            <svg style="width:15px;height:15px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <p style="font-size:13px;color:var(--emerald-800);font-weight:500;margin:0;">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div style="display:flex;align-items:center;gap:10px;background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);border-left:3px solid #c4453a;border-radius:6px;padding:10px 14px;">
            <svg style="width:15px;height:15px;color:#c4453a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            <p style="font-size:13px;color:#c4453a;font-weight:500;margin:0;">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- OTP inputs --}}
    <div>
        <div class="field-label" style="margin-bottom:10px;">Verification code</div>
        <div class="otp-wrap" id="otpWrap">
            <input class="otp-input" maxlength="1" inputmode="numeric" autocomplete="one-time-code">
            <input class="otp-input" maxlength="1" inputmode="numeric">
            <input class="otp-input" maxlength="1" inputmode="numeric">
            <div class="otp-sep">·</div>
            <input class="otp-input" maxlength="1" inputmode="numeric">
            <input class="otp-input" maxlength="1" inputmode="numeric">
            <input class="otp-input" maxlength="1" inputmode="numeric">
        </div>
        @if ($method === 'email')
        <div class="resend-row" style="margin-top:14px;">
            <span>Didn't receive a code?</span>
            <span>
                <span id="resendWaiting">Resend in <span id="resendCount">28</span>s</span>
                <form id="resendForm" method="POST" action="{{ route('client.verification.resend') }}" style="display:none;">
                    @csrf
                    <button type="submit" style="background:none;border:none;padding:0;font-family:var(--font-ui);font-size:12px;color:var(--emerald-700);font-weight:600;cursor:pointer;">Resend code</button>
                </form>
            </span>
        </div>
        @endif
    </div>

    {{-- Action buttons --}}
    <form method="POST" action="{{ route('client.verification.submit') }}" id="otpForm">
        @csrf
        <input type="hidden" name="code" id="otpCodeInput">
        <div style="display:flex;gap:10px;">
            <a href="{{ route('client.login') }}" class="btn btn-ghost" style="flex:0 0 auto;justify-content:center;">Cancel</a>
            <button type="submit" class="btn-auth" style="flex:1;">
                Verify and sign in
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
        </div>
    </form>

    {{-- Help --}}
    <div class="auth-meta">
        @if ($method === 'app')
            <span>Lost access to your authenticator app? <a href="{{ route('client.login') }}">Sign in again</a> or contact your administrator.</span>
        @else
            <span>Can't find the email? Check your spam folder, or use “Resend code” above.</span>
        @endif
    </div>

    @push('scripts')
    <script>
        // OTP input behaviour
        (function () {
            const inputs = Array.from(document.querySelectorAll('#otpWrap .otp-input'));
            const form   = document.getElementById('otpForm');
            const hidden = document.getElementById('otpCodeInput');

            function getCode() { return inputs.map(i => i.value).join(''); }

            inputs.forEach((inp, i) => {
                inp.addEventListener('input', (e) => {
                    const v = e.target.value.replace(/[^0-9]/g, '').slice(-1);
                    e.target.value = v;
                    v ? e.target.classList.add('filled') : e.target.classList.remove('filled');
                    if (v && inputs[i + 1]) inputs[i + 1].focus();
                    if (getCode().length === 6) { hidden.value = getCode(); form.submit(); }
                });
                inp.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && inputs[i - 1]) inputs[i - 1].focus();
                });
            });

            document.addEventListener('paste', (e) => {
                const txt = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
                if (!txt) return;
                e.preventDefault();
                txt.split('').forEach((ch, k) => {
                    if (inputs[k]) { inputs[k].value = ch; inputs[k].classList.add('filled'); }
                });
                const next = inputs[Math.min(txt.length, inputs.length - 1)];
                if (next) next.focus();
                if (txt.length === 6) { hidden.value = txt; form.submit(); }
            });

            // Set hidden value on manual form submit
            form.addEventListener('submit', () => { hidden.value = getCode(); });

            // Focus first input
            if (inputs[0]) inputs[0].focus();
        })();

        @if ($method === 'email')
        // Expiry countdown (10 min) — email codes only
        (function () {
            let secs = 600;
            const el = document.getElementById('otpTimer');
            if (!el) return;
            const t  = setInterval(() => {
                secs--;
                if (secs <= 0) { clearInterval(t); el.textContent = 'EXPIRED'; return; }
                const m = String(Math.floor(secs / 60)).padStart(1, '0');
                const s = String(secs % 60).padStart(2, '0');
                el.textContent = 'EXPIRES IN ' + m + ':' + s;
            }, 1000);
        })();

        // Resend countdown (28s) — email codes only
        (function () {
            let n       = 28;
            const count = document.getElementById('resendCount');
            const wait  = document.getElementById('resendWaiting');
            const resendForm = document.getElementById('resendForm');
            const t     = setInterval(() => {
                n--;
                if (n <= 0) {
                    clearInterval(t);
                    if (wait) wait.style.display = 'none';
                    if (resendForm) resendForm.style.display = 'inline';
                    return;
                }
                if (count) count.textContent = n;
            }, 1000);
        })();
        @endif
    </script>
    @endpush

</x-client.layouts.auth>
