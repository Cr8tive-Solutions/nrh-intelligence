<x-client.layouts.auth title="Forgot Password">

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Reset your password</h2>
        <p class="mt-1 text-sm text-slate-500">Enter your email and we'll send a reset link</p>
    </div>

    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
            <svg class="size-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <p class="text-sm text-emerald-700 font-medium">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.forgot.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                value="{{ old('email') }}"
                required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs
                       focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors
                       @error('email') border-red-400 @enderror"
                placeholder="you@company.com"
            />
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-700 focus:outline-none focus:ring-3 focus:ring-brand-500/30 transition-colors">
            Send reset link
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        <a href="{{ route('client.login') }}" class="font-medium text-brand-600 hover:text-brand-700 transition-colors">
            ← Back to sign in
        </a>
    </p>

</x-client.layouts.auth>
