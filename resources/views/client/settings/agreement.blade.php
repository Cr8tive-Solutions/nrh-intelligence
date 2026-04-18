<x-client.layouts.app pageTitle="Agreement">

    <div class="max-w-2xl space-y-5">

        {{-- Status banner --}}
        @php $daysLeft = $agreement['days_left']; @endphp
        <div class="flex items-start gap-3 rounded-xl border px-4 py-3.5
            {{ $daysLeft > 30 ? 'border-emerald-200 bg-emerald-50' : ($daysLeft > 0 ? 'border-amber-200 bg-amber-50' : 'border-red-200 bg-red-50') }}">
            <svg class="size-5 shrink-0 mt-0.5 {{ $daysLeft > 30 ? 'text-emerald-500' : ($daysLeft > 0 ? 'text-amber-500' : 'text-red-500') }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold {{ $daysLeft > 30 ? 'text-emerald-800' : ($daysLeft > 0 ? 'text-amber-800' : 'text-red-800') }}">
                    {{ $daysLeft > 30 ? 'Agreement is active' : ($daysLeft > 0 ? 'Agreement expiring soon' : 'Agreement has expired') }}
                </p>
                <p class="text-xs mt-0.5 {{ $daysLeft > 30 ? 'text-emerald-600' : ($daysLeft > 0 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $daysLeft > 0 ? $daysLeft . ' days remaining — expires ' . $agreement['expiry_date'] : 'Please contact your account manager to renew.' }}
                </p>
            </div>
        </div>

        {{-- Agreement details --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-5">{{ $agreement['type'] }}</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                @foreach ([
                    ['Agreement Type',  $agreement['type']],
                    ['Start Date',      $agreement['start_date']],
                    ['Expiry Date',     $agreement['expiry_date']],
                    ['Turnaround Time', $agreement['sla_tat']],
                    ['Billing Cycle',   $agreement['billing']],
                    ['Payment Method',  $agreement['payment']],
                ] as [$label, $value])
                    <div>
                        <dt class="text-xs font-medium text-slate-500">{{ $label }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 mt-0.5">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Terms --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Key Terms</h3>
            <ul class="space-y-2.5">
                @foreach ($agreement['terms'] as $term)
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="size-4 text-brand-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        {{ $term }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-xs text-slate-400 text-center">This is a read-only view. Contact your account manager for changes.</p>
    </div>

</x-client.layouts.app>
