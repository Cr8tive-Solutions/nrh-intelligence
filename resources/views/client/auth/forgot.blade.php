<x-client.layouts.auth
    title="Forgot Password"
    authTitle='Reset your <em>password.</em>'
    authSub="Enter your email and we'll send you a secure reset link."
    step="—"
    stepLabel="ACCOUNT RECOVERY"
    footerText="Remembered it?"
    footerLink="← Sign in"
    :footerHref="route('client.login')"
>

    @if (session('status'))
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-radius:var(--radius);margin-bottom:16px;">
            <svg style="width:15px;height:15px;color:var(--emerald-700);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <p style="font-size:13px;color:var(--emerald-800);font-weight:500;margin:0;">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div style="padding:12px 14px;background:rgba(196,69,58,0.06);border:1px solid rgba(196,69,58,0.2);border-radius:var(--radius);margin-bottom:16px;">
            <p style="font-size:12px;color:var(--danger);margin:0;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.forgot.submit') }}" style="display:flex;flex-direction:column;gap:14px;">
        @csrf

        <div class="field">
            <label class="field-label">Email address</label>
            <div class="input-wrap">
                <svg class="lead" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                </svg>
                <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="you@company.com" autocomplete="email"
                    onfocus="this.style.borderColor='var(--emerald-600)'" onblur="this.style.borderColor='var(--line)'" />
            </div>
        </div>

        <button type="submit" class="btn-auth">Send reset link</button>
    </form>

</x-client.layouts.auth>
