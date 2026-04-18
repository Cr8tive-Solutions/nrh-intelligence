<x-client.layouts.auth title="Two-Factor Verification">

    <div class="mb-6 text-center">
        <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-full bg-brand-50 border border-brand-200">
            <svg class="size-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Check your email</h2>
        <p class="mt-1 text-sm text-slate-500">
            We sent a 6-digit verification code to<br>
            <span class="font-medium text-slate-700">{{ session('2fa_email', 'your email') }}</span>
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.verification.submit') }}" x-data="{ code: '' }">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-3 text-center">Verification code</label>
            {{-- Hidden real input --}}
            <input type="hidden" name="code" x-model="code">
            {{-- Visual digit inputs --}}
            <div class="flex items-center justify-center gap-2"
                 x-data="{
                    digits: ['','','','','',''],
                    handleInput(i, e) {
                        const val = e.target.value.replace(/\D/g, '').slice(-1);
                        this.digits[i] = val;
                        this.code = this.digits.join('');
                        if (val && i < 5) $refs['d'+(i+1)].focus();
                    },
                    handleKeydown(i, e) {
                        if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                            this.digits[i-1] = '';
                            this.code = this.digits.join('');
                            $refs['d'+(i-1)].focus();
                        }
                    },
                    handlePaste(e) {
                        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
                        text.split('').forEach((c,i) => { if(i < 6) this.digits[i] = c; });
                        this.code = this.digits.join('');
                        $nextTick(() => $refs['d'+(Math.min(text.length,5))].focus());
                    }
                 }"
                 @paste.prevent="handlePaste($event)"
            >
                <template x-for="i in [0,1,2,3,4,5]" :key="i">
                    <input
                        :x-ref="'d'+i"
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        :value="digits[i]"
                        @input="handleInput(i, $event)"
                        @keydown="handleKeydown(i, $event)"
                        class="size-11 rounded-lg border border-slate-300 bg-white text-center text-lg font-semibold text-slate-900 shadow-xs
                               focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"
                    />
                </template>
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-700 focus:outline-none focus:ring-3 focus:ring-brand-500/30 transition-colors">
            Verify & Sign in
        </button>
    </form>

    <div class="mt-5 text-center">
        <p class="text-sm text-slate-500">
            Didn't receive the code?
        </p>
        <form method="POST" action="{{ route('client.verification.resend') }}" class="mt-1">
            @csrf
            <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                Resend code
            </button>
        </form>
        <a href="{{ route('client.login') }}" class="mt-2 block text-sm text-slate-400 hover:text-slate-600 transition-colors">
            ← Back to sign in
        </a>
    </div>

</x-client.layouts.auth>
