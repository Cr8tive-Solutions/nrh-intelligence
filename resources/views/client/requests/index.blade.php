<x-client.layouts.app pageTitle="Active Requests">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-slate-500 mt-0.5">Requests currently being processed</p>
        </div>
        <a href="{{ route('client.request.new') }}"
           class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Request
        </a>
    </div>

    {{-- Status filter --}}
    <div class="flex items-center gap-2 mb-4" x-data="{ filter: 'all' }">
        @foreach (['all' => 'All', '1' => 'New', '2' => 'In Progress'] as $val => $label)
            <button
                @click="filter = '{{ $val }}'"
                :class="filter === '{{ $val }}' ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                class="rounded-lg border px-3.5 py-1.5 text-sm font-medium transition-colors"
            >{{ $label }}</button>
        @endforeach

        <div class="ml-auto relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="text" placeholder="Search requests..." class="rounded-lg border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors w-56"/>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Request ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Candidates</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Submitted</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-medium text-slate-700">{{ $req['reference'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-sm text-slate-700">
                                    <svg class="size-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                    </svg>
                                    {{ $req['candidates_count'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php $sid = $req['status_id']; @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium
                                    {{ $sid === 1 ? 'bg-blue-50 text-blue-700 border-blue-200' : ($sid === 2 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                    <span class="size-1.5 rounded-full {{ $sid === 1 ? 'bg-blue-500' : ($sid === 2 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    {{ $req['status'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $req['created_at'] }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('client.requests.details', $req['id']) }}"
                                   class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <svg class="mx-auto size-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                </svg>
                                <p class="text-sm text-slate-400">No active requests.</p>
                                <a href="{{ route('client.request.new') }}" class="mt-2 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client.layouts.app>
