<x-client.layouts.app pageTitle="Request {{ $request->reference }}">

    @php
        $candidates = $request->candidates;
        $totalChecks = 0;
        $doneChecks = 0;
        $flaggedChecks = 0;
        foreach ($candidates as $c) {
            foreach ($c->scopeTypes as $s) {
                $totalChecks++;
                if ($s->pivot->status === 'complete') { $doneChecks++; }
                if ($s->pivot->status === 'flagged') { $flaggedChecks++; }
            }
        }

        $isCashBilled        = $request->customer?->isCashBilled();
        $isCashCycleNew      = $isCashBilled && $request->status === 'new';
        $isCashPaymentVerified = $isCashCycleNew && $request->isPaymentVerified();
        $isCashPaymentPending  = $isCashCycleNew && ! $request->isPaymentVerified();

        $bank            = config('billing.bank');
        $currency        = config('billing.currency', 'MYR');
        $popEmail        = config('billing.proof_of_payment_email');
        $afterPaymentSla = config('billing.sla_after_payment', '1 business day');
        $canUploadSlip   = auth()->user()?->canAny(['view-prices', 'manage-billing']);
        $canViewPrices   = auth()->user()?->can('view-prices');
        $hasSlip         = $request->hasPaymentSlip();
        $payTotal        = $isCashBilled ? $request->cashTotal() : 0;

        $typeLabel = match ($request->type) {
            'malaysia' => 'Malaysia Screening',
            'global'   => 'Global Screening',
            'kyc'      => 'KYC · Customer',
            'kyb'      => 'KYB · Business',
            'kys'      => 'KYS · Supplier',
            default    => ucfirst((string) $request->type),
        };

        $dueDate    = $request->created_at->copy()->addDays(5);
        $isRejected = $request->status === 'rejected';
        $isFlagged  = $flaggedChecks > 0;
        $isComplete = in_array($request->status, ['complete', 'updated']);

        // Hero card
        $heroState = match (true) {
            $isComplete => [
                'bg' => 'var(--emerald-50)', 'accent' => 'var(--emerald-600)', 'icon' => 'check',
                'title' => $request->status === 'updated' ? 'Updated report is ready' : 'Your report is ready',
                'desc'  => 'The background check is complete. Download your report below.',
            ],
            $isRejected => [
                'bg' => '#fbeeec', 'accent' => 'var(--danger)', 'icon' => 'x',
                'title' => 'Request could not be processed',
                'desc'  => $request->rejection_reason ?? 'This request was rejected. Please contact us if you need to resubmit.',
            ],
            $isCashPaymentPending && ! $hasSlip => [
                'bg' => 'rgba(184,147,31,0.06)', 'accent' => 'var(--gold-500)', 'icon' => 'alert',
                'title' => 'Action needed — payment required',
                'desc'  => 'Please transfer the amount shown below to start the background check. Upload your slip once done.',
            ],
            $isCashPaymentPending && $hasSlip => [
                'bg' => 'rgba(184,147,31,0.06)', 'accent' => 'var(--gold-500)', 'icon' => 'clock',
                'title' => 'Payment slip received — verifying',
                'desc'  => 'Our finance team is confirming your transfer. Processing will begin once payment is verified.',
            ],
            $isCashPaymentVerified => [
                'bg' => 'var(--emerald-50)', 'accent' => 'var(--emerald-600)', 'icon' => 'check',
                'title' => 'Payment confirmed — starting soon',
                'desc'  => 'Payment confirmed on ' . $request->payment_verified_at->format('d M Y') . '. Processing will begin within ' . $afterPaymentSla . '.',
            ],
            $isFlagged => [
                'bg' => 'rgba(184,147,31,0.06)', 'accent' => 'var(--gold-500)', 'icon' => 'flag',
                'title' => 'Flagged for analyst review',
                'desc'  => 'One or more checks need a closer look. You\'ll be notified once resolved.',
            ],
            $request->status === 'in_progress' => [
                'bg' => '#e7eff5', 'accent' => 'var(--info)', 'icon' => 'progress',
                'title' => 'Background check in progress',
                'desc'  => 'Our team is verifying the ' . ($candidates->count() > 1 ? 'candidates' : 'candidate') . '. You\'ll receive an update when the report is ready.',
            ],
            default => [
                'bg' => 'var(--paper)', 'accent' => 'var(--ink-300)', 'icon' => 'clock',
                'title' => 'Waiting for candidate consent',
                'desc'  => 'We have reached out to the ' . ($candidates->count() > 1 ? 'candidates' : 'candidate') . ' for consent. Processing begins once they respond.',
            ],
        };

        // Simplified tracker (3 steps for credit, 4 for cash)
        if ($isCashBilled) {
            $simpleSteps = [
                ['label' => 'Submitted', 'sub' => $request->created_at->format('d M Y'),
                 'done' => true,        'current' => false, 'flagged' => false],
                ['label' => 'Payment',   'sub' => $isCashPaymentVerified ? 'Confirmed' : ($hasSlip ? 'Slip uploaded' : 'Transfer required'),
                 'done'    => $isCashPaymentVerified || ! $isCashCycleNew,
                 'current' => $isCashPaymentPending, 'flagged' => false],
                ['label' => 'Checking',  'sub' => $isComplete ? 'Completed' : 'Background verification',
                 'done'    => $isComplete,
                 'current' => $request->status === 'in_progress' || ($isCashPaymentVerified && $request->status === 'new'),
                 'flagged' => $isFlagged],
                ['label' => 'Done',      'sub' => $isComplete ? $request->updated_at->format('d M Y') : 'Est. ' . $dueDate->format('d M'),
                 'done' => $isComplete,  'current' => false, 'flagged' => false],
            ];
        } else {
            $simpleSteps = [
                ['label' => 'Submitted', 'sub' => $request->created_at->format('d M Y'),
                 'done' => true, 'current' => false, 'flagged' => false],
                ['label' => 'Checking',  'sub' => $request->status === 'in_progress' ? 'In progress' : ($isComplete ? 'Completed' : 'Awaiting start'),
                 'done'    => $isComplete,
                 'current' => in_array($request->status, ['new', 'in_progress']),
                 'flagged' => $isFlagged],
                ['label' => 'Done',      'sub' => $isComplete ? $request->updated_at->format('d M Y') : 'Est. ' . $dueDate->format('d M'),
                 'done' => $isComplete, 'current' => false, 'flagged' => false],
            ];
        }
        $stepCount = count($simpleSteps);
    @endphp

    {{-- Page head --}}
    <div class="page-head">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <a href="{{ route('client.requests.index') }}" class="case-back" aria-label="Back to requests">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div>
                <div style="font-size:10px;color:var(--ink-400);letter-spacing:0.1em;font-family:var(--font-mono);text-transform:uppercase;">Your request number</div>
                <div style="font-size:15px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">{{ $request->reference }}</div>
            </div>
            <span style="padding:3px 10px;background:var(--gold-100);color:var(--gold-700);border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">{{ $typeLabel }}</span>
        </div>
        <div style="display:flex;gap:8px;">
            @if ($isComplete)
                <button type="button" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                    Download report
                </button>
            @elseif (! $isRejected)
                <button type="button" class="btn btn-ghost">Request update</button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div style="margin-bottom:16px;padding:10px 14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.25);border-left:3px solid var(--emerald-600);border-radius:var(--radius);font-size:13px;color:var(--emerald-700);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Hero status card --}}
    <div style="margin-bottom:20px;padding:22px 24px;background:{{ $heroState['bg'] }};border:1px solid {{ $heroState['accent'] }};border-radius:var(--radius-lg);display:flex;align-items:flex-start;gap:18px;">
        <div style="width:46px;height:46px;border-radius:50%;background:{{ $heroState['accent'] }};display:grid;place-items:center;flex-shrink:0;">
            @if ($heroState['icon'] === 'check')
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            @elseif ($heroState['icon'] === 'x')
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            @elseif ($heroState['icon'] === 'alert')
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            @elseif ($heroState['icon'] === 'flag')
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"/></svg>
            @elseif ($heroState['icon'] === 'progress')
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            @else {{-- clock --}}
                <svg style="width:22px;height:22px;color:white;" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            @endif
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:19px;font-weight:700;color:var(--ink-900);line-height:1.3;">{{ $heroState['title'] }}</div>
            <div style="font-size:14px;color:var(--ink-600);margin-top:6px;line-height:1.5;">{{ $heroState['desc'] }}</div>
            <div style="margin-top:10px;font-size:12px;color:var(--ink-400);">
                Submitted <b style="color:var(--ink-600);">{{ $request->created_at->format('d M Y') }}</b>
                by <b style="color:var(--ink-600);">{{ $request->submittedBy?->name ?? '—' }}</b>
                &nbsp;·&nbsp; {{ $candidates->count() }} {{ Str::plural('candidate', $candidates->count()) }}
                @if ($totalChecks > 0)
                    &nbsp;·&nbsp; {{ $doneChecks }}/{{ $totalChecks }} checks done
                @endif
            </div>
        </div>
        @if ($isComplete)
            <div style="flex-shrink:0;padding-top:2px;">
                <button type="button" class="btn btn-primary" style="font-size:14px;padding:10px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                    Download report
                </button>
            </div>
        @endif
    </div>

    {{-- Simplified progress tracker --}}
    <div class="tracker" style="margin-bottom:20px;">
        <div class="tracker-rail" style="--steps:{{ $stepCount }};">
            @foreach ($simpleSteps as $step)
                @php
                    $cls = '';
                    if ($step['done'])         { $cls = 'is-done'; }
                    elseif ($step['flagged'])  { $cls = 'is-flagged'; }
                    elseif ($step['current'])  { $cls = 'is-current'; }
                @endphp
                <div class="tracker-step {{ $cls }}">
                    <div class="dot">
                        @if ($step['done'])
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                        @elseif ($step['flagged'])
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 9v4M12 17h.01"/></svg>
                        @elseif ($step['current'])
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>
                        @endif
                    </div>
                    <div class="label">{{ $step['label'] }}</div>
                    @if (! empty($step['sub']))
                        <div class="when">{{ $step['sub'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
        @if ($isRejected)
            <div style="margin-top:16px;padding:10px 14px;background:#fbeeec;border:1px solid rgba(196,69,58,0.2);border-left:3px solid var(--danger);border-radius:6px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--danger);">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                <span><b>Request rejected</b> — {{ $request->rejection_reason ?? 'Contact us if you need to resubmit.' }}</span>
            </div>
        @endif
    </div>

    {{-- Payment block (cash billing, billing users only) --}}
    @if ($isCashPaymentPending && $canViewPrices)
        <div style="margin-bottom:20px;background:rgba(184,147,31,0.06);border:1px solid rgba(184,147,31,0.25);border-left:4px solid var(--gold-500);border-radius:var(--radius-lg);padding:22px 24px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <svg style="width:18px;height:18px;color:var(--gold-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                </svg>
                <div style="font-size:15px;font-weight:700;color:var(--ink-900);">Payment details</div>
            </div>
            <p style="font-size:14px;color:var(--ink-700);line-height:1.6;margin:0 0 18px;">
                Transfer the exact amount below to our bank account. Include your <b>request number</b> as the payment reference so we can match it quickly.
            </p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:18px 20px;margin-bottom:16px;">
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">Bank</div>
                    <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $bank['name'] }}</div>
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">Account holder</div>
                    <div style="font-size:14px;font-weight:600;color:var(--ink-900);">{{ $bank['account_holder'] }}</div>
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">Account number</div>
                    <div style="font-size:15px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">{{ $bank['account_number'] }}</div>
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">SWIFT / BIC</div>
                    <div style="font-size:14px;font-weight:700;color:var(--ink-900);font-family:var(--font-mono);">{{ $bank['swift'] }}</div>
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">Amount to pay</div>
                    <div style="font-size:24px;font-weight:800;color:var(--emerald-800);font-family:var(--font-mono);">{{ $currency }} {{ number_format($payTotal, 2) }}</div>
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-400);margin-bottom:4px;">Payment reference <span style="color:var(--danger);">*</span></div>
                    <div style="font-size:16px;font-weight:800;color:var(--emerald-800);font-family:var(--font-mono);">{{ $request->reference }}</div>
                    <div style="font-size:11px;color:var(--ink-500);margin-top:3px;">Include this in your transfer</div>
                </div>
            </div>

            @if ($hasSlip)
                <div style="display:flex;align-items:center;gap:12px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.25);border-left:3px solid var(--emerald-600);border-radius:var(--radius);padding:14px 16px;">
                    <svg style="width:20px;height:20px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:600;color:var(--ink-900);">Payment slip uploaded</div>
                        <div style="font-size:13px;color:var(--ink-600);margin-top:2px;">
                            Uploaded {{ $request->payment_slip_uploaded_at?->format('d M Y · H:i') }} — our finance team is verifying.
                        </div>
                    </div>
                    @if ($canUploadSlip)
                        <a href="{{ route('client.requests.payment-slip.download', $request->id) }}" style="font-size:13px;color:var(--emerald-700);font-weight:600;text-decoration:none;">View</a>
                        <form method="POST" action="{{ route('client.requests.payment-slip.destroy', $request->id) }}" style="margin:0;" onsubmit="return confirm('Remove the uploaded slip and re-upload?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;font-size:13px;color:var(--gold-700);font-weight:600;">Replace</button>
                        </form>
                    @endif
                </div>
            @elseif ($canUploadSlip)
                <div style="border-top:1px solid rgba(184,147,31,0.2);padding-top:16px;">
                    <div style="font-size:14px;font-weight:600;color:var(--ink-900);margin-bottom:6px;">Upload your payment slip</div>
                    <p style="font-size:13px;color:var(--ink-600);line-height:1.6;margin:0 0 12px;">
                        Attach the bank-transfer confirmation (PDF, JPG or PNG, max 5&nbsp;MB). Processing begins within {{ $afterPaymentSla }} of verification.
                    </p>
                    <form method="POST" action="{{ route('client.requests.payment-slip.store', $request->id) }}" enctype="multipart/form-data" style="margin:0;">
                        @csrf
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <input type="file" name="payment_slip" accept=".pdf,.jpg,.jpeg,.png" required style="font-size:13px;flex:1;min-width:200px;">
                            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5M5 20h14"/></svg>
                                Upload slip
                            </button>
                        </div>
                        @error('payment_slip')
                            <div style="margin-top:8px;font-size:13px;color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </form>
                    <p style="font-size:12px;color:var(--ink-500);line-height:1.6;margin:10px 0 0;">
                        Prefer email? Send your slip to
                        <a href="mailto:{{ $popEmail }}?subject=Proof of payment — {{ $request->reference }}" style="color:var(--ink-600);text-decoration:underline;">{{ $popEmail }}</a>
                        with your request number in the subject.
                    </p>
                </div>
            @else
                <p style="font-size:13px;color:var(--ink-600);line-height:1.6;margin:0;">
                    After the transfer, your team can upload the slip on this page or email it to
                    <a href="mailto:{{ $popEmail }}?subject=Proof of payment — {{ $request->reference }}" style="color:var(--emerald-700);font-weight:600;text-decoration:none;">{{ $popEmail }}</a>
                    with <b>{{ $request->reference }}</b> in the subject line.
                </p>
            @endif
        </div>
    @elseif ($isCashPaymentVerified && $canViewPrices)
        <div style="margin-bottom:20px;display:flex;align-items:center;gap:14px;background:var(--emerald-50);border:1px solid rgba(5,150,105,0.25);border-left:4px solid var(--emerald-600);border-radius:var(--radius-lg);padding:16px 20px;">
            <svg style="width:22px;height:22px;color:var(--emerald-700);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:var(--ink-900);">Payment confirmed</div>
                <div style="font-size:13px;color:var(--ink-600);margin-top:2px;">
                    Verified {{ $request->payment_verified_at->format('d M Y · H:i') }} — processing will begin within {{ $afterPaymentSla }}.
                </div>
            </div>
            @if ($request->hasPaymentSlip())
                <a href="{{ route('client.requests.payment-slip.download', $request->id) }}" style="font-size:13px;color:var(--emerald-700);font-weight:600;text-decoration:none;">View slip</a>
            @endif
        </div>
    @endif

    {{-- Candidates + sidebar --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

        {{-- Candidates --}}
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin:0 0 14px;">
                <h2 style="font-size:15px;font-weight:700;color:var(--ink-900);margin:0;">
                    Candidates
                    <span style="color:var(--ink-400);font-weight:400;font-size:13px;">· {{ $candidates->count() }}</span>
                </h2>
                @if ($canAddCandidate ?? false)
                    <button type="button" class="btn btn-ghost" style="font-size:12px;padding:5px 12px;"
                        @click="$dispatch('open-add-candidate')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        Add candidate
                    </button>
                @endif
            </div>

            @if ($candidates->isEmpty())
                <div class="card" style="padding:48px 20px;text-align:center;">
                    <p style="font-size:14px;color:var(--ink-400);margin:0;">No candidates on this request.</p>
                </div>
            @else
                <div class="cand-grid">
                    @foreach ($candidates as $candidate)
                        @php
                            $candDone     = $candidate->scopeTypes->where('pivot.status', 'complete')->count();
                            $candTotal    = $candidate->scopeTypes->count();
                            $candProgress = $candTotal > 0 ? round($candDone / $candTotal * 100) : 0;
                            $candPill     = match ($candidate->status) {
                                'complete'    => ['cls' => 'pill-clear',    'txt' => 'Cleared'],
                                'flagged'     => ['cls' => 'pill-flagged',  'txt' => 'Flagged — needs review'],
                                'in_progress' => ['cls' => 'pill-progress', 'txt' => 'Being checked'],
                                default       => ['cls' => 'pill-pending',  'txt' => 'Waiting'],
                            };
                        @endphp
                        <a href="{{ route('client.candidates.show', $candidate->id) }}" class="cand-card {{ $candidate->status === 'flagged' ? 'is-flagged' : '' }}">
                            @php
                                $isRedacted = $candidate->isRedacted();
                                $avatarTxt  = $isRedacted ? '··' : strtoupper(substr($candidate->name, 0, 2));
                            @endphp
                            <div class="head">
                                <div class="avatar" @if($isRedacted) style="background:var(--paper-2);color:var(--ink-400);" @endif>{{ $avatarTxt }}</div>
                                <div style="min-width:0;flex:1;">
                                    <div class="name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;{{ $isRedacted ? 'color:var(--ink-400);font-style:italic;' : '' }}">
                                        {{ $isRedacted ? 'Candidate erased' : $candidate->name }}
                                    </div>
                                    <div class="id">{{ $isRedacted ? 'Data erased ' . $candidate->redacted_at->format('d M Y') : $candidate->identity_number }}</div>
                                </div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $candProgress }}%;"></div>
                            </div>
                            <div class="row">
                                <span>{{ $candDone }}/{{ $candTotal }} checks done</span>
                                <span class="pill {{ $candPill['cls'] }}"><span class="dot"></span>{{ $candPill['txt'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="card">
                <div class="card-head">
                    <h3>Request details</h3>
                </div>
                <div class="identity">
                    <div class="id-row">
                        <span class="k">Request number</span>
                        <span class="v" style="font-family:var(--font-mono);">{{ $request->reference }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Type</span>
                        <span class="v" style="font-family:var(--font-ui);font-weight:600;white-space:normal;">{{ $typeLabel }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Submitted by</span>
                        <span class="v" style="font-family:var(--font-ui);white-space:normal;">{{ $request->submittedBy?->name ?? '—' }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Date submitted</span>
                        <span class="v">{{ $request->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="id-row">
                        <span class="k">Expected by</span>
                        <span class="v">{{ $dueDate->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            @php $allScopes = $candidates->flatMap(fn ($c) => $c->scopeTypes)->unique('id'); @endphp
            @if ($allScopes->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>What we're checking</h3>
                        <span class="count-pill">{{ $allScopes->count() }}</span>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:10px;">
                        @foreach ($allScopes as $scope)
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-700);">
                                <svg style="width:13px;height:13px;color:var(--emerald-600);flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M9 12l2 2 4-4"/></svg>
                                {{ $scope->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($request->currentReportVersions->isNotEmpty())
                <div class="card">
                    <div class="card-head">
                        <h3>Reports</h3>
                        <span class="count-pill">{{ $request->currentReportVersions->count() }}</span>
                    </div>
                    <div style="padding:6px 0;">
                        @foreach ($request->currentReportVersions as $rv)
                            @php
                                $reportLabel = match (true) {
                                    $rv->type === 'prelim'                             => 'Preliminary report',
                                    $rv->type === 'full' && $request->status === 'updated' => 'Updated report',
                                    $rv->type === 'full'                               => 'Full report',
                                    default                                            => ucfirst($rv->type) . ' report',
                                };
                            @endphp
                            <a href="{{ route('client.requests.reports.download', [$request->id, $rv->id]) }}"
                               style="display:flex;align-items:center;gap:10px;padding:14px 18px;border-bottom:1px solid var(--line);text-decoration:none;color:inherit;transition:background 120ms;"
                               onmouseover="this.style.background='var(--paper)'"
                               onmouseout="this.style.background='transparent'">
                                <div style="width:36px;height:36px;border-radius:var(--radius);background:var(--emerald-50);color:var(--emerald-700);display:grid;place-items:center;flex-shrink:0;">
                                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 12-3-3m0 0-3 3m3-3v6m1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--ink-900);">{{ $reportLabel }} <span style="color:var(--ink-400);font-family:var(--font-mono);font-weight:400;font-size:11px;">v{{ $rv->version }}</span></div>
                                    <div style="font-size:12px;color:var(--ink-500);margin-top:2px;">Issued {{ $rv->generated_at->diffForHumans() }}</div>
                                </div>
                                <svg style="width:16px;height:16px;color:var(--ink-400);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if ($canAddCandidate ?? false)
        <div x-data="{ open: false }" @open-add-candidate.window="open = true" @keydown.escape.window="open = false">
            <div x-show="open" x-cloak
                 style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:24px;">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);" @click="open = false"></div>
                <div style="position:relative;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,0.2);z-index:1;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--line);">
                        <div style="font-size:14px;font-weight:600;color:var(--ink-900);">Add candidate</div>
                        <button type="button" @click="open = false" style="background:none;border:0;cursor:pointer;color:var(--ink-400);padding:4px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('client.requests.candidates.store', $request->id) }}" style="padding:20px 22px;display:flex;flex-direction:column;gap:14px;">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div style="grid-column:1/-1;">
                                <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:4px;">Full name *</label>
                                <input type="text" name="name" required placeholder="e.g. Ahmad bin Ali"
                                    style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:4px;">Identity type *</label>
                                <select name="identity_type_id" required
                                    style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);">
                                    <option value="">Select…</option>
                                    @foreach ($identityTypes as $it)
                                        <option value="{{ $it->id }}">{{ $it->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:4px;">Identity number *</label>
                                <input type="text" name="identity_number" required placeholder="e.g. 900101-01-1234"
                                    style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:4px;">Mobile</label>
                                <input type="text" name="mobile" placeholder="+60 12-345 6789"
                                    style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);">
                            </div>
                            <div style="grid-column:1/-1;">
                                <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:4px;">Remarks</label>
                                <input type="text" name="remarks" placeholder="Optional notes"
                                    style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:var(--radius);font-size:13px;background:var(--card);color:var(--ink-900);font-family:var(--font-ui);">
                            </div>
                        </div>

                        <div>
                            <label style="font-size:11px;font-weight:600;color:var(--ink-500);text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:8px;">Scope of checks *</label>
                            <div style="display:flex;flex-direction:column;gap:6px;padding:10px 12px;border:1px solid var(--line);border-radius:var(--radius);max-height:160px;overflow-y:auto;">
                                @foreach ($availableScopeTypes as $scope)
                                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-800);cursor:pointer;">
                                        <input type="checkbox" name="scope_type_ids[]" value="{{ $scope->id }}" checked
                                            style="width:14px;height:14px;cursor:pointer;">
                                        {{ $scope->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if ($errors->any())
                            <div style="font-size:12px;color:var(--danger);">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:4px;">
                            <button type="button" class="btn btn-ghost" @click="open = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add candidate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</x-client.layouts.app>
