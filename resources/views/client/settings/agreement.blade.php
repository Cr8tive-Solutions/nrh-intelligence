<x-client.layouts.app pageTitle="Agreement">

    <div class="page-head">
        <div>
            <h1>Service <em>Agreement</em></h1>
            <div class="sub">Terms, SLA, and agreement details</div>
        </div>
    </div>

    <div style="max-width:640px;display:flex;flex-direction:column;gap:16px;">

        {{-- Status banner --}}
        @php $daysLeft = $agreement->days_left; @endphp
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:var(--radius);border-left:3px solid {{ $daysLeft > 30 ? 'var(--emerald-600)' : ($daysLeft > 0 ? 'var(--gold-500)' : 'var(--danger)') }};background:{{ $daysLeft > 30 ? 'var(--emerald-50)' : ($daysLeft > 0 ? 'var(--gold-100)' : '#fbeeec') }};border:1px solid {{ $daysLeft > 30 ? 'rgba(5,150,105,0.2)' : ($daysLeft > 0 ? 'rgba(184,147,31,0.2)' : 'rgba(196,69,58,0.2)') }};border-left-width:3px;">
            <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px;color:{{ $daysLeft > 30 ? 'var(--emerald-700)' : ($daysLeft > 0 ? 'var(--gold-600)' : 'var(--danger)') }};" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
            <div>
                <p style="font-size:13px;font-weight:600;color:{{ $daysLeft > 30 ? 'var(--emerald-800)' : ($daysLeft > 0 ? 'var(--gold-700)' : 'var(--danger)') }};margin:0;">
                    {{ $daysLeft > 30 ? 'Agreement is active' : ($daysLeft > 0 ? 'Agreement expiring soon' : 'Agreement has expired') }}
                </p>
                <p style="font-size:12px;color:var(--ink-500);margin:3px 0 0;">
                    {{ $daysLeft > 0 ? $daysLeft . ' days remaining — expires ' . $agreement->expiry_date->format('d M Y') : 'Please contact your account manager to renew.' }}
                </p>
            </div>
        </div>

        {{-- Agreement details --}}
        <div class="card">
            <div class="card-head">
                <h3>{{ $agreement->type }}</h3>
                <span class="pill pill-clear"><span class="dot"></span>Active</span>
            </div>
            <div style="padding:20px 24px;display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                @foreach ([
                    ['Agreement Type',  $agreement->type],
                    ['Start Date',      $agreement->start_date->format('d M Y')],
                    ['Expiry Date',     $agreement->expiry_date->format('d M Y')],
                    ['Turnaround SLA',  $agreement->sla_tat],
                    ['Billing Cycle',   $agreement->billing],
                    ['Payment Method',  $agreement->payment],
                ] as [$label, $value])
                    <div>
                        <dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500);">{{ $label }}</dt>
                        <dd style="font-size:14px;font-weight:600;color:var(--ink-900);margin:4px 0 0;">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Key terms --}}
        <div class="card">
            <div class="card-head">
                <h3>Key Terms</h3>
            </div>
            <ul style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;list-style:none;margin:0;">
                @foreach ($agreement->terms as $term)
                    <li style="display:flex;align-items:flex-start;gap:12px;font-size:13px;color:var(--ink-700);">
                        <svg style="width:14px;height:14px;color:var(--emerald-600);flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        {{ $term }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p style="font-size:12px;color:var(--ink-400);text-align:center;">This is a read-only view. Contact your account manager for changes.</p>
    </div>

</x-client.layouts.app>
