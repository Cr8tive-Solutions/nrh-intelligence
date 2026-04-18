<x-client.layouts.app pageTitle="Request Details">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('client.requests.index') }}" class="hover:text-brand-600 transition-colors">Active Requests</a>
        <svg class="size-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span class="text-slate-900 font-medium font-mono">{{ $request['reference'] }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Candidates table --}}
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900">Candidates</h3>
                    <span class="text-xs text-slate-400">{{ count($request['candidates']) }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/60">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Identity No.</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($request['candidates'] as $i => $candidate)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5 font-medium text-slate-900">{{ $candidate['name'] }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ $candidate['identity_number'] }}</td>
                                    <td class="px-5 py-3.5">
                                        @php $sid = $candidate['status_id']; @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium
                                            {{ $sid === 1 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($sid === 2 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                            <span class="size-1.5 rounded-full {{ $sid === 1 ? 'bg-blue-500' : ($sid === 2 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                            {{ $candidate['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Scopes --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Verification Scopes</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($request['scopes'] as $scope)
                        <span class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700">{{ $scope }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Request Info</h3>
                <dl class="space-y-3">
                    @foreach ([
                        ['Reference',    $request['reference'], 'mono'],
                        ['Status',       $request['status'],    ''],
                        ['Submitted By', $request['submitted_by'], ''],
                        ['Date',         $request['created_at'],   ''],
                    ] as [$label, $value, $extra])
                        <div>
                            <dt class="text-xs font-medium text-slate-500">{{ $label }}</dt>
                            <dd class="mt-0.5 text-sm text-slate-900 {{ $extra === 'mono' ? 'font-mono' : 'font-medium' }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <a href="{{ route('client.requests.index') }}"
               class="flex items-center justify-center gap-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back to Requests
            </a>
        </div>
    </div>

</x-client.layouts.app>
