<x-client.layouts.app pageTitle="Scopes — {{ $country['name'] }}">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('client.maps') }}" class="hover:text-brand-600 transition-colors">Scope Maps</a>
        <svg class="size-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        <span class="text-slate-900 font-medium">{{ $country['flag'] }} {{ $country['name'] }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($scopes as $scope)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-slate-900 text-sm">{{ $scope['name'] }}</h3>
                    <span class="rounded-full bg-brand-50 border border-brand-200 px-2.5 py-0.5 text-xs font-medium text-brand-700 shrink-0 ml-2">
                        {{ $scope['turnaround'] }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $scope['description'] }}</p>
            </div>
        @endforeach
    </div>

</x-client.layouts.app>
