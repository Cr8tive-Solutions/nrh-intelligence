<x-client.layouts.app pageTitle="Request Submitted">

    <div class="max-w-lg mx-auto py-12 text-center">
        <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-full bg-emerald-50 border border-emerald-200">
            <svg class="size-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-slate-900">Request Submitted!</h2>
        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
            Your background check request has been received. Our team will begin processing it shortly.
        </p>

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 px-5 py-4 text-left space-y-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">What happens next?</p>
            @foreach ([
                ['Processing begins', 'Our team reviews and assigns your request within 1 business day.'],
                ['Candidate verification', 'Checks are conducted per the selected scopes and turnaround times.'],
                ['Results delivered', 'You\'ll be notified by email when the report is ready.'],
                ['Monthly billing', 'This request will be included in your end-of-month invoice.'],
            ] as [$title, $desc])
                <div class="flex gap-3">
                    <div class="size-5 rounded-full bg-brand-100 flex items-center justify-center shrink-0 mt-0.5">
                        <div class="size-1.5 rounded-full bg-brand-600"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $title }}</p>
                        <p class="text-xs text-slate-500">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="{{ route('client.request.new') }}"
               class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                New Request
            </a>
            <a href="{{ route('client.requests.index') }}"
               class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                View Active Requests →
            </a>
        </div>
    </div>

</x-client.layouts.app>
