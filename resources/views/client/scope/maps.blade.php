<x-client.layouts.app pageTitle="Scope Maps">

    <div class="mb-6">
        <p class="text-sm text-slate-500">Countries where background verification services are available</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($countries as $country)
            <a href="{{ route('client.maps.country', $country['id']) }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 hover:border-brand-300 hover:shadow-sm transition-all">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-3xl">{{ $country['flag'] }}</span>
                    <span class="rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                        {{ $country['code'] }}
                    </span>
                </div>
                <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 transition-colors">{{ $country['name'] }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $country['region'] }}</p>
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                    <span class="text-xs text-slate-500">
                        <span class="font-semibold text-slate-900">{{ $country['scope_count'] }}</span> scopes available
                    </span>
                    <svg class="size-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

</x-client.layouts.app>
