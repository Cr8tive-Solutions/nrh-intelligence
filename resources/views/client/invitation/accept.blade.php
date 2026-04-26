<x-client.layouts.auth
    title="Activate account"
    authTitle='Welcome, <em>{{ $user->name }}.</em>'
    authSub='Set a password to activate your account at {{ $customer->name }}. Your invitation expires {{ $invitation->expires_at->diffForHumans() }}.'
    step="1 · 1"
    stepLabel="ACTIVATION"
    footerText="Already activated?"
    footerLink="Sign in →"
>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div style="display:flex;align-items:center;gap:10px;background:rgba(196,69,58,0.08);border:1px solid rgba(196,69,58,0.25);border-left:3px solid #c4453a;border-radius:6px;padding:10px 14px;">
            <svg style="width:15px;height:15px;color:#c4453a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            <p style="font-size:13px;color:#c4453a;font-weight:500;">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('client.invitation.accept', $invitation->token) }}" style="display:flex;flex-direction:column;gap:18px;">
        @csrf

        {{-- Email (read-only) --}}
        <div class="field">
            <div class="field-label">Work email</div>
            <div class="input-wrap">
                <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                <input
                    class="auth-input"
                    id="email"
                    type="email"
                    value="{{ $user->email }}"
                    readonly
                    style="background:var(--paper);color:var(--ink-500);cursor:not-allowed;"
                />
            </div>
        </div>

        {{-- Password --}}
        <div class="field" x-data="{ show: false }">
            <div class="field-label">
                <span>Set password</span>
                <span style="font-size:11px;color:var(--ink-400);font-weight:400;">Min 8 characters</span>
            </div>
            <div class="input-wrap">
                <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <input
                    class="auth-input"
                    id="password"
                    name="password"
                    :type="show ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="••••••••••••"
                    style="padding-right:42px;"
                    required
                    minlength="8"
                />
                <button type="button" class="eye-btn" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" :aria-pressed="show.toString()">
                    <svg x-show="!show" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg x-show="show" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
        </div>

        {{-- Confirm password --}}
        <div class="field">
            <div class="field-label">Confirm password</div>
            <div class="input-wrap">
                <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <input
                    class="auth-input"
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="••••••••••••"
                    required
                    minlength="8"
                />
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth">
            Activate account
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>

    </form>

    <div class="session-strip">
        <span>{{ $customer->name }}</span>
        <span style="text-align:right;">SECURE · TLS 1.3</span>
    </div>

</x-client.layouts.auth>
