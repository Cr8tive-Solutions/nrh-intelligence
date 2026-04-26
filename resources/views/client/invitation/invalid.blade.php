<x-client.layouts.auth
    title="Invitation unavailable"
    authTitle='Invitation <em>unavailable.</em>'
    authSub="This activation link can't be used right now. Details are below."
    step=""
    stepLabel=""
    footerText="Have an account?"
    footerLink="Sign in →"
>

    <div style="display:flex;align-items:flex-start;gap:12px;background:rgba(184,147,31,0.08);border:1px solid rgba(184,147,31,0.25);border-left:3px solid var(--gold-600);border-radius:6px;padding:14px 16px;">
        <svg style="width:18px;height:18px;color:var(--gold-700);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
        <div>
            @if ($reason === 'not_found')
                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 4px;">Invalid invitation link</p>
                <p style="font-size:13px;color:var(--ink-700);margin:0;">The link may be mistyped or the invitation was revoked. Ask your administrator to send a new invitation.</p>
            @elseif ($reason === 'expired')
                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 4px;">This invitation has expired</p>
                <p style="font-size:13px;color:var(--ink-700);margin:0;">
                    Ask your administrator@if (! empty($companyName)) at <b>{{ $companyName }}</b>@endif to resend it.
                </p>
            @endif
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;margin-top:18px;">
        <a href="{{ route('client.login') }}" class="btn-auth" style="text-decoration:none;justify-content:center;">
            Go to sign in
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="mailto:support@nrhintelligence.com" style="font-size:12px;color:var(--ink-500);text-align:center;text-decoration:none;">
            Contact support →
        </a>
    </div>

</x-client.layouts.auth>
