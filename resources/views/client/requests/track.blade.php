<x-client.layouts.app pageTitle="Track Request">

    {{-- Search --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-1">Search by Candidate</h3>
            <p class="text-xs text-slate-500 mb-4">Enter a candidate name or identity number to track their verification status.</p>
            <form method="POST" action="{{ route('client.requests.track.search') }}" class="flex gap-3">
                @csrf
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Name or identity number..."
                        class="w-full rounded-lg border border-slate-300 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"
                        autofocus
                    />
                </div>
                <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                    Search
                </button>
            </form>
        </div>

        {{-- Results --}}
        @if ($results !== null)
            @if ($results->isEmpty())
                <div class="bg-white rounded-xl border border-slate-200 py-16 text-center">
                    <svg class="mx-auto size-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <p class="text-sm text-slate-400">No candidates found for <span class="font-medium text-slate-600">"{{ $query }}"</span></p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($results as $result)
                        <div class="bg-white rounded-xl border border-slate-200 p-5">
                            {{-- Candidate header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $result['candidate_name'] }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $result['identity_number'] }}</p>
                                </div>
                                <a href="{{ route('client.requests.details', $result['request_id']) }}" class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">
                                    {{ $result['request_reference'] }} →
                                </a>
                            </div>

                            {{-- Status steps --}}
                            <div class="flex items-center gap-0 mb-4">
                                @php
                                    $trackSteps = [1 => 'Received', 2 => 'Processing', 3 => 'Complete'];
                                    $currentStep = $result['status_id'];
                                @endphp
                                @foreach ($trackSteps as $stepNum => $stepLabel)
                                    <div class="flex items-center {{ $stepNum < count($trackSteps) ? 'flex-1' : '' }}">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="size-7 rounded-full flex items-center justify-center text-xs font-semibold
                                                {{ $stepNum < $currentStep ? 'bg-emerald-500 text-white' : ($stepNum === $currentStep ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                                                @if ($stepNum < $currentStep)
                                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                @else
                                                    {{ $stepNum }}
                                                @endif
                                            </div>
                                            <span class="text-xs {{ $stepNum <= $currentStep ? 'text-slate-700 font-medium' : 'text-slate-400' }}">{{ $stepLabel }}</span>
                                        </div>
                                        @if ($stepNum < count($trackSteps))
                                            <div class="flex-1 h-px mx-2 mb-4 {{ $stepNum < $currentStep ? 'bg-emerald-400' : 'bg-slate-200' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Scopes --}}
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($result['scopes'] as $scope)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">{{ $scope }}</span>
                                @endforeach
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Last updated: {{ $result['updated_at'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

</x-client.layouts.app>
