<x-client.layouts.auth
    title="Registration Received"
    authTitle='Application <em>received.</em>'
    authSub='Our team will review your registration and be in touch within 1–2 business days.'
    step="—"
    stepLabel="PENDING REVIEW"
>

    <div style="text-align:center;">
        <div style="width:52px;height:52px;border-radius:50%;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:24px;height:24px;color:var(--emerald-700);" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>

        <p style="font-size:13px;color:var(--ink-500);line-height:1.6;margin:0 0 20px;">
            Thank you for registering. Our team will verify your company details and send your login credentials once approved.
        </p>

        <div style="background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);padding:16px 18px;text-align:left;">
            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--ink-400);margin:0 0 14px;">What happens next?</p>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach ([
                    ['We review your company details', 'Our team verifies your registration information.'],
                    ['Account approval',               'You\'ll receive an email with your login credentials.'],
                    ['Access the portal',              'Sign in and start submitting background check requests.'],
                ] as [$title, $desc])
                    <div style="display:flex;gap:12px;align-items:flex-start;">
                        <div style="width:18px;height:18px;border-radius:50%;background:rgba(5,150,105,0.1);border:1px solid rgba(5,150,105,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <div style="width:5px;height:5px;border-radius:50%;background:var(--emerald-700);"></div>
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:600;color:var(--ink-700);margin:0;">{{ $title }}</p>
                            <p style="font-size:12px;color:var(--ink-400);margin:2px 0 0;">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('client.login') }}" style="display:inline-block;margin-top:20px;font-size:13px;font-weight:600;color:var(--emerald-700);text-decoration:none;"
            onmouseover="this.style.color='var(--emerald-900)'" onmouseout="this.style.color='var(--emerald-700)'">
            ← Back to sign in
        </a>
    </div>

</x-client.layouts.auth>
