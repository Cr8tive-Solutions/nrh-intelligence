<x-client.layouts.app pageTitle="Transactions">

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Payment history recorded by admin</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Reference</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Description</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Method</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Amount</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $txn)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $txn['date'] }}</td>
                            <td class="px-5 py-3.5 font-mono text-xs font-medium text-slate-700">{{ $txn['reference'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-700">{{ $txn['description'] }}</td>
                            <td class="px-5 py-3.5">
                                @php $method = $txn['method']; @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium border
                                    {{ $method === 'Monthly Billing' ? 'bg-brand-50 text-brand-700 border-brand-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $method }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-900">
                                MYR {{ number_format($txn['amount'], 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('client.billing.transactions.receipt', $txn['id']) }}"
                                   class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-sm text-slate-400">No transactions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (count($transactions) > 0)
            <div class="flex items-center justify-between px-5 py-3.5 border-t border-slate-100 bg-slate-50/60">
                <p class="text-xs text-slate-500">{{ count($transactions) }} transactions</p>
                <p class="text-sm font-semibold text-slate-900">
                    Total: MYR {{ number_format($transactions->sum('amount'), 2) }}
                </p>
            </div>
        @endif
    </div>

</x-client.layouts.app>
