<x-client.layouts.app pageTitle="Invoice {{ $invoice['number'] }}">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('client.billing.invoices') }}" class="hover:text-brand-600 transition-colors">Invoices</a>
        <svg class="size-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span class="text-slate-900 font-medium font-mono">{{ $invoice['number'] }}</span>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">

            {{-- Invoice header --}}
            <div class="flex items-start justify-between px-8 py-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="size-8 rounded-lg bg-brand-600 flex items-center justify-center">
                            <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-slate-900">NRH INTELLIGENCE</span>
                    </div>
                    <p class="text-xs text-slate-500">Background Verification Platform</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-slate-900 font-mono">{{ $invoice['number'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $invoice['period'] }}</p>
                    <span class="mt-2 inline-block rounded-full border px-2.5 py-0.5 text-xs font-medium
                        {{ $invoice['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                        {{ $invoice['status'] }}
                    </span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="grid grid-cols-3 gap-6 px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                @foreach ([
                    ['Billed To', $invoice['company']],
                    ['Issue Date', $invoice['issued_at']],
                    ['Due Date',   $invoice['due_at']],
                ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                        <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Line items --}}
            <div class="px-8 py-5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Description</th>
                            <th class="pb-2 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">Qty</th>
                            <th class="pb-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide w-28">Unit Price</th>
                            <th class="pb-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide w-28">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($invoice['items'] as $item)
                            <tr>
                                <td class="py-3 text-slate-700">{{ $item['description'] }}</td>
                                <td class="py-3 text-center text-slate-500">{{ $item['qty'] }}</td>
                                <td class="py-3 text-right text-slate-500">MYR {{ number_format($item['unit_price'], 2) }}</td>
                                <td class="py-3 text-right font-medium text-slate-900">MYR {{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/50">
                <div class="ml-auto max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>MYR {{ number_format($invoice['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Tax (6% SST)</span>
                        <span>MYR {{ number_format($invoice['tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-base border-t border-slate-200 pt-2 mt-1">
                        <span>Total Due</span>
                        <span>MYR {{ number_format($invoice['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Payment via direct bank transfer. Please reference invoice number.</p>
                <a href="{{ route('client.billing.invoices.download', $invoice['id']) }}"
                   class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

</x-client.layouts.app>
