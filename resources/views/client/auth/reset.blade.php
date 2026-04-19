<x-client.layouts.auth
    title="Set New Password"
    authTitle='Set new <em>password.</em>'
    authSub='Must be at least 8 characters. Choose something strong.'
    step="—"
    stepLabel="CREDENTIAL RESET"
    footerText="Back to sign in?"
    footerLink="← Login"
    :footerHref="route('client.login')"
>

    @if ($errors->any())
        <div style="padding:12px 14px;background:rgba(196,69,58,0.06);border:1px solid rgba(196,69,58,0.2);border-radius:var(--radius);margin-bottom:16px;">
            <p style="font-size:12px;color:var(--danger);margin:0;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.reset.process') }}" style="display:flex;flex-direction:column;gap:14px;" x-data>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label class="field-label">Email address</label>
            <input type="email" name="email" value="{{ old('email', request('email')) }}" readonly
                class="auth-input"
                style="background:var(--paper);color:var(--ink-400);cursor:not-allowed;" />
        </div>

        <div class="field" x-data="{ show: false }">
            <label class="field-label">New password</label>
            <div style="position:relative;">
                <input :type="show ? 'text' : 'password'" name="password" class="auth-input" placeholder="Min. 8 characters"
                    style="padding-right:44px;"
                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
                <button type="button" @click="show = !show"
                    style="position:absolute;right:0;top:0;bottom:0;padding:0 12px;background:none;border:none;cursor:pointer;color:var(--ink-400);"
                    onmouseover="this.style.color='var(--ink-700)'" onmouseout="this.style.color='var(--ink-400)'">
                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="field">
            <label class="field-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="auth-input" placeholder="Re-enter new password"
                onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
        </div>

        <button type="submit" class="btn-auth">Reset password</button>
    </form>

</x-client.layouts.auth>
