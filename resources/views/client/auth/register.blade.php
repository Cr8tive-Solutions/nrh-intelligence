<x-client.layouts.auth title="Register">

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Create an account</h2>
        <p class="mt-1 text-sm text-slate-500">Fill in your company details to get started</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 space-y-1">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-700">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('client.register.submit') }}" class="space-y-4">
        @csrf

        {{-- Company Info --}}
        <div>
            <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1.5">Company name <span class="text-red-500">*</span></label>
            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error('company_name') border-red-400 @enderror"
                placeholder="ACME Sdn. Bhd."/>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1.5">First name <span class="text-red-500">*</span></label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error('first_name') border-red-400 @enderror"
                    placeholder="Ahmad"/>
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1.5">Last name <span class="text-red-500">*</span></label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error('last_name') border-red-400 @enderror"
                    placeholder="Razali"/>
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Work email <span class="text-red-500">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error('email') border-red-400 @enderror"
                placeholder="you@company.com"/>
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Phone number <span class="text-red-500">*</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error('phone') border-red-400 @enderror"
                placeholder="+60 12 345 6789"/>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">Company address</label>
            <textarea id="address" name="address" rows="2"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors resize-none"
                placeholder="No. 1, Jalan...">{{ old('address') }}</textarea>
        </div>

        <div class="flex items-start gap-2 pt-1">
            <input id="agree" name="agree" type="checkbox" required class="mt-0.5 size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <label for="agree" class="text-sm text-slate-600">
                I agree to the
                <a href="#" class="text-brand-600 hover:text-brand-700 font-medium">Terms of Service</a>
                and
                <a href="#" class="text-brand-600 hover:text-brand-700 font-medium">Privacy Policy</a>
            </label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-700 focus:outline-none focus:ring-3 focus:ring-brand-500/30 transition-colors">
            Submit registration
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('client.login') }}" class="font-medium text-brand-600 hover:text-brand-700 transition-colors">
            Sign in
        </a>
    </p>

</x-client.layouts.auth>
