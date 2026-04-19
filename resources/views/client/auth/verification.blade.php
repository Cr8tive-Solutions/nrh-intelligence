<x-client.layouts.auth
    title="Two-Factor Verification"
    authTitle='One more step to protect <em>every record.</em>'
    authSub='NRH holds sensitive PII — SSNs, criminal histories, credit files. A second factor is a covenant with the people being screened.'
    step="2 · 2"
    stepLabel="ZERO-TRUST · 2FA"
    footerText="Wrong account?"
    footerLink="Back to sign in →"
    :footerHref="route('client.login')"
>

    {{-- Left panel override: 2FA seal --}}
    @push('auth-seal-override')
    <svg class="auth-seal" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="60" cy="60" r="58" stroke="#D4AF37" stroke-width="1" opacity="0.6"/>
        <circle cx="60" cy="60" r="48" stroke="#D4AF37" stroke-width="0.5" opacity="0.5"/>
        <rect x="42" y="50" width="36" height="28" rx="3" stroke="#D4AF37" stroke-width="1.2" fill="none"/>
        <path d="M48 50 V42 a12 12 0 0 1 24 0 V50" stroke="#D4AF37" stroke-width="1.2" fill="none"/>
        <circle cx="60" cy="63" r="2" fill="#D4AF37"/>
        <path d="M60 65 v5" stroke="#D4AF37" stroke-width="1.4"/>
        <text x="60" y="110" fill="#D4AF37" font-family="Fraunces, serif" font-size="6" text-anchor="middle" letter-spacing="2">SECOND · FACTOR</text>
    </svg>
    @endpush

    {{-- Error --}}
    @if ($errors->any())
        <div style="display:flex;align-items:center;gap:10px;background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);border-left:3px solid #c4453a;border-radius:6px;padding:10px 14px;">
            <svg style="width:15px;height:15px;color:#c4453a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            <p style="font-size:13px;color:#c4453a;font-weight:500;">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- OTP form --}}
    <form method="POST" action="{{ route('client.verification.submit') }}"
        x-data="{
            digits: ['','','','','',''],
            get code() { return this.digits.join(''); },
            handleInput(i, e) {
                const val = e.target.value.replace(/\D/g,'').slice(-1);
                this.digits[i] = val;
                if (val && i < 5) $refs['d'+(i+1)].focus();
            },
            handleKeydown(i, e) {
                if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                    this.digits[i-1] = '';
                    $refs['d'+(i-1)].focus();
                }
            },
            handlePaste(e) {
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
                text.split('').forEach((c,i) => { if(i < 6) this.digits[i] = c; });
                $nextTick(() => $refs['d'+(Math.min(text.length,5))].focus());
            }
        }"
        @paste.prevent="handlePaste($event)"
        style="display:flex;flex-direction:column;gap:20px;"
    >
        @csrf
        <input type="hidden" name="code" :value="code">

        {{-- 6-digit OTP --}}
        <div>
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);font-weight:600;margin-bottom:14px;">
                Enter the 6-digit code sent to <b style="color:var(--ink-900);">{{ session('2fa_email', 'your authenticator') }}</b>
            </div>
            <div class="otp-wrap">
                <template x-for="i in [0,1,2]" :key="i">
                    <input
                        :x-ref="'d'+i"
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        :value="digits[i]"
                        @input="handleInput(i, $event)"
                        @keydown="handleKeydown(i, $event)"
                        :class="digits[i] ? 'otp-input filled' : 'otp-input'"
                    />
                </template>
                <span class="otp-sep">·</span>
                <template x-for="i in [3,4,5]" :key="i">
                    <input
                        :x-ref="'d'+i"
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        :value="digits[i]"
                        @input="handleInput(i, $event)"
                        @keydown="handleKeydown(i, $event)"
                        :class="digits[i] ? 'otp-input filled' : 'otp-input'"
                    />
                </template>
            </div>
        </div>

        {{-- Resend row --}}
        <div class="resend-row">
            <span x-data="{ seconds: 28 }" x-init="setInterval(() => { if (seconds > 0) seconds--; }, 1000)">
                <span x-show="seconds > 0" style="color:var(--ink-400);">Resend in <b x-text="seconds + 's'"></b></span>
                <span x-show="seconds === 0">
                    <form method="POST" action="{{ route('client.verification.resend') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:none;border:none;padding:0;font-family:var(--font-ui);font-size:12px;color:var(--emerald-700);font-weight:600;cursor:pointer;">
                            Resend code
                        </button>
                    </form>
                </span>
            </span>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--ink-400);">EXPIRES IN 10 MIN</span>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth">
            Verify &amp; access workspace
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>

    </form>

    {{-- Method picker --}}
    <div>
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.14em;color:var(--ink-500);font-weight:600;margin-bottom:10px;">Other verification methods</div>
        <div class="method-list">
            <button type="button" class="method primary">
                <div class="method-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                </div>
                <div>
                    <div class="method-name">NRH Authenticator</div>
                    <div class="method-desc">TOTP from your authenticator app</div>
                </div>
                <span class="method-badge">Primary</span>
            </button>
            <button type="button" class="method">
                <div class="method-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.86 13 19.79 19.79 0 0 1 1.77 4.4 2 2 0 0 1 3.75 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l1.17-1.06a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                    <div class="method-name">SMS to ···1847</div>
                    <div class="method-desc">Text message fallback</div>
                </div>
                <span class="method-badge">Fallback</span>
            </button>
            <button type="button" class="method">
                <div class="method-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                </div>
                <div>
                    <div class="method-name">YubiKey / Hardware</div>
                    <div class="method-desc">FIDO2 / WebAuthn</div>
                </div>
                <span class="method-badge">Hardware</span>
            </button>
        </div>
    </div>

</x-client.layouts.auth>
