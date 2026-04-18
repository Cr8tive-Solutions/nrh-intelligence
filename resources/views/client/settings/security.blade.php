<x-client.layouts.app pageTitle="Security">

    <div class="max-w-md space-y-5">

        <div class="bg-white rounded-xl border border-slate-200 p-6" x-data="{ show: { current: false, new: false, confirm: false } }">
            <h3 class="text-sm font-semibold text-slate-900 mb-5">Change Password</h3>

            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <svg class="size-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('client.settings.security') }}" class="space-y-4">
                @csrf

                @foreach ([
                    ['current_password', 'Current Password', 'current'],
                    ['password',         'New Password',     'new'],
                    ['password_confirmation', 'Confirm New Password', 'confirm'],
                ] as [$name, $label, $ref])
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ $label }}</label>
                        <div class="relative">
                            <input
                                :type="show.{{ $ref }} ? 'text' : 'password'"
                                name="{{ $name }}"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 pr-10 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors @error($name) border-red-400 @enderror"
                                placeholder="••••••••"
                            />
                            <button type="button" @click="show.{{ $ref }} = !show.{{ $ref }}" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </button>
                        </div>
                        @error($name)
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="flex justify-end pt-2">
                    <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- 2FA info --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Two-Factor Authentication</h3>
                    <p class="text-xs text-slate-500 mt-0.5">A verification code is emailed on every login.</p>
                </div>
                <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Enabled</span>
            </div>
        </div>

    </div>

</x-client.layouts.app>
