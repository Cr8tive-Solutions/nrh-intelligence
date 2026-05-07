<x-client.layouts.auth
    title="Access by invitation"
    authTitle='Access is by <em>invitation only.</em>'
    authSub="NRH Intelligence onboards customers manually after the SLA is signed and the search scopes have been configured. Your account is created and emailed to you by the NRH team."
    step=""
    stepLabel=""
    footerText="Already received an invitation?"
    footerLink="Sign in →"
    :footerHref="route('client.login')"
>

    <div style="display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;align-items:flex-start;gap:12px;background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-left:3px solid var(--emerald-600);border-radius:6px;padding:14px 16px;">
            <svg style="width:18px;height:18px;color:var(--emerald-700);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
            <div>
                <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 4px;">How onboarding works</p>
                <ol style="font-size:13px;color:var(--ink-700);line-height:1.6;margin:0;padding-left:18px;">
                    <li>NRH and your organisation sign the Service Agreement.</li>
                    <li>NRH configures your agreed search scopes and pricing.</li>
                    <li>NRH emails an activation link to your HR and Accounts contacts.</li>
                    <li>Each contact clicks the link, sets a password, and gains portal access.</li>
                </ol>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('client.login') }}" class="btn-auth" style="text-decoration:none;justify-content:center;">
                Go to sign in
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="mailto:hello@nrhintelligence.com?subject=Portal access enquiry" style="font-size:12px;color:var(--ink-500);text-align:center;text-decoration:none;">
                Contact NRH to start onboarding →
            </a>
        </div>
    </div>

</x-client.layouts.auth>
