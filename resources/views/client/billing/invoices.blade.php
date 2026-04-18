<x-client.layouts.app pageTitle="Invoices">

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Monthly invoices issued by NRH Intelligence</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Invoice No.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Period</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Issued</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Due</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Amount</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs font-medium text-slate-700">{{ $inv['number'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-900 font-medium">{{ $inv['period'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $inv['issued_at'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $inv['due_at'] }}</td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium
                                    {{ $inv['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $inv['status'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-900">MYR {{ number_format($inv['amount'], 2) }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('client.billing.invoices.show', $inv['id']) }}"
                                       class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">View</a>
                                    <a href="{{ route('client.billing.invoices.download', $inv['id']) }}"
                                       class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-sm text-slate-400">No invoices issued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client.layouts.app>
