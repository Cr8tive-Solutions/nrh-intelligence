<x-client.layouts.auth title="Registration Received">

    <div class="text-center">
        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-50 border border-emerald-200">
            <svg class="size-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Registration received!</h2>
        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
            Thank you for registering. Our team will review your application and send your login credentials once approved.
        </p>
        <p class="mt-3 text-xs text-slate-400">This typically takes 1–2 business days.</p>

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 px-5 py-4 text-left">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">What happens next?</p>
            <div class="space-y-2.5">
                @foreach ([
                    ['We review your company details', 'Our team verifies your registration information.'],
                    ['Account approval', 'You\'ll receive an email with your login credentials.'],
                    ['Access the portal', 'Sign in and start submitting background check requests.'],
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
        </div>

        <a href="{{ route('client.login') }}" class="mt-6 inline-block text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
            ← Back to sign in
        </a>
    </div>

</x-client.layouts.auth>
