<x-client.layouts.app pageTitle="Dashboard">

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        @php
            $stats = [
                ['label' => 'New Requests', 'value' => $stats['new'] ?? 0, 'color' => 'blue', 'icon' => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'In Progress', 'value' => $stats['pending'] ?? 0, 'color' => 'amber', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Completed', 'value' => $stats['complete'] ?? 0, 'color' => 'emerald', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Total Requests', 'value' => $stats['total'] ?? 0, 'color' => 'slate', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ];
            $colorMap = [
                'blue'    => 'bg-blue-50 text-blue-600 border-blue-100',
                'amber'   => 'bg-amber-50 text-amber-600 border-amber-100',
                'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'slate'   => 'bg-slate-100 text-slate-600 border-slate-200',
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</span>
                    <div class="size-8 rounded-lg border {{ $colorMap[$stat['color']] }} flex items-center justify-center shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Request Activity</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Requests submitted over time</p>
                </div>
                {{-- Filter tabs --}}
                <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-1" x-data="{ period: 'weekly' }">
                    @foreach (['daily' => 'Day', 'weekly' => 'Week', 'monthly' => 'Month'] as $key => $label)
                        <button
                            @click="period = '{{ $key }}'"
                            :class="period === '{{ $key }}' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                            class="rounded-md px-3 py-1 text-xs font-medium transition-all"
                        >{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            {{-- Chart placeholder --}}
            <div class="h-56 flex items-center justify-center rounded-lg bg-slate-50 border border-dashed border-slate-200">
                <div class="text-center">
                    <svg class="mx-auto size-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                    </svg>
                    <p class="text-xs text-slate-400">Chart.js renders here</p>
                </div>
            </div>
        </div>

        {{-- Account summary --}}
        <div class="space-y-4">
            {{-- Billing summary card --}}
            <div class="bg-brand-600 rounded-xl p-5 text-white">
                <p class="text-xs font-medium text-brand-200">Billing Method</p>
                <p class="mt-1 text-xl font-bold leading-tight">Monthly Billing</p>
                <p class="mt-0.5 text-xs text-brand-300">Cash / Direct Transfer</p>
                <div class="mt-4 flex items-center justify-between border-t border-brand-500 pt-3">
                    <div>
                        <p class="text-xs text-brand-300">Next invoice</p>
                        <p class="text-sm font-semibold">End of {{ now()->format('F Y') }}</p>
                    </div>
                    <a href="{{ route('client.billing.invoices') }}"
                       class="rounded-lg bg-white/20 hover:bg-white/30 px-3 py-1.5 text-xs font-semibold text-white transition-colors">
                        View Invoices
                    </a>
                </div>
            </div>

            {{-- Agreement status --}}
            <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">Agreement</h3>
                    @php $daysLeft = $agreementDaysLeft ?? 90; @endphp
                    @if ($daysLeft > 30)
                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Active</span>
                    @elseif ($daysLeft > 0)
                        <span class="rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-medium text-amber-700">Expiring</span>
                    @else
                        <span class="rounded-full bg-red-50 border border-red-200 px-2.5 py-0.5 text-xs font-medium text-red-700">Expired</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">Expires</p>
                <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $agreementExpiry ?? 'N/A' }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $daysLeft > 0 ? $daysLeft . ' days remaining' : 'Please renew' }}</p>
            </div>

            {{-- Quick actions --}}
            <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Quick Actions</h3>
                <div class="space-y-1.5">
                    <a href="{{ route('client.request.new') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        New Request
                    </a>
                    <a href="{{ route('client.requests.track') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        Track Candidate
                    </a>
                    <a href="{{ route('client.billing.transactions') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="size-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4 4 4m6 0v12m0 0 4-4m-4 4-4-4"/>
                        </svg>
                        Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent requests table --}}
    <div class="mt-6 bg-white rounded-xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">Recent Requests</h3>
            <a href="{{ route('client.requests.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Request ID</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Candidates</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentRequests ?? [] as $request)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-slate-700">{{ $request->reference }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $request->candidates_count }}</td>
                            <td class="px-5 py-3">
                                @include('client.partials._status-badge', ['status' => $request->status])
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $request->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('client.requests.details', $request->id) }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                No requests yet.
                                <a href="{{ route('client.request.new') }}" class="text-brand-600 hover:text-brand-700 font-medium">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client.layouts.app>
