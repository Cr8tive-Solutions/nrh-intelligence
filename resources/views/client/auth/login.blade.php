<x-client.layouts.auth title="Sign In">

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Welcome back</h2>
        <p class="mt-1 text-sm text-slate-500">Sign in to your client portal</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('client.login.submit') }}" class="space-y-4">
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

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <a href="{{ route('client.forgot') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                    Forgot password?
                </a>
            </div>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    name="password"
                    :type="show ? 'text' : 'password'"
                    autocomplete="current-password"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-900 placeholder-slate-400 shadow-xs
                           focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors
                           @error('password') border-red-400 @enderror"
                    placeholder="••••••••"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600">
                    <svg x-show="!show" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                    <svg x-show="show" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input id="remember" name="remember" type="checkbox" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <label for="remember" class="text-sm text-slate-600">Keep me signed in</label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-700 focus:outline-none focus:ring-3 focus:ring-brand-500/30 transition-colors">
            Sign in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Don't have an account?
        <a href="{{ route('client.register') }}" class="font-medium text-brand-600 hover:text-brand-700 transition-colors">
            Register your company
        </a>
    </p>

</x-client.layouts.auth>
