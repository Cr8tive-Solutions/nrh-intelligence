<x-client.layouts.app pageTitle="Request Submitted">

    <div style="max-width:560px;margin:40px auto;text-align:center;">
        <div style="width:64px;height:64px;border-radius:50%;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg style="width:28px;height:28px;color:var(--emerald-700);" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>

        <h2 style="font-family:var(--font-display);font-size:28px;font-weight:500;color:var(--ink-900);margin:0 0 8px;">Request <em>submitted.</em></h2>
        <p style="font-size:13px;color:var(--ink-500);line-height:1.6;margin:0 0 24px;">Your background check request has been received. Our team will begin processing it shortly.</p>

        <div class="card" style="padding:20px 24px;text-align:left;margin-bottom:28px;">
            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--ink-400);margin:0 0 16px;">What happens next?</p>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach ([
                    ['Processing begins',      'Our team reviews and assigns your request within 1 business day.'],
                    ['Candidate verification', 'Checks are conducted per the selected scopes and turnaround times.'],
                    ['Results delivered',      'You\'ll be notified by email when the report is ready.'],
                    ['Monthly billing',        'This request will be included in your end-of-month invoice.'],
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

        <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
            <a href="{{ route('client.request.new') }}" class="btn btn-ghost">New Request</a>
            <a href="{{ route('client.requests.index') }}" class="btn btn-primary">View Active Requests →</a>
        </div>
    </div>

</x-client.layouts.app>
