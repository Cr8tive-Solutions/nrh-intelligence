@php
    $copy = match ($state) {
        'submitted' => ['Registration <em>received.</em>', 'Thank you — your details are with the NRH team for verification.',
            'What happens next', 'We\'ll review your company details and email '.e($registration->email).' once your account is approved. You can then sign in with the password you just chose.', 'emerald'],
        'approved'  => ['Account <em>approved.</em>', 'Your registration has been approved.',
            'You can sign in now', 'Use '.e($registration->email).' and the password you chose when registering.', 'emerald'],
        'rejected'  => ['Registration <em>not approved.</em>', 'This registration was reviewed and not approved.',
            'Need help?', 'We\'ve emailed the reason to '.e($registration->email).'. Reply to that email or contact NRH if you have questions.', 'gold'],
        'expired'   => ['Link <em>expired.</em>', 'This registration link is no longer valid.',
            'Ask for a new link', 'Registration links are valid for 14 days. Contact the NRH team and we\'ll send you a fresh one.', 'gold'],
        default     => ['Link <em>unavailable.</em>', 'This registration link can\'t be used.',
            'Invalid or withdrawn link', 'The link may be mistyped, replaced by a newer one, or withdrawn. Contact the NRH team for help.', 'gold'],
    };
    [$title, $sub, $boxTitle, $boxText, $tone] = $copy;
    $green = $tone === 'emerald';
@endphp
<x-client.layouts.auth
    title="Registration"
    :authTitle="$title"
    :authSub="$sub"
    step=""
    stepLabel=""
    footerText="Have an account?"
    footerLink="Sign in →"
    :footerHref="route('client.login')"
>
    <div style="display:flex;align-items:flex-start;gap:12px;border-radius:6px;padding:14px 16px;
        {{ $green
            ? 'background:rgba(5,150,105,0.06);border:1px solid rgba(5,150,105,0.2);border-left:3px solid var(--emerald-600);'
            : 'background:rgba(184,147,31,0.08);border:1px solid rgba(184,147,31,0.25);border-left:3px solid var(--gold-600);' }}">
        <svg style="width:18px;height:18px;flex-shrink:0;margin-top:1px;color:{{ $green ? 'var(--emerald-700)' : 'var(--gold-700)' }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            @if ($green)
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            @endif
        </svg>
        <div>
            <p style="font-size:13px;font-weight:600;color:var(--ink-900);margin:0 0 4px;">{{ $boxTitle }}</p>
            <p style="font-size:13px;color:var(--ink-700);margin:0;">{!! $boxText !!}</p>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;margin-top:18px;">
        <a href="{{ route('client.login') }}" class="btn-auth" style="text-decoration:none;justify-content:center;">
            Go to sign in
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="mailto:hello@nrhintelligence.com?subject=Portal registration" style="font-size:12px;color:var(--ink-500);text-align:center;text-decoration:none;">
            Contact NRH →
        </a>
    </div>
</x-client.layouts.auth>
