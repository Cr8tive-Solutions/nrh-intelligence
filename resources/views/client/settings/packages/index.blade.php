<x-client.layouts.app pageTitle="Packages">

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Saved scope bundles for quick request creation</p>
        <button
            x-data
            @click="$dispatch('open-create-package')"
            class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Package
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse ($packages as $pkg)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $pkg['name'] }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $pkg['country'] }}</p>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($pkg['scopes'] as $scope)
                        <span class="rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $scope }}</span>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-3">Created {{ $pkg['created_at'] }}</p>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl border border-dashed border-slate-200 py-16 text-center">
                <svg class="mx-auto size-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm text-slate-400">No packages saved yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Create package modal --}}
    <div
        x-data="{ open: false, selected: [] }"
        @open-create-package.window="open = true"
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-slate-900">New Package</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Package Name</label>
                    <input type="text" placeholder="e.g. Standard Screening"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-2">Select Scopes</label>
                    <div class="space-y-2">
                        @foreach ($allScopes as $scope)
                            <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-3.5 py-2.5 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="checkbox" value="{{ $scope['id'] }}" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm text-slate-700">{{ $scope['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="open = false"
                        class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">Save Package</button>
                </div>
            </form>
        </div>
    </div>

</x-client.layouts.app>
